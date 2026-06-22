@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">
            @if($category->parent_id)
                Edit Brand
            @else
                Edit Fuel Type
            @endif
        </h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('manage.category') }}">Categories</a></li>
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
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 text-lg">
                    @if($category->parent_id)
                        Edit Brand
                    @else
                        Edit Fuel Type
                    @endif
                </h6>
            </div>

            <div class="card-body">
                <form id="categoryForm" action="{{ route('categories.update', $category) }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    @if($category->parent)
                        <div class="alert alert-info mb-4">
                            <div class="d-flex align-items-center">
                                <iconify-icon icon="solar:folder-outline" class="me-2"></iconify-icon>
                                <div><strong>Fuel Type:</strong> {{ $category->parent->name }}</div>
                            </div>
                        </div>
                    @endif

                    <div class="row g-4">
                        <!-- Name -->
                        <div class="col-md-7">
                            <div class="form-group">
                                <label class="form-label fw-medium">
                                    {{ $category->parent_id ? 'Brand Name' : 'Fuel Type Name' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <iconify-icon icon="solar:tag-outline"></iconify-icon>
                                    </span>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="categoryName"
                                           name="name"
                                           value="{{ old('name', $category->name) }}"
                                           required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-5">
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
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Icon -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-medium">Icon / Logo</label>
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
                                            <iconify-icon icon="ic:outline-delete"></iconify-icon>
                                        </button>
                                    </div>
                                    <input type="hidden" name="delete_icon_image" id="delete_icon_image" value="0">
                                </div>
                                @error('icon_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-32 pt-4 border-top">
                        <button type="submit" class="btn btn-success d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                            Update
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
.form-group { margin-bottom: 1.5rem; }
.image-upload-container { position: relative; }
.image-upload-box {
    border: 2px dashed var(--bs-border-color); border-radius: 0.5rem; padding: 1rem;
    text-align: center; cursor: pointer; transition: all 0.2s; position: relative;
    overflow: hidden; background: var(--bs-light-bg);
}
.image-upload-box:hover { border-color: var(--bs-primary); background: rgba(var(--bs-primary-rgb), 0.05); }
.image-upload-input { position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer; }
.upload-placeholder { pointer-events: none; }
.upload-preview { position: relative; border-radius: 0.5rem; overflow: hidden; border: 1px solid var(--bs-border-color); }
.upload-preview img { width: 100%; height: 120px; object-fit: contain; background: var(--bs-light-bg); }
.remove-image {
    position: absolute; top: 8px; right: 8px; width: 32px; height: 32px; padding: 0;
    display: flex; align-items: center; justify-content: center; border-radius: 50%;
}
.icon-2x { font-size: 2rem; }
.status-toggle-wrap { display: flex; align-items: center; gap: 0.75rem; height: 46px; }
.status-switch { position: relative; display: inline-block; width: 50px; height: 26px; flex-shrink: 0; }
.status-switch input { opacity: 0; width: 0; height: 0; }
.status-slider {
    position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
    background-color: #d9dde3; transition: 0.25s; border-radius: 999px;
}
.status-slider::before {
    position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px;
    background-color: #fff; transition: 0.25s; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.25);
}
.status-switch input:checked + .status-slider { background-color: #28a745; }
.status-switch input:checked + .status-slider::before { transform: translateX(24px); }
.status-toggle-label { font-size: 0.95rem; transition: color 0.2s; }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const uploadBox = $('#iconImageUpload');
    const previewBox = $('#iconImagePreview');
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
        $('#delete_icon_image').val('1');
        previewBox.addClass('d-none');
        uploadBox.removeClass('d-none');
        input.val('');
    });

    // Status toggle
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
});
</script>
@endpush