<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if this is an API request
        if ($request->expectsJson() || $request->is('api/*')) {
            $appointments = Appointment::with(['customer', 'service'])->get();

            return response()->json([
                'success' => true,
                'data' => $appointments->map(function ($appointment) {
                    return [
                        'id' => $appointment->id,
                        'name' => $appointment->customer->name ?? 'N/A',
                        'email' => $appointment->customer->email ?? 'N/A',
                        'phone' => $appointment->customer->phone ?? $appointment->customer->mobile ?? 'N/A',
                        'service' => $appointment->service->title ?? 'N/A',
                        'submitted_date' => $appointment->created_at->format('F j, Y, g:i a'),
                        'status' => $appointment->status,
                    ];
                })
            ]);
        }

        // Web view - redirect to manage
        return view('appointments.show', compact('appointment'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Appointment $appointment)
    {
        $appointment->load(['customer', 'service']);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $appointment->id,
                    'name' => $appointment->customer->name ?? 'N/A',
                    'email' => $appointment->customer->email ?? 'N/A',
                    'phone' => $appointment->customer->phone ?? $appointment->customer->mobile ?? 'N/A',
                    'service' => $appointment->service->title ?? 'N/A',
                    'submitted_date' => $appointment->created_at->format('Y-m-d H:i:s'),
                    'status' => $appointment->status,
                    'appointment_date' => $appointment->appointment_date,
                    'notes' => $appointment->notes,
                ]
            ]);
        }

        return view('appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        // Since we only allow viewing and status updates, redirect to manage page
        return redirect()->route('manage.appointments');
    }

    public function update(Request $request, Appointment $appointment)
    {
        // Check if this is a status update
        if ($request->has('status')) {
            return $this->updateStatus($request, $appointment);
        }

        // Full appointment update (if needed in the future)
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date|after:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $appointment->update($request->only([
            'customer_id',
            'service_id',
            'appointment_date',
            'notes'
        ]));

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Appointment updated successfully',
                'data' => $appointment->load(['customer', 'service'])
            ]);
        }

        return redirect()->route('manage.appointments')->with('success', 'Appointment updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Appointment deleted successfully'
            ]);
        }

        return redirect()->route('manage.appointments')->with('success', 'Appointment deleted successfully');
    }

    /**
     * Display the manage appointments page.
     */
    public function manage(Request $request)
    {
        $query = Appointment::with(['customer', 'service']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $appointments = $query->orderBy('created_at', 'desc')->paginate(15);
        $services = Service::all();

        // Statistics
        $stats = [
            'total' => Appointment::count(),
            'pending' => Appointment::where('status', 'pending')->count(),
            'approved' => Appointment::where('status', 'approved')->count(),
            'rejected' => Appointment::where('status', 'rejected')->count(),
        ];

        return view('appointments.manageAppointments', compact('appointments', 'services', 'stats'));
    }

    /**
     * Update appointment status.
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status',
                'errors' => $validator->errors()
            ], 422);
        }

        $appointment->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment status updated successfully',
            'data' => [
                'id' => $appointment->id,
                'status' => $appointment->status,
            ]
        ]);
    }
}