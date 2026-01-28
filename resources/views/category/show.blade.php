@extends('layouts.main')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <div>
            <h5 class="fw-semibold mb-1">Category Details</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('categories.index') }}">Categories</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $category->name }}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2">
                <iconify-icon icon="solar:pen-outline"></iconify-icon> Edit
            </a>
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
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
                    <h6 class="mb-0">Category Information</h6>
                </div>

                <div class="card-body">
                    <div class="row g-3">

                        {{-- Basic Info --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category Name</label>
                            <p class="mb-0">{{ $category->name }}</p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <p class="mb-0">
                                @if($category->status === 'active')
                                    <span class="badge bg-success-light text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-light text-danger">Inactive</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <p class="mb-0">{{ $category->description ?? 'No description provided.' }}</p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Created At</label>
                            <p class="mb-0">{{ $category->created_at->format('M d, Y H:i') }}</p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Updated At</label>
                            <p class="mb-0">{{ $category->updated_at->format('M d, Y H:i') }}</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Category Images</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if($category->banner_image)
                        <div class="col-12">
                            <label class="form-label fw-semibold">Banner Image</label>
                            <div class="border rounded p-2">
                                <img src="{{ asset($category->banner_image) }}" alt="Banner" class="img-fluid rounded">
                            </div>
                        </div>
                        @endif

                        @if($category->thumbnail_image)
                        <div class="col-12">
                            <label class="form-label fw-semibold">Thumbnail Image</label>
                            <div class="border rounded p-2">
                                <img src="{{ asset($category->thumbnail_image) }}" alt="Thumbnail" class="img-fluid rounded" style="max-height: 200px;">
                            </div>
                        </div>
                        @endif

                        @if($category->icon_image)
                        <div class="col-12">
                            <label class="form-label fw-semibold">Icon Image</label>
                            <div class="border rounded p-2">
                                <img src="{{ asset($category->icon_image) }}" alt="Icon" class="img-fluid rounded" style="max-height: 100px;">
                            </div>
                        </div>
                        @endif

                        @if(!$category->banner_image && !$category->thumbnail_image && !$category->icon_image)
                        <div class="col-12 text-center py-4">
                            <iconify-icon icon="solar:gallery-outline" style="font-size: 3rem;" class="text-muted mb-2"></iconify-icon>
                            <p class="text-muted mb-0">No images uploaded</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Products Count -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $category->products->count() }}</h4>
                            <small class="text-muted">Products</small>
                        </div>
                        <iconify-icon icon="solar:box-outline" style="font-size: 2rem;" class="text-primary"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection