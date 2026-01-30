<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        if (request()->is('api/*')) {
            $products = Product::with(['category', 'subcategory'])->latest()->get();
            $products = $products->map(function ($product) {
                if ($product->main_image) {
                    $product->main_image_url = url($product->main_image);
                    unset($product->main_image);
                }
                if ($product->gallery_images) {
                    $product->gallery_image_urls = array_map(function ($image) {
                        return url($image);
                    }, $product->gallery_images);
                    unset($product->gallery_images);
                }
                // Transform category images
                if ($product->category) {
                    if ($product->category->banner_image) {
                        $product->category->banner_image_url = url($product->category->banner_image);
                        unset($product->category->banner_image);
                    }
                    if ($product->category->thumbnail_image) {
                        $product->category->thumbnail_image_url = url($product->category->thumbnail_image);
                        unset($product->category->thumbnail_image);
                    }
                    if ($product->category->icon_image) {
                        $product->category->icon_image_url = url($product->category->icon_image);
                        unset($product->category->icon_image);
                    }
                }
                // Transform subcategory images
                if ($product->subcategory) {
                    if ($product->subcategory->banner_image) {
                        $product->subcategory->banner_image_url = url($product->subcategory->banner_image);
                        unset($product->subcategory->banner_image);
                    }
                    if ($product->subcategory->thumbnail_image) {
                        $product->subcategory->thumbnail_image_url = url($product->subcategory->thumbnail_image);
                        unset($product->subcategory->thumbnail_image);
                    }
                    if ($product->subcategory->icon_image) {
                        $product->subcategory->icon_image_url = url($product->subcategory->icon_image);
                        unset($product->subcategory->icon_image);
                    }
                }
                return $product;
            });
            return response()->json(['products' => $products], 200);
        }
        
        $products = Product::with(['category', 'subcategory'])->latest()->paginate(20);
        return view('product.productList', compact('products'));
    }

    public function manage()
    {
        $products = Product::with(['category', 'subcategory'])->latest()->paginate(20);

        // Calculate statistics
        $totalProducts = Product::where('status', 'active')->count();

        $totalValue = Product::where('status', 'active')
            ->selectRaw('SUM(cost_price * quantity) as total_value')
            ->value('total_value') ?? 0;

        $lowStock = Product::where('status', 'active')
            ->whereColumn('quantity', '<=', 'low_stock_alert')
            ->where('quantity', '>', 0)
            ->get();

        $outOfStock = Product::where('status', 'active')
            ->where('quantity', 0)
            ->get();

        return view('product.manageProduct', compact('products', 'totalProducts', 'totalValue', 'lowStock', 'outOfStock'));
    }

    public function create()
    {
        $categories = Category::whereNull('parent_id')->get();
        return view('product.addProduct', compact('categories'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'required|string|unique:products,sku',
                'category_id' => 'nullable|integer',
                'subcategory_id' => 'nullable|integer',
                'brand' => 'nullable|string|max:255',
                'unit' => 'required|string',
                'barcode' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'sale_price' => 'nullable|numeric|min:0',
                'cost_price' => 'nullable|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'low_stock_alert' => 'nullable|integer|min:0',
                'tax' => 'nullable|numeric|min:0|max:100',
                'tax_type' => 'nullable|in:exclusive,inclusive',
                'short_description' => 'nullable|string|max:500',
                'full_description' => 'nullable|string',
                'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'tags' => 'nullable|string',
                'notes' => 'nullable|string',
                'status' => 'required|in:active,inactive,draft',
            ]);

            // Handle main image upload
            if ($request->hasFile('main_image')) {
                $mainImage = $request->file('main_image');
                $mainImageName = time() . '_' . Str::random(10) . '.' . $mainImage->getClientOriginalExtension();
                $mainImage->move(public_path('uploads/products'), $mainImageName);
                $validated['main_image'] = 'uploads/products/' . $mainImageName;
            }

            // Handle gallery images upload
            if ($request->hasFile('gallery_images')) {
                $galleryImages = [];
                foreach ($request->file('gallery_images') as $image) {
                    $galleryImageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('uploads/products/gallery'), $galleryImageName);
                    $galleryImages[] = 'uploads/products/gallery/' . $galleryImageName;
                }
                $validated['gallery_images'] = $galleryImages;
            }

            // Generate slug
            $validated['slug'] = Str::slug($validated['name']);

            // Set default values for nullable fields that have database defaults
            if (!isset($validated['low_stock_alert']) || $validated['low_stock_alert'] === null) {
                $validated['low_stock_alert'] = 10;
            }
            if (!isset($validated['tax']) || $validated['tax'] === null) {
                $validated['tax'] = 0;
            }
            if (!isset($validated['tax_type']) || $validated['tax_type'] === null) {
                $validated['tax_type'] = 'exclusive';
            }

            // Check if saving as draft
            if ($request->has('save_draft')) {
                $validated['status'] = 'draft';
            }

            // Create product
            $product = Product::create($validated);

            return redirect()->route('manage.products')
                ->with('success', 'Product created successfully!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Product creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to create product: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Product $product)
    {
        if (request()->is('api/*')) {
            $product->load(['category', 'subcategory']);
            if ($product->main_image) {
                $product->main_image_url = url($product->main_image);
                unset($product->main_image);
            }
            if ($product->gallery_images) {
                $product->gallery_image_urls = array_map(function ($image) {
                    return url($image);
                }, $product->gallery_images);
                unset($product->gallery_images);
            }
            // Transform category images
            if ($product->category) {
                if ($product->category->banner_image) {
                    $product->category->banner_image_url = url($product->category->banner_image);
                    unset($product->category->banner_image);
                }
                if ($product->category->thumbnail_image) {
                    $product->category->thumbnail_image_url = url($product->category->thumbnail_image);
                    unset($product->category->thumbnail_image);
                }
                if ($product->category->icon_image) {
                    $product->category->icon_image_url = url($product->category->icon_image);
                    unset($product->category->icon_image);
                }
            }
            // Transform subcategory images
            if ($product->subcategory) {
                if ($product->subcategory->banner_image) {
                    $product->subcategory->banner_image_url = url($product->subcategory->banner_image);
                    unset($product->subcategory->banner_image);
                }
                if ($product->subcategory->thumbnail_image) {
                    $product->subcategory->thumbnail_image_url = url($product->subcategory->thumbnail_image);
                    unset($product->subcategory->thumbnail_image);
                }
                if ($product->subcategory->icon_image) {
                    $product->subcategory->icon_image_url = url($product->subcategory->icon_image);
                    unset($product->subcategory->icon_image);
                }
            }
            return response()->json(['product' => $product], 200);
        }
        return view('product.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::whereNull('parent_id')->get();
        return view('product.editProduct', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'category_id' => 'nullable|integer',
            'subcategory_id' => 'nullable|integer',
            'brand' => 'nullable|string|max:255',
            'unit' => 'required|string',
            'barcode' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'low_stock_alert' => 'nullable|integer|min:0',
            'tax' => 'nullable|numeric|min:0|max:100',
            'tax_type' => 'nullable|in:exclusive,inclusive',
            'short_description' => 'nullable|string|max:500',
            'full_description' => 'nullable|string',
            'tags' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft',
        ]);

        // Handle main image upload
        if ($request->hasFile('main_image')) {
            // Ensure products directory exists
            $productsDir = public_path('uploads/products');
            if (!file_exists($productsDir)) {
                mkdir($productsDir, 0755, true);
            }

            // Delete old image
            if ($product->main_image && file_exists(public_path($product->main_image))) {
                unlink(public_path($product->main_image));
            }

            $mainImage = $request->file('main_image');
            $mainImageName = time() . '_' . Str::random(10) . '.' . $mainImage->getClientOriginalExtension();
            $mainImage->move($productsDir, $mainImageName);
            $validated['main_image'] = 'uploads/products/' . $mainImageName;
        }

        // Handle remove image
        if ($request->has('remove_image') && $request->remove_image == '1') {
            if ($product->main_image && file_exists(public_path($product->main_image))) {
                unlink(public_path($product->main_image));
            }
            $validated['main_image'] = null;
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            $galleryImages = $product->gallery_images ?? [];

            // Ensure gallery directory exists
            $galleryDir = public_path('uploads/products/gallery');
            if (!file_exists($galleryDir)) {
                mkdir($galleryDir, 0755, true);
            }

            foreach ($request->file('gallery_images') as $image) {
                $galleryImageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->move($galleryDir, $galleryImageName);
                $galleryImages[] = 'uploads/products/gallery/' . $galleryImageName;
            }
            $validated['gallery_images'] = $galleryImages;
        }

        // Handle removal of individual gallery images
        if ($request->has('removedGalleryImages') && !empty($request->removedGalleryImages)) {
            $removedImages = explode(',', $request->removedGalleryImages);
            $currentGallery = $product->gallery_images ?? [];

            foreach ($removedImages as $removedImage) {
                $removedImage = trim($removedImage);
                if (in_array($removedImage, $currentGallery)) {
                    // Remove from array
                    $currentGallery = array_diff($currentGallery, [$removedImage]);
                    // Delete file
                    if (file_exists(public_path($removedImage))) {
                        unlink(public_path($removedImage));
                    }
                }
            }

            $validated['gallery_images'] = array_values($currentGallery);
        }

        // Update slug if name changed
        if ($validated['name'] !== $product->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Update product
        $product->update($validated);

        return redirect()->route('manage.products')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        // Delete images
        if ($product->main_image && file_exists(public_path($product->main_image))) {
            unlink(public_path($product->main_image));
        }

        if ($product->gallery_images) {
            foreach ($product->gallery_images as $image) {
                if (file_exists(public_path($image))) {
                    unlink(public_path($image));
                }
            }
        }

        $product->forceDelete();

        return redirect()->route('manage.products')
            ->with('success', 'Product deleted successfully!');
    }
}
