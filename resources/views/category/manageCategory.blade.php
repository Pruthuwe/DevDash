@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Category Management</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Categories</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-categories')))
        <a href="{{ route('add.subcategory') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-folder-outline"></iconify-icon>
            Add Subcategory
        </a>
        <a href="{{ route('add.category') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
            Add Category
        </a>
        @endif
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-24">
    <div class="col-md-4 col-sm-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-secondary-light mb-1">Total Categories</h6>
                    <h4 class="mb-0">{{ $totalCategories }}</h4>
                </div>
                <div class="stat-icon bg-primary-light">
                    <iconify-icon icon="solar:folder-outline" class="text-primary"></iconify-icon>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <small class="text-secondary-light">Main categories</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-secondary-light mb-1">Subcategories</h6>
                    <h4 class="mb-0">{{ $totalSubcategories }}</h4>
                </div>
                <div class="stat-icon bg-success-light">
                    <iconify-icon icon="solar:folder-open-outline" class="text-success"></iconify-icon>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <small class="text-secondary-light">Child categories</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-secondary-light mb-1">Active</h6>
                    <h4 class="mb-0">{{ $activeCategories }}</h4>
                </div>
                <div class="stat-icon bg-info-light">
                    <iconify-icon icon="solar:check-circle-outline" class="text-info"></iconify-icon>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <small class="text-secondary-light">Active categories</small>
            </div>
        </div>
    </div>
</div>

<!-- Categories Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">All Categories</h6>
        <div class="d-flex gap-2">
            <input type="text" class="form-control" placeholder="Search categories..." style="width: 250px;">
            <button class="btn btn-outline-secondary">
                <iconify-icon icon="solar:filter-outline"></iconify-icon>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="text-start">Category</th>
                        <th class="text-center">Image</th>
                        <th class="text-start">Description</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        @if($category->parent_id === null)
                        <tr class="parent-category" data-category-id="{{ $category->id }}">
                            <td class="text-start">
                                <div>
                                    <h6 class="mb-0">{{ $category->name }}</h6>
                                    @if($category->children->count() > 0)
                                        <small class="text-secondary-light">{{ $category->children->count() }} subcategories</small>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                @if($category->thumbnail_image)
                                    <img src="{{ asset($category->thumbnail_image) }}" alt="{{ $category->name }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-neutral-200 rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <iconify-icon icon="solar:folder-outline" class="text-secondary-light"></iconify-icon>
                                    </div>
                                @endif
                            </td>
                            <td class="text-start">
                                @if($category->description)
                                    {{ Str::limit($category->description, 50) }}
                                @else
                                    <span class="text-secondary-light">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($category->status === 'active')
                                    <span class="badge bg-success-light text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-light text-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    @if($category->children->count() > 0)
                                        <button type="button" class="btn btn-sm btn-outline-secondary category-toggle" data-category-id="{{ $category->id }}" title="Toggle Subcategories">
                                            <iconify-icon icon="solar:alt-arrow-down-outline" class="category-arrow" data-category-id="{{ $category->id }}"></iconify-icon>
                                        </button>
                                    @endif
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-categories')))
                                    <button type="button" class="btn btn-sm btn-outline-info" title="Add new Brand" data-parent-id="{{ $category->id }}" data-parent-name="{{ $category->name }}" onclick="openSubcategoryModal(this)">
                                        <iconify-icon icon="solar:add-folder-outline"></iconify-icon>
                                    </button>
                                    @endif
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'edit-categories')))
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <iconify-icon icon="solar:pen-outline"></iconify-icon>
                                    </a>
                                    @endif
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'delete-categories')))
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Category and Subcategories" onclick="return confirm('Are you sure you want to delete this category and all its subcategories? This action cannot be undone.')">
                                            <iconify-icon icon="ic:outline-delete"></iconify-icon>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @foreach($category->children as $subcategory)
                        <tr class="subcategory-row" data-parent-id="{{ $category->id }}" style="display: none;">
                            <td class="text-start">
                                <div class="ms-4">
                                    <span>{{ $subcategory->name }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($subcategory->thumbnail_image)
                                    <img src="{{ asset($subcategory->thumbnail_image) }}" alt="{{ $subcategory->name }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-neutral-200 rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <iconify-icon icon="solar:folder-outline" class="text-secondary-light"></iconify-icon>
                                    </div>
                                @endif
                            </td>
                            <td class="text-start">
                                @if($subcategory->description)
                                    {{ Str::limit($subcategory->description, 50) }}
                                @else
                                    <span class="text-secondary-light">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($subcategory->status === 'active')
                                    <span class="badge bg-success-light text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-light text-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'edit-categories')))
                                    <a href="{{ route('categories.edit', $subcategory) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <iconify-icon icon="solar:pen-outline"></iconify-icon>
                                    </a>
                                    @endif
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'delete-categories')))
                                    <form method="POST" action="{{ route('categories.destroy', $subcategory) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Subcategory" onclick="return confirm('Are you sure you want to delete this subcategory?')">
                                            <iconify-icon icon="solar:trash-outline"></iconify-icon>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <iconify-icon icon="solar:folder-outline" style="font-size: 4rem;" class="text-secondary-light mb-3"></iconify-icon>
                                <h5 class="text-secondary-light">No Categories Found</h5>
                                <p class="text-secondary-light mb-4">Start by adding your first category</p>
                                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                                    <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                                    Add Your First Category
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add Subcategory Modal -->
<div class="modal fade" id="subcategoryModal" tabindex="-1" aria-labelledby="subcategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subcategoryModalLabel">Add Subcategory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="subcategoryForm">
                @csrf
                <div class="modal-body">
                    <style>
                        #subcategoryModal input:not([readonly]),
                        #subcategoryModal textarea:not([readonly]),
                        #subcategoryModal select:not([disabled]) {
                            pointer-events: auto !important;
                        }
                        #subcategoryModal .form-control {
                            cursor: text !important;
                        }
                    </style>
                    <div class="mb-3">
                        <label for="parent_category" class="form-label">Parent Category</label>
                        <input type="text" class="form-control" id="parent_category" readonly>
                        <input type="hidden" id="parent_id" name="parent_id">
                    </div>
                    <div class="mb-3">
                        <label for="subcategory_name" class="form-label">Subcategory Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="subcategory_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="subcategory_description" class="form-label">Description</label>
                        <textarea class="form-control" id="subcategory_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="status-toggle-wrap">
                            <label class="status-switch mb-0">
                                <input type="checkbox" id="modalStatusToggle" checked>
                                <span class="status-slider"></span>
                            </label>
                            <span class="status-toggle-label text-success fw-semibold" id="modalStatusToggleLabel">Active</span>
                        </div>
                        <input type="hidden" name="status" id="subcategory_status" value="active">
                        <div class="form-text">Active subcategories are visible to customers</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="subcategory_banner_image" class="form-label">Banner Image</label>
                            <input type="file" class="form-control" id="subcategory_banner_image" name="banner_image" accept="image/*">
                            <small class="text-secondary-light">Optional banner image</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="subcategory_thumbnail_image" class="form-label">Thumbnail Image</label>
                            <input type="file" class="form-control" id="subcategory_thumbnail_image" name="thumbnail_image" accept="image/*">
                            <small class="text-secondary-light">Optional thumbnail image</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="subcategory_icon_image" class="form-label">Icon Image</label>
                            <input type="file" class="form-control" id="subcategory_icon_image" name="icon_image" accept="image/*">
                            <small class="text-secondary-light">Optional icon image</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Subcategory</button>
                </div>
            </form>
        </div>  
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalStatusToggle = document.getElementById('modalStatusToggle');
    if (modalStatusToggle) {
        modalStatusToggle.addEventListener('change', function() {
            const input = document.getElementById('subcategory_status');
            const label = document.getElementById('modalStatusToggleLabel');
            if (this.checked) {
                input.value = 'active';
                label.textContent = 'Active';
                label.classList.remove('text-danger');
                label.classList.add('text-success');
            } else {
                input.value = 'inactive';
                label.textContent = 'Inactive';
                label.classList.remove('text-success');
                label.classList.add('text-danger');
            }
        });
    }
});
</script>

<script>
/* global FormData */

function openSubcategoryModal(button) {
    const parentId = button.getAttribute('data-parent-id');
    const parentName = button.getAttribute('data-parent-name');
    
    document.getElementById('parent_id').value = parentId;
    document.getElementById('parent_category').value = parentName;
    document.getElementById('subcategory_name').value = '';
    document.getElementById('subcategory_description').value = '';
    document.getElementById('modalStatusToggle').checked = true;
    document.getElementById('subcategory_status').value = 'active';
    document.getElementById('modalStatusToggleLabel').textContent = 'Active';
    document.getElementById('modalStatusToggleLabel').classList.remove('text-danger');
    document.getElementById('modalStatusToggleLabel').classList.add('text-success');
    document.getElementById('subcategory_banner_image').value = '';
    document.getElementById('subcategory_thumbnail_image').value = '';
    document.getElementById('subcategory_icon_image').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('subcategoryModal'), {
        backdrop: 'static',
        keyboard: false
    });
    modal.show();
    
    // Ensure inputs are focusable after modal is shown
    setTimeout(() => {
        document.getElementById('subcategory_name').focus();
        // Remove any potential readonly/disabled states
        document.getElementById('subcategory_name').removeAttribute('readonly');
        document.getElementById('subcategory_description').removeAttribute('readonly');
    }, 300);
}

document.getElementById('subcategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new window.FormData(this);
    
    fetch('{{ route("store.subcategory") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('subcategoryModal'));
            modal.hide();
            
            // Show success message
            alert('Subcategory added successfully!');
            
            // Reload page to show new subcategory
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to add subcategory'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the subcategory.');
    });
});
</script>

<script>
// Toggle subcategory rows under each parent category
document.addEventListener('DOMContentLoaded', function() {
    // Add click event listeners to category toggle buttons
    document.querySelectorAll('.category-toggle').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const categoryId = this.getAttribute('data-category-id');
            toggleSubcategories(categoryId);
        });
    });
});

function toggleSubcategories(categoryId) {
    const subcategoryRows = document.querySelectorAll(`.subcategory-row[data-parent-id="${categoryId}"]`);
    const arrowIcon = document.querySelector(`.category-arrow[data-category-id="${categoryId}"]`);
    
    let isVisible = false;
    
    // Check if subcategories are currently visible
    subcategoryRows.forEach(row => {
        if (row.style.display !== 'none') {
            isVisible = true;
        }
    });
    
    if (isVisible) {
        // Hide subcategories
        subcategoryRows.forEach(row => {
            row.style.display = 'none';
        });
        if (arrowIcon) {
            arrowIcon.setAttribute('icon', 'solar:alt-arrow-down-outline');
        }
    } else {
        // Show subcategories
        subcategoryRows.forEach(row => {
            row.style.display = 'table-row';
        });
        if (arrowIcon) {
            arrowIcon.setAttribute('icon', 'solar:alt-arrow-up-outline');
        }
    }
}
</script>

@push('styles')
<style>
.stat-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: none;
    border-radius: 12px;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.bg-primary-light {
    background-color: var(--primary-light) !important;
}

.bg-success-light {
    background-color: var(--success-surface) !important;
}

.bg-info-light {
    background-color: var(--info-surface) !important;
}

/* Status Toggle Switch */
.status-toggle-wrap {
    display: flex;
    align-items: center;
    gap: 0.75rem;
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

@endsection