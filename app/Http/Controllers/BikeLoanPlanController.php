<?php

namespace App\Http\Controllers;

use App\Models\BikeLoanPlan;
use App\Models\Product;
use App\Models\FinanceCompany;
use Illuminate\Http\Request;

class BikeLoanPlanController extends Controller
{
    /**
     * List all loan plans attached to a bike — used by the admin
     * Loan Calculator page (to show what's already saved) and can
     * also be hit as /api/products/{product}/loan-plans by React.
     */
    public function index(Request $request, Product $product)
    {
        $plans = BikeLoanPlan::with('financeCompany')
            ->where('product_id', $product->id)
            ->get()
            ->map(function ($plan) {
                return [
                    'id'                  => $plan->id,
                    'finance_company_id'  => $plan->finance_company_id,
                    'finance_company_name'=> $plan->financeCompany->name,
                    'loan_amount'         => (float) $plan->loan_amount,
                    'interest_rate'       => (float) $plan->interest_rate,
                    'rmv'                 => (float) $plan->rmv,
                    'service_charge'      => $plan->service_charge,
                ];
            });

        if ($request->is('api/*')) {
            return response()->json(['loan_plans' => $plans], 200);
        }

        return response()->json($plans);
    }

    /**
     * Create or update the loan plan for a bike + finance company pair.
     * (Admin "Loan Calculator" Save button.)
     */

 
 public function loanPlans(Request $request)
{
    $query = BikeLoanPlan::with(['product', 'financeCompany']);

    if ($request->filled('product_id')) {
        $query->where('product_id', $request->product_id);
    }

    if ($request->filled('finance_company_id')) {
        $query->where('finance_company_id', $request->finance_company_id);
    }

    $allLoanPlans = $query->get()
        ->sortBy(fn($p) => $p->product->name ?? '')
        ->values();

    $bikes            = Product::orderBy('name')->get(['id', 'name']);
    $financeCompanies = FinanceCompany::orderBy('name')->get(['id', 'name']);

    return view('bike_loan_plans.manage', compact('allLoanPlans', 'bikes', 'financeCompanies'));
}
 
    public function store(Request $request)
    {
        $request->validate([
            'product_id'         => 'required|exists:products,id',
            'finance_company_id' => 'required|exists:finance_companies,id',
            'loan_amount'        => 'required|numeric|min:0',
            'interest_rate'      => 'nullable|numeric|min:0|max:100',
            'rmv'                => 'nullable|numeric|min:0',
        ]);

        $plan = BikeLoanPlan::updateOrCreate(
            [
                'product_id'         => $request->product_id,
                'finance_company_id' => $request->finance_company_id,
            ],
            [
                'loan_amount'   => $request->loan_amount,
                'interest_rate' => $request->interest_rate ?? 1.5,
                'rmv'           => $request->rmv ?? 10160,
            ]
        );

        return response()->json([
            'message' => 'Loan plan saved successfully.',
            'plan'    => $plan->load('financeCompany'),
        ], 200);
    }

    /**
     * Remove a finance company's loan plan from a bike.
     */
    public function destroy(BikeLoanPlan $bikeLoanPlan)
    {
        $bikeLoanPlan->delete();

        return response()->json(['message' => 'Loan plan removed successfully.'], 200);
    }
}
