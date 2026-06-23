<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class LoanCalculatorController extends Controller
{
    public function index()
    {
        // All fuel types with their brands
        $fuelTypesRaw = Category::whereNull('parent_id')
            ->where('status', 'active')
            ->with(['children' => fn($q) => $q->where('status', 'active')->orderBy('name')])
            ->orderBy('name')
            ->get();

        // Build clean array for JS — no closures in the view
        $fuelTypes = $fuelTypesRaw->map(function($ft) {
            return [
                'id'                       => $ft->id,
                'name'                     => $ft->name,
                'min_down_payment_percent' => $ft->min_down_payment_percent,
                'brands'                   => $ft->children->map(function($b) {
                    return [
                        'id'                       => $b->id,
                        'name'                     => $b->name,
                        'min_down_payment_percent' => $b->min_down_payment_percent,
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        // All active products — clean array for JS
        $productsRaw = Product::where('status', 'active')
            ->with(['category', 'subcategory'])
            ->orderBy('name')
            ->get(['id', 'name', 'category_id', 'subcategory_id',
                   'price', 'sale_price', 'loan_amount', 'rmv',
                   'service_charge', 'interest_rate']);

        $products = $productsRaw->map(function($p) {
            return [
                'id'            => $p->id,
                'name'          => $p->name,
                'category_id'   => $p->category_id,
                'subcategory_id'=> $p->subcategory_id,
                'price'         => floatval($p->sale_price ?? $p->price),
                'loan_amount'   => floatval($p->loan_amount ?? 0),
                'rmv'           => floatval($p->rmv ?? 10160),
                'interest_rate' => floatval($p->interest_rate ?? 1.5),
            ];
        })->values()->toArray();

        $financeCompanies = \App\Models\FinanceCompany::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return view('loan_calculator.index', compact('fuelTypes', 'products', 'financeCompanies'));
    }

    /**
     * JSON version of the same data, for the React frontend.
     * GET /api/loan-calculator-data
     */
    public function apiIndex()
    {
        $fuelTypesRaw = Category::whereNull('parent_id')
            ->where('status', 'active')
            ->with(['children' => fn($q) => $q->where('status', 'active')->orderBy('name')])
            ->orderBy('name')
            ->get();

        $fuelTypes = $fuelTypesRaw->map(function($ft) {
            return [
                'id'                       => $ft->id,
                'name'                     => $ft->name,
                'min_down_payment_percent' => $ft->min_down_payment_percent,
                'brands'                   => $ft->children->map(function($b) {
                    return [
                        'id'                       => $b->id,
                        'name'                     => $b->name,
                        'min_down_payment_percent' => $b->min_down_payment_percent,
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        $productsRaw = Product::where('status', 'active')
            ->with(['category', 'subcategory'])
            ->orderBy('name')
            ->get(['id', 'name', 'category_id', 'subcategory_id',
                   'price', 'sale_price', 'loan_amount', 'rmv',
                   'service_charge', 'interest_rate']);

        $products = $productsRaw->map(function($p) {
            return [
                'id'            => $p->id,
                'name'          => $p->name,
                'category_id'   => $p->category_id,
                'subcategory_id'=> $p->subcategory_id,
                'price'         => floatval($p->sale_price ?? $p->price),
                'loan_amount'   => floatval($p->loan_amount ?? 0),
                'rmv'           => floatval($p->rmv ?? 10160),
                'interest_rate' => floatval($p->interest_rate ?? 1.5),
            ];
        })->values()->toArray();

        return response()->json([
            'fuel_types' => $fuelTypes,
            'products'   => $products,
        ]);
    }
}