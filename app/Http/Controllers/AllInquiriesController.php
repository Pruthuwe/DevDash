<?php

namespace App\Http\Controllers;

use App\Models\LoanInquiry;
use App\Models\ProductEnquiry;
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
}