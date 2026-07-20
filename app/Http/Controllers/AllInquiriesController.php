<?php

namespace App\Http\Controllers;

use App\Models\LoanInquiry;
use App\Models\ProductEnquiry;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AllInquiriesController extends Controller
{
    public function manage(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->user_type === 'admin';

        $canViewLoan    = $isAdmin || optional(optional($user->role)->permissions)->contains('name', 'view-loan-inquiries');
        $canViewProduct = $isAdmin || optional(optional($user->role)->permissions)->contains('name', 'view-product-enquiries');

        if (!$canViewLoan && !$canViewProduct) {
            abort(403, 'You do not have permission to view inquiries.');
        }

        // Loan Inquiries
        $loanQuery = LoanInquiry::with('product');
        if ($request->filled('search')) {
            $s = $request->search;
            $loanQuery->where(function ($q) use ($s) {
                $q->where('name',  'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('city',  'like', "%{$s}%");
            });
        }
        if ($request->filled('status')) {
            $loanQuery->where('status', $request->status);
        }
        $loanInquiries = $canViewLoan
            ? $loanQuery->orderBy('created_at', 'desc')->paginate(15, ['*'], 'loan_page')
            : collect();

        // Product Enquiries
        $productQuery = ProductEnquiry::with('product');
        if ($request->filled('search')) {
            $s = $request->search;
            $productQuery->where(function ($q) use ($s) {
                $q->where('name',  'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('city',  'like', "%{$s}%");
            });
        }
        if ($request->filled('status')) {
            $productQuery->where('status', $request->status);
        }
        $productEnquiries = $canViewProduct
            ? $productQuery->orderBy('created_at', 'desc')->paginate(15, ['*'], 'product_page')
            : collect();

        // Stats (combined)
        $loanTotal    = $canViewLoan    ? LoanInquiry::count()   : 0;
        $productTotal = $canViewProduct ? ProductEnquiry::count() : 0;

        $stats = [
            'total'     => $loanTotal + $productTotal,
            'new'       => ($canViewLoan    ? LoanInquiry::where('status',    'new')->count() : 0)
                         + ($canViewProduct ? ProductEnquiry::where('status', 'new')->count() : 0),
            'contacted' => ($canViewLoan    ? LoanInquiry::where('status',    'contacted')->count() : 0)
                         + ($canViewProduct ? ProductEnquiry::where('status', 'contacted')->count() : 0),
            'closed'    => ($canViewLoan    ? LoanInquiry::where('status',    'closed')->count() : 0)
                         + ($canViewProduct ? ProductEnquiry::where('status', 'closed')->count() : 0),
        ];

        return view('all_inquiries.manage', compact('loanInquiries', 'productEnquiries', 'stats'));
    }

    /**
     * Update loan inquiry status — auto-create lead when status = 'contacted'
     */
    public function updateLoanInquiryStatus(Request $request, LoanInquiry $loanInquiry)
    {
        $request->validate(['status' => 'required|in:new,contacted,closed']);

        $oldStatus = $loanInquiry->status;
        $loanInquiry->update(['status' => $request->status]);

        // Auto-create lead when status changes to 'contacted' and wasn't contacted before
        if ($request->status === 'contacted' && $oldStatus !== 'contacted') {
            $this->createLeadFromLoanInquiry($loanInquiry);
        }

        return response()->json(['success' => true, 'message' => 'Status updated.']);
    }

    /**
     * Update product enquiry status — auto-create lead when status = 'contacted'
     */
    public function updateProductEnquiryStatus(Request $request, ProductEnquiry $productEnquiry)
    {
        $request->validate(['status' => 'required|in:new,contacted,closed']);

        $oldStatus = $productEnquiry->status;
        $productEnquiry->update(['status' => $request->status]);

        // Auto-create lead when status changes to 'contacted' and wasn't contacted before
        if ($request->status === 'contacted' && $oldStatus !== 'contacted') {
            $this->createLeadFromProductEnquiry($productEnquiry);
        }

        return response()->json(['success' => true, 'message' => 'Status updated.']);
    }

    /**
     * Create a Lead from Loan Inquiry (Web Enquiry source)
     */
    private function createLeadFromLoanInquiry(LoanInquiry $inquiry)
    {
        // Check if lead already exists with same phone
        $existing = Lead::where('phone', $inquiry->phone)->first();
        if ($existing) return;

        Lead::create([
            'name'          => $inquiry->name,
            'phone'         => $inquiry->phone,
            'email'         => $inquiry->email,
            'address'       => $inquiry->address,
            'city'          => $inquiry->city,
            'customer_type' => 'Individual',
            'lead_source'   => 'Web Enquiry',
            'status'        => 'Unassigned',
            'notes'         => 'Auto-created from Loan Inquiry #' . $inquiry->id,
        ]);
    }

    /**
     * Create a Lead from Product Enquiry (Web Enquiry source)
     */
    private function createLeadFromProductEnquiry(ProductEnquiry $enquiry)
    {
        // Check if lead already exists with same phone
        $existing = Lead::where('phone', $enquiry->phone)->first();
        if ($existing) return;

        Lead::create([
            'name'          => $enquiry->name,
            'phone'         => $enquiry->phone,
            'email'         => $enquiry->email,
            'address'       => $enquiry->address,
            'city'          => $enquiry->city,
            'customer_type' => 'Individual',
            'lead_source'   => 'Web Enquiry',
            'status'        => 'Unassigned',
            'notes'         => 'Auto-created from Product Enquiry #' . $enquiry->id,
        ]);
    }
}