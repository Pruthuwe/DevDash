@extends('layouts.main')

@section('content')

<!-- ── Page Header ── -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">View All Categories</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Categories</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('add.category') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
            Add Fuel Type
        </a>
        <a href="{{ route('add.subcategory') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
            Add Brand
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<!-- ── Stat Cards ── -->
<div class="row g-3 mb-24">
    <div class="col-md-4 col-sm-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-secondary-light mb-1">Fuel Types</h6>
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
                    <h6 class="text-secondary-light mb-1">Brands</h6>
                    <h4 class="mb-0">{{ $totalSubcategories }}</h4>
                </div>
                <div class="stat-icon bg-success-light">
                    <iconify-icon icon="solar:folder-open-outline" class="text-success"></iconify-icon>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <small class="text-secondary-light">Brands under Fuel Types</small>
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


{{-- ═══════════════════════════════════════════════════════════
     SINGLE TABLE — All Categories (Brands grouped under Fuel Type)
     Columns: # | Fuel Type | Brand Name | Icon | Status
══════════════════════════════════════════════════════════════ --}}
@php
    $bikeTypes = $categories->filter(fn($c) => $c->parent_id === null);
@endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0">All Categories</h6>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <!-- Search -->
            <input type="text"
                   id="categorySearch"
                   class="form-control form-control-sm"
                   placeholder="Search brand or Fuel Type..."
                   style="width:220px;">
            <!-- Filter by Fuel Type -->
            <select id="bikeTypeFilter" class="form-select form-select-sm" style="width:170px;">
                <option value="">All Fuel Types</option>
                @foreach($bikeTypes as $bt)
                    <option value="{{ $bt->id }}">{{ $bt->name }}</option>
                @endforeach
            </select>
            <!-- Filter by brand -->
            <select id="brandFilter" class="form-select form-select-sm" style="width:170px;">
                <option value="">All Brands</option>
                @foreach($allBrands as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="categoryTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width:60px;">#</th>
                        <th style="width:220px;">Fuel Type</th>
                        <th>Brand Name</th>
                        <th class="text-center" style="width:90px;">Icon</th>
                        <th class="text-center" style="width:120px;">Status</th>
                        <th class="text-center" style="width:110px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rowNum = 1; @endphp
                    @forelse($bikeTypes as $bikeType)
                        @php $brands = $bikeType->children; @endphp
                        @if($brands->count() > 0)
                            @foreach($brands as $brand)
                            <tr data-bike-type-id="{{ $bikeType->id }}"
                                data-brand-id="{{ $brand->id }}"
                                data-search="{{ strtolower($bikeType->name . ' ' . $brand->name) }}">
                                <td class="ps-3 text-secondary-light">{{ $rowNum++ }}</td>

                                {{-- Fuel Type (only show on first brand row of each type) --}}
                                <td>
                                    @if($loop->first)
                                    <div class="d-flex align-items-center gap-2">
                                        @if($bikeType->icon_image)
                                            <img src="{{ asset($bikeType->icon_image) }}"
                                                 alt="{{ $bikeType->name }}"
                                                 style="width:28px;height:28px;object-fit:contain;">
                                        @else
                                            <div class="rounded d-flex align-items-center justify-content-center bg-light"
                                                 style="width:28px;height:28px;">
                                                <iconify-icon icon="solar:folder-outline" class="text-secondary-light" style="font-size:14px;"></iconify-icon>
                                            </div>
                                        @endif
                                        <span class="fw-semibold text-primary">{{ $bikeType->name }}</span>
                                    </div>
                                    @else
                                        <span class="text-secondary-light ps-1" style="font-size:0.8rem;">↳</span>
                                    @endif
                                </td>

                                {{-- Brand Name --}}
                                <td class="fw-medium">{{ $brand->name }}</td>

                                {{-- Icon --}}
                                <td class="text-center">
                                    @if($brand->icon_image)
                                        <img src="{{ asset($brand->icon_image) }}"
                                             alt="{{ $brand->name }}"
                                             style="width:34px;height:34px;object-fit:contain;">
                                    @else
                                        <div class="rounded d-flex align-items-center justify-content-center bg-light mx-auto"
                                             style="width:34px;height:34px;">
                                            <iconify-icon icon="solar:tag-outline" class="text-secondary-light"></iconify-icon>
                                        </div>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="text-center">
                                    @if($brand->status === 'active')
                                        <span class="badge bg-success-light text-success px-3 py-2">Active</span>
                                    @else
                                        <span class="badge bg-danger-light text-danger px-3 py-2">Inactive</span>
                                    @endif
                                </td>

                                {{-- Action --}}
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
                            @endforeach
                        @else
                            {{-- Fuel Type exists but has no brands yet --}}
                            <tr data-bike-type-id="{{ $bikeType->id }}"
                                data-brand-id=""
                                data-search="{{ strtolower($bikeType->name) }}">
                                <td class="ps-3 text-secondary-light">{{ $rowNum++ }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($bikeType->icon_image)
                                            <img src="{{ asset($bikeType->icon_image) }}"
                                                 alt="{{ $bikeType->name }}"
                                                 style="width:28px;height:28px;object-fit:contain;">
                                        @else
                                            <div class="rounded d-flex align-items-center justify-content-center bg-light"
                                                 style="width:28px;height:28px;">
                                                <iconify-icon icon="solar:folder-outline" class="text-secondary-light" style="font-size:14px;"></iconify-icon>
                                            </div>
                                        @endif
                                        <span class="fw-semibold text-primary">{{ $bikeType->name }}</span>
                                    </div>
                                </td>
                                <td class="text-secondary-light fst-italic">No brands yet</td>
                                <td></td>
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
                                        <form action="{{ route('categories.destroy', $bikeType) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this Fuel Type?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <iconify-icon icon="ic:outline-delete"></iconify-icon>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <iconify-icon icon="solar:folder-outline"
                                          style="font-size:3rem;"
                                          class="text-secondary-light"></iconify-icon>
                            <p class="text-secondary-light mt-2 mb-0">No categories found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
        <div class="d-flex justify-content-center mt-4 p-3">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    function filterTable() {
        const search     = document.getElementById('categorySearch').value.toLowerCase();
        const filterType = document.getElementById('bikeTypeFilter').value;
        const filterBrand = document.getElementById('brandFilter').value;

        document.querySelectorAll('#categoryTable tbody tr').forEach(row => {
            const matchSearch = !search || row.getAttribute('data-search').includes(search);
            const matchType   = !filterType || row.getAttribute('data-bike-type-id') === filterType;
            const matchBrand  = !filterBrand || row.getAttribute('data-brand-id') === filterBrand;
            row.style.display = (matchSearch && matchType && matchBrand) ? '' : 'none';
        });
    }

    document.getElementById('categorySearch').addEventListener('input', filterTable);
    document.getElementById('bikeTypeFilter').addEventListener('change', filterTable);
    document.getElementById('brandFilter').addEventListener('change', filterTable);
});
</script>


@push('styles')
<style>
/* ── Stat cards ── */
.stat-card { transition: transform .2s, box-shadow .2s; border:none; border-radius:12px; }
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.1); }
.stat-icon { width:48px; height:48px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; }
.bg-primary-light { background-color: var(--primary-light) !important; }
.bg-success-light { background-color: var(--success-surface) !important; }
.bg-info-light    { background-color: var(--info-surface) !important; }

/* ── Table ── */
.table th { font-size:.8rem; text-transform:uppercase; letter-spacing:.04em; }
.table td { vertical-align:middle; }

/* ── Group separator between Fuel Types ── */
#categoryTable tbody tr:not([data-bike-type-id=""]) + tr[data-bike-type-id]:not(:first-child) td {
    border-top: 2px solid #e9ecef;
}
</style>
@endpush

@endsection