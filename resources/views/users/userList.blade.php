@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Users</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Users</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-users')))
        <a href="{{ route('users.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
            Add User
        </a>
        @endif
    </div>
</div>

<!-- Users Table -->
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
                        <th class="text-start">Name</th>
                        <th class="text-start">Email</th>
                        <th class="text-start">User Type</th>
                        <th class="text-start">Role</th>
                        <th class="text-start">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="text-start">{{ $user->name }}</td>
                            <td class="text-start">{{ $user->email }}</td>
                            <td class="text-start">
                                <span class="badge bg-primary-subtle text-primary">{{ ucfirst($user->user_type) }}</span>
                            </td>
                            <td class="text-start">
                                @if($user->role)
                                    <span class="badge bg-info-subtle text-info">{{ $user->role->name }}</span>
                                @else
                                    <span class="text-secondary-light">No role assigned</span>
                                @endif
                            </td>
                            <td class="text-start">
                                @if($user->status == 'active')
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'edit-users')))
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">
                                        <iconify-icon icon="solar:pen-outline"></iconify-icon>
                                    </a>
                                    @endif
                                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'delete-users')))
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                            <iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection