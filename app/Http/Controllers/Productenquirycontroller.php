<?php

namespace App\Http\Controllers;

use App\Models\ProductEnquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductEnquiryController extends Controller
{
    /**
     * Store a new product enquiry — called from the public "Enquire Now" popup.
     * No authentication required (guest lead capture).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'nullable|exists:products,id',
            'name'        => 'required|string|max:255',
            'phone'       => 'required|string|max:30',
            'message'     => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please check the details you entered.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $enquiry = ProductEnquiry::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Thanks! Our team will get back to you shortly.',
            'data'    => $enquiry,
        ], 201);
    }

    /**
     * Display the manage product enquiries page (DevDash admin).
     */
    public function manage(Request $request)
    {
        if (Auth::user()->user_type !== 'admin' && (!Auth::user()->role || !Auth::user()->role->permissions->contains('name', 'view-product-enquiries'))) {
            abort(403, 'You do not have permission to view product enquiries.');
        }

        $query = ProductEnquiry::with('product');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $productEnquiries = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total'     => ProductEnquiry::count(),
            'new'       => ProductEnquiry::where('status', 'new')->count(),
            'contacted' => ProductEnquiry::where('status', 'contacted')->count(),
            'closed'    => ProductEnquiry::where('status', 'closed')->count(),
        ];

        return view('product_enquiries.manage', compact('productEnquiries', 'stats'));
    }

    /**
     * Update a product enquiry's status (new / contacted / closed).
     */
    public function updateStatus(Request $request, ProductEnquiry $productEnquiry)
    {
        if (Auth::user()->user_type !== 'admin' && (!Auth::user()->role || !Auth::user()->role->permissions->contains('name', 'edit-product-enquiries'))) {
            abort(403, 'You do not have permission to edit product enquiries.');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:new,contacted,closed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $productEnquiry->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'data'    => ['id' => $productEnquiry->id, 'status' => $productEnquiry->status],
        ]);
    }

    /**
     * Delete a product enquiry.
     */
    public function destroy(ProductEnquiry $productEnquiry)
    {
        if (Auth::user()->user_type !== 'admin' && (!Auth::user()->role || !Auth::user()->role->permissions->contains('name', 'delete-product-enquiries'))) {
            abort(403, 'You do not have permission to delete product enquiries.');
        }

        $productEnquiry->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json(['success' => true, 'message' => 'Product enquiry deleted successfully']);
        }

        return redirect()->route('manage.product-enquiries')->with('success', 'Product enquiry deleted successfully');
    }
}