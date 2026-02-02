@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Supplier List</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Supplier List</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-suppliers')))
        <a href="{{ route('add.supplier') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:user-plus-outline"></iconify-icon>
            Add Supplier
        </a>
        @endif
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-lg">Suppliers</h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-info-light text-info">{{ $suppliers->total() }} Suppliers</span>
                </div>
            </div>

            <div class="card-body">
                <!-- Display Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ri-checkbox-circle-line me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ri-error-warning-line me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Suppliers Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Mobile</th>
                                <th>Address</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Country</th>
                                <th>Zip Code</th>
                                <th>VAT NO</th>
                                <th>Fax</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $counter = 1; @endphp
                            @forelse($suppliers as $supplier)
                                <tr>
                                    <td>{{ $counter++ }}</td>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ $supplier->email ?? '-' }}</td>
                                    <td>{{ $supplier->phone ?? '-' }}</td>
                                    <td>{{ $supplier->mobile ?? '-' }}</td>
                                    <td>{{ $supplier->address ?? '-' }}</td>
                                    <td>{{ $supplier->city ?? '-' }}</td>
                                    <td>{{ $supplier->state ?? '-' }}</td>
                                    <td>{{ $supplier->country ?? '-' }}</td>
                                    <td>{{ $supplier->zip_code ?? '-' }}</td>
                                    <td>{{ $supplier->vat_no ?? '-' }}</td>
                                    <td>{{ $supplier->fax ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'edit-suppliers')))
                                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-warning">
                                                <iconify-icon icon="solar:pen-outline"></iconify-icon>
                                            </a>
                                            @endif
                                            @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'delete-suppliers')))
                                            <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this supplier?')">
                                                    <iconify-icon icon="ic:outline-delete"></iconify-icon>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <iconify-icon icon="solar:users-group-two-rounded-outline" style="font-size: 3rem;" class="text-secondary-light mb-3"></iconify-icon>
                                            <h6 class="text-secondary-light">No suppliers found</h6>
                                            <p class="text-secondary-light mb-3">Start by adding your first supplier</p>
                                            @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-suppliers')))
                                            <a href="{{ route('add.supplier') }}" class="btn btn-primary d-flex align-items-center gap-2">
                                                <iconify-icon icon="solar:user-plus-outline"></iconify-icon>
                                                Add Supplier
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $suppliers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
