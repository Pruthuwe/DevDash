@extends('layouts.main')

@section('content')

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">All Inquiries</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">All Inquiries</li>
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
                            <iconify-icon icon="solar:clipboard-list-outline" class="fs-24"></iconify-icon>
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

<!-- Combined Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0">All Inquiries</h6>
        <div class="d-flex gap-2 flex-wrap w-100 w-md-auto">
            <input type="text" class="form-control flex-fill" id="searchInput" placeholder="Search name, phone, city...">
            <select class="form-select flex-fill" id="typeFilter">
                <option value="">All Types</option>
                <option value="loan">Loan Inquiry</option>
                <option value="product">Product Enquiry</option>
            </select>
            <select class="form-select flex-fill" id="statusFilter">
                <option value="">All Status</option>
                <option value="new">New</option>
                <option value="contacted">Contacted</option>
                <option value="closed">Closed</option>
            </select>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0" id="allInquiriesTable">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>City</th>
                        <th>Bike</th>
                        <th>Details</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    {{-- Loan Inquiries --}}
                    @foreach($loanInquiries as $inquiry)
                        <tr data-type="loan" data-status="{{ $inquiry->status }}">
                            <td>
                                <span class="badge bg-primary-100 text-primary-600 fw-medium px-2 py-1 inquiry-type-badge">
                                    <iconify-icon icon="solar:wallet-money-outline" class="me-1"></iconify-icon> Loan
                                </span>
                            </td>
                            <td class="fw-medium">{{ $inquiry->name }}</td>
                            <td>{{ $inquiry->phone }}</td>
                            <td>{{ $inquiry->city ?? '—' }}</td>
                            <td>{{ $inquiry->product->name ?? 'N/A' }}</td>
                            <td class="text-sm text-secondary-light">
                                @if($inquiry->bike_price)
                                    <span class="d-block">LKR {{ number_format($inquiry->bike_price, 2) }}</span>
                                @endif
                                @if($inquiry->monthly_payment)
                                    <span class="d-block text-xs">Monthly: LKR {{ number_format($inquiry->monthly_payment, 2) }}</span>
                                @endif
                            </td>
                            <td class="text-sm">{{ $inquiry->created_at->format('M j, Y, g:i a') }}</td>
                            <td>
                                <select class="form-select form-select-sm status-select"
                                        data-id="{{ $inquiry->id }}"
                                        data-type="loan"
                                        data-url="{{ route('loan-inquiries.update-status', $inquiry->id) }}"
                                        style="width:auto;">
                                    <option value="new"       @selected($inquiry->status === 'new')>New</option>
                                    <option value="contacted" @selected($inquiry->status === 'contacted')>Contacted</option>
                                    <option value="closed"   @selected($inquiry->status === 'closed')>Closed</option>
                                </select>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-info view-message-btn"
                                            title="View details"
                                            data-message="Bike: {{ $inquiry->product->name ?? 'N/A' }}&#10;Price: LKR {{ number_format($inquiry->bike_price ?? 0, 2) }}&#10;Monthly Payment: LKR {{ number_format($inquiry->monthly_payment ?? 0, 2) }}&#10;City: {{ $inquiry->city ?? '—' }}"
                                            data-name="{{ $inquiry->name }}">
                                        <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                    </button>
                                    <form method="POST" action="{{ route('loan-inquiries.destroy', $inquiry->id) }}" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Delete this loan inquiry?')" title="Delete">
                                            <iconify-icon icon="ic:outline-delete"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    {{-- Product Enquiries --}}
                    @foreach($productEnquiries as $enquiry)
                        <tr data-type="product" data-status="{{ $enquiry->status }}">
                            <td>
                                <span class="badge bg-info-100 text-info-600 fw-medium px-2 py-1 inquiry-type-badge">
                                    <iconify-icon icon="solar:chat-square-like-outline" class="me-1"></iconify-icon> Product
                                </span>
                            </td>
                            <td class="fw-medium">{{ $enquiry->name }}</td>
                            <td>{{ $enquiry->phone }}</td>
                            <td>{{ $enquiry->city ?? '—' }}</td>
                            <td>{{ $enquiry->product->name ?? 'N/A' }}</td>
                            <td class="text-sm text-secondary-light" style="max-width:220px;">
                                {{ \Illuminate\Support\Str::limit($enquiry->message, 70) ?: '—' }}
                            </td>
                            <td class="text-sm">{{ $enquiry->created_at->format('M j, Y, g:i a') }}</td>
                            <td>
                                <select class="form-select form-select-sm status-select"
                                        data-id="{{ $enquiry->id }}"
                                        data-type="product"
                                        data-url="{{ route('product-enquiries.update-status', $enquiry->id) }}"
                                        style="width:auto;">
                                    <option value="new"       @selected($enquiry->status === 'new')>New</option>
                                    <option value="contacted" @selected($enquiry->status === 'contacted')>Contacted</option>
                                    <option value="closed"   @selected($enquiry->status === 'closed')>Closed</option>
                                </select>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($enquiry->message)
                                        <button type="button" class="btn btn-sm btn-outline-info view-message-btn"
                                                title="View message"
                                                data-message="{{ $enquiry->message }}"
                                                data-name="{{ $enquiry->name }}">
                                            <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                        </button>
                                    @endif
                                    <form method="POST" action="{{ route('product-enquiries.destroy', $enquiry->id) }}" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Delete this product enquiry?')" title="Delete">
                                            <iconify-icon icon="ic:outline-delete"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if($loanInquiries->isEmpty() && $productEnquiries->isEmpty())
                        <tr><td colspan="9" class="text-center py-4">No inquiries found.</td></tr>
                    @endif

                </tbody>
            </table>
        </div>

        <!-- Pagination for both (shown separately below table) -->
        @if($loanInquiries->hasPages() || $productEnquiries->hasPages())
            <div class="px-4 py-3 d-flex flex-wrap gap-3 justify-content-between border-top">
                @if($loanInquiries->hasPages())
                    <div><small class="text-secondary-light me-2">Loan:</small>{{ $loanInquiries->links() }}</div>
                @endif
                @if($productEnquiries->hasPages())
                    <div><small class="text-secondary-light me-2">Product:</small>{{ $productEnquiries->links() }}</div>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="viewDetailsModalLabel">Details</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="viewDetailsModalBody" style="white-space: pre-line;"></p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    // Save previous status for stat-card delta
    $('.status-select').each(function () {
        $(this).data('prev-status', $(this).val());
    });

    // ── Search ──────────────────────────────────────────────────────────────
    $('#searchInput').on('keyup', applyFilters);

    // ── Type filter ─────────────────────────────────────────────────────────
    $('#typeFilter').on('change', applyFilters);

    // ── Status filter ────────────────────────────────────────────────────────
    $('#statusFilter').on('change', applyFilters);

    function applyFilters() {
        const search = $('#searchInput').val().toLowerCase();
        const type   = $('#typeFilter').val();
        const status = $('#statusFilter').val();

        $('#allInquiriesTable tbody tr').each(function () {
            const rowType   = $(this).data('type')   || '';
            const rowStatus = $(this).data('status') || '';
            const text      = $(this).text().toLowerCase();

            const matchSearch = !search || text.includes(search);
            const matchType   = !type   || rowType === type;
            const matchStatus = !status || rowStatus === status;

            $(this).toggle(matchSearch && matchType && matchStatus);
        });
    }

    // ── Status AJAX update ───────────────────────────────────────────────────
    $('.status-select').on('change', function () {
        const $select  = $(this);
        const url      = $select.data('url');
        const status   = $select.val();
        const prevStatus = $select.data('prev-status');

        $.ajax({
            url: url,
            type: 'POST',
            data: { status: status, _token: $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                if (response.success) {
                    $select.closest('tr').data('status', status);
                    updateStatCards(prevStatus, status);
                    $select.data('prev-status', status);
                } else {
                    alert(response.message || 'Failed to update status');
                    $select.val(prevStatus);
                }
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message || 'An error occurred while updating status');
                $select.val(prevStatus);
            }
        });
    });

    // ── View details modal ───────────────────────────────────────────────────
    $('.view-message-btn').on('click', function () {
        const name    = $(this).data('name');
        const message = $(this).data('message') || 'No details provided.';
        $('#viewDetailsModalLabel').text(name + "'s Details");
        $('#viewDetailsModalBody').text(message);
        $('#viewDetailsModal').modal('show');
    });

    // ── Stat card update ─────────────────────────────────────────────────────
    function updateStatCards(oldStatus, newStatus) {
        if (oldStatus) {
            const $old = $('#stat-' + oldStatus);
            if ($old.length) $old.text(Math.max(0, parseInt($old.text()) - 1));
        }
        if (newStatus) {
            const $new = $('#stat-' + newStatus);
            if ($new.length) $new.text(parseInt($new.text()) + 1);
        }
    }

});
</script>
@endpush

@push('styles')
<style>
/* Type badge (Loan / Product) — must never wrap, on any screen size */
#allInquiriesTable .inquiry-type-badge {
    white-space: nowrap !important;
    display: inline-flex;
    align-items: center;
    text-align: left;
}
#allInquiriesTable .inquiry-type-badge iconify-icon {
    display: inline-flex;
    align-items: center;
    vertical-align: middle;
    line-height: 1;
}
#allInquiriesTable td:first-child {
    white-space: nowrap;
}
</style>
@endpush