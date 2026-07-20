@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Add New Product</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Product</li>
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
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="mb-0 text-lg">Product Creation Wizard</h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-light text-primary">Draft</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="saveDraft">
                        <iconify-icon icon="solar:save-outline"></iconify-icon>
                        Save Draft
                    </button>
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
                
                <!-- Progress Steps -->
                <div class="wizard-progress mb-40">
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar" role="progressbar" id="progressBar" aria-label="Form progress" style="width: 0%"></div>
                    </div>
                    <div class="d-flex justify-content-between position-relative mt-8">
                        @php 
                        $steps = ['Basic Info', 'Pricing & Stock', 'Images', 'Description & Tags'];
                        @endphp
                        @foreach($steps as $index => $step)
                        @php $stepNumber = $index + 1; @endphp
                        <div class="wizard-step {{ $index === 0 ? 'active' : '' }}" data-step="{{ $stepNumber }}">
                            <div class="step-icon">
                                {{ $stepNumber }}
                            </div>
                            <div class="step-label mt-2">{{ $step }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Form Wizard -->
                <div class="form-wizard">
                    <form id="productForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Hidden unit field — always piece for bikes -->
                        <input type="hidden" name="unit" value="piece">
                        
                        <!-- Step 1: Basic Product Info -->
                        <div class="wizard-step-content active" id="step-1">
                            <div class="step-header mb-24">
                                <h5 class="mb-2">Basic Product Information</h5>
                                <p class="text-secondary-light mb-0">Enter the basic details of your product</p>
                            </div>

                            <div class="row g-4">
                                <!-- Product Name -->
                                <div class="col-md-6">
                                    <label class="form-label" for="productName">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="productName" name="name" placeholder="Enter product name" value="{{ old('name') }}" required>
                                    <div class="invalid-feedback">Product name is required</div>
                                </div>

                               <!-- Fuel Type (Category) -->
                                <div class="col-md-6">
                                    <label class="form-label" for="categorySelect">Fuel Type <span class="text-danger">*</span></label>
                                    <select class="form-select" name="category_id" id="categorySelect" required>
                                        <option value="">Select Fuel Type</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Fuel Type is required</div>
                                </div>

                                <!-- Brand (Subcategory) -->
                                <div class="col-md-6">
                                    <label class="form-label" for="subcategorySelect">Brand</label>
                                    <select class="form-select" name="subcategory_id" id="subcategorySelect">
                                        <option value="">Select Fuel Type first</option>
                                    </select>
                                    <small class="text-secondary-light">Select Fuel Type above to load brands</small>
                                </div>

                                <!-- Product Status -->
                                <div class="col-md-6">
                                    <label class="form-label" for="statusSelect">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" name="status" id="statusSelect" required>
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-40 pt-4 border-top">
                                <div>
                                    <button type="button" class="btn btn-outline-secondary" id="saveAsDraftBtn">
                                        <iconify-icon icon="solar:save-outline"></iconify-icon>
                                        Save as Draft
                                    </button>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-primary d-flex align-items-center gap-2 px-32 next-step" data-next="2">
                                        Continue to Pricing
                                        <iconify-icon icon="solar:arrow-right-outline"></iconify-icon>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Pricing & Stock -->
                        <div class="wizard-step-content" id="step-2">
                            <div class="step-header mb-24">
                                <h5 class="mb-2">Pricing & Stock Management</h5>
                                <p class="text-secondary-light mb-0">Set pricing, discounts, and inventory details</p>
                            </div>

                            <div class="row g-4">
                                <!-- Regular Price -->
                                <div class="col-md-6">
                                    <label class="form-label" for="regularPrice">Regular Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rs</span>
                                        <input type="number" class="form-control" name="price" id="regularPrice" placeholder="0.00" step="0.01" min="0" value="{{ old('price') }}" required>
                                    </div>
                                    <div class="invalid-feedback">Regular price is required</div>
                                </div>

                                <!-- Sale Price -->
                                <div class="col-md-6">
                                    <label class="form-label" for="salePrice">Sale Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rs</span>
                                        <input type="number" class="form-control" name="sale_price" id="salePrice" placeholder="0.00" step="0.01" min="0" value="{{ old('sale_price') }}">
                                    </div>
                                    <small class="text-secondary-light d-flex align-items-center gap-1 mt-1">
                                        <iconify-icon icon="solar:info-circle-outline"></iconify-icon>
                                        Leave empty if no discount
                                    </small>
                                </div>

                                <!-- Discount Percentage (Auto-calculated) -->
                                <div class="col-md-6">
                                    <label class="form-label" for="discountPercent">Discount Percentage (Auto-calculated)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light" id="discountPercent" readonly placeholder="0%">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>

                                <!-- Cost Price -->
                                <div class="col-md-6">
                                    <label class="form-label" for="costPrice">Purchase Cost <span class="text-muted small">(Private)</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rs</span>
                                        <input type="text" inputmode="decimal" class="form-control decimal-input" name="cost_price" id="costPrice" placeholder="0.00" value="{{ old('cost_price') }}">
                                    </div>
                                    <small class="text-secondary-light">What you paid the supplier — for profit tracking only</small>
                                </div>

                                <!-- Stock Quantity -->
                                <div class="col-md-6">
                                    <label class="form-label" for="quantityInput">Stock Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="quantity" id="quantityInput" placeholder="0" min="0" value="{{ old('quantity') }}" required>
                                    <div class="invalid-feedback">Stock quantity is required</div>
                                </div>

                                <!-- Low Stock Alert -->
                                <div class="col-md-6">
                                    <label class="form-label" for="lowStockInput">Low Stock Alert</label>
                                    <input type="number" class="form-control" name="low_stock_alert" id="lowStockInput" placeholder="2" min="0" value="{{ old('low_stock_alert', 2) }}">
                                    <small class="text-secondary-light">Alert when stock falls below this number</small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-40 pt-4 border-top">
                                <div>
                                    <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2 prev-step" data-prev="1">
                                        <iconify-icon icon="solar:arrow-left-outline"></iconify-icon>
                                        Back to Basic Info
                                    </button>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-primary px-32 d-flex align-items-center gap-2 next-step" data-next="3">
                                        Continue to Images
                                        <iconify-icon icon="solar:arrow-right-outline"></iconify-icon>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Product Images -->
                        <div class="wizard-step-content" id="step-3">
                            <div class="step-header mb-24">
                                <h5 class="mb-2">Product Images & Gallery</h5>
                                <p class="text-secondary-light mb-0">Upload product images for better presentation</p>
                            </div>

                            <div class="row g-4">
                                <!-- Main Product Image -->
                                <div class="col-12">
                                    <label class="form-label" for="mainImageInput">Main Product Image <span class="text-danger">*</span></label>
                                    <div class="image-upload-container">
                                        <div class="image-upload-box" id="mainImageUpload">
                                            <input type="file" class="image-upload-input" name="main_image" accept="image/*" id="mainImageInput" aria-label="Upload main product image">
                                            <div class="upload-placeholder">
                                                <iconify-icon icon="solar:cloud-upload-outline" class="icon-4x text-primary mb-3"></iconify-icon>
                                                <h6>Drop your image here, or <span class="text-primary">browse</span></h6>
                                                <p class="text-secondary-light mb-0">Supports: JPG, JPEG, PNG (Max: 2MB)</p>
                                            </div>
                                        </div>
                                        <div class="upload-preview d-none mt-3" id="mainImagePreview">
                                            <img src="" alt="Preview" class="preview-image">
                                            <button type="button" class="btn btn-danger btn-sm remove-image" aria-label="Remove main image">
                                                <iconify-icon icon="solar:trash-bin-outline"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-40 pt-4 border-top">
                                <div>
                                    <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2 prev-step" data-prev="2">
                                        <iconify-icon icon="solar:arrow-left-outline"></iconify-icon>
                                        Back to Pricing
                                    </button>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-primary px-32 d-flex align-items-center gap-2 next-step" data-next="4">
                                        Continue to Description
                                        <iconify-icon icon="solar:arrow-right-outline"></iconify-icon>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Description & Tags -->
                        <div class="wizard-step-content" id="step-4">
                            <div class="step-header mb-24">
                                <h5 class="mb-2">Description & Tags</h5>
                                <p class="text-secondary-light mb-0">Add a short description and searchable tags</p>
                            </div>

                            <div class="row g-4">
                                <!-- Short Description -->
                                <div class="col-12">
                                    <label class="form-label" for="shortDescInput">Short Description</label>
                                    <textarea class="form-control" name="short_description" id="shortDescInput" rows="4" placeholder="Brief product summary (max 500 characters)" maxlength="500">{{ old('short_description') }}</textarea>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-secondary-light">This will appear in product listings</small>
                                        <small class="text-secondary-light char-count">0/500</small>
                                    </div>
                                </div>

                                <!-- Product Tags -->
                                <div class="col-12">
                                    <label class="form-label" for="tagsInput">Product Tags</label>
                                    <input type="text" class="form-control" name="tags" id="tagsInput" placeholder="e.g., KTM, Adventure, Petrol, 390cc (comma separated)" value="{{ old('tags') }}">
                                    <small class="text-secondary-light">Separate tags with commas — used for search</small>
                                </div>

                                <!-- Engine Spec -->
                                <div class="col-12">
                                    <label class="form-label" for="engineSpecInput">Engine Specification</label>
                                    <input type="text" class="form-control" name="engine_spec" id="engineSpecInput" placeholder="e.g., 248.8cc, Single Cylinder, Liquid Cooled" value="{{ old('engine_spec') }}">
                                    <small class="text-secondary-light">Shown below the product name on the storefront</small>
                                </div>

                                <!-- Highlights -->
                                <div class="col-12">
                                    <label class="form-label" for="highlightsInput">Highlights</label>
                                    <input type="text" class="form-control" name="highlights" id="highlightsInput" placeholder="e.g., ABS, LED Lights, Digital Console (comma separated)" value="{{ old('highlights') }}">
                                    <small class="text-secondary-light">Feature badges shown on the product card</small>
                                </div>

                                <!-- Rating -->
                                <div class="col-md-6">
                                    <label class="form-label" for="ratingInput">Rating (1–5)</label>
                                    <input type="number" class="form-control" name="rating" id="ratingInput" placeholder="5" min="1" max="5" step="0.1" value="{{ old('rating', 5) }}">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-40 pt-4 border-top">
                                <div>
                                    <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2 prev-step" data-prev="3">
                                        <iconify-icon icon="solar:arrow-left-outline"></iconify-icon>
                                        Back to Images
                                    </button>
                                </div>
                                <div class="d-flex gap-3">
                                    <button type="submit" class="btn btn-success px-32" name="save_draft" value="1">
                                        <iconify-icon icon="solar:save-outline"></iconify-icon>
                                        Save Draft
                                    </button>
                                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 px-32" name="publish_product" value="1">
                                        <iconify-icon icon="solar:upload-outline"></iconify-icon>
                                        Publish Product
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <div class="success-icon">
                        <iconify-icon icon="solar:check-circle-outline" class="text-success"></iconify-icon>
                    </div>
                </div>
                <h5 class="mb-3">Product Created Successfully!</h5>
                <p class="text-secondary-light mb-4">Your product has been added to the store.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('manage.products') }}" class="btn btn-outline-secondary">View All Products</a>
                    <button class="btn btn-primary" onclick="location.reload()">Add Another Product</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Wizard Progress */
.wizard-progress {
    position: relative;
}

.wizard-step {
    text-align: center;
    position: relative;
    z-index: 2;
}

.wizard-step .step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--bs-light);
    color: var(--bs-gray);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin: 0 auto;
    transition: all 0.3s;
    border: 2px solid transparent;
}

.wizard-step.active .step-icon {
    background: var(--bs-primary);
    color: white;
    border-color: var(--bs-primary);
}

.wizard-step.completed .step-icon {
    background: var(--bs-success);
    color: white;
    border-color: var(--bs-success);
}

.wizard-step .step-label {
    font-size: 12px;
    font-weight: 500;
    color: var(--bs-gray);
}

.wizard-step.active .step-label {
    color: var(--bs-primary);
    font-weight: 600;
}

.wizard-step-content {
    display: none;
    animation: fadeIn 0.3s ease-in-out;
}

.wizard-step-content.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Form Groups */
.step-header {
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--bs-border-color);
}

/* Image Upload */
.image-upload-container {
    position: relative;
}

.image-upload-box, .gallery-upload-box {
    border: 2px dashed var(--bs-border-color);
    border-radius: 0.5rem;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
    overflow: hidden;
    background: var(--bs-light-bg);
}

.image-upload-box:hover, .gallery-upload-box:hover {
    border-color: var(--bs-primary);
    background: rgba(var(--bs-primary-rgb), 0.05);
}

.image-upload-box.drag-over, .gallery-upload-box.drag-over {
    border-color: var(--bs-primary);
    background: rgba(var(--bs-primary-rgb), 0.1);
}

.image-upload-input, .gallery-upload-input {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
}

.upload-placeholder {
    pointer-events: none;
}

.upload-preview {
    position: relative;
    width: 200px;
    height: 200px;
    border-radius: 0.5rem;
    overflow: hidden;
    border: 1px solid var(--bs-border-color);
}

.preview-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.remove-image {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

/* Success Icon */
.success-icon {
    width: 80px;
    height: 80px;
    background: rgba(var(--bs-success-rgb), 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    margin: 0 auto 1.5rem;
}

/* Icon Sizes */
.icon-2x { font-size: 2rem; }
.icon-4x { font-size: 4rem; }

/* Responsive Adjustments */
@media (max-width: 768px) {
    /* Steps row stays horizontal — just shrink icons and hide labels */
    .wizard-progress .d-flex.justify-content-between {
        justify-content: space-between !important;
    }

    .wizard-step {
        flex: 1;
        text-align: center;
    }

    .step-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .step-actions .btn {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let currentStep = 1;
    const totalSteps = 4;
    
    function initWizard() {
        updateProgressBar();
        updateStepVisibility();
    }
    
    function updateProgressBar() {
        const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
        $('#progressBar').css('width', progress + '%');
        
        $('.wizard-step').each(function() {
            const step = parseInt($(this).data('step'));
            $(this).removeClass('active completed');
            
            if (step < currentStep) {
                $(this).addClass('completed');
            } else if (step === currentStep) {
                $(this).addClass('active');
            }
        });
    }
    
    function updateStepVisibility() {
        $('.wizard-step-content').removeClass('active');
        $('#step-' + currentStep).addClass('active');
    }
    
    $(document).on('click', '.next-step', function(e) {
        e.preventDefault();
        const nextStep = parseInt($(this).data('next'));
        if (!validateStep(currentStep)) return;
        currentStep = nextStep;
        updateProgressBar();
        updateStepVisibility();
        $('html, body').animate({ scrollTop: $('.wizard-step-content.active').offset().top - 100 }, 300);
    });
    
    $(document).on('click', '.prev-step', function(e) {
        e.preventDefault();
        const prevStep = parseInt($(this).data('prev'));
        currentStep = prevStep;
        updateProgressBar();
        updateStepVisibility();
        $('html, body').animate({ scrollTop: $('.wizard-step-content.active').offset().top - 100 }, 300);
    });
    
    function validateStep(step) {
        const stepContent = $('#step-' + step);
        let isValid = true;

        stepContent.find('[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (step === 3) {
            const mainImageInput = $('#mainImageInput')[0];
            if (!mainImageInput || mainImageInput.files.length === 0) {
                $('#mainImageUpload').css('border-color', 'red');
                $('#mainImageError').remove();
                $('#mainImageUpload').after('<div id="mainImageError" class="text-danger mt-1 small">Please upload a main product image</div>');
                isValid = false;
            } else {
                $('#mainImageUpload').css('border-color', '');
                $('#mainImageError').remove();
            }
        }

        return isValid;
    }
    
    function calculateDiscount() {
        const price = parseFloat($('#regularPrice').val()) || 0;
        const salePrice = parseFloat($('#salePrice').val()) || 0;
        if (price > 0 && salePrice > 0 && salePrice < price) {
            const discount = ((price - salePrice) / price) * 100;
            $('#discountPercent').val(discount.toFixed(2) + '%');
        } else {
            $('#discountPercent').val('0%');
        }
    }
    
    $('#regularPrice, #salePrice').on('input', calculateDiscount);

    $('.decimal-input').on('input', function() {
        let val = $(this).val().replace(/[^0-9.]/g, '');
        const parts = val.split('.');
        if (parts.length > 2) val = parts[0] + '.' + parts.slice(1).join('');
        $(this).val(val);
    });
    
    $('#mainImageInput').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#mainImagePreview img').attr('src', e.target.result);
                $('#mainImagePreview').removeClass('d-none');
                $('#mainImageUpload').hide();
            };
            reader.readAsDataURL(file);
        }
    });
    
    $(document).on('click', '.remove-image', function() {
        $('#mainImagePreview').addClass('d-none');
        $('#mainImageUpload').show();
        $('#mainImageInput').val('');
    });
    
    $('.image-upload-box, .gallery-upload-box').on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('drag-over');
    });
    
    $('.image-upload-box, .gallery-upload-box').on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('drag-over');
    });
    
    $('.image-upload-box, .gallery-upload-box').on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('drag-over');
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            $(this).find('input')[0].files = files;
            $(this).find('input').trigger('change');
        }
    });
    
    $('[maxlength]').on('input', function() {
        const maxLength = parseInt($(this).attr('maxlength'));
        const currentLength = $(this).val().length;
        const charCount = $(this).closest('.col-12').find('.char-count');
        if (charCount.length) {
            charCount.text(currentLength + '/' + maxLength);
            if (currentLength >= maxLength) charCount.addClass('text-danger');
            else charCount.removeClass('text-danger');
        }
    });
    
    $('#productForm').submit(function(e) {
        let allValid = true;
        for (let i = 1; i <= totalSteps; i++) {
            if (!validateStep(i)) {
                e.preventDefault();
                allValid = false;
                currentStep = i;
                updateProgressBar();
                updateStepVisibility();
                break;
            }
        }
        if (!allValid) {
            $('html, body').animate({ scrollTop: $('.is-invalid').first().offset().top - 100 }, 300);
            return false;
        }
        return true;
    });
    
    $('#saveAsDraftBtn, #saveDraft').click(function() {
        $('[name="save_draft"]').val('1');
        $('#productForm').submit();
    });
    
    initWizard();
});

// Load brands when Fuel Type is selected
$('#categorySelect').on('change', function() {
    const categoryId = $(this).val();
    const brandSelect = $('#subcategorySelect');
    brandSelect.html('<option value="">Loading...</option>');
    if (!categoryId) {
        brandSelect.html('<option value="">Select Fuel Type first</option>');
        return;
    }
    $.get('/categories/' + categoryId + '/subcategories', function(data) {
        brandSelect.html('<option value="">Select Brand</option>');
        if (data.subcategories && data.subcategories.length > 0) {
            data.subcategories.forEach(function(sub) {
                const selected = '{{ old("subcategory_id") }}' == sub.id ? 'selected' : '';
                brandSelect.append('<option value="' + sub.id + '" ' + selected + '>' + sub.name + '</option>');
            });
        } else {
            brandSelect.html('<option value="">No brands found</option>');
        }
    });
});

if ($('#categorySelect').val()) {
    $('#categorySelect').trigger('change');
}
</script>
@endpush