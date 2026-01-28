<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchases = Purchase::with('supplier')->latest()->get();
        return view('purchase.purchaseList', compact('purchases'));
    }

    /**
     * Display manage purchases page with statistics.
     */
    public function manage()
    {
        $totalPurchases = Purchase::count();
        $completedPurchases = Purchase::where('status', 'completed')->count();
        $pendingPurchases = Purchase::where('status', 'pending')->count();
        $totalAmount = Purchase::where('status', 'completed')->sum('total_amount');
        $purchases = Purchase::with(['supplier', 'purchaseItems.product'])->latest()->get();
        
        return view('purchase.managePurchase', compact(
            'purchases', 
            'totalPurchases', 
            'completedPurchases', 
            'pendingPurchases', 
            'totalAmount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::where('status', 'active')->get();
        $referenceNumber = Purchase::generateReferenceNumber();

        return view('purchase.addPurchase', compact(
            'suppliers',
            'products',
            'referenceNumber'
        ));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'status' => 'required|in:pending,completed,cancelled',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Calculate total amount
            $totalAmount = 0;
            foreach ($request->products as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            // Create purchase
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'reference_number' => $request->reference_number ?: Purchase::generateReferenceNumber(),
                'status' => $request->status,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
            ]);

            // Create purchase items and update product quantities
            foreach ($request->products as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);

                // Update product quantity if status is completed
                if ($request->status === 'completed') {
                    $product = Product::find($item['product_id']);
                    $product->quantity += $item['quantity'];
                    $product->save();
                }
            }

            DB::commit();

            return redirect()->route('purchases.index')
                ->with('success', 'Purchase created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create purchase: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'purchaseItems.product']);
        return view('purchase.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase)
    {
        $suppliers = Supplier::all();
        $products = Product::where('status', 'active')->get();
        $purchase->load('purchaseItems');
        
        return view('purchase.editPurchase', compact('purchase', 'suppliers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'status' => 'required|in:pending,completed,cancelled',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // If changing from completed to other status, revert product quantities
            if ($purchase->status === 'completed' && $request->status !== 'completed') {
                foreach ($purchase->purchaseItems as $item) {
                    $product = Product::find($item->product_id);
                    $product->quantity -= $item->quantity;
                    $product->save();
                }
            }

            // Calculate total amount
            $totalAmount = 0;
            foreach ($request->products as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            // Update purchase
            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'reference_number' => $request->reference_number,
                'status' => $request->status,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
            ]);

            // Delete old purchase items
            $purchase->purchaseItems()->delete();

            // Create new purchase items and update product quantities
            foreach ($request->products as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);

                // Update product quantity if status is completed
                if ($request->status === 'completed') {
                    $product = Product::find($item['product_id']);
                    $product->quantity += $item['quantity'];
                    $product->save();
                }
            }

            DB::commit();

            return redirect()->route('purchases.index')
                ->with('success', 'Purchase updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update purchase: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        try {
            DB::beginTransaction();

            // If purchase is completed, revert product quantities
            if ($purchase->status === 'completed') {
                foreach ($purchase->purchaseItems as $item) {
                    $product = Product::find($item->product_id);
                    $product->quantity -= $item->quantity;
                    $product->save();
                }
            }

            $purchase->delete();

            DB::commit();

            return redirect()->route('purchases.index')
                ->with('success', 'Purchase deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete purchase: ' . $e->getMessage());
        }
    }

    /**
     * Get product details by ID (for AJAX)
     */
    public function getProductDetails($id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => $product->price,
            'cost_price' => $product->cost_price,
            'quantity' => $product->quantity,
            'unit' => $product->unit,
        ]);
    }

    public function searchProducts(Request $request)
    {
        try {
            $search = $request->get('q', '');
            
            Log::info('Search products called with q: ' . $search);
            
            $query = Product::query(); // Remove status filter temporarily
            
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('sku', 'like', '%' . $search . '%');
                });
            }
            
            $products = $query->limit(20)->get(['id', 'name', 'sku', 'cost_price', 'price']);
            
            Log::info('Search products result: ' . $products->count() . ' products found for search: ' . $search);
            Log::info('Products data: ' . $products->toJson());
            
            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Error in searchProducts: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
