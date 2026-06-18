@extends('layouts.main')

@section('content')

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

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Product Management</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manage Products</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-products')))
        <a href="{{ route('add.product') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
            Add Product
        </a>
        @endif
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-24">
    <div class="col-md-4 col-sm-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-secondary-light mb-1">Total Products</h6>
                    <h4 class="mb-0">{{ $totalProducts }}</h4>
                </div>
                <div class="stat-icon bg-primary-light">
                    <iconify-icon icon="solar:box-outline" class="text-primary"></iconify-icon>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <small class="text-secondary-light">All active products</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 col-sm-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-secondary-light mb-1">Low Stock</h6>
                    <h4 class="mb-0">{{ count($lowStock) }}</h4>
                </div>
                <div class="stat-icon bg-warning-light">
                    <iconify-icon icon="solar:info-circle-outline" class="text-warning"></iconify-icon>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <small class="text-secondary-light">Needs restocking</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-secondary-light mb-1">Out of Stock</h6>
                    <h4 class="mb-0">{{ count($outOfStock) }}</h4>
                </div>
                <div class="stat-icon bg-danger-light">
                    <iconify-icon icon="solar:danger-triangle-outline" class="text-danger"></iconify-icon>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <small class="text-secondary-light">Require attention</small>
            </div>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">All Products</h6>
        <div class="d-flex gap-2">
            <input type="text" class="form-control" placeholder="Search products..." style="width: 250px;" id="searchInput">
            <select class="form-select" style="width: 150px;" id="statusFilter">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="draft">Draft</option>
            </select>
            <button class="btn btn-outline-secondary" id="filterBtn">
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
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-products')))
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                    </a>
                                    @endif
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'edit-products')))
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <iconify-icon icon="solar:pen-outline"></iconify-icon>
                                    </a>
                                    @endif
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'delete-products')))
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <iconify-icon icon="ic:outline-delete"></iconify-icon>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <iconify-icon icon="solar:box-outline" style="font-size: 4rem;" class="text-secondary-light mb-3"></iconify-icon>
                                <h5 class="text-secondary-light">No Products Found</h5>
                                <p class="text-secondary-light mb-4">Start by adding your first product</p>
                                @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-products')))
                                <a href="{{ route('add.product') }}" class="btn btn-primary">
                                    <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                                    Add Your First Product
                                </a>
                                @endif
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

