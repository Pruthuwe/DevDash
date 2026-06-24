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

        // Web Enquiry source gets High priority (as per business rule)
        $data = $validator->validated();
        $data['priority'] = ($data['lead_source'] === 'Web Enquiry') ? 'High' : 'Normal';

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
                    'priority'                 => ($source === 'Web Enquiry') ? 'High' : 'Normal',
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

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name',  'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('city',  'like', "%{$s}%");
            });
        }

        // Filters
        if ($request->filled('status'))      $query->where('status',      $request->status);
        if ($request->filled('source'))      $query->where('lead_source', $request->source);
        if ($request->filled('priority'))    $query->where('priority',    $request->priority);
        if ($request->filled('assigned_to')) $query->where('assigned_to', $request->assigned_to);

        // Date range
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);

        // Web Enquiry leads always float to top (priority rule from notes)
        $query->orderByRaw("FIELD(lead_source, 'Web Enquiry') DESC")
              ->orderByRaw("FIELD(priority, 'High', 'Normal', 'Low')")
              ->orderBy('created_at', 'desc');

        $leads = $query->paginate(15)->withQueryString();

        $stats = [
            'total'      => Lead::count(),
            'unassigned' => Lead::where('status', 'Unassigned')->count(),
            'interested' => Lead::where('status', 'Interested')->count(),
            'converted'  => Lead::where('status', 'Converted')->count(),
        ];

        $officers = User::where('status', 'active')
                        ->whereNotNull('role_id')
                        ->orderBy('name')
                        ->get();

        $brands = Category::whereNotNull('parent_id')->orderBy('name')->get(['id', 'name']);

        return view('leads.list', compact('leads', 'stats', 'officers', 'brands'));
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
            'leads_per_officer' => round(Lead::whereNotNull('assigned_to')->count() / max(1, User::whereNotNull('role_id')->count()), 1),
            'assigned_today'  => Lead::whereNotNull('assigned_to')->whereDate('updated_at', today())->count(),
        ];

        $officers = User::where('status','active')->whereNotNull('role_id')->orderBy('name')->get();
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
        $officers = User::where('status','active')->whereNotNull('role_id')->orderBy('name')->get();
        $selectedOfficer = null;
        $leads = collect();

        if ($request->filled('officer_id')) {
            $selectedOfficer = User::find($request->officer_id);
            $query = Lead::with(['followups.doneBy', 'officer', 'brand', 'modelProduct'])
                         ->where('assigned_to', $request->officer_id);

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(fn($q) => $q->where('name','like',"%{$s}%")->orWhere('phone','like',"%{$s}%"));
            }

            $leads = $query->orderBy('created_at','desc')->paginate(10)->withQueryString();
        }

        return view('leads.followup', compact('officers','selectedOfficer','leads'));
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
            'followup_at' => 'required|date',
            'method'      => 'required|in:Call,Visit,WhatsApp,Email,Other',
            'feedback'    => 'nullable|string|max:1000',
            'status'      => 'required|in:Interested,Need More Info,Follow Up Later,Not Interested,Converted,Closed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $followup = $lead->followups()->create([
            ...$validator->validated(),
            'done_by' => Auth::id(),
        ]);

        // Update lead status to match latest follow-up status
        $lead->update(['status' => $request->status]);

        $followup->load('doneBy');

        return response()->json([
            'success'  => true,
            'message'  => 'Follow-up saved successfully.',
            'followup' => $followup,
            'lead_status' => $lead->status,
        ]);
    }
}
