@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Categories</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Categories</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('add.category') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
            Add Category
        </a>
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
                                    <a href="{{ route('categories.show', $category) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                    </a>
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
                                    <a href="{{ route('categories.show', $subcategory) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                    </a>
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

<script>
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

@endsection