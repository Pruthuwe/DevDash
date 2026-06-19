<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function manage()
    {
        $categories = Category::whereNull('parent_id')->with('children')->paginate(15);

        // Calculate statistics
        $totalCategories = Category::whereNull('parent_id')->count();
        $totalSubcategories = Category::whereNotNull('parent_id')->count();
        $activeCategories = Category::where('status', 'active')->count();

        return view('category.manageCategory', compact('categories', 'totalCategories', 'totalSubcategories', 'activeCategories'));
    }

    public function create()
    {
        return view('category.addCategory');
    }

    public function createSubcategory()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('category.addSubcategory', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'thumbnail_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'icon_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Handle file uploads
        $bannerImagePath = null;
        $thumbnailImagePath = null;
        $iconImagePath = null;

        if ($request->hasFile('banner_image')) {
            $bannerFile = $request->file('banner_image');
            $bannerFileName = time() . '_banner_' . $bannerFile->getClientOriginalName();
            $bannerFile->move(public_path('uploads/categories/banners'), $bannerFileName);
            $bannerImagePath = 'uploads/categories/banners/' . $bannerFileName;
        }

        if ($request->hasFile('thumbnail_image')) {
            $thumbnailFile = $request->file('thumbnail_image');
            $thumbnailFileName = time() . '_thumbnail_' . $thumbnailFile->getClientOriginalName();
            $thumbnailFile->move(public_path('uploads/categories/thumbnails'), $thumbnailFileName);
            $thumbnailImagePath = 'uploads/categories/thumbnails/' . $thumbnailFileName;
        }

        if ($request->hasFile('icon_image')) {
            $iconFile = $request->file('icon_image');
            $iconFileName = time() . '_icon_' . $iconFile->getClientOriginalName();
            $iconFile->move(public_path('uploads/categories/icons'), $iconFileName);
            $iconImagePath = 'uploads/categories/icons/' . $iconFileName;
        }

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'banner_image' => $bannerImagePath,
            'thumbnail_image' => $thumbnailImagePath,
            'icon_image' => $iconImagePath,
        ]);

        return redirect()->route('manage.category')->with('success', 'Category created successfully.');
    }

    public function storeSubcategory(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'thumbnail_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'icon_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Handle image uploads
        $bannerImagePath = null;
        $thumbnailImagePath = null;
        $iconImagePath = null;

        if ($request->hasFile('banner_image')) {
            $bannerFile = $request->file('banner_image');
            $bannerFileName = time() . '_subcategory_banner_' . $bannerFile->getClientOriginalName();
            $bannerFile->move(public_path('uploads/categories/banners'), $bannerFileName);
            $bannerImagePath = 'uploads/categories/banners/' . $bannerFileName;
        }

        if ($request->hasFile('thumbnail_image')) {
            $thumbnailFile = $request->file('thumbnail_image');
            $thumbnailFileName = time() . '_subcategory_thumb_' . $thumbnailFile->getClientOriginalName();
            $thumbnailFile->move(public_path('uploads/categories'), $thumbnailFileName);
            $thumbnailImagePath = 'uploads/categories/' . $thumbnailFileName;
        }

        if ($request->hasFile('icon_image')) {
            $iconFile = $request->file('icon_image');
            $iconFileName = time() . '_subcategory_icon_' . $iconFile->getClientOriginalName();
            $iconFile->move(public_path('uploads/categories/icons'), $iconFileName);
            $iconImagePath = 'uploads/categories/icons/' . $iconFileName;
        }

        Category::create([
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'banner_image' => $bannerImagePath,
            'thumbnail_image' => $thumbnailImagePath,
            'icon_image' => $iconImagePath,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Brand created successfully.']);
        }

        return redirect()->route('manage.category')->with('success', 'Subcategory created successfully.');
    }

    public function edit(Category $category)
    {
        $category->load('children', 'parent');
        return view('category.editCategory', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'thumbnail_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'icon_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Handle file uploads
        $bannerImagePath = $category->banner_image;
        $thumbnailImagePath = $category->thumbnail_image;
        $iconImagePath = $category->icon_image;

        if ($request->hasFile('banner_image')) {
            // Delete old banner if exists
            if ($category->banner_image && file_exists(public_path($category->banner_image))) {
                unlink(public_path($category->banner_image));
            }
            $bannerFile = $request->file('banner_image');
            $bannerFileName = time() . '_banner_' . $bannerFile->getClientOriginalName();
            $bannerFile->move(public_path('uploads/categories/banners'), $bannerFileName);
            $bannerImagePath = 'uploads/categories/banners/' . $bannerFileName;
        }

        if ($request->hasFile('thumbnail_image')) {
            // Delete old thumbnail if exists
            if ($category->thumbnail_image && file_exists(public_path($category->thumbnail_image))) {
                unlink(public_path($category->thumbnail_image));
            }
            $thumbnailFile = $request->file('thumbnail_image');
            $thumbnailFileName = time() . '_thumbnail_' . $thumbnailFile->getClientOriginalName();
            $thumbnailFile->move(public_path('uploads/categories/thumbnails'), $thumbnailFileName);
            $thumbnailImagePath = 'uploads/categories/thumbnails/' . $thumbnailFileName;
        }

        if ($request->hasFile('icon_image')) {
            // Delete old icon if exists
            if ($category->icon_image && file_exists(public_path($category->icon_image))) {
                unlink(public_path($category->icon_image));
            }
            $iconFile = $request->file('icon_image');
            $iconFileName = time() . '_icon_' . $iconFile->getClientOriginalName();
            $iconFile->move(public_path('uploads/categories/icons'), $iconFileName);
            $iconImagePath = 'uploads/categories/icons/' . $iconFileName;
        }

        // Handle delete flags
        if ($request->input('delete_banner_image') == '1') {
            if ($category->banner_image && file_exists(public_path($category->banner_image))) {
                unlink(public_path($category->banner_image));
            }
            $bannerImagePath = null;
        }

        if ($request->input('delete_thumbnail_image') == '1') {
            if ($category->thumbnail_image && file_exists(public_path($category->thumbnail_image))) {
                unlink(public_path($category->thumbnail_image));
            }
            $thumbnailImagePath = null;
        }

        if ($request->input('delete_icon_image') == '1') {
            if ($category->icon_image && file_exists(public_path($category->icon_image))) {
                unlink(public_path($category->icon_image));
            }
            $iconImagePath = null;
        }

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'banner_image' => $bannerImagePath,
            'thumbnail_image' => $thumbnailImagePath,
            'icon_image' => $iconImagePath,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Category updated successfully.']);
        }

        return redirect()->route('manage.category')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        // If this is a parent category (no parent_id), delete all subcategories too
        if ($category->parent_id === null) {
            // Delete all subcategories and their images
            foreach ($category->children as $subcategory) {
                // Delete subcategory images
                if ($subcategory->banner_image && file_exists(public_path($subcategory->banner_image))) {
                    unlink(public_path($subcategory->banner_image));
                }
                if ($subcategory->thumbnail_image && file_exists(public_path($subcategory->thumbnail_image))) {
                    unlink(public_path($subcategory->thumbnail_image));
                }
                if ($subcategory->icon_image && file_exists(public_path($subcategory->icon_image))) {
                    unlink(public_path($subcategory->icon_image));
                }
                // Delete the subcategory
                $subcategory->delete();
            }
        }

        // Delete the category's own images
        if ($category->banner_image && file_exists(public_path($category->banner_image))) {
            unlink(public_path($category->banner_image));
        }
        if ($category->thumbnail_image && file_exists(public_path($category->thumbnail_image))) {
            unlink(public_path($category->thumbnail_image));
        }
        if ($category->icon_image && file_exists(public_path($category->icon_image))) {
            unlink(public_path($category->icon_image));
        }

        $category->delete();

        $message = $category->parent_id === null
            ? 'Category and all its brands deleted successfully.'
            : 'Brand deleted successfully.';

        return redirect()->route('manage.category')->with('success', $message);
    }

    public function getSubcategories(Category $category)
    {
        $subcategories = $category->children()->get(['id', 'name']);
        
        return response()->json([
            'subcategories' => $subcategories
        ]);
    }

    // ── API methods for the React frontend ──────────────────────────────

    public function apiIndex()
    {
        $categories = Category::whereNull('parent_id')
            ->where('status', 'active')
            ->with(['children' => function ($q) {
                $q->where('status', 'active');
            }])
            ->get();

        $categories = $categories->map(function ($category) {
            return $this->buildCategoryResponse($category);
        });

        return response()->json(['categories' => $categories]);
    }

    public function apiSubcategories($id)
    {
        $category = Category::findOrFail($id);

        $subcategories = $category->children()
            ->where('status', 'active')
            ->get()
            ->map(function ($sub) {
                return $this->buildCategoryResponse($sub);
            });

        return response()->json(['subcategories' => $subcategories]);
    }

    private function buildCategoryResponse($category)
    {
        $data = [
            'id'                  => $category->id,
            'name'                => $category->name,
            'description'         => $category->description,
            'status'              => $category->status,
            'banner_image_url'    => $category->banner_image    ? url($category->banner_image)    : null,
            'thumbnail_image_url' => $category->thumbnail_image ? url($category->thumbnail_image) : null,
            'icon_image_url'      => $category->icon_image      ? url($category->icon_image)      : null,
        ];

        if ($category->relationLoaded('children')) {
            $data['subcategories'] = $category->children->map(function ($child) {
                return $this->buildCategoryResponse($child);
            })->values();
        }

        return $data;
    }
}