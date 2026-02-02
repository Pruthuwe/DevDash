@extends('layouts.main')

@section('content')
{{-- Page Header --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <h6 class="fw-semibold mb-0">Purchase Details</h6>
    <ul class="d-flex align-items-center gap-2">
        <li class="fw-medium">
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                Dashboard
            </a>
        </li>
        <li>-</li>
        <li class="fw-medium">
            <a href="{{ route('purchases.index') }}">Manage Purchases</a>
        </li>
        <li>-</li>
        <li class="fw-medium">{{ $purchase->reference_number }}</li>
    </ul>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Purchase #{{ $purchase->reference_number }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn btn-warning btn-sm d-flex align-items-center gap-1">
                        <iconify-icon icon="ic:outline-edit"></iconify-icon>
                        Edit
                    </a>
                    <button onclick="window.print()" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                        <iconify-icon icon="ic:outline-print"></iconify-icon>
                        Print
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="mb-3">Purchase Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-semibold">Reference Number:</td>
                                <td>{{ $purchase->reference_number }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Purchase Date:</td>
                                <td>{{ $purchase->purchase_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Status:</td>
                                <td>
                                    <span class="badge
                                        @if($purchase->status == 'pending') bg-warning
                                        @elseif($purchase->status == 'completed') bg-success
                                        @elseif($purchase->status == 'cancelled') bg-danger
                                        @else bg-secondary
                                        @endif">
                                        {{ ucfirst($purchase->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Total Amount:</td>
                                <td>${{ number_format($purchase->total_amount, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3">Supplier Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-semibold">Name:</td>
                                <td>{{ $purchase->supplier->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Email:</td>
                                <td>{{ $purchase->supplier->email ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Phone:</td>
                                <td>{{ $purchase->supplier->mobile ?? $purchase->supplier->phone ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Address:</td>
                                <td>{{ $purchase->supplier->address ?: 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <h6 class="mb-3">Purchase Items</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->purchaseItems as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->product->sku ?: 'N/A' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                    <td>${{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total Amount:</td>
                                <td class="fw-bold">${{ number_format($purchase->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($purchase->notes)
                <div class="row">
                    <div class="col-12">
                        <h6 class="mb-2">Notes</h6>
                        <p class="text-secondary-light">{{ $purchase->notes }}</p>
                    </div>
                </div>
                @endif
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