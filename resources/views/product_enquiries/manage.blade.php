@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Product Enquiries</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Product Enquiries</li>
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
                            <iconify-icon icon="solar:chat-square-like-outline" class="fs-24"></iconify-icon>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1" id="stat-total">{{ $stats['total'] }}</h6>
                        <p class="mb-0 text-sm text-secondary-light fw-medium">Total Enquiries</p>
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

<!-- Product Enquiries Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0">All Product Enquiries</h6>
        <div class="d-flex gap-2 flex-wrap w-100 w-md-auto">
            <input type="text" class="form-control flex-fill" id="searchInput" placeholder="Search name, phone...">
            <select class="form-select flex-fill" id="statusFilter">
                <option value="">All Status</option>
                <option value="new">New</option>
                <option value="contacted">Contacted</option>
                <option value="closed">Closed</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="productEnquiriesTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Bike</th>
                        <th>Message</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productEnquiries as $enquiry)
                        <tr data-id="{{ $enquiry->id }}">
                            <td>{{ $enquiry->name }}</td>
                            <td>{{ $enquiry->phone }}</td>
                            <td>{{ $enquiry->product->name ?? 'N/A' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($enquiry->message, 60) ?: 'N/A' }}</td>
                            <td>{{ $enquiry->created_at->format('F j, Y, g:i a') }}</td>
                            <td>
                                <select class="form-select form-select-sm status-select" data-id="{{ $enquiry->id }}" style="width: auto;">
                                    <option value="new" @selected($enquiry->status === 'new')>New</option>
                                    <option value="contacted" @selected($enquiry->status === 'contacted')>Contacted</option>
                                    <option value="closed" @selected($enquiry->status === 'closed')>Closed</option>
                                </select>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="javascript:void(0)" class="btn btn-sm btn-outline-info view-message-btn" title="View Full Message" data-message="{{ $enquiry->message }}" data-name="{{ $enquiry->name }}">
                                        <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                    </a>
                                    <form method="POST" action="{{ route('product-enquiries.destroy', $enquiry->id) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this enquiry? This action cannot be undone.')">
                                            <iconify-icon icon="ic:outline-delete"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No product enquiries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($productEnquiries) && $productEnquiries->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $productEnquiries->links() }}
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
        $('#productEnquiriesTable tbody tr').each(function() {
            $(this).toggle($(this).text().toLowerCase().includes(filter));
        });
    });

    $('#statusFilter').on('change', function() {
        const filter = this.value.toLowerCase();
        $('#productEnquiriesTable tbody tr').each(function() {
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
            url: `/product-enquiries/${id}/update-status`,
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

    $('.view-message-btn').on('click', function() {
        alert($(this).data('name') + ' says:\n\n' + ($(this).data('message') || 'No message provided.'));
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