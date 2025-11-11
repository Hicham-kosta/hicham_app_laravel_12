@extends('front.layout.layout')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-lg rounded-4 border-0">
        <div class="card-body p-4">
          <h3 class="text-center mb-4">Welcome Back</h3>
          <p class="text-muted text-center mb-4">Sign in to your account to continue</p>

          {{-- Success/Error Messages --}}
          <div id="loginSuccess"></div>

          <form id="loginForm" novalidate>
            @csrf

            {{-- Email --}}
            <div class="mb-3">
              <label for="loginEmail" class="form-label">Email Address</label>
              <input 
                type="email" 
                id="loginEmail" 
                class="form-control" 
                name="email" 
                placeholder="Enter your email" 
                required
              >
              <small class="help-block text-danger" data-error-for="email"></small>
            </div>

            {{-- Password --}}
            <div class="mb-3">
              <label for="loginPassword" class="form-label">Password</label>
              <input 
                type="password" 
                id="loginPassword" 
                class="form-control" 
                name="password" 
                placeholder="Enter your password" 
                required
              >
              <small class="help-block text-danger" data-error-for="password"></small>
            </div>

            {{-- User Type --}}
            <div class="mb-4">
              <label class="form-label d-block">Login as</label>
              <div class="form-check form-check-inline">
                <input 
                  class="form-check-input" 
                  type="radio" 
                  name="user_type" 
                  id="userTypeCustomer" 
                  value="Customer" 
                  checked
                >
                <label class="form-check-label" for="userTypeCustomer">Customer</label>
              </div>
              <div class="form-check form-check-inline">
                <input 
                  class="form-check-input" 
                  type="radio" 
                  name="user_type" 
                  id="userTypeVendor" 
                  value="Vendor"
                >
                <label class="form-check-label" for="userTypeVendor">Vendor</label>
              </div>
              <small class="help-block text-danger" data-error-for="user_type"></small>
            </div>

            {{-- Remember Me --}}
            <div class="mb-4">
              <div class="form-check">
                <input 
                  class="form-check-input" 
                  type="checkbox" 
                  name="remember" 
                  id="rememberMe"
                >
                <label class="form-check-label" for="rememberMe">
                  Remember me
                </label>
              </div>
            </div>

            {{-- Submit Button --}}
            <div class="d-grid">
              <button 
                type="submit" 
                id="loginButton" 
                class="btn btn-primary btn-lg rounded-pill"
              >
                Sign In
              </button>
            </div>
          </form>

          {{-- Forgot Password Link --}}
          <div class="text-center mt-4">
            <a href="{{ route('user.password.forgot') }}" class="text-decoration-none d-block mb-2">
              <i class="fas fa-key me-2"></i>Forgot your password?
            </a>
          </div>

          {{-- Register Link --}}
          <div class="text-center mt-3">
            <a href="{{ route('user.register') }}" class="text-decoration-none">
              <i class="fas fa-user-plus me-2"></i>Don't have an account? Register here
            </a>
          </div>

          {{-- Divider --}}
          <div class="position-relative my-4">
            <hr>
            <div class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted">
              <small>Or continue with</small>
            </div>
          </div>

          {{-- Social Login (Optional) --}}
          <div class="d-grid gap-2">
            <button type="button" class="btn btn-outline-secondary rounded-pill">
              <i class="fab fa-google me-2"></i>Continue with Google
            </button>
            <button type="button" class="btn btn-outline-secondary rounded-pill">
              <i class="fab fa-facebook me-2"></i>Continue with Facebook
            </button>
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

.btn-outline-secondary {
  border: 2px solid #e9ecef;
  padding: 10px 24px;
  transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
  border-color: #007bff;
  transform: translateY(-1px);
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
}

.alert-danger {
  background: linear-gradient(135deg, #f8d7da 0%, #f1b0b7 100%);
  color: #721c24;
}

.form-check-input:checked {
  background-color: #007bff;
  border-color: #007bff;
}

.position-relative hr {
  border-color: #e9ecef;
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
</style>

{{-- Font Awesome for icons --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

@endsection