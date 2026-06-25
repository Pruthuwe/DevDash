<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Lead;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // Sub-cat 1: LEAD CAPTURE — Add New Lead form
    // ─────────────────────────────────────────────────────────────

    public function create()
    {
        $brands = Category::whereNotNull('parent_id')->orderBy('name')->get(['id', 'name']);
        return view('leads.capture', compact('brands'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                      => 'required|string|max:255',
            'phone'                     => 'required|string|max:30',
            'email'                     => 'nullable|email|max:255',
            'address'                   => 'nullable|string|max:500',
            'city'                      => 'nullable|string|max:120',
            'customer_type'             => 'required|in:Individual,Dealer,Other',
            'company_name'              => 'nullable|string|max:255',
            'vehicle_brand_id'          => 'nullable|exists:categories,id',
            'vehicle_model_product_id'  => 'nullable|exists:products,id',
            'vehicle_model_text'        => 'nullable|string|max:150',
            'budget_range'              => 'nullable|numeric|min:0',
            'quantity_needed'           => 'nullable|integer|min:1',
            'lead_source'               => 'required|in:Walk-in,Web Enquiry,Social Media,Phone Call,Other',
            'notes'                     => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // Same person contacting again through a different channel: keep one
        // lead record and update lead_source to whichever channel is most
        // recent, rather than create a duplicate. "Same person" = same phone
        // number, the only field that's reliably present on every channel.
        $existing = Lead::where('phone', $data['phone'])->latest()->first();
        if ($existing) {
            $existing->update(array_merge($data, ['lead_source' => $data['lead_source']]));
            return redirect()->route('leads.list')
                ->with('success', 'Existing lead updated — contacted again via ' . $data['lead_source'] . '.');
        }

        Lead::create($data);

        return redirect()->route('leads.list')
            ->with('success', 'Lead added successfully.');
    }

    /**
     * Get models (product names) for a given brand — AJAX, used by both the
     * Add Lead form and the Lead Assignment filter bar.
     * Reuses the same products table the bike catalog is built on.
     */
    public function getModelsForBrand(Category $brand)
    {
        $models = Product::where('subcategory_id', $brand->id)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->unique('name')
            ->values();

        return response()->json(['models' => $models]);
    }
    

    /**
     * Import leads from Excel
     * Required columns: name, phone
     * Optional: email, address, city, customer_type, company_name,
     *           vehicle_brand, vehicle_model, budget_range, quantity_needed,
     *           lead_source
     * vehicle_brand is matched against existing Brand (subcategory) names;
     * vehicle_model is stored as free text unless it matches an existing
     * product name for that brand, in which case it's linked.
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            $file = $request->file('excel_file');
            $data = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $sheet = $data->getActiveSheet()->toArray(null, true, true, true);

            // First row = headers
            $headers = array_map('strtolower', array_map('trim', $sheet[1]));
            $headerMap = array_flip($headers);

            $brandsByName = Category::whereNotNull('parent_id')->get()->keyBy(fn($c) => strtolower($c->name));

            $imported = 0;
            $skipped  = [];

            for ($i = 2; $i <= count($sheet); $i++) {
                $row = $sheet[$i];
                $values = array_values($row);

                $name  = $values[$headerMap['name']  ?? 0] ?? null;
                $phone = $values[$headerMap['phone'] ?? 1] ?? null;

                if (!$name || !$phone) {
                    $skipped[] = "Row {$i}: missing name or phone";
                    continue;
                }

                $brandName = trim($values[$headerMap['vehicle_brand'] ?? 99] ?? '');
                $brand     = $brandName ? $brandsByName->get(strtolower($brandName)) : null;

                $modelText = trim($values[$headerMap['vehicle_model'] ?? 99] ?? '');
                $modelProductId = null;
                if ($brand && $modelText) {
                    $modelProductId = Product::where('subcategory_id', $brand->id)
                        ->where('name', 'like', $modelText)
                        ->value('id');
                }

                $source = $values[$headerMap['lead_source'] ?? 99] ?? 'Other';
                $customerType = $values[$headerMap['customer_type'] ?? 99] ?? 'Individual';

                Lead::create([
                    'name'                     => $name,
                    'phone'                    => $phone,
                    'email'                    => $values[$headerMap['email']            ?? 99] ?? null,
                    'address'                  => $values[$headerMap['address']          ?? 99] ?? null,
                    'city'                     => $values[$headerMap['city']             ?? 99] ?? null,
                    'customer_type'            => in_array($customerType, ['Individual', 'Dealer', 'Other']) ? $customerType : 'Individual',
                    'company_name'             => $values[$headerMap['company_name']     ?? 99] ?? null,
                    'vehicle_brand_id'         => $brand?->id,
                    'vehicle_model_product_id' => $modelProductId,
                    'vehicle_model_text'       => $modelText ?: null,
                    'budget_range'             => $values[$headerMap['budget_range']     ?? 99] ?? null,
                    'quantity_needed'          => $values[$headerMap['quantity_needed']  ?? 99] ?? 1,
                    'lead_source'              => in_array($source, ['Walk-in','Web Enquiry','Social Media','Phone Call','Other']) ? $source : 'Other',
                ]);
                $imported++;
            }

            $message = "{$imported} leads imported successfully.";
            if (count($skipped)) {
                $message .= ' ' . count($skipped) . ' row(s) skipped (missing name/phone).';
            }

            return response()->json(['success' => true, 'message' => $message, 'skipped' => $skipped]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Import failed: ' . $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Sub-cat 2: LEAD LIST — all leads table
    // ─────────────────────────────────────────────────────────────

    public function list(Request $request)
    {
        $query = Lead::with(['officer', 'latestFollowup', 'brand', 'modelProduct']);

        $this->applyListFilters($query, $request);

        // Web Enquiry leads float to top, then most recently updated first.
        $query->orderByRaw("FIELD(lead_source, 'Web Enquiry') DESC")
              ->orderBy('updated_at', 'desc');

        $leads = $query->paginate(15)->withQueryString();

        $stats = [
            'total'      => Lead::count(),
            'unassigned' => Lead::where('status', 'Unassigned')->count(),
            'interested' => Lead::where('status', 'Interested')->count(),
            'converted'  => Lead::where('status', 'Converted')->count(),
        ];

        $brands = Category::whereNotNull('parent_id')->orderBy('name')->get(['id', 'name']);

        return view('leads.list', compact('leads', 'stats', 'brands'));
    }

    /**
     * Shared search/source/date filters between the list page and CSV export
     * — keeps both reading from the same rules instead of drifting apart.
     * Lead List intentionally has no status / assigned-to filter (per
     * business decision — that belongs on the Assignment screen instead).
     */
    private function applyListFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name',  'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('city',  'like', "%{$s}%");
            });
        }

        if ($request->filled('source')) $query->where('lead_source', $request->source);

        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);
    }

    /**
     * Export the (optionally filtered) lead list as CSV.
     */
    public function exportList(Request $request)
    {
        $query = Lead::with(['officer', 'brand', 'modelProduct']);
        $this->applyListFilters($query, $request);
        $leads = $query->orderBy('created_at', 'desc')->get();

        $filename = 'leads-export-' . now()->format('Y-m-d_His') . '.csv';

        $columns = ['ID', 'Name', 'Phone', 'Email', 'City', 'Customer Type', 'Company',
                    'Brand', 'Model', 'Budget Range', 'Quantity Needed', 'Lead Source',
                    'Status', 'Assigned To', 'Created At'];

        return response()->streamDownload(function () use ($leads, $columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            foreach ($leads as $lead) {
                fputcsv($out, [
                    $lead->id,
                    $lead->name,
                    $lead->phone,
                    $lead->email,
                    $lead->city,
                    $lead->customer_type,
                    $lead->company_name,
                    $lead->brand?->name,
                    $lead->vehicle_model_display,
                    $lead->budget_range,
                    $lead->quantity_needed,
                    $lead->lead_source,
                    $lead->status,
                    $lead->officer?->name,
                    $lead->created_at?->format('Y-m-d H:i'),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Get single lead details (AJAX — used by lead detail modal)
     */
    public function show(Lead $lead)
    {
        $lead->load(['officer', 'followups.doneBy', 'brand', 'modelProduct']);
        return response()->json(['success' => true, 'data' => $lead]);
    }

    /**
     * Update lead details (AJAX from detail modal Save button)
     */
    public function update(Request $request, Lead $lead)
    {
        $validator = Validator::make($request->all(), [
            'name'                      => 'required|string|max:255',
            'phone'                     => 'required|string|max:30',
            'email'                     => 'nullable|email|max:255',
            'address'                   => 'nullable|string|max:500',
            'city'                      => 'nullable|string|max:120',
            'customer_type'             => 'required|in:Individual,Dealer,Other',
            'company_name'              => 'nullable|string|max:255',
            'vehicle_brand_id'          => 'nullable|exists:categories,id',
            'vehicle_model_product_id'  => 'nullable|exists:products,id',
            'vehicle_model_text'        => 'nullable|string|max:150',
            'budget_range'              => 'nullable|numeric|min:0',
            'quantity_needed'           => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $lead->update($validator->validated());
        $lead->load(['brand', 'modelProduct']);

        return response()->json(['success' => true, 'message' => 'Lead details saved.', 'data' => $lead]);
    }

    /**
     * Delete a lead
     */
    public function destroy(Lead $lead)
    {
        $lead->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Lead deleted.']);
        }
        return redirect()->route('leads.list')->with('success', 'Lead deleted successfully.');
    }

    // ─────────────────────────────────────────────────────────────
    // Sub-cat 3: LEAD ASSIGNMENT
    // ─────────────────────────────────────────────────────────────

    public function assignment(Request $request)
    {
        $query = Lead::with(['officer', 'latestFollowup', 'brand', 'modelProduct']);

        if ($request->filled('customer_type'))     $query->where('customer_type',      $request->customer_type);
        if ($request->filled('vehicle_brand_id'))   $query->where('vehicle_brand_id',   $request->vehicle_brand_id);
        if ($request->filled('vehicle_model_product_id')) $query->where('vehicle_model_product_id', $request->vehicle_model_product_id);
        if ($request->filled('status'))             $query->where('status',             $request->status);
        if ($request->filled('sales_officer'))      $query->where('assigned_to',        $request->sales_officer);
        if ($request->filled('search'))  {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name','like',"%{$s}%")->orWhere('phone','like',"%{$s}%")->orWhere('id','like',"%{$s}%"));
        }

        $leads = $query->orderByRaw("FIELD(lead_source,'Web Enquiry') DESC")
                       ->orderBy('created_at','desc')
                       ->paginate(10)
                       ->withQueryString();

        $stats = [
            'total'           => Lead::count(),
            'unassigned'      => Lead::whereNull('assigned_to')->count(),
            'assigned'        => Lead::whereNotNull('assigned_to')->count(),
            'leads_per_officer' => round(Lead::whereNotNull('assigned_to')->count() / max(1, User::withLeadsPermission('edit')->count()), 1),
            'assigned_today'  => Lead::whereNotNull('assigned_to')->whereDate('updated_at', today())->count(),
        ];

        $officers = User::withLeadsPermission('edit')->orderBy('name')->get();
        $brands   = Category::whereNotNull('parent_id')->orderBy('name')->get(['id', 'name']);

        return view('leads.assignment', compact('leads','stats','officers','brands'));
    }

    /**
     * Assign selected leads to a sales officer (bulk)
     */
    public function assignLeads(Request $request)
    {
        $request->validate([
            'lead_ids'    => 'required|array',
            'lead_ids.*'  => 'exists:leads,id',
            'assigned_to' => 'required|exists:users,id',
        ]);

        $officer = User::withLeadsPermission('edit')->find($request->assigned_to);
        if (!$officer) {
            return response()->json([
                'success' => false,
                'message' => 'That user does not have a role with leads permission.',
            ], 422);
        }

        Lead::whereIn('id', $request->lead_ids)
            ->update([
                'assigned_to' => $request->assigned_to,
                'status'      => DB::raw("CASE WHEN status = 'Unassigned' THEN 'Interested' ELSE status END"),
            ]);

        return response()->json([
            'success' => true,
            'message' => count($request->lead_ids) . ' lead(s) assigned successfully.',
        ]);
    }

    /**
     * Get a single lead's assignment history (AJAX for modal)
     */
    public function assignmentHistory(Lead $lead)
    {
        $lead->load(['officer', 'followups.doneBy', 'brand', 'modelProduct']);
        return response()->json(['success' => true, 'data' => $lead]);
    }

    // ─────────────────────────────────────────────────────────────
    // Sub-cat 4: LEAD FOLLOW-UP
    // ─────────────────────────────────────────────────────────────

    public function followUp(Request $request)
    {
        $officers = User::withLeadsPermission('edit')->orderBy('name')->get();
        $selectedOfficer = null;
        $leads = collect();

        if ($request->filled('officer_id')) {
            $selectedOfficer = User::find($request->officer_id);
            $query = Lead::with(['latestFollowup', 'officer', 'brand', 'modelProduct'])
                         ->withCount('followups')
                         ->where('assigned_to', $request->officer_id);

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(fn($q) => $q->where('name','like',"%{$s}%")->orWhere('phone','like',"%{$s}%"));
            }

            $leads = $query->orderBy('created_at','desc')->paginate(10)->withQueryString();
        }

        // Reminder banner: leads with a scheduled next-call date that's due
        // today or overdue. Scoped to leads this user is allowed to assign
        // (i.e. everyone, for admins/leads-permission viewers) so it's a
        // useful "what's due" list rather than just the currently selected
        // officer's leads.
        $dueFollowups = Lead::with(['officer', 'latestFollowup'])
            ->followupDue()
            ->orderBy('updated_at')
            ->limit(10)
            ->get();

        return view('leads.followup', compact('officers', 'selectedOfficer', 'leads', 'dueFollowups'));
    }

    /**
     * Get lead + follow-up history for the detail modal (AJAX)
     */
    public function followUpDetail(Lead $lead)
    {
        $lead->load(['followups' => fn($q) => $q->with('doneBy')->orderBy('created_at','desc'), 'brand', 'modelProduct']);
        return response()->json(['success' => true, 'data' => $lead]);
    }

    /**
     * Save a new follow-up record
     */
    public function storeFollowUp(Request $request, Lead $lead)
    {
        $validator = Validator::make($request->all(), [
            'followup_at'      => 'required|date',
            'next_followup_at' => 'nullable|date|required_if:status,Need More Info,Follow Up Later',
            'method'           => 'required|in:Call,Visit,WhatsApp,Email,Other',
            'feedback'         => 'nullable|string|max:1000',
            'status'           => 'required|in:Interested,Need More Info,Follow Up Later,Not Interested,Converted,Sales Done',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // next_followup_at only makes sense while the lead still needs a
        // future callback — clear it for every other status so a stale date
        // can't keep triggering the reminder after the lead has moved on.
        if (!in_array($data['status'], ['Need More Info', 'Follow Up Later'])) {
            $data['next_followup_at'] = null;
        }

        $followup = $lead->followups()->create([
            ...$data,
            'done_by' => Auth::id(),
        ]);

        // Update lead status to match latest follow-up status
        $lead->update(['status' => $data['status']]);

        $followup->load('doneBy');

        return response()->json([
            'success'  => true,
            'message'  => 'Follow-up saved successfully.',
            'followup' => $followup,
            'lead_status' => $lead->status,
        ]);
    }
}
