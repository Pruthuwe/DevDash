<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('supplier.addSupplier');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'vat_no' => 'nullable|string|max:50',
            'fax' => 'nullable|string|max:20',
        ]);

        Supplier::create($request->all());

        return redirect()->route('manage.suppliers')->with('success', 'Supplier added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return view('supplier.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('supplier.editSupplier', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email,' . $supplier->id,
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'vat_no' => 'nullable|string|max:50',
            'fax' => 'nullable|string|max:20',
        ]);

        $supplier->update($request->all());

        return redirect()->route('manage.suppliers')->with('success', 'Supplier updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('manage.suppliers')->with('success', 'Supplier deleted successfully!');
    }

    /**
     * Manage suppliers page with statistics.
     */
    public function manage()
    {
        $suppliers = Supplier::latest()->paginate(15);

        // Calculate statistics
        $totalSuppliers = Supplier::count();

        return view('supplier.manageSupplier', compact('suppliers', 'totalSuppliers'));
    }
}
