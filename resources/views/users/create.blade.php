@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Add New User</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add New</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline"></iconify-icon>
            Back to Users
        </a>
    </div>
</div>

<!-- Main Content -->
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 text-lg">Create New User</h6>
                <p class="text-secondary-light mb-0 mt-2">Add a new user to the system</p>
            </div>

            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <!-- Name -->
                    <div class="mb-24">
                        <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control radius-8" id="name" name="name" placeholder="Enter full name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-24">
                        <label for="email" class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Email Address <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control radius-8" id="email" name="email" placeholder="Enter email address" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-24">
                        <label for="password" class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control radius-8" id="password" name="password" placeholder="Enter password" required>
                        @error('password')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-24">
                        <label for="password_confirmation" class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Confirm Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control radius-8" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" required>
                    </div>

                    <!-- User Type -->
                    <div class="mb-24">
                        <label for="user_type" class="form-label fw-semibold text-primary-light text-sm mb-8">
                            User Type <span class="text-danger">*</span>
                        </label>
                        <select class="form-control radius-8" id="user_type" name="user_type" required>
                            <option value="">Select user type</option>
                            <option value="admin" {{ old('user_type') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ old('user_type') == 'user' ? 'selected' : '' }}>User</option>
                        </select>
                        @error('user_type')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-24">
                        <label for="status" class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select class="form-control radius-8" id="status" name="status" required>
                            <option value="">Select status</option>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection