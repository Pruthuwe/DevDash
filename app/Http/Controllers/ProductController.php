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
            $query = Product::with(['category', 'subcategory'])
                ->where('status', 'active');

            // Filter: ?category=electric | petrol | all
            if (request()->has('category') && request('category') !== 'all') {
                $type = request('category');
                $query->whereHas('category', function ($q) use ($type) {
                    $q->where('name', 'like', '%' . $type . '%');
                });
            }

            // Filter: ?brand=KTM
            if (request()->has('brand')) {
                $query->where('brand', request('brand'));
            }

            // Search: ?search=duke
            if (request()->has('search')) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . request('search') . '%')
                      ->orWhere('brand', 'like', '%' . request('search') . '%')
                      ->orWhere('tags', 'like', '%' . request('search') . '%');
                });
            }

            $products = $query->latest()->get();

            $products = $products->map(function ($product) {
                if ($product->main_image) {
                    $product->main_image_url = url($product->main_image);
                    unset($product->main_image);
                }
                if ($product->gallery_images) {
                    $product->gallery_image_urls = array_map(fn($img) => url($img), $product->gallery_images);
                    unset($product->gallery_images);
                }
                if ($product->category) {
                    $product->category->thumbnail_image_url = $product->category->thumbnail_image ? url($product->category->thumbnail_image) : null;
                    unset($product->category->thumbnail_image, $product->category->banner_image, $product->category->icon_image);
                }
                return $product;
            });

            return response()->json([
                'products' => $products,
                'total' => $products->count(),
            ], 200);
        }

        $products = Product::with(['category', 'subcategory'])->latest()->paginate(20);
        return view('product.productList', compact('products'));
    }

    public function manage()
    {
        $products = Product::with(['category', 'subcategory'])->latest()->paginate(20);

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
                'name'             => 'required|string|max:255',
                'sku'              => 'nullable|string|unique:products,sku',
                'category_id'      => 'nullable|integer',
                'subcategory_id'   => 'nullable|integer',
                'brand'            => 'nullable|string|max:255',
                'unit'             => 'nullable|string',
                'price'            => 'required|numeric|min:0',
                'sale_price'       => 'nullable|numeric|min:0',
                'cost_price'       => 'nullable|numeric|min:0',
                'quantity'         => 'required|integer|min:0',
                'low_stock_alert'  => 'nullable|integer|min:0',
                'tax'              => 'nullable|numeric|min:0|max:100',
                'tax_type'         => 'nullable|in:exclusive,inclusive',
                'short_description'=> 'nullable|string|max:500',
                'main_image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'tags'             => 'nullable|string',
                'status'           => 'required|in:active,inactive,draft',
                'engine_spec'      => 'nullable|string|max:255',
                'highlights'       => 'nullable|string',
                'rating'           => 'nullable|numeric|min:1|max:5',
            ]);

            // Convert highlights from comma-separated string to array
            if (!empty($validated['highlights'])) {
                $validated['highlights'] = array_map('trim', explode(',', $validated['highlights']));
            }

            // Always set unit to piece for bike store
            $validated['unit'] = 'piece';

            // Handle main image upload
            if ($request->hasFile('main_image')) {
                $productsDir = public_path('uploads/products');
                if (!file_exists($productsDir)) {
                    mkdir($productsDir, 0755, true);
                }

                $mainImage = $request->file('main_image');
                $mainImageName = time() . '_' . Str::random(10) . '.' . $mainImage->getClientOriginalExtension();
                $mainImage->move($productsDir, $mainImageName);
                $validated['main_image'] = 'uploads/products/' . $mainImageName;
            }

            // Handle gallery images upload
            if ($request->hasFile('gallery_images')) {
                $galleryDir = public_path('uploads/products/gallery');
                if (!file_exists($galleryDir)) {
                    mkdir($galleryDir, 0755, true);
                }

                $galleryImages = [];
                foreach ($request->file('gallery_images') as $image) {
                    $galleryImageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                    $image->move($galleryDir, $galleryImageName);
                    $galleryImages[] = 'uploads/products/gallery/' . $galleryImageName;
                }
                $validated['gallery_images'] = $galleryImages;
            }

            // Auto-generate SKU if not provided
            if (!isset($validated['sku']) || is_null($validated['sku']) || $validated['sku'] === '') {
                $validated['sku'] = 'PRD-' . strtoupper(Str::slug($validated['name'], '-')) . '-' . rand(100, 999);
            }
            // Auto-generate unique slug
$baseSlug = Str::slug($validated['name']);
$slug = $baseSlug;
$count = 1;
while (Product::where('slug', $slug)->exists()) {
    $slug = $baseSlug . '-' . $count++;
}
$validated['slug'] = $slug;

            // Set default values for nullable fields
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
            Log::error('Product creation failed: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
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
            'name'             => 'required|string|max:255',
            'sku'              => 'required|string|unique:products,sku,' . $product->id,
            'category_id'      => 'nullable|integer',
            'subcategory_id'   => 'nullable|integer',
            'brand'            => 'nullable|string|max:255',
            'unit'             => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'sale_price'       => 'nullable|numeric|min:0',
            'cost_price'       => 'nullable|numeric|min:0',
            'quantity'         => 'required|integer|min:0',
            'low_stock_alert'  => 'nullable|integer|min:0',
            'tax'              => 'nullable|numeric|min:0|max:100',
            'tax_type'         => 'nullable|in:exclusive,inclusive',
            'short_description'=> 'nullable|string|max:500',
            'tags'             => 'nullable|string',
                'status'           => 'required|in:active,inactive,draft',
                'engine_spec'      => 'nullable|string|max:255',
                'highlights'       => 'nullable|string',
                'rating'           => 'nullable|numeric|min:1|max:5',
            ]);

            // Convert highlights from comma-separated string to array
            if (!empty($validated['highlights'])) {
                $validated['highlights'] = array_map('trim', explode(',', $validated['highlights']));
            }

        // Always keep unit as piece for bike store
        $validated['unit'] = 'piece';

        // Handle main image upload
        if ($request->hasFile('main_image')) {
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

        // Handle remove main image
        if ($request->has('remove_image') && $request->remove_image == '1') {
            if ($product->main_image && file_exists(public_path($product->main_image))) {
                unlink(public_path($product->main_image));
            }
            $validated['main_image'] = null;
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            $galleryDir = public_path('uploads/products/gallery');
            if (!file_exists($galleryDir)) {
                mkdir($galleryDir, 0755, true);
            }

            $galleryImages = $product->gallery_images ?? [];

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
                    $currentGallery = array_diff($currentGallery, [$removedImage]);
                    if (file_exists(public_path($removedImage))) {
                        unlink(public_path($removedImage));
                    }
                }
            }

            $validated['gallery_images'] = array_values($currentGallery);
        }

        // Auto-generate SKU if name changed and SKU is empty
        if ($validated['name'] !== $product->name) {
            if (!isset($validated['sku']) || is_null($validated['sku']) || $validated['sku'] === '') {
                $validated['sku'] = 'PRD-' . strtoupper(Str::slug($validated['name'], '-')) . '-' . rand(100, 999);
            }
        }

        // Set defaults for nullable fields that cannot be null in DB
if (!isset($validated['tax_type']) || is_null($validated['tax_type'])) {
    $validated['tax_type'] = 'exclusive';
}
if (!isset($validated['tax']) || is_null($validated['tax'])) {
    $validated['tax'] = 0;
}
if (!isset($validated['low_stock_alert']) || is_null($validated['low_stock_alert'])) {
    $validated['low_stock_alert'] = 10;
}

$product->update($validated);;

        return redirect()->route('manage.products')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        // Delete main image
        if ($product->main_image && file_exists(public_path($product->main_image))) {
            unlink(public_path($product->main_image));
        }

        // Delete gallery images
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