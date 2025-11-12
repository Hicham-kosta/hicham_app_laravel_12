@extends('front.layout.layout')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Page Header Start -->
<div class="container-fluid bg-secondary mb-5">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 150px">
        <h1 class="font-weight-semi-bold text-uppercase mb-3">Change Password</h1>
        <div class="d-inline-flex">
            <p class="m-0"><a href="{{ url('/') }}">Home</a></p>
            <p class="m-0 px-2">-</p>
            <p class="m-0">Change Password</p>
        </div>
    </div>
</div>
<!-- Page Header End -->

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg rounded-4 border-0">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">Change Your Password</h3>
                    <p class="text-muted text-center mb-4">Update your password to keep your account secure</p>

                    {{-- Success/Error Messages --}}
                    <div id="changePasswordSuccess">
                        @if (session('success'))
                            <div class="alert alert-success border-0 rounded-3">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            </div>
                        @endif
                    </div>

                    <form id="changePasswordForm" novalidate>
                        @csrf

                        {{-- Email (Read-only) --}}
                        <div class="mb-4">
                            <label for="userEmail" class="form-label">Your Email</label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="userEmail" 
                                value="{{ $user->email }}" 
                                readonly
                            >
                            <div class="form-text text-muted">
                                <small><i class="fas fa-info-circle me-1"></i>Your account email</small>
                            </div>
                        </div>

                        {{-- Current Password --}}
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                            <input 
                                type="password" 
                                class="form-control" 
                                name="current_password" 
                                id="current_password" 
                                placeholder="Enter your current password" 
                                required
                            >
                            <p class="help-block text-danger" data-error-for="current_password"></p>
                        </div>

                        {{-- New Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <input 
                                type="password" 
                                class="form-control" 
                                name="password" 
                                id="password" 
                                placeholder="Enter new password" 
                                required
                            >
                            <div class="form-text text-muted">
                                <small><i class="fas fa-shield-alt me-1"></i>Password must be at least 8 characters with mixed case, numbers, and symbols</small>
                            </div>
                            <p class="help-block text-danger" data-error-for="password"></p>
                        </div>

                        {{-- Confirm New Password --}}
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <input 
                                type="password" 
                                class="form-control" 
                                name="password_confirmation" 
                                id="password_confirmation" 
                                placeholder="Confirm new password" 
                                required
                            >
                            <p class="help-block text-danger" data-error-for="password_confirmation"></p>
                        </div>

                        {{-- Password Requirements --}}
                        <div class="alert alert-info border-0 rounded-3 mb-4">
                            <small class="d-block mb-2"><strong><i class="fas fa-list-check me-2"></i>Password Requirements:</strong></small>
                            <small class="d-block mb-1">• Minimum 8 characters long</small>
                            <small class="d-block mb-1">• Uppercase and lowercase letters</small>
                            <small class="d-block">• Numbers and special characters</small>
                        </div>

                        {{-- Submit Button --}}
                        <div class="d-grid">
                            <button 
                                type="submit" 
                                id="changePasswordBtn" 
                                class="btn btn-primary btn-lg rounded-pill"
                            >
                                <i class="fas fa-key me-2"></i>Change Password
                            </button>
                        </div>
                    </form>

                    {{-- Back to Account Link --}}
                    <div class="text-center mt-4">
                        <a href="{{ route('user.account') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-2"></i>Back to Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.card-body {
    padding: 2.5rem !important;
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 12px 16px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
}

.form-control[readonly] {
    background-color: #f8f9fa;
    border-color: #e9ecef;
    color: #6c757d;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    padding: 12px 24px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

.btn-primary:disabled {
    transform: none;
    box-shadow: none;
    opacity: 0.7;
}

.help-block {
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.text-decoration-none {
    color: #6c757d;
    transition: color 0.3s ease;
}

.text-decoration-none:hover {
    color: #007bff;
}

.alert {
    border-radius: 12px;
    border: none;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border-left: 4px solid #28a745;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f1b0b7 100%);
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    color: #0c5460;
    border-left: 4px solid #17a2b8;
}

.form-text {
    font-size: 0.8rem;
}

.text-danger {
    color: #dc3545 !important;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .card-body {
        padding: 2rem 1.5rem !important;
    }
    
    .btn {
        padding: 10px 20px;
    }
}

/* Password strength indicator */
.password-strength {
    height: 4px;
    border-radius: 2px;
    margin-top: 8px;
    transition: all 0.3s ease;
}

.strength-weak { background: #dc3545; width: 25%; }
.strength-fair { background: #ffc107; width: 50%; }
.strength-good { background: #17a2b8; width: 75%; }
.strength-strong { background: #28a745; width: 100%; }

/* Real-time password match indicator */
.password-match {
    border-color: #28a745 !important;
}

.password-mismatch {
    border-color: #dc3545 !important;
}
</style>

{{-- Font Awesome for icons --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection