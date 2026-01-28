@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Add Supplier</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Supplier</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('manage.suppliers') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline"></iconify-icon>
            Back to Suppliers
        </a>
    </div>
</div>

<!-- Main Content -->
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-lg">Add Supplier</h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success-light text-success">Adding</span>
                </div>
            </div>

            <div class="card-body">
                <!-- Display Success/Error Messages -->
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ri-error-warning-line me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Form -->
                <form id="supplierForm" action="{{ route('suppliers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Supplier Information -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Supplier Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row gy-3">
                                        <!-- Supplier Name -->
                                        <div class="col-md-6">
                                            <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Enter supplier name" required>
                                        </div>

                                        <!-- Phone -->
                                        <div class="col-md-6">
                                            <label class="form-label">Phone</label>
                                            <input type="text" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="Enter phone number">
                                        </div>

                                        <!-- Mobile No -->
                                        <div class="col-md-6">
                                            <label class="form-label">Mobile No <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="mobile" value="{{ old('mobile') }}" placeholder="Enter mobile number" required>
                                        </div>

                                        <!-- Email Address -->
                                        <div class="col-md-6">
                                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Enter email address" required>
                                        </div>

                                        <!-- VAT NO -->
                                        <div class="col-md-6">
                                            <label class="form-label">VAT NO</label>
                                            <input type="text" class="form-control" name="vat_no" value="{{ old('vat_no') }}" placeholder="Enter VAT number">
                                        </div>

                                        <!-- Fax -->
                                        <div class="col-md-6">
                                            <label class="form-label">Fax</label>
                                            <input type="text" class="form-control" name="fax" value="{{ old('fax') }}" placeholder="Enter fax number">
                                        </div>

                                        <!-- Address -->
                                        <div class="col-md-12">
                                            <label class="form-label">Address <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="address" rows="3" placeholder="Enter address" required>{{ old('address') }}</textarea>
                                        </div>

                                        <!-- Country -->
                                        <div class="col-md-6">
                                            <label class="form-label">Country</label>
                                            <input type="text" class="form-control" name="country" value="{{ old('country') }}" placeholder="Enter country">
                                        </div>

                                        <!-- State -->
                                        <div class="col-md-6">
                                            <label class="form-label">State</label>
                                            <input type="text" class="form-control" name="state" value="{{ old('state') }}" placeholder="Enter state">
                                        </div>

                                        <!-- City -->
                                        <div class="col-md-6">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control" name="city" value="{{ old('city') }}" placeholder="Enter city">
                                        </div>

                                        <!-- Zip Code -->
                                        <div class="col-md-6">
                                            <label class="form-label">Zip Code</label>
                                            <input type="text" class="form-control" name="zip_code" value="{{ old('zip_code') }}" placeholder="Enter zip code">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('manage.suppliers') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:user-plus-outline"></iconify-icon>
                            Add Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
