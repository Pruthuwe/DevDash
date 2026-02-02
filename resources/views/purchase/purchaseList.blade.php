@extends('layouts.main')

@section('content')

<!-- Display Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ri-checkbox-circle-line me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ri-error-warning-line me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Purchase List</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Purchase List</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('add.purchase') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
            Add Purchase
        </a>
    </div>
</div>

<!-- Purchases Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 text-lg">All Purchases</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="purchasesTable">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td><strong>{{ $purchase->reference_number }}</strong></td>
                            <td>{{ $purchase->supplier->name }}</td>
                            <td>{{ $purchase->purchase_date->format('M d, Y') }}</td>
                            <td><strong>${{ number_format($purchase->total_amount, 2) }}</strong></td>
                            <td>
                                @if($purchase->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($purchase->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-sm btn-outline-info" title="View">
                                        <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                    </a>
                                    <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <iconify-icon icon="solar:pen-outline"></iconify-icon>
                                    </a>
                                    <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-secondary-light">
                                    <iconify-icon icon="solar:box-outline" class="fs-1"></iconify-icon>
                                    <p class="mb-0 mt-2">No purchases found</p>
                                    <a href="{{ route('add.purchase') }}" class="btn btn-sm btn-primary mt-2">
                                        <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                                        Create Your First Purchase
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endsection
