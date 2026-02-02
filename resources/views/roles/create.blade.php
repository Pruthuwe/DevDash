@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Add New Role</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add New</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline"></iconify-icon>
            Back to Roles
        </a>
    </div>
</div>

<!-- Main Content -->
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 text-lg">Create New Role</h6>
                <p class="text-secondary-light mb-0 mt-2">Define a new role and assign permissions</p>
            </div>

            <div class="card-body">
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf

                    <!-- Role Name -->
                    <div class="mb-24">
                        <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Role Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control radius-8" id="name" name="name" placeholder="Enter role name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Permissions -->
                    <div class="mb-24">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Permissions
                        </label>
                        <div class="row">
                            @foreach($permissions as $module => $perms)
                                <div class="col-md-6 mb-16">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 text-capitalize">{{ $module }}</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach($perms as $permission)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm-{{ $permission->id }}">
                                                    <label class="form-check-label text-capitalize" for="perm-{{ $permission->id }}">
                                                        {{ explode('-', $permission->name)[0] }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('permissions')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection