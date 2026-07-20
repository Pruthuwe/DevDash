@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">My Profile</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">My Profile</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Main Content -->
<div class="row justify-content-center">
    <div class="col-xl-6">

        @if(session('success'))
            <div class="alert alert-success mb-24">{{ session('success') }}</div>
        @endif

        <!-- Account Info -->
        <div class="card mb-24">
            <div class="card-header">
                <h6 class="mb-0 text-lg">Account Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-16">
                    <span class="text-secondary-light text-sm">Name</span>
                    <p class="fw-semibold mb-0">{{ $user->name }}</p>
                </div>
                <div class="mb-16">
                    <span class="text-secondary-light text-sm">Email</span>
                    <p class="fw-semibold mb-0">{{ $user->email }}</p>
                </div>
                <div class="mb-0">
                    <span class="text-secondary-light text-sm">Account Type</span>
                    <p class="fw-semibold mb-0 text-capitalize">{{ $user->user_type }}{{ $user->role ? ' — ' . $user->role->name : '' }}</p>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 text-lg">Change Password</h6>
                <p class="text-secondary-light mb-0 mt-2">Update your own login password</p>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update-password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-24">
                        <label for="current_password" class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Current Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control radius-8" id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-24">
                        <label for="password" class="form-label fw-semibold text-primary-light text-sm mb-8">
                            New Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control radius-8" id="password" name="password" minlength="8" required>
                        @error('password')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-24">
                        <label for="password_confirmation" class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Confirm New Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control radius-8" id="password_confirmation" name="password_confirmation" minlength="8" required>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection
