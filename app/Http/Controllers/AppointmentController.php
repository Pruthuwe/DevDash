<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use GuzzleHttp\Client;
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
        // Check permissions
        if (Auth::user()->user_type !== 'admin' && (!Auth::user()->role || !Auth::user()->role->permissions->contains('name', 'view-appointments'))) {
            abort(403, 'You do not have permission to view appointments.');
        }

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
        // Check permissions
        if (Auth::user()->user_type !== 'admin' && (!Auth::user()->role || !Auth::user()->role->permissions->contains('name', 'view-appointments'))) {
            abort(403, 'You do not have permission to view appointments.');
        }

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
        // Check permissions
        if (Auth::user()->user_type !== 'admin' && (!Auth::user()->role || !Auth::user()->role->permissions->contains('name', 'edit-appointments'))) {
            abort(403, 'You do not have permission to edit appointments.');
        }

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
        // Check permissions
        if (Auth::user()->user_type !== 'admin' && (!Auth::user()->role || !Auth::user()->role->permissions->contains('name', 'delete-appointments'))) {
            abort(403, 'You do not have permission to delete appointments.');
        }

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
        // Check permissions
        if (Auth::user()->user_type !== 'admin' && (!Auth::user()->role || !Auth::user()->role->permissions->contains('name', 'view-appointments'))) {
            abort(403, 'You do not have permission to view appointments.');
        }
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
        // Check permissions
        if (Auth::user()->user_type !== 'admin' && (!Auth::user()->role || !Auth::user()->role->permissions->contains('name', 'edit-appointments'))) {
            abort(403, 'You do not have permission to edit appointments.');
        }

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

    /**
     * Import appointment from JSON API.
     */
    public function importFromJson(Request $request)
    {
        // Check permissions
        if (Auth::user()->user_type !== 'admin' && (!Auth::user()->role || !Auth::user()->role->permissions->contains('name', 'create-appointments'))) {
            abort(403, 'You do not have permission to create appointments.');
        }

        // Placeholder API URL - replace with actual API endpoint
        $apiUrl = 'https://api.example.com/appointments'; // TODO: Replace with actual API URL

        try {
            // Fetch data from API
            $client = new Client();
            $response = $client->get($apiUrl);
            $data = json_decode($response->getBody(), true);

            // Assuming the API returns an array of appointments or a single appointment
            // If it's a single appointment, wrap it in an array
            if (isset($data['customer'])) {
                $appointmentsData = [$data];
            } elseif (is_array($data)) {
                $appointmentsData = $data;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid API response structure'
                ], 422);
            }

            $importedCount = 0;

            foreach ($appointmentsData as $appointmentData) {
                // Validate JSON structure
                if (!isset($appointmentData['customer']) || !isset($appointmentData['service']) || !isset($appointmentData['submitted_at'])) {
                    continue; // Skip invalid entries
                }

                // Handle customer
                $customer = Customer::firstOrCreate(
                    ['email' => $appointmentData['customer']['email']],
                    [
                        'name' => $appointmentData['customer']['name'],
                        'phone' => $appointmentData['customer']['phone_number'] ?? null,
                        'mobile' => $appointmentData['customer']['phone_number'] ?? null,
                    ]
                );

                // Handle service
                $service = Service::firstOrCreate(
                    ['title' => $appointmentData['service']['name']],
                    ['description' => null]
                );

                // Create appointment (avoid duplicates based on customer, service, and date)
                $existingAppointment = Appointment::where('customer_id', $customer->id)
                    ->where('service_id', $service->id)
                    ->where('appointment_date', $appointmentData['submitted_at']['formatted'])
                    ->first();

                if (!$existingAppointment) {
                    Appointment::create([
                        'customer_id' => $customer->id,
                        'service_id' => $service->id,
                        'appointment_date' => $appointmentData['submitted_at']['formatted'],
                        'notes' => $appointmentData['notes'] ?? null,
                        'status' => 'pending',
                    ]);
                    $importedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$importedCount} appointment(s) from API"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch data from API: ' . $e->getMessage()
            ], 500);
        }
    }
}