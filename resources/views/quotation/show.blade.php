@extends('layouts.main')

@section('content')
{{-- Page Header --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <h6 class="fw-semibold mb-0">Quotation Details</h6>
    <ul class="d-flex align-items-center gap-2">
        <li class="fw-medium">
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                Dashboard
            </a>
        </li>
        <li>-</li>
        <li class="fw-medium">
            <a href="{{ route('manage.quotations') }}">Manage Quotations</a>
        </li>
        <li>-</li>
        <li class="fw-medium">{{ $quotation->quotation_number }}</li>
    </ul>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Quotation #{{ $quotation->quotation_number }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('quotations.edit', $quotation->id) }}" class="btn btn-warning btn-sm">
                        <iconify-icon icon="ic:outline-edit"></iconify-icon>
                        Edit
                    </a>
                    <button onclick="window.print()" class="btn btn-primary btn-sm">
                        <iconify-icon icon="ic:outline-print"></iconify-icon>
                        Print
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="mb-3">Quotation Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-semibold">Quotation Number:</td>
                                <td>{{ $quotation->quotation_number }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Date:</td>
                                <td>{{ $quotation->quotation_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Valid Until:</td>
                                <td>{{ $quotation->valid_until->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Status:</td>
                                <td>
                                    <span class="badge
                                        @if($quotation->status == 'draft') bg-secondary
                                        @elseif($quotation->status == 'sent') bg-primary
                                        @elseif($quotation->status == 'accepted') bg-success
                                        @elseif($quotation->status == 'rejected') bg-danger
                                        @else bg-warning
                                        @endif">
                                        {{ ucfirst($quotation->status) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3">Customer Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-semibold">Name:</td>
                                <td>{{ $quotation->customer_name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Email:</td>
                                <td>{{ $quotation->customer_email ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Phone:</td>
                                <td>{{ $quotation->customer_phone }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Address:</td>
                                <td>{{ $quotation->customer_address ?: 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <h6 class="mb-3">Quotation Items</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotation->quotationItems as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                    <td>${{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        @if($quotation->notes)
                            <h6 class="mb-2">Notes</h6>
                            <p class="text-secondary-light">{{ $quotation->notes }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="text-end">
                            <table class="table table-borderless w-auto ms-auto">
                                <tr>
                                    <td class="fw-semibold">Subtotal:</td>
                                    <td class="text-end">${{ number_format($quotation->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Tax (10%):</td>
                                    <td class="text-end">${{ number_format($quotation->tax, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Discount:</td>
                                    <td class="text-end">${{ number_format($quotation->discount, 2) }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold">Total:</td>
                                    <td class="text-end fw-bold">${{ number_format($quotation->total, 2) }}</td>
                                </tr>
                            </table>
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