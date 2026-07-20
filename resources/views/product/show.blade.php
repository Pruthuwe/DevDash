@extends('layouts.main')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-24">
        <div>
            <h6 class="fw-semibold mb-2">Product Details</h6>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('manage.products') }}">Products</a></li>
                    <li class="breadcrumb-item active">{{ $product->name }}</li>
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

    <div class="row g-3">

        {{-- ── LEFT: main details ─────────────────────────────────── --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Product Information</h6>
                </div>
                <div class="card-body">

                    {{-- Section 1: Basic Info --}}
                    <h6 class="fw-semibold text-secondary-light text-xs text-uppercase letter-spacing-1 mb-3">Basic Information</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-1">Product Name</label>
                            <div>{{ $product->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-1">Fuel Type</label>
                            <div>{{ $product->category?->name ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-1">Brand</label>
                            <div>{{ $product->subcategory?->name ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-1">Status</label>
                            <div>
                                @if($product->status === 'active')
                                    <span class="badge bg-success-light text-success">Active</span>
                                @elseif($product->status === 'draft')
                                    <span class="badge bg-warning-light text-warning">Draft</span>
                                @else
                                    <span class="badge bg-danger-light text-danger">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr class="mb-4">

                    {{-- Section 2: Pricing & Stock --}}
                    <h6 class="fw-semibold text-secondary-light text-xs text-uppercase letter-spacing-1 mb-3">Pricing & Stock</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-1">Regular Price</label>
                            <div class="fw-bold text-primary fs-6">Rs {{ number_format($product->price, 2) }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-1">Sale Price</label>
                            <div>{{ $product->sale_price ? 'Rs '.number_format($product->sale_price, 2) : '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-1">Purchase Cost</label>
                            <div class="text-secondary-light">{{ $product->cost_price ? 'Rs '.number_format($product->cost_price, 2) : '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-1">Stock Quantity</label>
                            <div>
                                @if(($product->quantity ?? 0) > 0)
                                    <span class="badge bg-success-light text-success">{{ $product->quantity }} available</span>
                                @else
                                    <span class="badge bg-danger-light text-danger">Out of stock</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-1">Low Stock Alert</label>
                            <div>{{ $product->low_stock_alert ?? '—' }}</div>
                        </div>

                        {{--
                        ───────────────────────────────────────────────────────────
                        COMMENTED OUT: Loan Amount, RMV Fee, Service Charge, Interest Rate
                        Reason: These are not part of the Add Product / Edit Product bike
                        form fields, so they shouldn't surface on the Show page either.
                        Un-comment only if these fields are re-introduced to the
                        Add/Edit forms.
                        ───────────────────────────────────────────────────────────

                        @if($product->loan_amount)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-1">Loan Amount</label>
                            <div>Rs {{ number_format($product->loan_amount, 2) }}</div>
                        </div>
                        @endif

                        @if($product->rmv)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-1">RMV Fee</label>
                            <div>Rs {{ number_format($product->rmv, 2) }}</div>
                        </div>
                        @endif

                        @if($product->service_charge)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-1">Service Charge</label>
                            <div>Rs {{ number_format($product->service_charge, 2) }}</div>
                        </div>
                        @endif

                        @if($product->interest_rate)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-1">Interest Rate</label>
                            <div>{{ $product->interest_rate }}%</div>
                        </div>
                        @endif
                        --}}
                    </div>

                    <hr class="mb-4">

                    {{-- Section 3: Description & Tags --}}
                    <h6 class="fw-semibold text-secondary-light text-xs text-uppercase letter-spacing-1 mb-3">Description & Tags</h6>
                    <div class="row g-3">
                        @if($product->short_description)
                        <div class="col-12">
                            <label class="form-label fw-semibold text-sm mb-1">Short Description</label>
                            <p class="mb-0">{{ $product->short_description }}</p>
                        </div>
                        @endif

                        @if($product->tags)
                        <div class="col-12">
                            <label class="form-label fw-semibold text-sm mb-1">Tags</label>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                @foreach(array_map('trim', explode(',', $product->tags)) as $tag)
                                    @if($tag)
                                        <span class="badge bg-neutral-100 text-neutral-600 fw-medium">{{ $tag }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($product->engine_spec)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-1">Engine Specification</label>
                            <div>{{ $product->engine_spec }}</div>
                        </div>
                        @endif

                        @if($product->highlights)
                        <div class="col-12">
                            <label class="form-label fw-semibold text-sm mb-1">Highlights</label>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                @foreach((is_array($product->highlights) ? $product->highlights : array_map('trim', explode(',', $product->highlights))) as $h)
                                    @if($h)
                                        <span class="badge bg-info-100 text-info-600 fw-medium">{{ $h }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($product->rating)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-1">Rating</label>
                            <div class="d-flex align-items-center gap-1">
                                <iconify-icon icon="solar:star-bold" class="text-warning-600"></iconify-icon>
                                <span>{{ $product->rating }} / 5</span>
                            </div>
                        </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        {{-- ── RIGHT: image + quick stats ────────────────────────── --}}
        <div class="col-lg-4">
            <div class="row g-3">

                {{-- Image --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-header"><h6 class="mb-0">Product Image</h6></div>
                        <div class="card-body text-center">
                            @if($product->main_image)
                                <img src="{{ asset($product->main_image) }}"
                                     class="img-fluid rounded" style="max-height:220px; object-fit:contain;">
                            @else
                                <div class="bg-neutral-100 rounded d-flex align-items-center justify-content-center" style="height:180px;">
                                    <iconify-icon icon="solar:box-outline" class="text-secondary-light" style="font-size:3rem;"></iconify-icon>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Quick Info --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-header"><h6 class="mb-0">Quick Info</h6></div>
                        <div class="card-body">
                            <dl class="row g-2 mb-0" style="font-size:0.875rem;">
                                <dt class="col-6 text-secondary-light fw-medium">Price</dt>
                                <dd class="col-6 fw-bold text-primary mb-0">Rs {{ number_format($product->price, 2) }}</dd>

                                @if($product->sale_price)
                                <dt class="col-6 text-secondary-light fw-medium">Sale</dt>
                                <dd class="col-6 text-success fw-semibold mb-0">Rs {{ number_format($product->sale_price, 2) }}</dd>
                                @endif

                                <dt class="col-6 text-secondary-light fw-medium">Stock</dt>
                                <dd class="col-6 mb-0">
                                    @if(($product->quantity ?? 0) > 0)
                                        <span class="badge bg-success-light text-success">{{ $product->quantity }} available</span>
                                    @else
                                        <span class="badge bg-danger-light text-danger">Out of stock</span>
                                    @endif
                                </dd>

                                <dt class="col-6 text-secondary-light fw-medium">Status</dt>
                                <dd class="col-6 mb-0">
                                    @if($product->status === 'active')
                                        <span class="badge bg-success-light text-success">Active</span>
                                    @elseif($product->status === 'draft')
                                        <span class="badge bg-warning-light text-warning">Draft</span>
                                    @else
                                        <span class="badge bg-danger-light text-danger">Inactive</span>
                                    @endif
                                </dd>

                                <dt class="col-6 text-secondary-light fw-medium">Created</dt>
                                <dd class="col-6 mb-0">{{ $product->created_at->format('M d, Y') }}</dd>

                                <dt class="col-6 text-secondary-light fw-medium">Updated</dt>
                                <dd class="col-6 mb-0">{{ $product->updated_at->format('M d, Y') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection