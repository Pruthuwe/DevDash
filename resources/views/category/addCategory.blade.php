@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Add New Category</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('manage.category') }}">Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add New</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('manage.category') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline"></iconify-icon>
            Back to Categories
        </a>
    </div>
</div>

<!-- Main Content -->
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 text-lg">Create New Category</h6>
                <p class="text-secondary-light mb-0 mt-2">Organize your products with main categories</p>
            </div>
            
            <div class="card-body">
                <!-- Form Steps -->
                <div class="form-steps mb-40">
                    <div class="d-flex justify-content-between position-relative">
                        <div class="form-step active">
                            <div class="step-icon">
                                <iconify-icon icon="solar:folder-outline"></iconify-icon>
                            </div>
                            <div class="step-label mt-2">Basic Info</div>
                        </div>
                        <div class="form-step">
                            <div class="step-icon">
                                <iconify-icon icon="solar:image-outline"></iconify-icon>
                            </div>
                            <div class="step-label mt-2">Media</div>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar" id="formProgress" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Category Form -->
                <form id="categoryForm" action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Step 1: Basic Information -->
                    <div class="form-step-content active" id="step1">
                        <h6 class="mb-4 text-primary">Basic Information</h6>
                        
                        <div class="row g-4">
                            <!-- Category Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-medium">
                                        Category Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <iconify-icon icon="solar:tag-outline"></iconify-icon>
                                        </span>
                                        <input type="text" 
                                               class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                               id="categoryName" 
                                               name="name" 
                                               value="{{ old('name') }}"
                                               placeholder="Enter category name (e.g., Electronics, Clothing)"
                                               required
                                               oninput="updatePreview()">
                                    </div>
                                    <div class="form-text">Give your category a clear and descriptive name</div>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-medium">Status</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status" id="statusActive" value="active" {{ old('status', 'active') == 'active' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="statusActive">
                                                <span class="d-flex align-items-center gap-1">
                                                    Active
                                                </span>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive" {{ old('status') == 'inactive' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="statusInactive">
                                                <span class="d-flex align-items-center gap-1">
                                                    Inactive
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-text">Active categories are visible to customers</div>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label fw-medium">
                                        Description
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="4"
                                              placeholder="Describe this category and what products it contains"
                                              required>{{ old('description') }}</textarea>
                                    <div class="form-text d-flex justify-content-between">
                                        <span>Brief description for customers</span>
                                        <span class="char-count">0/500</span>
                                    </div>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-40 pt-4 border-top">
                            <button type="button" class="btn btn-primary px-32 d-flex align-items-center gap-2 next-step" data-next="2">
                                Continue to Media
                                <iconify-icon icon="solar:arrow-right-outline"></iconify-icon>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 2: Media -->
                    <div class="form-step-content" id="step2">
                        <h6 class="mb-4 text-primary">Category Media</h6>
                        
                        <div class="row g-4">
                            <!-- Banner Image -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label fw-medium">
                                        Banner Image
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="image-upload-container">
                                        <div class="image-upload-box" id="bannerImageUpload">
                                            <div class="upload-placeholder">
                                                <iconify-icon icon="solar:gallery-add-outline" class="icon-4x text-secondary-light"></iconify-icon>
                                                <div class="mt-3">
                                                    <h6 class="mb-1">Drop banner image here or click to upload</h6>
                                                    <p class="text-secondary-light mb-0">Recommended: 1200×400px, JPG, PNG or WebP</p>
                                                </div>
                                            </div>
                                            <input type="file" 
                                                   class="image-upload-input @error('banner_image') is-invalid @enderror" 
                                                   name="banner_image" 
                                                   accept="image/*"
                                                   required>
                                        </div>
                                        <div class="upload-preview d-none" id="bannerImagePreview">
                                            <img src="" alt="Preview" class="preview-image">
                                            <button type="button" class="btn btn-danger btn-sm remove-image">
                                                <iconify-icon icon="solar:trash-bin-outline"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-text">Large banner image for category pages</div>
                                    @error('banner_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Thumbnail Image -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-medium">
                                        Thumbnail Image
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="image-upload-container">
                                        <div class="image-upload-box" id="thumbnailImageUpload">
                                            <div class="upload-placeholder">
                                                <iconify-icon icon="solar:gallery-add-outline" class="icon-2x text-secondary-light"></iconify-icon>
                                                <div class="mt-2">
                                                    <p class="mb-0">Upload thumbnail</p>
                                                </div>
                                            </div>
                                            <input type="file" 
                                                   class="image-upload-input @error('thumbnail_image') is-invalid @enderror" 
                                                   name="thumbnail_image" 
                                                   accept="image/*"
                                                   required>
                                        </div>
                                        <div class="upload-preview d-none" id="thumbnailImagePreview">
                                            <img src="" alt="Preview" class="preview-image">
                                            <button type="button" class="btn btn-danger btn-sm remove-image">
                                                <iconify-icon icon="solar:trash-bin-outline"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-text">Small image for category lists (300×300px)</div>
                                    @error('thumbnail_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Icon Image -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-medium">Icon Image</label>
                                    <div class="image-upload-container">
                                        <div class="image-upload-box" id="iconImageUpload">
                                            <div class="upload-placeholder">
                                                <iconify-icon icon="solar:gallery-add-outline" class="icon-2x text-secondary-light"></iconify-icon>
                                                <div class="mt-2">
                                                    <p class="mb-0">Upload icon</p>
                                                </div>
                                            </div>
                                            <input type="file" 
                                                   class="image-upload-input @error('icon_image') is-invalid @enderror" 
                                                   name="icon_image" 
                                                   accept="image/*">
                                        </div>
                                        <div class="upload-preview d-none" id="iconImagePreview">
                                            <img src="" alt="Preview" class="preview-image">
                                            <button type="button" class="btn btn-danger btn-sm remove-image">
                                                <iconify-icon icon="solar:trash-bin-outline"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-text">Small icon for navigation (100×100px)</div>
                                    @error('icon_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Image Guidelines -->
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Image Guidelines</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start gap-2">
                                                    <iconify-icon icon="solar:info-circle-outline" class="text-primary mt-1"></iconify-icon>
                                                    <div>
                                                        <h6 class="mb-1">Format</h6>
                                                        <p class="text-secondary-light mb-0">Use JPG, PNG, or WebP format</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start gap-2">
                                                    <iconify-icon icon="solar:info-circle-outline" class="text-primary mt-1"></iconify-icon>
                                                    <div>
                                                        <h6 class="mb-1">Size Limit</h6>
                                                        <p class="text-secondary-light mb-0">Max 2MB per image</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start gap-2">
                                                    <iconify-icon icon="solar:info-circle-outline" class="text-primary mt-1"></iconify-icon>
                                                    <div>
                                                        <h6 class="mb-1">Background</h6>
                                                        <p class="text-secondary-light mb-0">Transparent or white background preferred</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-40 pt-4 border-top">
                            <div>
                                <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2 prev-step" data-prev="1">
                                    <iconify-icon icon="solar:arrow-left-outline"></iconify-icon>
                                    Back to Basic Info
                                </button>
                            </div>
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                                    <iconify-icon icon="solar:upload-outline"></iconify-icon>
                                    Create Category
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Preview Sidebar -->
    <div class="col-xl-4">
        <div class="card sticky-top" style="top: 100px;">
            <div class="card-header">
                <h6 class="mb-0">Category Preview</h6>
            </div>
            <div class="card-body">
                <div class="category-preview">
                    <div class="preview-header text-center mb-4">
                        <div class="preview-image mb-3">
                            <div class="thumbnail-placeholder bg-light rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; margin: 0 auto;">
                                <iconify-icon icon="solar:folder-outline" class="text-primary icon-3x"></iconify-icon>
                            </div>
                        </div>
                        <h5 id="previewName" class="mb-2">Category Name</h5>
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                            <span class="badge bg-success" id="previewStatus">Active</span>
                        </div>
                    </div>
                    
                    <div class="preview-details">
                        <h6 class="mb-3">Details</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <small class="text-secondary-light">Display Order</small>
                                <div class="fw-medium" id="previewOrder">0</div>
                            </div>
                            <div class="col-6">
                                <small class="text-secondary-light">Status</small>
                                <div class="fw-medium" id="previewStatusText">Active</div>
                            </div>
                            <div class="col-12">
                                <small class="text-secondary-light">Description</small>
                                <div class="text-truncate" id="previewDescription">Category description will appear here...</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="preview-features mt-4">
                        <h6 class="mb-3">Features</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-2">
                                    <iconify-icon icon="solar:star-outline" class="text-secondary-light"></iconify-icon>
                                    <small>Featured: <span id="previewFeatured">No</span></small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-2">
                                    <iconify-icon icon="solar:menu-dots-outline" class="text-secondary-light"></iconify-icon>
                                    <small>In Menu: <span id="previewMenu">Yes</span></small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-2">
                                    <iconify-icon icon="solar:home-outline" class="text-secondary-light"></iconify-icon>
                                    <small>Homepage: <span id="previewHomepage">No</span></small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-2">
                                    <iconify-icon icon="solar:filter-outline" class="text-secondary-light"></iconify-icon>
                                    <small>Filtering: <span id="previewFilter">Yes</span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="preview-tips mt-4 pt-4 border-top">
                        <h6 class="mb-2">Tips</h6>
                        <ul class="text-secondary-light small">
                            <li>Use descriptive names that customers understand</li>
                            <li>Add clear category descriptions</li>
                            <li>Upload high-quality images</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Form Steps */
.form-steps {
    position: relative;
}

.form-step {
    text-align: center;
    position: relative;
    z-index: 2;
    flex: 1;
}

.form-step .step-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--bs-light);
    color: var(--bs-gray);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin: 0 auto;
    transition: all 0.3s;
    border: 2px solid transparent;
}

.form-step.active .step-icon {
    background: var(--bs-primary);
    color: white;
    border-color: var(--bs-primary);
}

.form-step.completed .step-icon {
    background: var(--bs-success);
    color: white;
    border-color: var(--bs-success);
}

.form-step .step-label {
    font-size: 12px;
    font-weight: 500;
    color: var(--bs-gray);
    margin-top: 8px;
}

.form-step.active .step-label {
    color: var(--bs-primary);
    font-weight: 600;
}

/* Form Steps Content */
.form-step-content {
    display: none;
    animation: fadeIn 0.3s ease-in-out;
}

.form-step-content.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Form Groups */
.form-group {
    margin-bottom: 1.5rem;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

/* Card Switches */
.card-switch {
    padding: 1rem;
    border: 1px solid var(--bs-border-color);
    border-radius: 0.5rem;
    background: var(--bs-light-bg);
    transition: all 0.2s;
    height: 100%;
}

.card-switch:hover {
    border-color: var(--bs-primary);
    background: rgba(var(--bs-primary-rgb), 0.05);
}

.card-switch .form-check-input:checked {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
}

.switch-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Image Upload */
.image-upload-container {
    position: relative;
}

.image-upload-box {
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

.image-upload-box:hover {
    border-color: var(--bs-primary);
    background: rgba(var(--bs-primary-rgb), 0.05);
}

.image-upload-box.drag-over {
    border-color: var(--bs-primary);
    background: rgba(var(--bs-primary-rgb), 0.1);
}

.image-upload-input {
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
    border-radius: 0.5rem;
    overflow: hidden;
    border: 1px solid var(--bs-border-color);
}

.upload-preview img {
    width: 100%;
    height: 200px;
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

/* Category Preview */
.category-preview {
    padding: 1rem;
}

.thumbnail-placeholder {
    width: 100px;
    height: 100px;
    border-radius: 12px;
}

/* Color Preview */
.color-preview {
    border: 1px solid var(--bs-border-color);
}

/* Icon Sizes */
.icon-2x { font-size: 2rem; }
.icon-3x { font-size: 3rem; }
.icon-4x { font-size: 4rem; }

/* Responsive */
@media (max-width: 768px) {
    .form-steps .d-flex {
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .form-step {
        flex: 0 0 calc(50% - 0.5rem);
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
<script>
$(document).ready(function() {
    let currentStep = 1;
        const totalSteps = 2;
    function initForm() {
        updateProgressBar();
        updateStepVisibility();
        setupEventListeners();
        updatePreview();
    }
    
    // Update progress bar
    function updateProgressBar() {
        const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
        $('#formProgress').css('width', progress + '%');
        
        // Update step indicators
        $('.form-step').each(function(index) {
            const stepNumber = index + 1;
            $(this).removeClass('active completed');
            
            if (stepNumber < currentStep) {
                $(this).addClass('completed');
            } else if (stepNumber === currentStep) {
                $(this).addClass('active');
            }
        });
    }
    
    // Update step visibility
    function updateStepVisibility() {
        $('.form-step-content').removeClass('active');
        $('#step' + currentStep).addClass('active');
    }
    
    // Setup event listeners
    function setupEventListeners() {
        // Next step buttons
        $('.next-step').click(function(e) {
            e.preventDefault();
            
            const nextStep = parseInt($(this).data('next'));
            const currentStepContent = $('#step' + currentStep);
            
            // Validate current step
            if (!validateStep(currentStep)) {
                return;
            }
            
            currentStep = nextStep;
            updateProgressBar();
            updateStepVisibility();
            updatePreview();
            
            // Scroll to top
            $('html, body').animate({
                scrollTop: $('.form-step-content.active').offset().top - 100
            }, 300);
        });
        
        // Previous step buttons
        $('.prev-step').click(function(e) {
            e.preventDefault();
            
            const prevStep = parseInt($(this).data('prev'));
            currentStep = prevStep;
            updateProgressBar();
            updateStepVisibility();
            updatePreview();
            
            // Scroll to top
            $('html, body').animate({
                scrollTop: $('.form-step-content.active').offset().top - 100
            }, 300);
        });
        
        // Character counters
        $('#description, #metaTitle, #metaDescription').on('input', function() {
            const maxLength = $(this).attr('maxlength') || 500;
            const currentLength = $(this).val().length;
            const charCount = $(this).closest('.form-group').find('.char-count');
            
            if (charCount.length) {
                charCount.text(currentLength + '/' + maxLength);
                
                if (currentLength > maxLength) {
                    charCount.addClass('text-danger');
                } else {
                    charCount.removeClass('text-danger');
                }
            }
            
            updatePreview();
        });
        
        // Color picker
        $('#categoryColor').on('input', function() {
            $('.color-preview').css('background-color', $(this).val());
        });
        
        // Image upload handlers
        setupImageUpload('bannerImageUpload', 'bannerImagePreview');
        setupImageUpload('thumbnailImageUpload', 'thumbnailImagePreview');
        setupImageUpload('iconImageUpload', 'iconImagePreview');
        
        // Form inputs for preview
        $('#categoryName, #description, #displayOrder, input[name="status"], input[type="checkbox"]').on('input change', updatePreview);
        
        // SEO preview updates
        $('#metaTitle, #metaDescription').on('input', updateSeoPreview);
        
        // Form submission - with better error handling
        $('#categoryForm').submit(function(e) {
            console.log('Form submission started');
            
            // Basic validation for required fields
            let isValid = true;
            let firstInvalidField = null;
            
            $(this).find('[required]').each(function() {
                const $field = $(this);
                const fieldName = $field.attr('name') || $field.attr('id');
                
                if ($field.attr('type') === 'file') {
                    if (!$field[0].files || $field[0].files.length === 0) {
                        console.log('File field ' + fieldName + ' is required but empty');
                        $field.addClass('is-invalid');
                        if (!firstInvalidField) firstInvalidField = $field;
                        isValid = false;
                    } else {
                        console.log('File field ' + fieldName + ' has file:', $field[0].files[0].name);
                        $field.removeClass('is-invalid');
                    }
                } else {
                    if (!$field.val() || !$field.val().trim()) {
                        console.log('Field ' + fieldName + ' is required but empty');
                        $field.addClass('is-invalid');
                        if (!firstInvalidField) firstInvalidField = $field;
                        isValid = false;
                    } else {
                        console.log('Field ' + fieldName + ' has value:', $field.val());
                        $field.removeClass('is-invalid');
                    }
                }
            });

            if (!isValid) {
                console.log('Form validation failed, preventing submission');
                e.preventDefault();
                if (firstInvalidField) {
                    $('html, body').animate({
                        scrollTop: firstInvalidField.offset().top - 100
                    }, 300);
                }
                return false;
            }
            
            console.log('Form validation passed, allowing submission');
            // Allow form to submit normally
        });
    }
    
    // Setup image upload
    function setupImageUpload(uploadId, previewId) {
        const uploadBox = $('#' + uploadId);
        const previewBox = $('#' + previewId);
        const input = uploadBox.find('input');
        
        input.change(function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewBox.find('img').attr('src', e.target.result);
                    previewBox.removeClass('d-none');
                    uploadBox.addClass('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
        
        previewBox.find('.remove-image').click(function() {
            previewBox.addClass('d-none');
            uploadBox.removeClass('d-none');
            input.val('');
        });
    }
    
    // Validate step
    function validateStep(step) {
        const stepContent = $('#step' + step);
        let isValid = true;
        
        stepContent.find('[required]').each(function() {
            const $field = $(this);
            
            // Handle file inputs differently
            if ($field.attr('type') === 'file') {
                if (!$field[0].files || $field[0].files.length === 0) {
                    $field.addClass('is-invalid');
                    isValid = false;
                } else {
                    $field.removeClass('is-invalid');
                }
            } else {
                // Handle text inputs and other types
                if (!$field.val() || !$field.val().trim()) {
                    $field.addClass('is-invalid');
                    isValid = false;
                } else {
                    $field.removeClass('is-invalid');
                }
            }
        });
        
        return isValid;
    }
    
    // Validate all steps
    function validateAllSteps() {
        let allValid = true;
        for (let i = 1; i <= totalSteps; i++) {
            if (!validateStep(i)) {
                allValid = false;
                currentStep = i;
                updateProgressBar();
                updateStepVisibility();
                break;
            }
        }
        return allValid;
    }
    
    // Reset color
    function resetColor() {
        $('#categoryColor').val('#3b82f6');
        $('.color-preview').css('background-color', '#3b82f6');
    }
    
    // Update preview
    function updatePreview() {
        // Name
        const name = $('#categoryName').val() || 'Category Name';
        $('#previewName').text(name);
        
        // Status
        const status = $('input[name="status"]:checked').val() === 'active' ? 'Active' : 'Inactive';
        $('#previewStatus').text(status);
        $('#previewStatus').removeClass('bg-success bg-secondary').addClass(status === 'Active' ? 'bg-success' : 'bg-secondary');
        $('#previewStatusText').text(status);
        
        // Order
        const order = $('#displayOrder').val() || '0';
        $('#previewOrder').text(order);
        
        // Description
        const desc = $('#description').val() || 'Category description will appear here...';
        $('#previewDescription').text(desc);
        
        // Features
        $('#previewFeatured').text($('#featuredSwitch').is(':checked') ? 'Yes' : 'No');
        $('#previewMenu').text($('#menuSwitch').is(':checked') ? 'Yes' : 'No');
        $('#previewHomepage').text($('#homepageSwitch').is(':checked') ? 'Yes' : 'No');
        $('#previewFilter').text($('#filterSwitch').is(':checked') ? 'Yes' : 'No');
    }
    
    // Initialize the form
    initForm();
});
</script>
@endpush