<!-- Product View Modal -->
<div class="modal fade" id="productViewModal" tabindex="-1" aria-labelledby="productViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productViewModalLabel">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <!-- Product Image -->
                    <div class="col-md-4">
                        <div class="text-center">
                            <img id="modalProductImage" src="" alt="Product Image" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
                        </div>
                    </div>
                    
                    <!-- Product Details -->
                    <div class="col-md-8">
                        <div class="row g-3">
                            <div class="col-12">
                                <h4 id="modalProductName" class="mb-1"></h4>
                                <p class="text-secondary-light mb-0" id="modalProductBrand"></p>
                            </div>
                            
                            <div class="col-md-6">
                                <strong>SKU:</strong> <span id="modalProductSKU"></span>
                            </div>
                            
                            <div class="col-md-6">
                                <strong>Category:</strong> <span id="modalProductCategory"></span>
                            </div>
                            
                            <div class="col-md-6">
                                <strong>Price:</strong> <span id="modalProductPrice" class="text-success fw-bold"></span>
                            </div>
                            
                            <div class="col-md-6">
                                <strong>Stock:</strong> <span id="modalProductStock"></span>
                            </div>
                            
                            <div class="col-12">
                                <strong>Status:</strong> <span id="modalProductStatus"></span>
                            </div>
                            
                            <div class="col-12">
                                <strong>Description:</strong>
                                <p id="modalProductDescription" class="mt-2"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="editProductBtn" class="btn btn-primary">Edit Product</a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function loadProductDetailsFromData(button) {
    const productData = JSON.parse(button.getAttribute('data-product'));
    
    // Set modal title
    document.getElementById('productViewModalLabel').textContent = productData.name;
    
    // Set product details
    document.getElementById('modalProductName').textContent = productData.name;
    document.getElementById('modalProductSKU').textContent = productData.sku;
    document.getElementById('modalProductBrand').textContent = productData.brand || 'N/A';
    document.getElementById('modalProductCategory').textContent = productData.category ? 'Category ' + productData.category : 'N/A';
    
    // Set price (show sale price if available)
    const displayPrice = (productData.sale_price && productData.sale_price !== null && productData.sale_price !== '' && parseFloat(productData.sale_price) > 0) ? 
        '<del class="text-secondary-light">$' + parseFloat(productData.price).toFixed(2) + '</del> $' + parseFloat(productData.sale_price).toFixed(2) : 
        '$' + parseFloat(productData.price).toFixed(2);
    document.getElementById('modalProductPrice').innerHTML = displayPrice;
    
    // Set stock
    document.getElementById('modalProductStock').textContent = productData.quantity + ' in stock';
    
    // Set status with badge
    let statusBadge = '';
    if (productData.status === 'active') {
        statusBadge = '<span class="badge bg-success">Active</span>';
    } else if (productData.status === 'inactive') {
        statusBadge = '<span class="badge bg-danger">Inactive</span>';
    } else if (productData.status === 'draft') {
        statusBadge = '<span class="badge bg-warning">Draft</span>';
    }
    document.getElementById('modalProductStatus').innerHTML = statusBadge;
    
    // Set description
    const description = (productData.full_description && productData.full_description !== null && productData.full_description !== '') ? 
        productData.full_description : (productData.short_description || 'No description available');
    document.getElementById('modalProductDescription').textContent = description;
    
    // Set image
    const imageElement = document.getElementById('modalProductImage');
    if (productData.image && productData.image !== null && productData.image !== '') {
        imageElement.src = '{{ url("/") }}/' + productData.image;
        imageElement.style.display = 'block';
    } else {
        imageElement.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xMDAgMTAwSDEwMFYxMDBaTTAgMEgyMDBWMjAwSDBWMFoiIGZpbGw9IiNGOUY5RjkiLz4KPHBhdGggZD0iTTEwMCAxMzAuNUM5NS44IDEzMC41IDkyIDEyNi44IDkyIDEyM1Y3N0MxMDAgMTMwLjVDMTA0LjIgMTMwLjUgMTA4IDEyNi44IDEwOCAxMjNWNzdDMTEwMCA5NUMxMDQuMiA5NSAxMDggOTkuMiAxMDggMTA0VjEwNEMxMDggMTA4LjggMTA0LjIgMTEzIDEwMCAxMTNNOTIgMTA0QzkyIDEwOC44IDk1LjggMTEzIDEwMCAxMTNNMTEwIDEwNEMxMTAgMTA4LjggMTA2LjggMTEzIDEwMiAxMTMiIGZpbGw9IiM5Q0E0QUYiLz4KPC9zdmc+Cg==';
        imageElement.style.display = 'block';
        imageElement.alt = 'No image available';
    }
    
    // Set edit button link
    document.getElementById('editProductBtn').href = '{{ route("products.edit", ":id") }}'.replace(':id', productData.id);
}

// Search and filter functionality
$(document).ready(function() {
    $('#searchInput').on('keyup', function() {
        filterProducts();
    });
    
    $('#statusFilter').on('change', function() {
        filterProducts();
    });
    
    $('#filterBtn').on('click', function() {
        filterProducts();
    });
});

function filterProducts() {
    const searchTerm = $('#searchInput').val().toLowerCase();
    const statusFilter = $('#statusFilter').val();
    
    $('tbody tr').each(function() {
        const row = $(this);
        const productName = row.find('td:first h6').text().toLowerCase();
        const productSKU = row.find('td:nth-child(2)').text().toLowerCase();
        const productStatus = row.find('td:nth-child(7) .badge').text().toLowerCase();
        
        const matchesSearch = productName.includes(searchTerm) || productSKU.includes(searchTerm);
        const matchesStatus = !statusFilter || productStatus.includes(statusFilter.toLowerCase());
        
        if (matchesSearch && matchesStatus) {
            row.show();
        } else {
            row.hide();
        }
    });
}
</script>
@endpush

@push('styles')
<style>
.stat-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: none;
    border-radius: 12px;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.bg-primary-light {
    background-color: var(--primary-light) !important;
}

.bg-success-light {
    background-color: var(--success-surface) !important;
}

.bg-warning-light {
    background-color: var(--warning-surface) !important;
}

.bg-danger-light {
    background-color: var(--danger-surface) !important;
}
</style>
@endpush