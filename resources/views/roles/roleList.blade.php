@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Roles</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Roles</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-roles')))
        <a href="{{ route('roles.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
            Add Role
        </a>
        @endif
    </div>
</div>

<!-- Roles Table -->
<div class="card">
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
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="text-start">Role Name</th>
                        <th class="text-start">Permissions</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td class="text-start">{{ $role->name }}</td>
                            <td class="text-start">
                                @if($role->permissions->count() > 0)
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($role->permissions->take(3) as $permission)
                                            <span class="badge bg-primary-subtle text-primary">{{ $permission->name }}</span>
                                        @endforeach
                                        @if($role->permissions->count() > 3)
                                            <span class="badge bg-secondary">+{{ $role->permissions->count() - 3 }} more</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-secondary-light">No permissions</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'edit-roles')))
                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-outline-primary">
                                        <iconify-icon icon="solar:pen-outline"></iconify-icon>
                                    </a>
                                    @endif
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'delete-roles')))
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                            <iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No roles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection