@extends('layouts.main')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <div>
            <h5 class="fw-semibold mb-1">Purchase Details</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('manage.purchases') }}">Purchases</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $purchase->reference_number }}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2">
                <iconify-icon icon="solar:pen-outline"></iconify-icon> Edit
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
                <iconify-icon icon="solar:printer-outline"></iconify-icon> Print
            </button>
            <a href="{{ route('manage.purchases') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
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
                    <h6 class="mb-0">Purchase Information</h6>
                </div>

                <div class="card-body">
                    <div class="row g-3">

                        {{-- Basic Info --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Reference Number</label>
                            <div>{{ $purchase->reference_number }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Purchase Date</label>
                            <div>{{ $purchase->purchase_date->format('M d, Y') }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <div>
                                @if($purchase->status == 'pending')
                                    <span class="badge bg-warning-light text-warning">Pending</span>
                                @elseif($purchase->status == 'completed')
                                    <span class="badge bg-success-light text-success">Completed</span>
                                @elseif($purchase->status == 'cancelled')
                                    <span class="badge bg-danger-light text-danger">Cancelled</span>
                                @else
                                    <span class="badge bg-secondary-light text-secondary">Unknown</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Amount</label>
                            <div class="fw-bold text-primary">${{ number_format($purchase->total_amount, 2) }}</div>
                        </div>

                        {{-- Supplier Info --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Supplier</label>
                            <div>{{ $purchase->supplier->name }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Supplier Email</label>
                            <div>{{ $purchase->supplier->email ?: 'Not specified' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Supplier Phone</label>
                            <div>{{ $purchase->supplier->mobile ?? $purchase->supplier->phone ?: 'Not specified' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Supplier Address</label>
                            <div>{{ $purchase->supplier->address ?: 'Not specified' }}</div>
                        </div>

                    </div>

                    <hr>

                    {{-- Notes --}}
                    @if($purchase->notes)
                        <div>
                            <label class="form-label fw-semibold">Notes</label>
                            <p class="mb-0">{{ $purchase->notes }}</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="col-lg-4 col-md-12">
            <div class="row g-3">

                <!-- Purchase Items -->
                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">Purchase Items</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchase->purchaseItems as $item)
                                            <tr>
                                                <td class="text-truncate" style="max-width: 120px;" title="{{ $item->product->name }}">
                                                    {{ Str::limit($item->product->name, 15) }}
                                                </td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>${{ number_format($item->unit_price, 2) }}</td>
                                                <td>${{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">Total:</td>
                                            <td class="fw-bold">${{ number_format($purchase->total_amount, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
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
                            <div><strong>Items:</strong> {{ $purchase->purchaseItems->count() }}</div>
                            <div><strong>Created:</strong> {{ $purchase->created_at->format('M d, Y') }}</div>
                            <div><strong>Updated:</strong> {{ $purchase->updated_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

@section('styles')
<style>
    @media print {
        .card-header .d-flex {
            display: none !important;
        }
        .sidebar, .navbar, .btn {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection