@extends('layouts.main')

@section('content')

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Contact Messages</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact Messages</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-24">
    <div class="col-md-4 col-sm-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar avatar-lg bg-primary-50 text-primary-600 rounded">
                            <iconify-icon icon="solar:letter-outline" class="fs-24"></iconify-icon>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">{{ $stats['total'] }}</h6>
                        <p class="mb-0 text-sm text-secondary-light fw-medium">Total Messages</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar avatar-lg bg-warning-50 text-warning-600 rounded">
                            <iconify-icon icon="solar:bell-outline" class="fs-24"></iconify-icon>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">{{ $stats['unread'] }}</h6>
                        <p class="mb-0 text-sm text-secondary-light fw-medium">Unread</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar avatar-lg bg-success-50 text-success-600 rounded">
                            <iconify-icon icon="solar:check-read-outline" class="fs-24"></iconify-icon>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">{{ $stats['read'] }}</h6>
                        <p class="mb-0 text-sm text-secondary-light fw-medium">Read</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Messages Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">All Contact Messages</h6>
        <div class="d-flex gap-2">
            <input type="text" class="form-control" id="searchInput" placeholder="Search messages...">
            <select class="form-select" id="statusFilter">
                <option value="">All</option>
                <option value="unread">Unread</option>
                <option value="read">Read</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="contactsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Message</th>
                        <th>Received</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                        <tr data-id="{{ $contact->id }}">
                            <td>{{ $contact->name }}</td>
                            <td><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></td>
                            <td>{{ $contact->mobile ?? '—' }}</td>
                            <td>
                                <span title="{{ $contact->message }}">
                                    {{ Str::limit($contact->message, 60) }}
                                </span>
                            </td>
                            <td>{{ $contact->created_at->format('M j, Y g:i a') }}</td>
                            <td>
                                <span class="badge bg-{{ $contact->status === 'unread' ? 'warning' : 'success' }}-light text-{{ $contact->status === 'unread' ? 'warning' : 'success' }}">
                                    {{ ucfirst($contact->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if($contact->status === 'unread')
                                    <button class="btn btn-sm btn-outline-success mark-read-btn" data-id="{{ $contact->id }}" title="Mark as Read">
                                        <iconify-icon icon="solar:check-read-outline"></iconify-icon>
                                    </button>
                                    @endif
                                    <form method="POST" action="{{ route('contacts.destroy', $contact->id) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this message?')">
                                            <iconify-icon icon="ic:outline-delete"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No messages yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contacts->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $contacts->links() }}
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    // Search
    $('#searchInput').on('keyup', function () {
        const filter = this.value.toLowerCase();
        $('#contactsTable tbody tr').each(function () {
            $(this).toggle($(this).text().toLowerCase().includes(filter));
        });
    });

    // Status filter
    $('#statusFilter').on('change', function () {
        const filter = this.value.toLowerCase();
        $('#contactsTable tbody tr').each(function () {
            if (!filter) { $(this).show(); return; }
            const status = $(this).find('.badge').text().trim().toLowerCase();
            $(this).toggle(status === filter);
        });
    });

    // Mark as read
    $('.mark-read-btn').on('click', function () {
        const id  = $(this).data('id');
        const row = $(this).closest('tr');
        $.ajax({
            url:  `/contacts/${id}/mark-read`,
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                if (res.success) {
                    row.find('.badge')
                        .removeClass('bg-warning-light text-warning')
                        .addClass('bg-success-light text-success')
                        .text('Read');
                    row.find('.mark-read-btn').remove();
                    toastr.success('Marked as read');
                }
            },
            error: function () { toastr.error('Failed to update status'); }
        });
    });
});
</script>
@endpush