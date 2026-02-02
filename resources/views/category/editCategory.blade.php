@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">
            @if($category->parent_id)
                Edit Subcategory
            @else
                Edit Category
            @endif
        </h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('manage.category') }}">Category Management</a></li>
                @if($category->parent)
                    <li class="breadcrumb-item"><a href="{{ route('categories.show', $category->parent) }}">{{ $category->parent->name }}</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
                <h6 class="mb-0 text-lg">
                    @if($category->parent_id)
                        Edit Subcategory
                    @else
                        Edit Category
                    @endif
                </h6>
                <p class="text-secondary-light mb-0 mt-2">
                    @if($category->parent_id)
                        Update subcategory information and media
                    @else
                        Update category information and media
                    @endif
                </p>
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
                <form id="categoryForm" action="{{ route('categories.update', $category) }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- Parent Category Info (for subcategories) -->
                    @if($category->parent)
                        <div class="alert alert-info mb-4">
                            <div class="d-flex align-items-center">
                                <iconify-icon icon="solar:folder-outline" class="me-2"></iconify-icon>
                                <div>
                                    <strong>Parent Category:</strong> {{ $category->parent->name }}
                                    @if($category->parent->children->count() > 1)
                                        <br><small class="text-secondary-light">This subcategory belongs to a category with {{ $category->parent->children->count() }} total subcategories</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Other Subcategories under same parent -->
                        @if($category->parent->children->count() > 1)
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Other Subcategories in "{{ $category->parent->name }}"</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        @foreach($category->parent->children->where('id', '!=', $category->id) as $sibling)
                                            <div class="col-md-6 col-lg-4">
                                                <div class="d-flex align-items-center p-2 border rounded">
                                                    @if($sibling->thumbnail_image)
                                                        <img src="{{ asset($sibling->thumbnail_image) }}" alt="{{ $sibling->name }}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-secondary-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <iconify-icon icon="solar:folder-outline" class="text-secondary-light"></iconify-icon>
                                                        </div>
                                                    @endif
                                                    <div class="flex-grow-1">
                                                        <div class="fw-medium">{{ $sibling->name }}</div>
                                                        <small class="text-secondary-light">{{ $sibling->status }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

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
                                               value="{{ old('name', $category->name) }}"
                                               placeholder="Enter category name (e.g., Electronics, Clothing)"
                                               required>
                                    </div>
                                    <div class="form-text text-secondary-light">Give your category a clear and descriptive name</div>
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
                                            <input class="form-check-input" type="radio" name="status" id="statusActive" value="active" {{ old('status', $category->status) == 'active' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="statusActive">
                                                <span class="d-flex align-items-center gap-1">
                                                    <iconify-icon icon="solar:check-circle-outline" class="text-success"></iconify-icon>
                                                    Active
                                                </span>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive" {{ old('status', $category->status) == 'inactive' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="statusInactive">
                                                <span class="d-flex align-items-center gap-1">
                                                    <iconify-icon icon="solar:minus-circle-outline" class="text-danger"></iconify-icon>
                                                    Inactive
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    @error('status')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label fw-medium">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description"
                                              name="description"
                                              rows="4"
                                              placeholder="Describe your category...">{{ old('description', $category->description) }}</textarea>
                                    <div class="form-text text-secondary-light">Brief description for customers</div>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-40 pt-4 border-top">
                        </div>
                    </div>

                    <!-- Step 2: Media -->
                    <div class="form-step-content" id="step2">
                        <h6 class="mb-4 text-primary">Category Media</h6>

                        <div class="row g-4">
                            <!-- Banner Image -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label fw-medium">Banner Image</label>
                                    <div class="image-upload-container">
                                        <div class="image-upload-box {{ $category->banner_image ? 'd-none' : '' }}" id="bannerImageUpload">
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
                                                   accept="image/*">
                                        </div>
                                        <div class="upload-preview {{ $category->banner_image ? '' : 'd-none' }}" id="bannerImagePreview">
                                            <img src="{{ $category->banner_image ? '/' . $category->banner_image : '' }}" alt="Preview" class="preview-image">
                                            <button type="button" class="btn btn-danger btn-sm remove-image" data-field="banner_image">
                                                <iconify-icon icon="solar:trash-bin-outline"></iconify-icon>
                                            </button>
                                        </div>
                                        <input type="hidden" name="delete_banner_image" id="delete_banner_image" value="0">
                                    </div>
                                    <div class="form-text text-secondary-light">Large banner image for category pages</div>
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
                                        <div class="image-upload-box {{ $category->thumbnail_image ? 'd-none' : '' }}" id="thumbnailImageUpload">
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
                                        <div class="upload-preview {{ $category->thumbnail_image ? '' : 'd-none' }}" id="thumbnailImagePreview">
                                            <img src="{{ $category->thumbnail_image ? '/' . $category->thumbnail_image : '' }}" alt="Preview" class="preview-image">
                                            <button type="button" class="btn btn-danger btn-sm remove-image" data-field="thumbnail_image">
                                                <iconify-icon icon="solar:trash-bin-outline"></iconify-icon>
                                            </button>
                                        </div>
                                        <input type="hidden" name="delete_thumbnail_image" id="delete_thumbnail_image" value="0">
                                    </div>
                                    <div class="form-text text-secondary-light">Small image for category lists (300×300px)</div>
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
                                        <div class="image-upload-box {{ $category->icon_image ? 'd-none' : '' }}" id="iconImageUpload">
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
                                        <div class="upload-preview {{ $category->icon_image ? '' : 'd-none' }}" id="iconImagePreview">
                                            <img src="{{ $category->icon_image ? '/' . $category->icon_image : '' }}" alt="Preview" class="preview-image">
                                            <button type="button" class="btn btn-danger btn-sm remove-image" data-field="icon_image">
                                                <iconify-icon icon="solar:trash-bin-outline"></iconify-icon>
                                            </button>
                                        </div>
                                        <input type="hidden" name="delete_icon_image" id="delete_icon_image" value="0">
                                    </div>
                                    <div class="form-text text-secondary-light">Small icon for navigation (100×100px)</div>
                                    @error('icon_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Image Guidelines -->
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-secondary-light">
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
                            <button type="button" class="btn btn-outline-secondary prev-step d-flex align-items-center gap-2" data-prev="1">
                                <iconify-icon icon="solar:arrow-left-outline"></iconify-icon>
                                Back
                            </button>
                            <button type="submit" class="btn btn-success d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                                Update Category
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



@push('scripts')
<script>
$(document).ready(function() {
    let currentStep = 1;
    const totalSteps = 2;

    function initForm() {
        updateProgressBar();
        updateStepVisibility();
        setupEventListeners();
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

            // Scroll to top
            $('html, body').animate({
                scrollTop: $('.form-step-content.active').offset().top - 100
            }, 300);
        });

        // Image upload handlers
        setupImageUpload('bannerImageUpload', 'bannerImagePreview');
        setupImageUpload('thumbnailImageUpload', 'thumbnailImagePreview');
        setupImageUpload('iconImageUpload', 'iconImagePreview');

        // Form submission
        $('#categoryForm').submit(function(e) {
            // Basic validation for required fields
            let isValid = true;
            $(this).find('[required]').each(function() {
                const $field = $(this);
                if ($field.attr('type') === 'file') {
                    if (!$field[0].files || $field[0].files.length === 0) {
                        // Check if there's already an existing image
                        let hasExistingImage = false;
                        if ($field.attr('name') === 'banner_image' && '{{ $category->banner_image }}') {
                            hasExistingImage = true;
                        } else if ($field.attr('name') === 'thumbnail_image' && '{{ $category->thumbnail_image }}') {
                            hasExistingImage = true;
                        } else if ($field.attr('name') === 'icon_image' && '{{ $category->icon_image }}') {
                            hasExistingImage = true;
                        }
                        if (!hasExistingImage) {
                            $field.addClass('is-invalid');
                            isValid = false;
                        }
                    } else {
                        $field.removeClass('is-invalid');
                    }
                } else {
                    if (!$field.val().trim()) {
                        $field.addClass('is-invalid');
                        isValid = false;
                    } else {
                        $field.removeClass('is-invalid');
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $('.is-invalid').first().offset().top - 100
                }, 300);
                return false;
            }
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
            const field = $(this).data('field');
            if (field) {
                $('#delete_' + field).val('1');
            }
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
                    // Check if there's already an existing image
                    let hasExistingImage = false;
                    if ($field.attr('name') === 'banner_image' && '{{ $category->banner_image }}') {
                        hasExistingImage = true;
                    } else if ($field.attr('name') === 'thumbnail_image' && '{{ $category->thumbnail_image }}') {
                        hasExistingImage = true;
                    } else if ($field.attr('name') === 'icon_image' && '{{ $category->icon_image }}') {
                        hasExistingImage = true;
                    }
                    if (!hasExistingImage) {
                        $field.addClass('is-invalid');
                        isValid = false;
                    } else {
                        $field.removeClass('is-invalid');
                    }
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

    // Initialize the form
    initForm();
});
</script>
@endpush

@endsection