@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Add Subcategory</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('manage.category') }}">Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Subcategory</li>
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
                <h6 class="mb-0 text-lg">Create New Subcategory</h6>
                <p class="text-secondary-light mb-0 mt-2">Add a brand or subcategory under an existing category</p>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form id="subcategoryPageForm" action="{{ route('store.subcategory') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <h6 class="mb-4 text-primary">Basic Information</h6>

                    <div class="row g-4">
                        <!-- Parent Category Dropdown -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-medium">
                                    Parent Category
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <iconify-icon icon="solar:folder-outline"></iconify-icon>
                                    </span>
                                    <select class="form-select form-control-lg @error('parent_id') is-invalid @enderror"
                                            id="parent_id"
                                            name="parent_id"
                                            required>
                                        <option value="" disabled {{ old('parent_id') ? '' : 'selected' }}>Select a category</option>
                                        @foreach($parentCategories as $parent)
                                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-text">Choose which category this subcategory belongs to</div>
                                @error('parent_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Subcategory Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-medium">
                                    Subcategory Name
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <iconify-icon icon="solar:tag-outline"></iconify-icon>
                                    </span>
                                    <input type="text"
                                           class="form-control form-control-lg @error('name') is-invalid @enderror"
                                           id="subcategoryName"
                                           name="name"
                                           value="{{ old('name') }}"
                                           placeholder="e.g., KTM, Bajaj"
                                           required>
                                </div>
                                <div class="form-text">Give this subcategory a clear name</div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                                          maxlength="500"
                                          placeholder="Describe this subcategory">{{ old('description') }}</textarea>
                                <div class="form-text d-flex justify-content-between">
                                    <span>Brief description for customers</span>
                                    <span class="char-count">0/500</span>
                                </div>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status Toggle -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-medium">Status</label>
                                <div class="status-toggle-wrap">
                                    <label class="status-switch mb-0">
                                        <input type="checkbox" id="statusToggle" checked>
                                        <span class="status-slider"></span>
                                    </label>
                                    <span class="status-toggle-label text-success fw-semibold" id="statusToggleLabel">Active</span>
                                </div>
                                <input type="hidden" name="status" id="statusInput" value="active">
                                <div class="form-text">Active subcategories are visible to customers</div>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <h6 class="mb-4 mt-40 pt-4 border-top text-primary">Subcategory Media <span class="text-secondary-light fw-normal">(optional)</span></h6>

                    <div class="row g-4">
                        <!-- Banner Image -->
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label fw-medium">Banner Image</label>
                                <div class="image-upload-container">
                                    <div class="image-upload-box" id="subBannerImageUpload">
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
                                    <div class="upload-preview d-none" id="subBannerImagePreview">
                                        <img src="" alt="Preview" class="preview-image">
                                        <button type="button" class="btn btn-danger btn-sm remove-image">
                                            <iconify-icon icon="solar:trash-bin-outline"></iconify-icon>
                                        </button>
                                    </div>
                                </div>
                                @error('banner_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Thumbnail Image -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-medium">Thumbnail Image</label>
                                <div class="image-upload-container">
                                    <div class="image-upload-box" id="subThumbnailImageUpload">
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
                                    <div class="upload-preview d-none" id="subThumbnailImagePreview">
                                        <img src="" alt="Preview" class="preview-image">
                                        <button type="button" class="btn btn-danger btn-sm remove-image">
                                            <iconify-icon icon="solar:trash-bin-outline"></iconify-icon>
                                        </button>
                                    </div>
                                </div>
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
                                    <div class="image-upload-box" id="subIconImageUpload">
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
                                    <div class="upload-preview d-none" id="subIconImagePreview">
                                        <img src="" alt="Preview" class="preview-image">
                                        <button type="button" class="btn btn-danger btn-sm remove-image">
                                            <iconify-icon icon="solar:trash-bin-outline"></iconify-icon>
                                        </button>
                                    </div>
                                </div>
                                @error('icon_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-40 pt-4 border-top">
                        <button type="submit" class="btn btn-primary px-32 d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:upload-outline"></iconify-icon>
                            Create Subcategory
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

.status-switch input:focus-visible + .status-slider {
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.25);
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
    // Live image preview on upload
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

    setupImageUpload('subBannerImageUpload', 'subBannerImagePreview');
    setupImageUpload('subThumbnailImageUpload', 'subThumbnailImagePreview');
    setupImageUpload('subIconImageUpload', 'subIconImagePreview');

    // Description character counter
    $('#description').on('input', function() {
        const maxLength = $(this).attr('maxlength') || 500;
        const currentLength = $(this).val().length;
        $(this).closest('.form-group').find('.char-count').text(currentLength + '/' + maxLength);
    }).trigger('input');

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

    // Required-field check before submit
    $('#subcategoryPageForm').submit(function(e) {
        let isValid = true;
        let firstInvalidField = null;

        $(this).find('[required]').each(function() {
            const $field = $(this);

            if (!$field.val() || !$field.val().trim()) {
                $field.addClass('is-invalid');
                if (!firstInvalidField) firstInvalidField = $field;
                isValid = false;
            } else {
                $field.removeClass('is-invalid');
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
