@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Products</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Products</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('add.product') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
            Add Product
        </a>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">All Products</h6>
        <div class="d-flex gap-2">
            <input type="text" class="form-control" placeholder="Search products..." style="width: 250px;">
            <button class="btn btn-outline-secondary">
                <iconify-icon icon="solar:filter-outline"></iconify-icon>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <h6 class="mb-0">{{ $product->name }}</h6>
                                @if($product->brand)
                                <small class="text-secondary-light">{{ $product->brand }}</small>
                                @endif
                            </td>
                            <td>{{ $product->sku }}</td>
                            <td>
                                @if($product->main_image)
                                    <img src="{{ asset($product->main_image) }}" alt="{{ $product->name }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-neutral-200 rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <iconify-icon icon="solar:box-outline" class="text-secondary-light"></iconify-icon>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($product->category)
                                <span class="badge bg-primary-light text-primary">{{ $product->category->name }}</span>
                                @else
                                <span class="text-secondary-light">—</span>
                                @endif
                            </td>
                            <td>
                                @if($product->subcategory)
                                <span class="badge bg-info-light text-info">{{ $product->subcategory->name }}</span>
                                @else
                                <span class="text-secondary-light">—</span>
                                @endif
                            </td>
                            <td>
                                @if($product->sale_price && $product->sale_price > 0)
                                    <div>
                                        <span class="text-decoration-line-through text-secondary-light small">${{ number_format($product->price, 2) }}</span>
                                        <strong class="text-success d-block">${{ number_format($product->sale_price, 2) }}</strong>
                                    </div>
                                @else
                                    <strong>${{ number_format($product->price, 2) }}</strong>
                                @endif
                            </td>
                            <td>
                                @if($product->quantity > 0)
                                    <span class="badge bg-success-light text-success">{{ $product->quantity }} in stock</span>
                                @else
                                    <span class="badge bg-danger-light text-danger">Out of stock</span>
                                @endif
                            </td>   
                            <td>
                                @if($product->status === 'active')
                                    <span class="badge bg-success-light text-success">Active</span>
                                @elseif($product->status === 'draft')
                                    <span class="badge bg-warning-light text-warning">Draft</span>
                                @else
                                    <span class="badge bg-danger-light text-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <iconify-icon icon="solar:box-outline" style="font-size: 4rem;" class="text-secondary-light mb-3"></iconify-icon>
                                <h5 class="text-secondary-light">No Products Found</h5>
                                <p class="text-secondary-light mb-4">Start by adding your first product</p>
                                <a href="{{ route('add.product') }}" class="btn btn-primary">
                                    <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                                    Add Your First Product
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($products->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>

@endsection