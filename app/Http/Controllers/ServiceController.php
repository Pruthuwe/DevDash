<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::all();
        if (request()->is('api/*')) {
            return response()->json(['services' => $services], 200);
        }
        return view('service.manageService', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (request()->is('api/*')) {
            return response()->json(['message' => 'Create form not available via API'], 404);
        }
        return view('service.addService');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $service = Service::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        if (request()->is('api/*')) {
            return response()->json(['message' => 'Service created successfully.', 'service' => $service], 201);
        }
        return redirect()->route('manage.services')->with('success', 'Service created successfully.');
    }

    public function show(string $id)
    {
        $service = Service::findOrFail($id);
        if (request()->is('api/*')) {
            return response()->json(['service' => $service], 200);
        }
        // If needed, add a show view
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $service = Service::findOrFail($id);
        if (request()->is('api/*')) {
            return response()->json(['service' => $service], 200);
        }
        return view('service.editService', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $service->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        if (request()->is('api/*')) {
            return response()->json(['message' => 'Service updated successfully.', 'service' => $service], 200);
        }
        return redirect()->route('manage.services')->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        if (request()->is('api/*')) {
            return response()->json(['message' => 'Service deleted successfully.'], 200);
        }
        return redirect()->route('manage.services')->with('success', 'Service deleted successfully.');
    }
}
