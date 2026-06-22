<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * View All Categories (merged Fuel Type + Brand table)
     */
    public function manage(Request $request)
    {
        $query = Category::whereNull('parent_id')->with('children');

        // Optional: filter by a specific Fuel Type
        if ($request->filled('bike_type_id')) {
            $query->where('id', $request->bike_type_id);
        }

        $categories = $query->paginate(15);

        // For the Brand filter dropdown (all brands, regardless of which Fuel Type)
        $allBrands = Category::whereNotNull('parent_id')->orderBy('name')->get(['id', 'name', 'parent_id']);

        // Calculate statistics
        $totalCategories = Category::whereNull('parent_id')->count();
        $totalSubcategories = Category::whereNotNull('parent_id')->count();
        $activeCategories = Category::where('status', 'active')->count();

        return view('category.manageCategory', compact(
            'categories', 'totalCategories', 'totalSubcategories', 'activeCategories', 'allBrands'
        ));
    }

    /**
     * Add Fuel Type page (form + table list)
     */
    public function create()
    {
        $bikeTypes = Category::whereNull('parent_id')->orderBy('name')->paginate(15);

        return view('category.addCategory', compact('bikeTypes'));
    }

    /**
     * Add Brand page (form + table list)
     */
    public function createSubcategory()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        $brands = Category::whereNotNull('parent_id')
            ->with('parent')
            ->orderBy('name')
            ->paginate(15);

        return view('category.addSubcategory', compact('parentCategories', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'min_down_payment_percent' => 'nullable|numeric|min:0|max:100',
            'icon_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $iconImagePath = null;

        if ($request->hasFile('icon_image')) {
            $iconFile = $request->file('icon_image');
            $iconFileName = time() . '_icon_' . $iconFile->getClientOriginalName();
            $iconFile->move(public_path('uploads/categories/icons'), $iconFileName);
            $iconImagePath = 'uploads/categories/icons/' . $iconFileName;
        }

        Category::create([
            'name' => $request->name,
            'status' => $request->status,
            'min_down_payment_percent' => $request->min_down_payment_percent,
            'icon_image' => $iconImagePath,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Fuel Type created successfully.']);
        }

        return redirect()->route('add.category')->with('success', 'Fuel Type created successfully.');
    }

    public function storeSubcategory(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'min_down_payment_percent' => 'nullable|numeric|min:0|max:100',
            'icon_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $iconImagePath = null;

        if ($request->hasFile('icon_image')) {
            $iconFile = $request->file('icon_image');
            $iconFileName = time() . '_subcategory_icon_' . $iconFile->getClientOriginalName();
            $iconFile->move(public_path('uploads/categories/icons'), $iconFileName);
            $iconImagePath = 'uploads/categories/icons/' . $iconFileName;
        }

        Category::create([
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'status' => $request->status,
            'min_down_payment_percent' => $request->min_down_payment_percent,
            'icon_image' => $iconImagePath,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Brand created successfully.']);
        }

        return redirect()->route('add.subcategory')->with('success', 'Brand created successfully.');
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
            'status' => 'required|in:active,inactive',
            'min_down_payment_percent' => 'nullable|numeric|min:0|max:100',
            'icon_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $iconImagePath = $category->icon_image;

        if ($request->hasFile('icon_image')) {
            if ($category->icon_image && file_exists(public_path($category->icon_image))) {
                unlink(public_path($category->icon_image));
            }
            $iconFile = $request->file('icon_image');
            $iconFileName = time() . '_icon_' . $iconFile->getClientOriginalName();
            $iconFile->move(public_path('uploads/categories/icons'), $iconFileName);
            $iconImagePath = 'uploads/categories/icons/' . $iconFileName;
        }

        if ($request->input('delete_icon_image') == '1') {
            if ($category->icon_image && file_exists(public_path($category->icon_image))) {
                unlink(public_path($category->icon_image));
            }
            $iconImagePath = null;
        }

        $category->update([
            'name' => $request->name,
            'status' => $request->status,
            'min_down_payment_percent' => $request->min_down_payment_percent,
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
            foreach ($category->children as $subcategory) {
                if ($subcategory->icon_image && file_exists(public_path($subcategory->icon_image))) {
                    unlink(public_path($subcategory->icon_image));
                }
                $subcategory->delete();
            }
        }

        if ($category->icon_image && file_exists(public_path($category->icon_image))) {
            unlink(public_path($category->icon_image));
        }

        $category->delete();

        $message = $category->parent_id === null
            ? 'Fuel Type and all its brands deleted successfully.'
            : 'Brand deleted successfully.';

        return redirect()->back()->with('success', $message);
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
            ->with(['children' => fn($q) => $q->where('status', 'active')])
            ->get();

        return response()->json([
            'categories' => $categories->map(fn($c) => $this->buildCategoryResponse($c))
        ]);
    }

    public function apiSubcategories($id)
    {
        $category = Category::findOrFail($id);
        $subcategories = $category->children()
            ->where('status', 'active')
            ->get()
            ->map(fn($s) => $this->buildCategoryResponse($s));

        return response()->json(['subcategories' => $subcategories]);
    }

    private function buildCategoryResponse($category)
    {
        $data = [
            'id'                        => $category->id,
            'name'                      => $category->name,
            'status'                    => $category->status,
            'min_down_payment_percent'  => $category->min_down_payment_percent !== null ? (float) $category->min_down_payment_percent : null,
            'icon_image_url'            => $category->icon_image ? url($category->icon_image) : null,
        ];

        if ($category->relationLoaded('children')) {
            $data['subcategories'] = $category->children
                ->map(fn($child) => $this->buildCategoryResponse($child))
                ->values();
        }

        return $data;
    }
}