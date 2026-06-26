@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
<section class="auth bg-base d-flex flex-wrap">
    {{-- Left Side Image Section --}}
    <div class="auth-left d-lg-block d-none">
        <div class="d-flex align-items-center flex-column h-100 justify-content-center">
            <img src="{{ asset('assets/images/auth/370x530 (1).png') }}" alt="Authentication">
        </div>
    </div>

    {{-- Right Side Form Section --}}
    <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
        <div class="max-w-464-px mx-auto w-100">
            {{-- Header Section --}}
            <div>
                <a href="{{ url('/') }}" class="mb-40 max-w-350-px d-flex justify-content-center">
    <img
        src="{{ asset('assets/images/logo-icon.png') }}"
        alt="Logo"
        style="width:100px; height:auto;"
    >
</a>
                <h4 class="mb-12">Sign In to your Account</h4>
                <p class="mb-32 text-secondary-light text-lg">Welcome back! please enter your detail</p>
            </div>

            {{-- Success/Error Messages --}}
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

            {{-- Login Form --}}
            <form action="{{ route('login') }}" method="POST" class="form">
                @csrf
                
                {{-- Email Field --}}
                <div class="icon-field mb-16">
                    <span class="icon top-50 translate-middle-y">
                        <iconify-icon icon="mage:email"></iconify-icon>
                    </span>
                    <input type="email" 
                           name="email" 
                           class="form-control h-56-px bg-neutral-50 radius-12 @error('email') is-invalid @enderror" 
                           placeholder="Email"
                           value="{{ old('email') }}"
                           required>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                {{-- Password Field --}}
                <div class="position-relative mb-20">
                    <div class="icon-field">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                        </span>
                        <input type="password" 
                               name="password"
                               class="form-control h-56-px bg-neutral-50 radius-12 @error('password') is-invalid @enderror"
                               id="your-password" 
                               placeholder="Password"
                               required>
                    </div>
                    <span class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                          data-toggle="#your-password"></span>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                {{-- Remember Me & Forgot Password --}}
                <div class="d-flex justify-content-between gap-2">
                    <div class="form-check style-check d-flex align-items-center">
                        <input class="form-check-input border border-neutral-300" 
                               type="checkbox" 
                               name="remember"
                               id="remember"
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>
                    <a href="{{ route('password.request') }}" class="text-primary-600 fw-medium">
                        Forgot Password?
                    </a>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32">
                    Sign In
                </button>

                {{-- Additional Links (Optional) --}}
                @if (Route::has('register'))
                    <div class="mt-32 text-center">
                        <p class="text-secondary-light">
                            Don't have an account? 
                            <a href="{{ route('register') }}" class="text-primary-600 fw-medium">Sign Up</a>
                        </p>
                    </div>
                @endif
            </form>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    /* Hide browser's built-in password toggle icons */
    input[type="password"]::-ms-reveal {
        display: none;
    }
    input[type="password"]::-webkit-password-toggle {
        -webkit-appearance: none;
    }
    input[type="password"]::-webkit-credentials-auto-fill-button {
        display: none;
    }
</style>
@endpush

@push('scripts')
<script>
    // Password Show Hide Functionality
    function initializePasswordToggle(toggleSelector) {
        $(toggleSelector).on('click', function() {
            $(this).toggleClass("ri-eye-off-line");
            var input = $($(this).attr("data-toggle"));
            if (input.attr("type") === "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    }
    
    // Initialize on document ready
    $(document).ready(function() {
        initializePasswordToggle('.toggle-password');
    });
</script>
@endpush
