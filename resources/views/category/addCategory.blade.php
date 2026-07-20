@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Add Fuel Type</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('manage.category') }}">Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Fuel Type</li>
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

<!-- ── Add Fuel Type Form ── -->
<div class="card mb-24">
    <div class="card-header">
        <h6 class="mb-0 text-lg">Create New Fuel Type</h6>
        <p class="text-secondary-light mb-0 mt-2">e.g. Electric Bikes, Petrol Bikes</p>
    </div>

    <div class="card-body">
        <form id="categoryForm" action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-4">
                <!-- Fuel Type Name -->
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="form-label fw-medium">
                            Fuel Type Name
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
                                   value="{{ old('name') }}"
                                   placeholder="e.g., Electric"
                                   required>
                        </div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Icon -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label fw-medium">Icon / Logo</label>
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
                                    <iconify-icon icon="ic:outline-delete"></iconify-icon>
                                </button>
                            </div>
                        </div>
                        @error('icon_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label fw-medium">Status</label>
                        <div class="status-toggle-wrap">
                            <label class="status-switch mb-0">
                                <input type="checkbox" id="statusToggle" {{ old('status', 'active') == 'active' ? 'checked' : '' }}>
                                <span class="status-slider"></span>
                            </label>
                            <span class="status-toggle-label {{ old('status', 'active') == 'active' ? 'text-success' : 'text-danger' }} fw-semibold" id="statusToggleLabel">{{ old('status', 'active') == 'active' ? 'Active' : 'Inactive' }}</span>
                        </div>
                        <input type="hidden" name="status" id="statusInput" value="{{ old('status', 'active') }}">
                        @error('status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                
            </div>

            <div class="d-flex justify-content-end mt-24">
                <button type="submit" class="btn btn-primary px-32 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                    Add Fuel Type
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ── Fuel Type List ── -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0">All Fuel Types</h6>
        <input type="text"
               id="bikeTypeSearch"
               class="form-control form-control-sm"
               placeholder="Search by name..."
               style="width:220px;">
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="bikeTypeTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width:60px;">#</th>
                        <th style="width:90px;">Icon</th>
                        <th>Name</th>
                        <th class="text-center" style="width:120px;">Status</th>
                        <th class="text-center" style="width:110px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bikeTypes as $index => $bikeType)
                    <tr data-search="{{ strtolower($bikeType->name) }}">
                        <td class="ps-3 text-secondary-light">{{ $bikeTypes->firstItem() + $index }}</td>
                        <td>
                            @if($bikeType->icon_image)
                                <img src="{{ asset($bikeType->icon_image) }}" alt="{{ $bikeType->name }}" style="width:36px;height:36px;object-fit:contain;">
                            @else
                                <div class="rounded d-flex align-items-center justify-content-center bg-light" style="width:36px;height:36px;">
                                    <iconify-icon icon="solar:folder-outline" class="text-secondary-light"></iconify-icon>
                                </div>
                            @endif
                        </td>
                        <td class="fw-medium">{{ $bikeType->name }}</td>
                        <td class="text-center">
                            @if($bikeType->status === 'active')
                                <span class="badge bg-success-light text-success px-3 py-2">Active</span>
                            @else
                                <span class="badge bg-danger-light text-danger px-3 py-2">Inactive</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('categories.edit', $bikeType) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <iconify-icon icon="solar:pen-outline"></iconify-icon>
                                </a>
                                <form action="{{ route('categories.destroy', $bikeType) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this Fuel Type and all its brands?');">
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
                        <td colspan="5" class="text-center py-5">
                            <iconify-icon icon="solar:folder-outline" style="font-size:3rem;" class="text-secondary-light"></iconify-icon>
                            <p class="text-secondary-light mt-2 mb-0">No Fuel Types yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bikeTypes->hasPages())
        <div class="d-flex justify-content-center mt-4 p-3">
            {{ $bikeTypes->links() }}
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
    padding: 1rem;
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
    height: 90px;
    object-fit: contain;
    background: var(--bs-light-bg);
}

.remove-image {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 26px;
    height: 26px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.icon-2x { font-size: 2rem; }

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
    // Live icon preview
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

    // Table search
    $('#bikeTypeSearch').on('input', function() {
        const search = $(this).val().toLowerCase();
        $('#bikeTypeTable tbody tr').each(function() {
            const match = !search || ($(this).data('search') || '').toString().includes(search);
            $(this).toggle(match);
        });
    });
});
</script>
@endpush