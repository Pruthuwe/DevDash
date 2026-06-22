@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Add Brand</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('manage.category') }}">Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Brand</li>
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

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<!-- ── Add Brand Form ── -->
<div class="card mb-24">
    <div class="card-header">
        <h6 class="mb-0 text-lg">Create New Brand</h6>
        <p class="text-secondary-light mb-0 mt-2">Add a new brand under an existing Fuel Type</p>
    </div>

    <div class="card-body">
        <form id="subcategoryPageForm" action="{{ route('store.subcategory') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-4">
                <!-- Parent Fuel Type Dropdown -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label fw-medium">
                            Fuel Type
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <iconify-icon icon="solar:folder-outline"></iconify-icon>
                            </span>
                            <select class="form-select @error('parent_id') is-invalid @enderror"
                                    id="parent_id"
                                    name="parent_id"
                                    required>
                                <option value="" disabled {{ old('parent_id') ? '' : 'selected' }}>Select Fuel Type</option>
                                @foreach($parentCategories as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('parent_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Brand Name -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label fw-medium">
                            Brand Name
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <iconify-icon icon="solar:tag-outline"></iconify-icon>
                            </span>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="subcategoryName"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="e.g., KTM, Bajaj"
                                   required>
                        </div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Icon -->
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label fw-medium">Icon / Logo</label>
                        <div class="image-upload-container">
                            <div class="image-upload-box" id="subIconImageUpload">
                                <div class="upload-placeholder">
                                    <iconify-icon icon="solar:gallery-add-outline" class="icon-2x text-secondary-light"></iconify-icon>
                                    <div class="mt-1">
                                        <p class="mb-0 small">Upload</p>
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
                                    <iconify-icon icon="ic:outline-delete"></iconify-icon>
                                </button>
                            </div>
                        </div>
                        @error('icon_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Status Toggle -->
                <div class="col-md-2">
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
                        @error('status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Minimum Down Payment % override (Loan Calculator) -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label fw-medium">Minimum Down Payment % <span class="text-secondary-light">(override)</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" max="100"
                                   class="form-control @error('min_down_payment_percent') is-invalid @enderror"
                                   name="min_down_payment_percent"
                                   value="{{ old('min_down_payment_percent') }}"
                                   placeholder="Leave blank to use the Fuel Type's %">
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-secondary-light">Only fill this in if this brand needs a different minimum than its Fuel Type.</small>
                        @error('min_down_payment_percent')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-24">
                <button type="submit" class="btn btn-primary px-32 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                    Add Brand
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ── Brand List ── -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0">All Brands</h6>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <input type="text"
                   id="brandSearch"
                   class="form-control form-control-sm"
                   placeholder="Search by name..."
                   style="width:200px;">
            <select id="bikeTypeFilter" class="form-select form-select-sm" style="width:170px;">
                <option value="">All Fuel Types</option>
                @foreach($parentCategories as $parent)
                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="brandTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width:60px;">#</th>
                        <th style="width:90px;">Icon</th>
                        <th>Brand Name</th>
                        <th style="width:180px;">Fuel Type</th>
                        <th class="text-center" style="width:120px;">Status</th>
                        <th class="text-center" style="width:110px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($brands as $index => $brand)
                    <tr data-search="{{ strtolower($brand->name . ' ' . ($brand->parent->name ?? '')) }}"
                        data-bike-type-id="{{ $brand->parent_id }}">
                        <td class="ps-3 text-secondary-light">{{ $brands->firstItem() + $index }}</td>
                        <td>
                            @if($brand->icon_image)
                                <img src="{{ asset($brand->icon_image) }}" alt="{{ $brand->name }}" style="width:36px;height:36px;object-fit:contain;">
                            @else
                                <div class="rounded d-flex align-items-center justify-content-center bg-light" style="width:36px;height:36px;">
                                    <iconify-icon icon="solar:tag-outline" class="text-secondary-light"></iconify-icon>
                                </div>
                            @endif
                        </td>
                        <td class="fw-medium">{{ $brand->name }}</td>
                        <td>
                            <span class="badge bg-primary-light text-primary">{{ $brand->parent->name ?? '—' }}</span>
                        </td>
                        <td class="text-center">
                            @if($brand->status === 'active')
                                <span class="badge bg-success-light text-success px-3 py-2">Active</span>
                            @else
                                <span class="badge bg-danger-light text-danger px-3 py-2">Inactive</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('categories.edit', $brand) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <iconify-icon icon="solar:pen-outline"></iconify-icon>
                                </a>
                                <form action="{{ route('categories.destroy', $brand) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this brand?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <iconify-icon icon="ic:outline-delete"></iconify-icon>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <iconify-icon icon="solar:tag-outline" style="font-size:3rem;" class="text-secondary-light"></iconify-icon>
                            <p class="text-secondary-light mt-2 mb-0">No brands yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($brands->hasPages())
        <div class="d-flex justify-content-center mt-4 p-3">
            {{ $brands->links() }}
        </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
.form-group { margin-bottom: 1.5rem; }

.image-upload-container { position: relative; }

.image-upload-box {
    border: 2px dashed var(--bs-border-color);
    border-radius: 0.5rem;
    padding: 0.75rem;
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

.upload-placeholder { pointer-events: none; }

.upload-preview {
    position: relative;
    border-radius: 0.5rem;
    overflow: hidden;
    border: 1px solid var(--bs-border-color);
}

.upload-preview img {
    width: 100%;
    height: 70px;
    object-fit: contain;
    background: var(--bs-light-bg);
}

.remove-image {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 24px;
    height: 24px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.icon-2x { font-size: 1.5rem; }

/* Status Toggle Switch */
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
    // Icon preview
    const uploadBox = $('#subIconImageUpload');
    const previewBox = $('#subIconImagePreview');
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

    // Table search + Fuel Type filter
    function filterBrandTable() {
        const search = $('#brandSearch').val().toLowerCase();
        const filterType = $('#bikeTypeFilter').val();

        $('#brandTable tbody tr').each(function() {
            const $row = $(this);
            const matchSearch = !search || ($row.data('search') || '').toString().includes(search);
            const matchType = !filterType || ($row.data('bike-type-id') || '').toString() === filterType;
            $row.toggle(matchSearch && matchType);
        });
    }

    $('#brandSearch').on('input', filterBrandTable);
    $('#bikeTypeFilter').on('change', filterBrandTable);
});
</script>
@endpush