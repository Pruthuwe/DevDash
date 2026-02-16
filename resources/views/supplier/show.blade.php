@extends('layouts.main')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <div>
            <h5 class="fw-semibold mb-1">Supplier Details</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('manage.suppliers') }}">Suppliers</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $supplier->name }}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2">
                <iconify-icon icon="solar:pen-outline"></iconify-icon> Edit
            </a>
            <a href="{{ route('manage.suppliers') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
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
                    <h6 class="mb-0">Supplier Information</h6>
                </div>

                <div class="card-body">
                    <div class="row g-3">

                        {{-- Basic Info --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Supplier Name</label>
                            <div>{{ $supplier->name }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <div>{{ $supplier->email ?: 'Not specified' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <div>{{ $supplier->phone ?: 'Not specified' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mobile</label>
                            <div>{{ $supplier->mobile ?: 'Not specified' }}</div>
                        </div>

                        {{-- Address Info --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Address</label>
                            <div>{{ $supplier->address ?: 'Not specified' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">City</label>
                            <div>{{ $supplier->city ?: 'Not specified' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">State</label>
                            <div>{{ $supplier->state ?: 'Not specified' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Country</label>
                            <div>{{ $supplier->country ?: 'Not specified' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Zip Code</label>
                            <div>{{ $supplier->zip_code ?: 'Not specified' }}</div>
                        </div>

                        {{-- Business Info --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">VAT Number</label>
                            <div>{{ $supplier->vat_no ?: 'Not specified' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fax</label>
                            <div>{{ $supplier->fax ?: 'Not specified' }}</div>
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
                            @if($supplier->email)
                                <div class="mb-2">
                                    <label class="fw-semibold">Email</label>
                                    <div>
                                        <a href="mailto:{{ $supplier->email }}" class="text-decoration-none">
                                            {{ $supplier->email }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if($supplier->phone)
                                <div class="mb-2">
                                    <label class="fw-semibold">Phone</label>
                                    <div>
                                        <a href="tel:{{ $supplier->phone }}" class="text-decoration-none">
                                            {{ $supplier->phone }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if($supplier->mobile)
                                <div class="mb-2">
                                    <label class="fw-semibold">Mobile</label>
                                    <div>
                                        <a href="tel:{{ $supplier->mobile }}" class="text-decoration-none">
                                            {{ $supplier->mobile }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if($supplier->address)
                                <div class="mb-2">
                                    <label class="fw-semibold">Full Address</label>
                                    <div>
                                        {{ $supplier->address }}
                                        @if($supplier->city), {{ $supplier->city }}@endif
                                        @if($supplier->state), {{ $supplier->state }}@endif
                                        @if($supplier->country), {{ $supplier->country }}@endif
                                        @if($supplier->zip_code) - {{ $supplier->zip_code }}@endif
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
                            <div><strong>Supplier ID:</strong> #{{ $supplier->id }}</div>
                            <div><strong>Created:</strong> {{ $supplier->created_at->format('M d, Y') }}</div>
                            <div><strong>Updated:</strong> {{ $supplier->updated_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection