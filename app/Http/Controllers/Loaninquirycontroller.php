<?php

namespace App\Http\Controllers;

use App\Models\LoanInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoanInquiryController extends Controller
{
    /**
     * Store loan inquiry from public Loan Calculator popup.
     * No authentication required (guest lead capture).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id'       => 'nullable|exists:products,id',
            'name'             => 'required|string|max:255',
            'phone'            => 'required|string|max:30',
            'city'             => 'nullable|string|max:120',
            'bike_price'       => 'nullable|numeric|min:0',
            'loan_amount'      => 'nullable|numeric|min:0',
            'bike_dp'          => 'nullable|numeric|min:0',
            'service_charge'   => 'nullable|numeric|min:0',
            'rmv'              => 'nullable|numeric|min:0',
            'minimum_dp'       => 'nullable|numeric|min:0',
            'interest_rate'    => 'nullable|numeric|min:0|max:100',
            'loan_term_months' => 'nullable|integer|min:1|max:120',
            'monthly_payment'  => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please check the details you entered.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $loanInquiry = LoanInquiry::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Thanks! Our team will contact you shortly.',
            'data'    => $loanInquiry,
        ], 201);
    }

    public function manage(Request $request)
    {
        if (Auth::user()->user_type !== 'admin' && (!Auth::user()->role || !Auth::user()->role->permissions->contains('name', 'view-loan-inquiries'))) {
            abort(403, 'You do not have permission to view loan inquiries.');
        }

        $query = LoanInquiry::with('product');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $loanInquiries = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total'     => LoanInquiry::count(),
            'new'       => LoanInquiry::where('status', 'new')->count(),
            'contacted' => LoanInquiry::where('status', 'contacted')->count(),
            'closed'    => LoanInquiry::where('status', 'closed')->count(),
        ];

        return view('loan_inquiries.manage', compact('loanInquiries', 'stats'));
    }

    public function updateStatus(Request $request, LoanInquiry $loanInquiry)
    {
        if (Auth::user()->user_type !== 'admin' && (!Auth::user()->role || !Auth::user()->role->permissions->contains('name', 'edit-loan-inquiries'))) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:new,contacted,closed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $loanInquiry->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'data'    => ['id' => $loanInquiry->id, 'status' => $loanInquiry->status],
        ]);
    }

    public function destroy(LoanInquiry $loanInquiry)
    {
        if (Auth::user()->user_type !== 'admin' && (!Auth::user()->role || !Auth::user()->role->permissions->contains('name', 'delete-loan-inquiries'))) {
            abort(403);
        }

        $loanInquiry->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json(['success' => true, 'message' => 'Loan inquiry deleted successfully']);
        }

        return redirect()->route('manage.loan-inquiries')->with('success', 'Loan inquiry deleted successfully');
    }
}