<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogs = Blog::all();
        if (request()->is('api/*')) {
            $blogs = $blogs->map(function ($blog) {
                if ($blog->images) {
                    $images = json_decode($blog->images, true);
                    $blog->image_urls = array_map(function ($image) {
                        return url($image);
                    }, $images);
                    unset($blog->images);
                }
                return $blog;
            });
            return response()->json(['blogs' => $blogs], 200);
        }
        return view('blog.blogManage', compact('blogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (request()->is('api/*')) {
            return response()->json(['message' => 'Create form not available via API'], 404);
        }
        $products = \App\Models\Product::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        return view('blog.addBlog', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string', // ← was required, now nullable (auto-generated on frontend)
            'images.*'    => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/blogs'), $imageName);
                $images[] = 'uploads/blogs/' . $imageName;
            }
        }

        $blog = Blog::create([
            'title'       => $request->title,
            'description' => $request->description ?? null, // ← null if not submitted
            'images'      => json_encode($images),
        ]);

        if (request()->is('api/*')) {
            if ($blog->images) {
                $imgs = json_decode($blog->images, true);
                $blog->image_urls = array_map(function ($image) {
                    return url($image);
                }, $imgs);
                unset($blog->images);
            }
            return response()->json(['message' => 'Blog created successfully.', 'blog' => $blog], 201);
        }
        return redirect()->route('manage.blogs')->with('success', 'Blog created successfully.');
    }

    public function show(string $id)
    {
        $blog = Blog::findOrFail($id);
        if (request()->is('api/*')) {
            if ($blog->images) {
                $images = json_decode($blog->images, true);
                $blog->image_urls = array_map(function ($image) {
                    return url($image);
                }, $images);
                unset($blog->images);
            }
            return response()->json(['blog' => $blog], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $blog = Blog::findOrFail($id);
        if (request()->is('api/*')) {
            return response()->json(['blog' => $blog], 200);
        }
        $products = \App\Models\Product::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        return view('blog.editBlog', compact('blog', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $blog = Blog::findOrFail($id);

        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string', // ← was required, now nullable
            'images.*'       => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_images'  => 'nullable|array',
            'delete_images.*'=> 'integer',
        ]);

        $images = json_decode($blog->images, true) ?? [];

        // Delete selected images
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $index) {
                if (isset($images[$index])) {
                    if (file_exists(public_path($images[$index]))) {
                        unlink(public_path($images[$index]));
                    }
                    unset($images[$index]);
                }
            }
            $images = array_values($images);
        }

        // Handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/blogs'), $imageName);
                $images[] = 'uploads/blogs/' . $imageName;
            }
        }

        $blog->update([
            'title'       => $request->title,
            'description' => $request->description ?? null, // ← null if not submitted
            'images'      => json_encode($images),
        ]);

        if (request()->is('api/*')) {
            if ($blog->images) {
                $imgs = json_decode($blog->images, true);
                $blog->image_urls = array_map(function ($image) {
                    return url($image);
                }, $imgs);
                unset($blog->images);
            }
            return response()->json(['message' => 'Blog updated successfully.', 'blog' => $blog], 200);
        }
        return redirect()->route('manage.blogs')->with('success', 'Blog updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $blog = Blog::findOrFail($id);

        if ($blog->images) {
            $images = json_decode($blog->images, true);
            foreach ($images as $image) {
                if (file_exists(public_path($image))) {
                    unlink(public_path($image));
                }
            }
        }

        $blog->delete();

        if (request()->is('api/*')) {
            return response()->json(['message' => 'Blog deleted successfully.'], 200);
        }
        return redirect()->route('manage.blogs')->with('success', 'Blog deleted successfully.');
    }
}