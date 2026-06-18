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
            <div class="card-header d-flex justify-content-between align-items-center">
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

                                <!-- Category -->
                                <div class="col-md-6">
                                    <label class="form-label" for="categorySelect">Category <span class="text-danger">*</span></label>
                                    <select class="form-select" name="category_id" id="categorySelect" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Category is required</div>
                                </div>

                                <!-- Brand -->
                                <div class="col-md-6">
                                    <label class="form-label" for="brandInput">Brand</label>
                                    <input type="text" class="form-control" id="brandInput" name="brand" placeholder="Enter brand name" value="{{ old('brand') }}">
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
                                        <input type="number" class="form-control" name="cost_price" id="costPrice" placeholder="0.00" step="0.01" min="0" value="{{ old('cost_price') }}">
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
                                    <input type="number" class="form-control" name="low_stock_alert" id="lowStockInput" placeholder="10" min="0" value="{{ old('low_stock_alert') }}">
                                    <small class="text-secondary-light">Alert when stock falls below this number</small>
                                </div>

                                <!-- Loan Amount -->
                                <div class="col-md-6">
                                    <label class="form-label" for="loanAmountInput">Loan Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rs</span>
                                        <input type="number" class="form-control" name="loan_amount" id="loanAmountInput" placeholder="0.00" step="0.01" min="0" value="{{ old('loan_amount') }}">
                                    </div>
                                    <small class="text-secondary-light">Amount financed via loan (leave 0 for full cash)</small>
                                </div>

                                <!-- RMV Fee -->
                                <div class="col-md-6">
                                    <label class="form-label" for="rmvInput">RMV Fee</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rs</span>
                                        <input type="number" class="form-control" name="rmv" id="rmvInput" placeholder="10160.00" step="0.01" min="0" value="{{ old('rmv', 10160) }}">
                                    </div>
                                    <small class="text-secondary-light">Revenue & Motor Vehicle Department registration fee</small>
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

                                <!-- Product Gallery -->
                                <div class="col-12">
                                    <label class="form-label" for="galleryInput">Product Gallery (Optional)</label>
                                    <div class="gallery-upload-box">
                                        <input type="file" class="gallery-upload-input" name="gallery_images[]" accept="image/*" id="galleryInput" aria-label="Upload gallery images" multiple>
                                        <div class="upload-placeholder">
                                            <iconify-icon icon="solar:gallery-add-outline" class="icon-4x text-primary mb-3"></iconify-icon>
                                            <h6>Drop multiple images here, or <span class="text-primary">browse</span></h6>
                                            <p class="text-secondary-light mb-0">Upload up to 5 additional images</p>
                                        </div>
                                    </div>
                                    <div class="gallery-upload-container mt-3">
                                        <div class="gallery-grid" id="galleryGrid">
                                            <!-- Gallery images will be displayed here -->
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

/* Gallery Grid */
.gallery-upload-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 1rem;
}

.gallery-grid {
    display: contents;
}

.gallery-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 0.5rem;
    overflow: hidden;
    border: 1px solid var(--bs-border-color);
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gallery-item .remove-btn {
    position: absolute;
    top: 4px;
    right: 4px;
    opacity: 0;
    transition: opacity 0.2s;
}

.gallery-item:hover .remove-btn {
    opacity: 1;
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
    .wizard-progress .d-flex {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .wizard-step {
        flex-direction: row;
        align-items: center;
        gap: 1rem;
    }
    
    .gallery-upload-container {
        grid-template-columns: repeat(3, 1fr);
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
    
    // Initialize wizard
    function initWizard() {
        updateProgressBar();
        updateStepVisibility();
    }
    
    // Update progress bar
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
    
    // Update step visibility
    function updateStepVisibility() {
        $('.wizard-step-content').removeClass('active');
        $('#step-' + currentStep).addClass('active');
    }
    
    // Navigate to next step
    $(document).on('click', '.next-step', function(e) {
        e.preventDefault();
        
        const nextStep = parseInt($(this).data('next'));
        
        if (!validateStep(currentStep)) {
            return;
        }
        
        currentStep = nextStep;
        updateProgressBar();
        updateStepVisibility();
        
        $('html, body').animate({
            scrollTop: $('.wizard-step-content.active').offset().top - 100
        }, 300);
    });
    
    // Navigate to previous step
    $(document).on('click', '.prev-step', function(e) {
        e.preventDefault();
        
        const prevStep = parseInt($(this).data('prev'));
        currentStep = prevStep;
        updateProgressBar();
        updateStepVisibility();
        
        $('html, body').animate({
            scrollTop: $('.wizard-step-content.active').offset().top - 100
        }, 300);
    });
    
    // Validate step
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

    // Validate main image on step 3
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
    
    // Calculate discount percentage — runs when either price field changes
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
    
    // Main image upload
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
    
    // Remove main image
    $(document).on('click', '.remove-image', function() {
        $('#mainImagePreview').addClass('d-none');
        $('#mainImageUpload').show();
        $('#mainImageInput').val('');
    });
    
    // Gallery image upload
    $('.gallery-upload-input').change(function(e) {
        const files = e.target.files;
        const galleryGrid = $('#galleryGrid');
        
        for (let i = 0; i < Math.min(files.length, 5); i++) {
            const file = files[i];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const galleryItem = `
                    <div class="gallery-item">
                        <img src="${e.target.result}" alt="Gallery image">
                        <button type="button" class="btn btn-danger btn-sm remove-btn" aria-label="Remove image">
                            <iconify-icon icon="solar:trash-bin-outline"></iconify-icon>
                        </button>
                    </div>
                `;
                galleryGrid.append(galleryItem);
            };
            
            reader.readAsDataURL(file);
        }
        
        $(this).val('');
    });
    
    // Remove gallery image
    $(document).on('click', '.gallery-item .remove-btn', function() {
        $(this).closest('.gallery-item').remove();
    });
    
    // Drag and drop
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
    
    // Character counter for short description
    $('[maxlength]').on('input', function() {
        const maxLength = parseInt($(this).attr('maxlength'));
        const currentLength = $(this).val().length;
        const charCount = $(this).closest('.col-12').find('.char-count');
        
        if (charCount.length) {
            charCount.text(currentLength + '/' + maxLength);
            
            if (currentLength >= maxLength) {
                charCount.addClass('text-danger');
            } else {
                charCount.removeClass('text-danger');
            }
        }
    });
    
    // Form submission
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
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 300);
            return false;
        }
        
        return true;
    });
    
    // Save as draft
    $('#saveAsDraftBtn, #saveDraft').click(function() {
        $('[name="save_draft"]').val('1');
        $('#productForm').submit();
    });
    
    // Initialize wizard
    initWizard();
});
</script>
@endpush