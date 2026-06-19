@extends('layouts.main')

@section('content')

{{--
    Rebuilt as a single page to match Add Category — no more 2-step wizard.
    Old multi-step version kept at: _backups/category/editCategory.blade.php.old-wizard-version
--}}

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">
            @if($category->parent_id)
                Edit Brand
            @else
                Edit Category
            @endif
        </h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('manage.category') }}">Category Management</a></li>
                @if($category->parent)
                    <li class="breadcrumb-item">{{ $category->parent->name }}</li>
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
                        Edit Brand
                    @else
                        Edit Category
                    @endif
                </h6>
                <p class="text-secondary-light mb-0 mt-2">
                    @if($category->parent_id)
                        Update brand information and media
                    @else
                        Update category information and media
                    @endif
                </p>
            </div>

            <div class="card-body">
                <form id="categoryForm" action="{{ route('categories.update', $category) }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- Parent Category Info (for brands/subcategories) -->
                    @if($category->parent)
                        <div class="alert alert-info mb-4">
                            <div class="d-flex align-items-center">
                                <iconify-icon icon="solar:folder-outline" class="me-2"></iconify-icon>
                                <div>
                                    <strong>Parent Category:</strong> {{ $category->parent->name }}
                                    @if($category->parent->children->count() > 1)
                                        <br><small class="text-secondary-light">This brand belongs to a category with {{ $category->parent->children->count() }} total brands</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($category->parent->children->count() > 1)
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Other Brands in "{{ $category->parent->name }}"</h6>
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
                                <div class="status-toggle-wrap">
                                    <label class="status-switch mb-0">
                                        <input type="checkbox" id="statusToggle" {{ old('status', $category->status) == 'active' ? 'checked' : '' }}>
                                        <span class="status-slider"></span>
                                    </label>
                                    <span class="status-toggle-label {{ old('status', $category->status) == 'active' ? 'text-success' : 'text-danger' }} fw-semibold" id="statusToggleLabel">{{ old('status', $category->status) == 'active' ? 'Active' : 'Inactive' }}</span>
                                </div>
                                <input type="hidden" name="status" id="statusInput" value="{{ old('status', $category->status) }}">
                                <div class="form-text">Active {{ $category->parent_id ? 'brands' : 'categories' }} are visible to customers</div>
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

                    <h6 class="mb-4 mt-40 pt-4 border-top text-primary">Category Media</h6>

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
                                               accept="image/*">
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

                    <div class="d-flex justify-content-end mt-40 pt-4 border-top">
                        <button type="submit" class="btn btn-success d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                            Update Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.form-group {
    margin-bottom: 1.5rem;
}

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

.icon-2x { font-size: 2rem; }
.icon-4x { font-size: 4rem; }

/* Status Toggle Switch */
.status-toggle-wrap {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    height: 46px;
}

.status-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 26px;
    flex-shrink: 0;
}

.status-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.status-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #d9dde3;
    transition: 0.25s;
    border-radius: 999px;
}

.status-slider::before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background-color: #fff;
    transition: 0.25s;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.25);
}

.status-switch input:checked + .status-slider {
    background-color: #28a745;
}

.status-switch input:checked + .status-slider::before {
    transform: translateX(24px);
}

.status-toggle-label {
    font-size: 0.95rem;
    transition: color 0.2s;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
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

    setupImageUpload('bannerImageUpload', 'bannerImagePreview');
    setupImageUpload('thumbnailImageUpload', 'thumbnailImagePreview');
    setupImageUpload('iconImageUpload', 'iconImagePreview');

    // Status toggle switch
    const statusToggle = document.getElementById('statusToggle');
    const statusLabel = document.getElementById('statusToggleLabel');
    const statusInput = document.getElementById('statusInput');

    statusToggle.addEventListener('change', function() {
        if (this.checked) {
            statusInput.value = 'active';
            statusLabel.textContent = 'Active';
            statusLabel.classList.remove('text-danger');
            statusLabel.classList.add('text-success');
        } else {
            statusInput.value = 'inactive';
            statusLabel.textContent = 'Inactive';
            statusLabel.classList.remove('text-success');
            statusLabel.classList.add('text-danger');
        }
    });

    // Simple required-field check before submit. Image fields only fail
    // validation if there's no existing image already saved for them.
    $('#categoryForm').submit(function(e) {
        let isValid = true;
        let firstInvalidField = null;

        $(this).find('[required]').each(function() {
            const $field = $(this);

            if ($field.attr('type') === 'file') {
                const hasExistingImage =
                    ($field.attr('name') === 'banner_image' && '{{ $category->banner_image }}') ||
                    ($field.attr('name') === 'thumbnail_image' && '{{ $category->thumbnail_image }}') ||
                    ($field.attr('name') === 'icon_image' && '{{ $category->icon_image }}');

                if ((!$field[0].files || $field[0].files.length === 0) && !hasExistingImage) {
                    $field.addClass('is-invalid');
                    if (!firstInvalidField) firstInvalidField = $field;
                    isValid = false;
                } else {
                    $field.removeClass('is-invalid');
                }
            } else {
                if (!$field.val() || !$field.val().trim()) {
                    $field.addClass('is-invalid');
                    if (!firstInvalidField) firstInvalidField = $field;
                    isValid = false;
                } else {
                    $field.removeClass('is-invalid');
                }
            }
        });

        if (!isValid) {
            e.preventDefault();
            if (firstInvalidField) {
                $('html, body').animate({ scrollTop: firstInvalidField.offset().top - 100 }, 300);
            }
            return false;
        }
    });
});
</script>
@endpush