@extends('layouts.main')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <div>
            <h5 class="fw-semibold mb-1">Appointment Details</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('manage.appointments') }}">Appointments</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $appointment->customer->name ?? 'N/A' }}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('manage.appointments') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
                <iconify-icon icon="solar:arrow-left-outline"></iconify-icon> Back
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="row g-3">

        <!-- LEFT COLUMN -->
        <div class="col-lg-8 col-md-12">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Appointment Information</h6>
                </div>

                <div class="card-body">
                    <div class="row g-3">

                        {{-- Customer Info --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Customer Name</label>
                            <div>{{ $appointment->customer->name ?? 'N/A' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <div>{{ $appointment->customer->email ?? 'N/A' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <div>{{ $appointment->customer->phone ?? $appointment->customer->mobile ?? 'N/A' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Service</label>
                            <div>{{ $appointment->service->title ?? 'N/A' }}</div>
                        </div>

                        {{-- Appointment Details --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Appointment Date</label>
                            <div>{{ $appointment->appointment_date ? $appointment->appointment_date->format('M d, Y H:i') : 'N/A' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Submitted Date</label>
                            <div>{{ $appointment->created_at->format('M d, Y H:i') }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <div>
                                @if($appointment->status === 'pending')
                                    <span class="badge bg-warning-light text-warning">Pending</span>
                                @elseif($appointment->status === 'approved')
                                    <span class="badge bg-success-light text-success">Approved</span>
                                @elseif($appointment->status === 'rejected')
                                    <span class="badge bg-danger-light text-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary-light text-secondary">Unknown</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Last Updated</label>
                            <div>{{ $appointment->updated_at->format('M d, Y H:i') }}</div>
                        </div>

                    </div>

                    <hr>

                    {{-- Notes --}}
                    @if($appointment->notes)
                        <div>
                            <label class="form-label fw-semibold">Notes</label>
                            <p class="mb-0">{{ $appointment->notes }}</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="col-lg-4 col-md-12">
            <div class="row g-3">

                <!-- Status & Actions -->
                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">Status & Actions</h6>
                        </div>
                        <div class="card-body">

                            <div class="mb-3">
                                <label class="fw-semibold">Current Status</label>
                                <div class="mt-1">
                                    @if($appointment->status === 'pending')
                                        <span class="badge bg-warning-light text-warning fs-6 px-3 py-2">Pending</span>
                                    @elseif($appointment->status === 'approved')
                                        <span class="badge bg-success-light text-success fs-6 px-3 py-2">Approved</span>
                                    @elseif($appointment->status === 'rejected')
                                        <span class="badge bg-danger-light text-danger fs-6 px-3 py-2">Rejected</span>
                                    @else
                                        <span class="badge bg-secondary-light text-secondary fs-6 px-3 py-2">Unknown</span>
                                    @endif
                                </div>
                            </div>

                            @if($appointment->status === 'pending')
                            <hr>
                            <div class="mb-2">
                                <label class="fw-semibold">Actions</label>
                            </div>

                            @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'edit-appointments')))
                            <div class="d-grid gap-2">
                                <form method="POST" action="{{ route('appointments.update', $appointment) }}" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2" onclick="return confirm('Are you sure you want to approve this appointment?')">
                                        <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                                        Approve Appointment
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('appointments.update', $appointment) }}" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2" onclick="return confirm('Are you sure you want to reject this appointment?')">
                                        <iconify-icon icon="solar:close-circle-outline"></iconify-icon>
                                        Reject Appointment
                                    </button>
                                </form>
                            </div>
                            @endif
                            @endif

                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">Customer Information</h6>
                        </div>
                        <div class="card-body">

                            <div class="mb-2">
                                <label class="fw-semibold">Name</label>
                                <div>{{ $appointment->customer->name ?? 'N/A' }}</div>
                            </div>

                            <div class="mb-2">
                                <label class="fw-semibold">Email</label>
                                <div class="text-truncate">{{ $appointment->customer->email ?? 'N/A' }}</div>
                            </div>

                            <div class="mb-2">
                                <label class="fw-semibold">Phone</label>
                                <div>{{ $appointment->customer->phone ?? $appointment->customer->mobile ?? 'N/A' }}</div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Service Info -->
                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">Service Information</h6>
                        </div>
                        <div class="card-body">

                            <div class="mb-2">
                                <label class="fw-semibold">Service</label>
                                <div>{{ $appointment->service->title ?? 'N/A' }}</div>
                            </div>

                            <div class="mb-2">
                                <label class="fw-semibold">Appointment Date</label>
                                <div>{{ $appointment->appointment_date ? $appointment->appointment_date->format('M d, Y') : 'N/A' }}</div>
                            </div>

                            <div class="mb-2">
                                <label class="fw-semibold">Appointment Time</label>
                                <div>{{ $appointment->appointment_date ? $appointment->appointment_date->format('H:i') : 'N/A' }}</div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">Statistics</h6>
                        </div>
                        <div class="card-body">
                            <div><strong>Created:</strong> {{ $appointment->created_at->format('M d, Y H:i') }}</div>
                            <div><strong>Updated:</strong> {{ $appointment->updated_at->format('M d, Y H:i') }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection