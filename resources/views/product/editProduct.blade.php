@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Edit Product</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('manage.products') }}">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Product</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('manage.products') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline"></iconify-icon>
            Back to Products
        </a>
    </div>
</div>

<!-- Main Content -->
<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-lg">Edit Product</h6>
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
                <form id="productForm" action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Hidden field for removed gallery images -->
                    <input type="hidden" name="removed_gallery_images" id="removedGalleryImages" value=""> 

                    <!-- Basic Product Info -->
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row gy-3">
                                        <!-- Product Name -->
                                        <div class="col-md-6">
                                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" value="{{ old('name', $product->name) }}" placeholder="Enter product name" required>
                                        </div>

                                        <!-- SKU -->
                                        <div class="col-md-6">
                                            <label class="form-label">SKU <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="sku" value="{{ old('sku', $product->sku) }}" placeholder="Enter SKU" required>
                                        </div>

                                        <!-- Category -->
                                        <div class="col-md-6">
                                            <label class="form-label">Category</label>
                                            <select class="form-select" name="category_id" id="categorySelect">
                                                <option value="">Select Category</option>
                                                @foreach($categories ?? [] as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Subcategory -->
                                        <div class="col-md-6">
                                            <label class="form-label">Subcategory</label>
                                            <select class="form-select" name="subcategory_id" id="subcategorySelect">
                                                <option value="">Select Subcategory</option>
                                                @if($product->subcategory)
                                                    <option value="{{ $product->subcategory->id }}" selected>{{ $product->subcategory->name }}</option>
                                                @endif
                                            </select>
                                        </div>

                                        <!-- Brand -->
                                        <div class="col-md-6">
                                            <label class="form-label">Brand</label>
                                            <input type="text" class="form-control" name="brand" value="{{ old('brand', $product->brand) }}" placeholder="Enter brand name">
                                        </div>

                                        <!-- Unit -->
                                        <div class="col-md-6">
                                            <label class="form-label">Unit <span class="text-danger">*</span></label>
                                            <select class="form-select" name="unit" required>
                                                <option value="">Select Unit</option>
                                                <option value="piece" {{ old('unit', $product->unit) == 'piece' ? 'selected' : '' }}>Piece</option>
                                                <option value="kg" {{ old('unit', $product->unit) == 'kg' ? 'selected' : '' }}>Kilogram</option>
                                                <option value="gram" {{ old('unit', $product->unit) == 'gram' ? 'selected' : '' }}>Gram</option>
                                                <option value="liter" {{ old('unit', $product->unit) == 'liter' ? 'selected' : '' }}>Liter</option>
                                                <option value="ml" {{ old('unit', $product->unit) == 'ml' ? 'selected' : '' }}>Milliliter</option>
                                                <option value="meter" {{ old('unit', $product->unit) == 'meter' ? 'selected' : '' }}>Meter</option>
                                                <option value="cm" {{ old('unit', $product->unit) == 'cm' ? 'selected' : '' }}>Centimeter</option>
                                            </select>
                                        </div>

                                        <!-- Barcode -->
                                        <div class="col-md-6">
                                            <label class="form-label">Barcode</label>
                                            <input type="text" class="form-control" name="barcode" value="{{ old('barcode', $product->barcode) }}" placeholder="Enter barcode">
                                        </div>

                                        <!-- Status -->
                                        <div class="col-md-6">
                                            <label class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select" name="status" required>
                                                <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <!-- Product Image -->
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Product Image</h6>
                                    @if($product->main_image)
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="removeImageBtn">
                                            <iconify-icon icon="solar:trash-bin-outline"></iconify-icon>
                                            Remove
                                        </button>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <div class="text-center">
                                        @if($product->main_image)
                                            <img id="imagePreview" src="{{ asset($product->main_image) }}" alt="Product Image" class="img-fluid rounded mb-3" style="max-height: 200px;">
                                        @else
                                            <div id="imagePreview" class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                                                <iconify-icon icon="solar:box-outline" style="font-size: 3rem;" class="text-secondary-light"></iconify-icon>
                                            </div>
                                        @endif
                                        <input type="file" class="form-control" name="main_image" id="mainImageInput" accept="image/*" onchange="previewImage(this)">
                                        <small class="text-secondary-light">Upload product image (JPG, PNG, max 2MB)</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Gallery Images -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Gallery Images</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Current Gallery Images -->
                                    @if($product->gallery_images && count($product->gallery_images) > 0)
                                        <div class="mb-3">
                                            <h6 class="text-sm mb-3">Current Images:</h6>
                                            <div class="row g-2" id="currentGallery">
                                                @foreach($product->gallery_images as $index => $galleryImage)
                                                    <div class="col-4 position-relative">
                                                        <img src="{{ asset($galleryImage) }}" alt="Gallery Image" class="img-fluid rounded" style="width: 100%; height: 80px; object-fit: cover;">
                                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" data-image='{{ json_encode($galleryImage) }}' style="padding: 0.125rem 0.25rem;">
                                                            <iconify-icon icon="solar:trash-bin-outline" style="font-size: 0.75rem;"></iconify-icon>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Upload New Gallery Images -->
                                    <div class="text-center">
                                        <input type="file" class="form-control" name="gallery_images[]" id="galleryImagesInput" accept="image/*" multiple onchange="previewGalleryImages(this)">
                                        <small class="text-secondary-light">Upload multiple gallery images (JPG, PNG, max 2MB each)</small>
                                    </div>

                                    <!-- Gallery Preview -->
                                    <div id="galleryPreview" class="row g-2 mt-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Inventory -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Pricing & Inventory</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row gy-3">
                                        <!-- Price -->
                                        <div class="col-md-4">
                                            <label class="form-label">Price <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" placeholder="0.00" required>
                                            </div>
                                        </div>

                                        <!-- Sale Price -->
                                        <div class="col-md-4">
                                            <label class="form-label">Sale Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" step="0.01" min="0" placeholder="0.00">
                                            </div>
                                            <small class="text-secondary-light">Leave empty if no sale price</small>
                                        </div>

                                        <!-- Cost Price -->
                                        <div class="col-md-4">
                                            <label class="form-label">Cost Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" step="0.01" min="0" placeholder="0.00">
                                            </div>
                                        </div>

                                        <!-- Quantity -->
                                        <div class="col-md-6">
                                            <label class="form-label">Quantity</label>
                                            <input type="number" class="form-control" name="quantity" value="{{ old('quantity', $product->quantity) }}" min="0" placeholder="Enter quantity">
                                        </div>

                                        <!-- Low Stock Alert -->
                                        <div class="col-md-6">
                                            <label class="form-label">Low Stock Alert</label>
                                            <input type="number" class="form-control" name="low_stock_alert" value="{{ old('low_stock_alert', $product->low_stock_alert) }}" min="0" placeholder="Alert threshold">
                                        </div>

                                        <!-- Tax -->
                                        <div class="col-md-6">
                                            <label class="form-label">Tax (%)</label>
                                            <input type="number" class="form-control" name="tax" value="{{ old('tax', $product->tax) }}" step="0.01" min="0" max="100" placeholder="0.00">
                                        </div>

                                        <!-- Tax Type -->
                                        <div class="col-md-6">
                                            <label class="form-label">Tax Type</label>
                                            <select class="form-select" name="tax_type">
                                                <option value="">Select Tax Type</option>
                                                <option value="exclusive" {{ old('tax_type', $product->tax_type) == 'exclusive' ? 'selected' : '' }}>Exclusive</option>
                                                <option value="inclusive" {{ old('tax_type', $product->tax_type) == 'inclusive' ? 'selected' : '' }}>Inclusive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Description</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row gy-3">
                                        <!-- Short Description -->
                                        <div class="col-md-6">
                                            <label class="form-label">Short Description</label>
                                            <textarea class="form-control" name="short_description" rows="3" placeholder="Brief product description">{{ old('short_description', $product->short_description) }}</textarea>
                                        </div>

                                        <!-- Full Description -->
                                        <div class="col-md-6">
                                            <label class="form-label">Full Description</label>
                                            <textarea class="form-control" name="full_description" rows="3" placeholder="Detailed product description">{{ old('full_description', $product->full_description) }}</textarea>
                                        </div>

                                        <!-- Tags -->
                                        <div class="col-md-6">
                                            <label class="form-label">Tags</label>
                                            <input type="text" class="form-control" name="tags" value="{{ old('tags', $product->tags) }}" placeholder="Enter tags separated by commas">
                                        </div>

                                        <!-- Notes -->
                                        <div class="col-md-6">
                                            <label class="form-label">Notes</label>
                                            <textarea class="form-control" name="notes" rows="2" placeholder="Internal notes">{{ old('notes', $product->notes) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('manage.products') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <iconify-icon icon="solar:save-outline"></iconify-icon>
                            Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Category and Subcategory AJAX
$(document).ready(function() {
    const categorySelect = $('#categorySelect');
    const subcategorySelect = $('#subcategorySelect');

    categorySelect.on('change', function() {
        const categoryId = $(this).val();
        const currentSubcategoryId = subcategorySelect.val(); // Store current selection

        if (categoryId) {
            $.ajax({
                url: `/categories/${categoryId}/subcategories`,
                type: 'GET',
                success: function(data) {
                    subcategorySelect.empty().append('<option value="">Select Subcategory</option>');

                    if (data.subcategories && data.subcategories.length > 0) {
                        data.subcategories.forEach(function(subcategory) {
                            const isSelected = (subcategory.id == currentSubcategoryId) ? 'selected' : '';
                            subcategorySelect.append(`<option value="${subcategory.id}" ${isSelected}>${subcategory.name}</option>`);
                        });
                        subcategorySelect.prop('disabled', false);
                    } else {
                        subcategorySelect.prop('disabled', true);
                    }
                },
                error: function() {
                    subcategorySelect.empty().append('<option value="">Select Subcategory</option>');
                    subcategorySelect.prop('disabled', true);
                }
            });
        } else {
            subcategorySelect.empty().append('<option value="">Select Subcategory</option>');
            subcategorySelect.prop('disabled', true);
        }
    });

    // Load subcategories if category is already selected
    if (categorySelect.val()) {
        categorySelect.trigger('change');
    }

    // Handle remove main image button
    $('#removeImageBtn').on('click', function() {
        if (confirm('Are you sure you want to remove the current image?')) {
            $('#imagePreview').replaceWith('<div id="imagePreview" class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;"><iconify-icon icon="solar:box-outline" style="font-size: 3rem;" class="text-secondary-light"></iconify-icon></div>');
            // Add a hidden input to indicate image should be removed
            $('<input>').attr({
                type: 'hidden',
                name: 'remove_image',
                value: '1'
            }).appendTo('#productForm');
            $(this).hide();
        }
    });

    // Handle remove gallery image buttons
    $('#currentGallery').on('click', '.btn-danger', function() {
        const imagePath = $(this).data('image');
        removeGalleryImage(this, imagePath);
    });
});

// Function to remove gallery image
function removeGalleryImage(button, imagePath) {
    if (confirm('Are you sure you want to remove this gallery image?')) {
        // Remove the image element
        $(button).closest('.col-4').remove();

        // Add to removed images list
        let removedImages = $('#removedGalleryImages').val();
        if (removedImages) {
            removedImages += ',' + imagePath;
        } else {
            removedImages = imagePath;
        }
        $('#removedGalleryImages').val(removedImages);
    }
}

// Function to preview gallery images
function previewGalleryImages(input) {
    const previewContainer = $('#galleryPreview');
    previewContainer.empty();

    if (input.files && input.files.length > 0) {
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = $('<div>').addClass('col-4');
                const img = $('<img>')
                    .attr('src', e.target.result)
                    .addClass('img-fluid rounded')
                    .css({
                        'width': '100%',
                        'height': '80px',
                        'object-fit': 'cover'
                    });
                col.append(img);
                previewContainer.append(col);
            };
            reader.readAsDataURL(file);
        });
    }
}
</script>
@endpush