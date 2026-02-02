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
        <h6 class="fw-semibold mb-2">Purchase Management</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manage Purchases</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-purchases')))
        <a href="{{ route('add.purchase') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
            Add Purchase
        </a>
        @endif
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-24">
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-secondary-light mb-1">Total Purchases</h6>
                    <h4 class="mb-0">{{ $totalPurchases }}</h4>
                </div>
                <div class="stat-icon bg-primary-light">
                    <iconify-icon icon="solar:cart-large-outline" class="text-primary"></iconify-icon>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <small class="text-secondary-light">All purchase orders</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-secondary-light mb-1">Total Amount</h6>
                    <h4 class="mb-0">${{ number_format($totalAmount, 2) }}</h4>
                </div>
                <div class="stat-icon bg-success-light">
                    <iconify-icon icon="solar:dollar-outline" class="text-success"></iconify-icon>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <small class="text-secondary-light">Completed purchases</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-secondary-light mb-1">Pending</h6>
                    <h4 class="mb-0">{{ $pendingPurchases }}</h4>
                </div>
                <div class="stat-icon bg-warning-light">
                    <iconify-icon icon="solar:clock-circle-outline" class="text-warning"></iconify-icon>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <small class="text-secondary-light">Awaiting completion</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-secondary-light mb-1">Completed</h6>
                    <h4 class="mb-0">{{ $completedPurchases }}</h4>
                </div>
                <div class="stat-icon bg-info-light">
                    <iconify-icon icon="solar:check-circle-outline" class="text-info"></iconify-icon>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <small class="text-secondary-light">Successfully processed</small>
            </div>
        </div>
    </div>
</div>

<!-- Purchases Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 text-lg">All Purchases</h6>
        <div class="d-flex gap-2">
            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search purchases..." style="width: 250px;">
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="purchasesTable">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td>
                                <strong>{{ $purchase->reference_number }}</strong>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <iconify-icon icon="solar:user-outline" class="text-primary"></iconify-icon>
                                    {{ $purchase->supplier->name }}
                                </div>
                            </td>
                            <td>{{ $purchase->purchase_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-info-light text-info">
                                    {{ $purchase->purchaseItems->count() }} items
                                </span>
                            </td>
                            <td>
                                <strong>${{ number_format($purchase->total_amount, 2) }}</strong>
                            </td>
                            <td>
                                @if($purchase->status === 'completed')
                                    <span class="badge bg-success-light text-success d-flex align-items-center gap-2">
                                        <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                                        Completed
                                    </span>
                                @elseif($purchase->status === 'pending')
                                    <span class="badge bg-warning-light text-warning d-flex align-items-center gap-2">
                                        <iconify-icon icon="solar:clock-circle-outline"></iconify-icon>
                                        Pending
                                    </span>
                                @else
                                    <span class="badge bg-danger-light text-danger d-flex align-items-center gap-2">
                                        <iconify-icon icon="solar:close-circle-outline"></iconify-icon>
                                        Cancelled
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-purchases')))
                                    <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-sm btn-outline-info" title="View">
                                        <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                    </a>
                                    @endif
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'edit-purchases')))
                                    <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <iconify-icon icon="solar:pen-outline"></iconify-icon>
                                    </a>
                                    @endif
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'delete-purchases')))
                                    <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this purchase?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <iconify-icon icon="ic:outline-delete"></iconify-icon>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-secondary-light">
                                    <iconify-icon icon="solar:box-outline" class="fs-1"></iconify-icon>
                                    <p class="mb-0 mt-2">No purchases found</p>
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-purchases')))
                                    <a href="{{ route('add.purchase') }}" class="btn btn-sm btn-primary mt-2">
                                        <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                                        Create Your First Purchase
                                    </a>
                                    @endif
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
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#purchasesTable tbody tr');
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });

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

@push('styles')
<style>
    .stat-card {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.2s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 24px;
    }
    
    .bg-primary-light {
        background-color: rgba(99, 102, 241, 0.1);
    }
    
    .bg-success-light {
        background-color: rgba(34, 197, 94, 0.1);
    }
    
    .bg-warning-light {
        background-color: rgba(251, 191, 36, 0.1);
    }
    
    .bg-info-light {
        background-color: rgba(59, 130, 246, 0.1);
    }
    
    .bg-danger-light {
        background-color: rgba(239, 68, 68, 0.1);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.05);
    }
</style>
@endpush
