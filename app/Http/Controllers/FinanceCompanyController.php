<?php

namespace App\Http\Controllers;

use App\Models\FinanceCompany;
use Illuminate\Http\Request;

class FinanceCompanyController extends Controller
{
    /**
     * Display a listing of finance companies.
     * Admin Blade view, or JSON for /api/finance-companies (public, active only).
     */
    public function index(Request $request)
    {
        if ($request->is('api/*')) {
            $companies = FinanceCompany::where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name']);

            return response()->json(['finance_companies' => $companies], 200);
        }

        $financeCompanies = FinanceCompany::orderBy('name')->get();

        return view('finance_company.manage', compact('financeCompanies'));
    }

    /**
     * Store a newly created finance company.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ]);

        FinanceCompany::create([
            'name'   => $request->name,
            'status' => $request->status ?? 'active',
        ]);

        if ($request->is('api/*')) {
            return response()->json(['message' => 'Finance company created successfully.'], 201);
        }

        return redirect()->route('manage.finance-companies')->with('success', 'Finance company added successfully.');
    }

    /**
     * Update the specified finance company.
     */
    public function update(Request $request, FinanceCompany $financeCompany)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ]);

        $financeCompany->update([
            'name'   => $request->name,
            'status' => $request->status ?? $financeCompany->status,
        ]);

        if ($request->is('api/*')) {
            return response()->json(['message' => 'Finance company updated successfully.'], 200);
        }

        return redirect()->route('manage.finance-companies')->with('success', 'Finance company updated successfully.');
    }

    /**
     * Remove the specified finance company.
     */
    public function destroy(FinanceCompany $financeCompany)
    {
        $financeCompany->delete();

        if (request()->is('api/*')) {
            return response()->json(['message' => 'Finance company deleted successfully.'], 200);
        }

        return redirect()->route('manage.finance-companies')->with('success', 'Finance company deleted successfully.');
    }
}
