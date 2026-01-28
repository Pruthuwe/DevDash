@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Edit Supplier</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('supplier.list') }}">Suppliers</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Supplier</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('supplier.list') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
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
                <h6 class="mb-0 text-lg">Edit Supplier</h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning-light text-warning">Editing</span>
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
                <form id="supplierForm" action="{{ route('suppliers.update', $supplier) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

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
                                            <input type="text" class="form-control" name="name" value="{{ old('name', $supplier->name) }}" placeholder="Enter supplier name" required>
                                        </div>

                                        <!-- Phone -->
                                        <div class="col-md-6">
                                            <label class="form-label">Phone</label>
                                            <input type="text" class="form-control" name="phone" value="{{ old('phone', $supplier->phone) }}" placeholder="Enter phone number">
                                        </div>

                                        <!-- Mobile No -->
                                        <div class="col-md-6">
                                            <label class="form-label">Mobile No</label>
                                            <input type="text" class="form-control" name="mobile" value="{{ old('mobile', $supplier->mobile) }}" placeholder="Enter mobile number">
                                        </div>

                                        <!-- Email Address -->
                                        <div class="col-md-6">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" class="form-control" name="email" value="{{ old('email', $supplier->email) }}" placeholder="Enter email address">
                                        </div>

                                        <!-- VAT NO -->
                                        <div class="col-md-6">
                                            <label class="form-label">VAT NO</label>
                                            <input type="text" class="form-control" name="vat_no" value="{{ old('vat_no', $supplier->vat_no) }}" placeholder="Enter VAT number">
                                        </div>

                                        <!-- Fax -->
                                        <div class="col-md-6">
                                            <label class="form-label">Fax</label>
                                            <input type="text" class="form-control" name="fax" value="{{ old('fax', $supplier->fax) }}" placeholder="Enter fax number">
                                        </div>

                                        <!-- Address -->
                                        <div class="col-md-12">
                                            <label class="form-label">Address</label>
                                            <textarea class="form-control" name="address" rows="3" placeholder="Enter address">{{ old('address', $supplier->address) }}</textarea>
                                        </div>

                                        <!-- Country -->
                                        <div class="col-md-6">
                                            <label class="form-label">Country</label>
                                            <input type="text" class="form-control" name="country" value="{{ old('country', $supplier->country) }}" placeholder="Enter country">
                                        </div>

                                        <!-- State -->
                                        <div class="col-md-6">
                                            <label class="form-label">State</label>
                                            <input type="text" class="form-control" name="state" value="{{ old('state', $supplier->state) }}" placeholder="Enter state">
                                        </div>

                                        <!-- City -->
                                        <div class="col-md-6">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control" name="city" value="{{ old('city', $supplier->city) }}" placeholder="Enter city">
                                        </div>

                                        <!-- Zip Code -->
                                        <div class="col-md-6">
                                            <label class="form-label">Zip Code</label>
                                            <input type="text" class="form-control" name="zip_code" value="{{ old('zip_code', $supplier->zip_code) }}" placeholder="Enter zip code">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('supplier.list') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <iconify-icon icon="solar:save-outline"></iconify-icon>
                            Update Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
