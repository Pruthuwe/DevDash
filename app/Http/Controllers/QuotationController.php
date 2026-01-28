<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quotations = Quotation::with('user')->latest()->paginate(10);
        return view('quotation.manageQuotation', compact('quotations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::where('status', 'active')->get();
        $customers = \App\Models\Customer::all();
        return view('quotation.addQuotion', compact('products', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'quotation_number' => 'required|unique:quotations',
            'quotation_date' => 'required|date',
            'valid_until' => 'required|date|after:quotation_date',
            'customer_id' => 'required|exists:customers,id',
            'customer_phone' => 'required',
            'product' => 'required|array',
            'quantity' => 'required|array',
            'price' => 'required|array',
            'discount' => 'nullable|numeric',
            'status' => 'required'
        ]);

        DB::transaction(function () use ($request) {

            $subtotal = 0;

            foreach ($request->product as $i => $pid) {
                $subtotal += $request->quantity[$i] * $request->price[$i];
            }

            $tax = 0;
            $total = $subtotal - ($request->discount ?? 0);

            $customer = \App\Models\Customer::find($request->customer_id);

            $quotation = Quotation::create([
                'quotation_number' => $request->quotation_number,
                'quotation_date' => $request->quotation_date,
                'valid_until' => $request->valid_until,
                'customer_id' => $request->customer_id,
                'customer_name' => $customer->name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $request->discount ?? 0,
                'total' => $total,
                'status' => $request->status,
                'user_id' => auth()->id(),
            ]);

            foreach ($request->product as $i => $pid) {
                $product = Product::find($pid);
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $pid,
                    'product_name' => $product->name,
                    'quantity' => $request->quantity[$i],
                    'unit_price' => $request->price[$i],
                    'total' => $request->quantity[$i] * $request->price[$i],
                ]);
            }
        });

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation created successfully');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $quotation = Quotation::with(['quotationItems.product', 'user'])->findOrFail($id);
        return view('quotation.show', compact('quotation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $quotation = Quotation::with('quotationItems')->findOrFail($id);
        $products = Product::where('status', 'active')->get();
        $customers = \App\Models\Customer::all();
        return view('quotation.edit', compact('quotation', 'products', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'quotation_number' => 'required|unique:quotations,quotation_number,' . $id,
            'quotation_date' => 'required|date',
            'valid_until' => 'required|date|after:quotation_date',
            'customer_id' => 'required|exists:customers,id',
            'customer_phone' => 'required',
            'product' => 'required|array',
            'quantity' => 'required|array',
            'price' => 'required|array',
            'discount' => 'nullable|numeric',
            'status' => 'required'
        ]);

        DB::transaction(function () use ($request, $id) {

            $subtotal = 0;

            foreach ($request->product as $i => $pid) {
                $subtotal += $request->quantity[$i] * $request->price[$i];
            }

            $tax = 0;
            $total = $subtotal - ($request->discount ?? 0);

            $customer = \App\Models\Customer::find($request->customer_id);

            $quotation = Quotation::findOrFail($id);
            $quotation->update([
                'quotation_number' => $request->quotation_number,
                'quotation_date' => $request->quotation_date,
                'valid_until' => $request->valid_until,
                'customer_id' => $request->customer_id,
                'customer_name' => $customer->name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $request->discount ?? 0,
                'total' => $total,
                'status' => $request->status,
            ]);

            // Delete existing items and create new ones
            $quotation->quotationItems()->delete();

            foreach ($request->product as $i => $pid) {
                $product = Product::find($pid);
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $pid,
                    'product_name' => $product->name,
                    'quantity' => $request->quantity[$i],
                    'unit_price' => $request->price[$i],
                    'total' => $request->quantity[$i] * $request->price[$i],
                ]);
            }
        });

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->delete();

        return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully!');
    }
}
