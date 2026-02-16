@extends('layouts.main')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <div>
            <h5 class="fw-semibold mb-1">Customer Details</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('manage.customers') }}">Customers</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $customer->name }}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2">
                <iconify-icon icon="solar:pen-outline"></iconify-icon> Edit
            </a>
            <a href="{{ route('manage.customers') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
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
                    <h6 class="mb-0">Customer Information</h6>
                </div>

                <div class="card-body">
                    <div class="row g-3">

                        {{-- Basic Info --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Customer Name</label>
                            <div>{{ $customer->name }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <div>{{ $customer->email ?: 'Not specified' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <div>{{ $customer->phone ?: 'Not specified' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mobile</label>
                            <div>{{ $customer->mobile ?: 'Not specified' }}</div>
                        </div>

                        {{-- Address Info --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Address</label>
                            <div>{{ $customer->address ?: 'Not specified' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">City</label>
                            <div>{{ $customer->city ?: 'Not specified' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">State</label>
                            <div>{{ $customer->state ?: 'Not specified' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Country</label>
                            <div>{{ $customer->country ?: 'Not specified' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Zip Code</label>
                            <div>{{ $customer->zip_code ?: 'Not specified' }}</div>
                        </div>

                        {{-- Business Info --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">VAT Number</label>
                            <div>{{ $customer->vat_no ?: 'Not specified' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fax</label>
                            <div>{{ $customer->fax ?: 'Not specified' }}</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="col-lg-4 col-md-12">
            <div class="row g-3">

                <!-- Contact Information -->
                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">Contact Information</h6>
                        </div>
                        <div class="card-body">
                            @if($customer->email)
                                <div class="mb-2">
                                    <label class="fw-semibold">Email</label>
                                    <div>
                                        <a href="mailto:{{ $customer->email }}" class="text-decoration-none">
                                            {{ $customer->email }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if($customer->phone)
                                <div class="mb-2">
                                    <label class="fw-semibold">Phone</label>
                                    <div>
                                        <a href="tel:{{ $customer->phone }}" class="text-decoration-none">
                                            {{ $customer->phone }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if($customer->mobile)
                                <div class="mb-2">
                                    <label class="fw-semibold">Mobile</label>
                                    <div>
                                        <a href="tel:{{ $customer->mobile }}" class="text-decoration-none">
                                            {{ $customer->mobile }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if($customer->address)
                                <div class="mb-2">
                                    <label class="fw-semibold">Full Address</label>
                                    <div>
                                        {{ $customer->address }}
                                        @if($customer->city), {{ $customer->city }}@endif
                                        @if($customer->state), {{ $customer->state }}@endif
                                        @if($customer->country), {{ $customer->country }}@endif
                                        @if($customer->zip_code) - {{ $customer->zip_code }}@endif
                                    </div>
                                </div>
                            @endif
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
                            <div><strong>Customer ID:</strong> #{{ $customer->id }}</div>
                            <div><strong>Created:</strong> {{ $customer->created_at->format('M d, Y') }}</div>
                            <div><strong>Updated:</strong> {{ $customer->updated_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection