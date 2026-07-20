@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Loan Inquiries</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Loan Inquiries</li>
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
                            <iconify-icon icon="solar:wallet-money-outline" class="fs-24"></iconify-icon>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1" id="stat-total">{{ $stats['total'] }}</h6>
                        <p class="mb-0 text-sm text-secondary-light fw-medium">Total Inquiries</p>
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
                            <iconify-icon icon="solar:bell-outline" class="fs-24"></iconify-icon>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1" id="stat-new">{{ $stats['new'] }}</h6>
                        <p class="mb-0 text-sm text-secondary-light fw-medium">New</p>
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
                        <div class="avatar avatar-lg bg-info-50 text-info-600 rounded">
                            <iconify-icon icon="solar:phone-calling-outline" class="fs-24"></iconify-icon>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1" id="stat-contacted">{{ $stats['contacted'] }}</h6>
                        <p class="mb-0 text-sm text-secondary-light fw-medium">Contacted</p>
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
                        <h6 class="mb-1" id="stat-closed">{{ $stats['closed'] }}</h6>
                        <p class="mb-0 text-sm text-secondary-light fw-medium">Closed</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loan Inquiries Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">All Loan Inquiries</h6>
        <div class="d-flex gap-2">
            <input type="text" class="form-control" id="searchInput" placeholder="Search name, phone, city...">
            <select class="form-select" id="statusFilter">
                <option value="">All Status</option>
                <option value="new">New</option>
                <option value="contacted">Contacted</option>
                <option value="closed">Closed</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="loanInquiriesTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>City</th>
                        <th>Bike</th>
                        <th>Finance Company</th>
                        <th>Bike Price</th>
                        <th>Monthly Payment</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loanInquiries as $inquiry)
                        <tr data-id="{{ $inquiry->id }}">
                            <td>{{ $inquiry->name }}</td>
                            <td>{{ $inquiry->phone }}</td>
                            <td>{{ $inquiry->city ?? 'N/A' }}</td>
                            <td>{{ $inquiry->product->name ?? 'N/A' }}</td>
                            <td>{{ $inquiry->finance_company ?? 'N/A' }}</td>
                            <td>{{ $inquiry->bike_price ? 'LKR ' . number_format($inquiry->bike_price, 2) : 'N/A' }}</td>
                            <td>{{ $inquiry->monthly_payment ? 'LKR ' . number_format($inquiry->monthly_payment, 2) : 'N/A' }}</td>
                            <td>{{ $inquiry->created_at->format('F j, Y, g:i a') }}</td>
                            <td>
                                <select class="form-select form-select-sm status-select" data-id="{{ $inquiry->id }}" style="width: auto;">
                                    <option value="new" @selected($inquiry->status === 'new')>New</option>
                                    <option value="contacted" @selected($inquiry->status === 'contacted')>Contacted</option>
                                    <option value="closed" @selected($inquiry->status === 'closed')>Closed</option>
                                </select>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('loan-inquiries.destroy', $inquiry->id) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this loan inquiry? This action cannot be undone.')">
                                        <iconify-icon icon="ic:outline-delete"></iconify-icon>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">No loan inquiries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($loanInquiries) && $loanInquiries->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $loanInquiries->links() }}
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {

    $('.status-select').each(function() {
        $(this).data('prev-status', $(this).val());
    });

    $('#searchInput').on('keyup', function() {
        const filter = this.value.toLowerCase();
        $('#loanInquiriesTable tbody tr').each(function() {
            $(this).toggle($(this).text().toLowerCase().includes(filter));
        });
    });

    $('#statusFilter').on('change', function() {
        const filter = this.value.toLowerCase();
        $('#loanInquiriesTable tbody tr').each(function() {
            if (filter === '') { $(this).show(); return; }
            const status = $(this).find('.status-select').val();
            $(this).toggle(status === filter);
        });
    });

    $('.status-select').on('change', function() {
        const id = $(this).data('id');
        const status = $(this).val();
        const $select = $(this);

        $.ajax({
            url: `/loan-inquiries/${id}/update-status`,
            type: 'POST',
            data: { status: status, _token: $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                if (response.success) {
                    updateStatCards($select.data('prev-status'), status);
                    $select.data('prev-status', status);
                } else {
                    alert(response.message || 'Failed to update status');
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'An error occurred while updating status');
            }
        });
    });

    function updateStatCards(oldStatus, newStatus) {
        if (oldStatus) {
            const $old = $('#stat-' + oldStatus);
            $old.text(Math.max(0, parseInt($old.text()) - 1));
        }
        if (newStatus) {
            const $new = $('#stat-' + newStatus);
            $new.text(parseInt($new.text()) + 1);
        }
    }

});
</script>
@endpush
