@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Edit Role</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
                <h6 class="mb-0 text-lg">Edit Role</h6>
                <p class="text-secondary-light mb-0 mt-2">Update role information and permissions</p>
            </div>

            <div class="card-body">
                <form action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Role Name -->
                    <div class="mb-24">
                        <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Role Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control radius-8" id="name" name="name" placeholder="Enter role name" value="{{ old('name', $role->name) }}" required>
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
                                        <div class="card-header">
                                            <h6 class="mb-0 text-capitalize">{{ $module }}</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach($perms as $permission)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm-{{ $permission->id }}"
                                                        {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
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
                        <button type="submit" class="btn btn-primary">Update Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const allCheckboxes = Array.from(document.querySelectorAll('.permission-checkbox'));
    const moduleSelectAlls = document.querySelectorAll('.module-select-all');
    const masterSelectAll = document.getElementById('selectAllPermissions');

    function checkboxesForModule(module) {
        return allCheckboxes.filter(cb => cb.dataset.module === module);
    }

    function syncModuleCheckbox(module) {
        const boxes = checkboxesForModule(module);
        const moduleAll = document.querySelector(`.module-select-all[data-module="${module}"]`);
        if (moduleAll) {
            moduleAll.checked = boxes.length > 0 && boxes.every(cb => cb.checked);
        }
    }

    function syncMasterCheckbox() {
        if (masterSelectAll) {
            masterSelectAll.checked = allCheckboxes.length > 0 && allCheckboxes.every(cb => cb.checked);
        }
    }

    allCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            syncModuleCheckbox(this.dataset.module);
            syncMasterCheckbox();
        });
    });

    moduleSelectAlls.forEach(moduleAll => {
        moduleAll.addEventListener('change', function () {
            checkboxesForModule(this.dataset.module).forEach(cb => cb.checked = this.checked);
            syncMasterCheckbox();
        });
    });

    if (masterSelectAll) {
        masterSelectAll.addEventListener('change', function () {
            allCheckboxes.forEach(cb => cb.checked = this.checked);
            moduleSelectAlls.forEach(moduleAll => moduleAll.checked = this.checked);
        });
    }

    Array.from(new Set(allCheckboxes.map(cb => cb.dataset.module))).forEach(syncModuleCheckbox);
    syncMasterCheckbox();
});
</script>

@endsection