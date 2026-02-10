@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Appointment Management</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Appointments</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-24">
    <div class="col-md-3 col-sm-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar avatar-lg bg-primary-50 text-primary-600 rounded">
                            <iconify-icon icon="solar:calendar-outline" class="fs-24"></iconify-icon>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">{{ $stats['total'] }}</h6>
                        <p class="mb-0 text-sm text-secondary-light fw-medium">Total Appointments</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar avatar-lg bg-warning-50 text-warning-600 rounded">
                            <iconify-icon icon="solar:clock-circle-outline" class="fs-24"></iconify-icon>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">{{ $stats['pending'] }}</h6>
                        <p class="mb-0 text-sm text-secondary-light fw-medium">Pending</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar avatar-lg bg-success-50 text-success-600 rounded">
                            <iconify-icon icon="solar:check-circle-outline" class="fs-24"></iconify-icon>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">{{ $stats['approved'] }}</h6>
                        <p class="mb-0 text-sm text-secondary-light fw-medium">Approved</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar avatar-lg bg-danger-50 text-danger-600 rounded">
                            <iconify-icon icon="solar:close-circle-outline" class="fs-24"></iconify-icon>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">{{ $stats['rejected'] }}</h6>
                        <p class="mb-0 text-sm text-secondary-light fw-medium">Rejected</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Appointments Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">All Appointments</h6>
        <div class="d-flex gap-2">
            <input type="text" class="form-control" id="searchInput" placeholder="Search appointments...">
            <select class="form-select" id="statusFilter">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="appointmentsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Service</th>
                        <th>Submitted Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        <tr data-id="{{ $appointment->id }}">
                            <td>{{ $appointment->customer->name ?? 'N/A' }}</td>
                            <td>{{ $appointment->customer->email ?? 'N/A' }}</td>
                            <td>{{ $appointment->customer->phone ?? $appointment->customer->mobile ?? 'N/A' }}</td>
                            <td>{{ $appointment->service->title ?? 'N/A' }}</td>
                            <td>{{ $appointment->created_at->format('F j, Y, g:i a') }}</td>
                            <td>
                                <span class="badge bg-{{ $appointment->status == 'pending' ? 'warning' : ($appointment->status == 'approved' ? 'success' : 'danger') }}-light text-{{ $appointment->status == 'pending' ? 'warning' : ($appointment->status == 'approved' ? 'success' : 'danger') }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-appointments')))
                                    <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                        <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                    </a>
                                    @endif
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'edit-appointments')))
                                    @if($appointment->status === 'pending')
                                    <button type="button" class="btn btn-sm btn-outline-success approve-btn" data-id="{{ $appointment->id }}" title="Approve Appointment">
                                        <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger reject-btn" data-id="{{ $appointment->id }}" title="Reject Appointment">
                                        <iconify-icon icon="solar:close-circle-outline"></iconify-icon>
                                    </button>
                                    @endif
                                    @endif
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'delete-appointments')))
                                    <form method="POST" action="{{ route('appointments.destroy', $appointment->id) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Appointment" onclick="return confirm('Are you sure you want to delete this appointment? This action cannot be undone.')">
                                            <iconify-icon icon="ic:outline-delete"></iconify-icon>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No appointments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(isset($appointments) && $appointments->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Search functionality
    $('#searchInput').on('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = $('#appointmentsTable tbody tr');

        rows.each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(filter));
        });
    });

    // Status filter
    $('#statusFilter').on('change', function() {
        const filter = this.value.toLowerCase();
        const rows = $('#appointmentsTable tbody tr');

        rows.each(function() {
            if (filter === '') {
                $(this).show();
            } else {
                const status = $(this).find('.badge').text().toLowerCase();
                $(this).toggle(status.includes(filter));
            }
        });
    });

    // Approve appointment
    $('.approve-btn').on('click', function() {
        const appointmentId = $(this).data('id');
        const row = $(this).closest('tr');

        if (confirm('Are you sure you want to approve this appointment?')) {
            updateAppointmentStatus(appointmentId, 'approved', row);
        }
    });

    // Reject appointment
    $('.reject-btn').on('click', function() {
        const appointmentId = $(this).data('id');
        const row = $(this).closest('tr');

        if (confirm('Are you sure you want to reject this appointment?')) {
            updateAppointmentStatus(appointmentId, 'rejected', row);
        }
    });

    function updateAppointmentStatus(appointmentId, status, row) {
        $.ajax({
            url: `/appointments/${appointmentId}/update-status`,
            type: 'POST',
            data: {
                status: status,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Update the status badge
                    const badgeClass = status === 'approved' ? 'bg-success-light text-success' : 'bg-danger-light text-danger';
                    row.find('.badge').removeClass('bg-warning-light text-warning bg-success-light text-success bg-danger-light text-danger')
                        .addClass(badgeClass)
                        .text(status.charAt(0).toUpperCase() + status.slice(1));

                    // Hide action buttons for this row
                    row.find('.approve-btn, .reject-btn').hide();

                    // Show success message
                    toastr.success(response.message || `Appointment ${status} successfully!`);

                    // Update statistics if available
                    updateStatistics();
                } else {
                    toastr.error(response.message || 'Failed to update appointment status');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response?.message || 'An error occurred while updating the appointment status');
            }
        });
    }

    function updateStatistics() {
        // Reload the page to update statistics
        location.reload();
    }
});
</script>
@endpush