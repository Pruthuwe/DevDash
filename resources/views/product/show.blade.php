@extends('layouts.main')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <div>
            <h5 class="fw-semibold mb-1">Product Details</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('manage.products') }}">Products</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $product->name }}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2">
                <iconify-icon icon="solar:pen-outline"></iconify-icon> Edit
            </a>
            <a href="{{ route('manage.products') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
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
                    <h6 class="mb-0">Product Information</h6>
                </div>

                <div class="card-body">
                    <div class="row g-3">

                        {{-- Basic Info --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Product Name</label>
                            <div>{{ $product->name }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SKU</label>
                            <div>{{ $product->sku }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Slug</label>
                            <div class="text-secondary-light">{{ $product->slug }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Brand</label>
                            <div>{{ $product->brand ?? 'Not specified' }}</div>
                        </div>

                        {{-- Category --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category ID</label>
                            <div>
                                @if($product->category_id)
                                    <span class="badge bg-primary-light text-primary">
                                        {{ $product->category_id }}
                                    </span>
                                @else
                                    <span class="text-secondary-light">Not assigned</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Subcategory ID</label>
                            <div>
                                {{ $product->subcategory_id ?? 'Not assigned' }}
                            </div>
                        </div>

                        {{-- Units --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Unit</label>
                            <div>{{ $product->unit }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Barcode</label>
                            <div>{{ $product->barcode ?? 'Not specified' }}</div>
                        </div>

                        {{-- Pricing --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Price</label>
                            <div class="fw-bold text-primary">
                                ${{ number_format($product->price, 2) }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sale Price</label>
                            <div>
                                {{ $product->sale_price ? '$'.number_format($product->sale_price,2) : '—' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cost Price</label>
                            <div>
                                {{ $product->cost_price ? '$'.number_format($product->cost_price,2) : '—' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Quantity</label>
                            <div>
                                {{ $product->quantity ?? '—' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Low Stock Alert</label>
                            <div>
                                {{ $product->low_stock_alert ?? '—' }}
                            </div>
                        </div>

                        {{-- Tax --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tax</label>
                            <div>
                                {{ $product->tax ? $product->tax.'%' : 'No tax' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tax Type</label>
                            <div>
                                {{ ucfirst($product->tax_type ?? 'exclusive') }}
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <div>
                                @if($product->status === 'active')
                                    <span class="badge bg-success-light text-success">Active</span>
                                @elseif($product->status === 'draft')
                                    <span class="badge bg-warning-light text-warning">Draft</span>
                                @elseif($product->status === 'inactive')
                                    <span class="badge bg-danger-light text-danger">Inactive</span>
                                @else
                                    <span class="badge bg-secondary-light text-secondary">Unknown</span>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Featured --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Featured</label>
                            <div>
                                @if($product->is_featured)
                                    <span class="badge bg-info-light text-info">Yes</span>
                                @else
                                    <span class="text-secondary-light">No</span>
                                @endif
                            </div>
                        </div>

                        {{-- Views --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Views</label>
                            <div>{{ $product->views ?? 0 }}</div>
                        </div>

                    </div>

                    <hr>

                    {{-- Descriptions --}}
                    @if($product->short_description)
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Short Description</label>
                            <p class="mb-0">{{ $product->short_description }}</p>
                        </div>
                    @endif

                    @if($product->full_description)
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Full Description</label>
                            <div>{!! $product->full_description !!}</div>
                        </div>
                    @endif

                    {{-- Tags & Notes --}}
                    @if($product->tags)
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Tags</label>
                            <p class="mb-0">{{ $product->tags }}</p>
                        </div>
                    @endif

                    @if($product->notes)
                        <div>
                            <label class="form-label fw-semibold">Notes</label>
                            <p class="mb-0">{{ $product->notes }}</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="col-lg-4 col-md-12">
            <div class="row g-3">

                <!-- Image -->
                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">Product Image</h6>
                        </div>
                        <div class="card-body text-center">
                            @if($product->main_image)
                                <img src="{{ asset($product->main_image) }}"
                                     class="img-fluid rounded"
                                     style="max-height:180px;">
                            @else
                                <div class="bg-neutral-200 rounded d-flex align-items-center justify-content-center"
                                     style="height:180px;">
                                    <iconify-icon icon="solar:box-outline"
                                                  class="text-secondary-light"
                                                  style="font-size:3rem;"></iconify-icon>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">Pricing & Inventory</h6>
                        </div>
                        <div class="card-body">

                            <div class="mb-2">
                                <label class="fw-semibold">Price</label>
                                <div class="fs-5 fw-bold text-primary">
                                    ${{ number_format($product->price, 2) }}
                                </div>
                            </div>

                            @if($product->sale_price)
                                <div class="mb-2 text-success fw-bold">
                                    Sale: ${{ number_format($product->sale_price, 2) }}
                                </div>
                            @endif

                            <hr>

                            <div class="mb-2">
                                <label class="fw-semibold">Stock</label>
                                <div>
                                    @if($product->quantity > 0)
                                        <span class="badge bg-success-light text-success">
                                            {{ $product->quantity }} available
                                        </span>
                                    @else
                                        <span class="badge bg-danger-light text-danger">
                                            Out of stock
                                        </span>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">Statistics</h6>
                        </div>
                        <div class="card-body">
                            <div><strong>Views:</strong> {{ $product->views ?? 0 }}</div>
                            <div><strong>Created:</strong> {{ $product->created_at->format('M d, Y') }}</div>
                            <div><strong>Updated:</strong> {{ $product->updated_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
