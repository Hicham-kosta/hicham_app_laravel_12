@extends('front.layout.layout')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-lg rounded-4 border-0">
        <div class="card-body p-4">
          <h3 class="text-center mb-4">Create Your Account</h3>
          <p class="text-muted text-center mb-4">Join us today and start your journey</p>

          {{-- Success/Error Messages --}}
          <div id="registerSuccess"></div>

          <form id="registerForm" novalidate>
            @csrf

            {{-- Name --}}
            <div class="mb-3">
              <label for="name" class="form-label">Full Name</label>
              <input 
                type="text" 
                id="name" 
                class="form-control" 
                name="name" 
                placeholder="Enter your full name"
                required
              >
              <small class="help-block text-danger" data-error-for="name"></small>
            </div>

            {{-- Email --}}
            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input 
                type="email" 
                id="email" 
                class="form-control" 
                name="email" 
                placeholder="Enter your email"
                required
              >
              <small class="help-block text-danger" data-error-for="email"></small>
            </div>

            {{-- Password --}}
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input 
                type="password" 
                id="password" 
                class="form-control" 
                name="password" 
                placeholder="Create a password"
                required
              >
              <small class="help-block text-danger" data-error-for="password"></small>
              <small class="form-text text-muted">Password must be at least 8 characters long.</small>
            </div>

            {{-- Confirm Password --}}
            <div class="mb-4">
              <label for="password_confirmation" class="form-label">Confirm Password</label>
              <input 
                type="password" 
                id="password_confirmation" 
                class="form-control" 
                name="password_confirmation" 
                placeholder="Confirm your password"
                required
              >
              <small class="help-block text-danger" data-error-for="password_confirmation"></small>
            </div>

            {{-- Password Requirements --}}
            <div class="alert alert-info border-0 rounded-3 mb-4">
              <small class="d-block mb-1"><strong>Password Requirements:</strong></small>
              <small class="d-block mb-1">• At least 8 characters</small>
              <small class="d-block">• Use a mix of letters, numbers, and symbols for security</small>
            </div>

            {{-- User Type --}}
            <div class="mb-4">
              <label class="form-label d-block">I want to register as</label>
              <div class="form-check form-check-inline">
                <input 
                  class="form-check-input" 
                  type="radio" 
                  name="user_type" 
                  id="userTypeCustomer" 
                  value="Customer" 
                  checked
                >
                <label class="form-check-label" for="userTypeCustomer">
                  <i class="fas fa-user me-1"></i> Customer
                </label>
              </div>
              <div class="form-check form-check-inline">
                <input 
                  class="form-check-input" 
                  type="radio" 
                  name="user_type" 
                  id="userTypeVendor" 
                  value="Vendor"
                >
                <label class="form-check-label" for="userTypeVendor">
                  <i class="fas fa-store me-1"></i> Vendor
                </label>
              </div>
              <small class="help-block text-danger" data-error-for="user_type"></small>
            </div>

            {{-- Terms and Conditions --}}
            <div class="mb-4">
              <div class="form-check">
                <input 
                  class="form-check-input" 
                  type="checkbox" 
                  name="terms" 
                  id="terms"
                  required
                >
                <label class="form-check-label" for="terms">
                  I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and <a href="#" class="text-decoration-none">Privacy Policy</a>
                </label>
                <small class="help-block text-danger" data-error-for="terms"></small>
              </div>
            </div>

            {{-- Submit Button --}}
            <div class="d-grid">
              <button 
                type="submit" 
                id="registerButton" 
                class="btn btn-primary btn-lg rounded-pill"
              >
                <i class="fas fa-user-plus me-2"></i>Create Account
              </button>
            </div>
          </form>

          {{-- Divider --}}
          <div class="position-relative my-4">
            <hr>
            <div class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted">
              <small>Already have an account?</small>
            </div>
          </div>

          {{-- Login Link --}}
          <div class="text-center">
            <a href="{{ route('user.login') }}" class="btn btn-outline-secondary rounded-pill w-100">
              <i class="fas fa-sign-in-alt me-2"></i>Sign In to Your Account
            </a>
          </div>

          {{-- Social Register (Optional) --}}
          <div class="mt-4">
            <div class="d-grid gap-2">
              <button type="button" class="btn btn-outline-secondary rounded-pill">
                <i class="fab fa-google me-2"></i>Sign up with Google
              </button>
              <button type="button" class="btn btn-outline-secondary rounded-pill">
                <i class="fab fa-facebook me-2"></i>Sign up with Facebook
              </button>
            </div>
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

.alert-info {
  background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
  color: #0c5460;
  border-left: 4px solid #17a2b8;
}

.form-check-input:checked {
  background-color: #007bff;
  border-color: #007bff;
}

.position-relative hr {
  border-color: #e9ecef;
}

.form-text {
  font-size: 0.8rem;
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

<script>
document.addEventListener('DOMContentLoaded', function() {
  const passwordInput = document.getElementById('password');
  const confirmInput = document.getElementById('password_confirmation');
  
  // Real-time password confirmation check
  if (passwordInput && confirmInput) {
    function checkPasswordMatch() {
      if (passwordInput.value && confirmInput.value) {
        if (passwordInput.value !== confirmInput.value) {
          confirmInput.classList.add('password-mismatch');
          confirmInput.classList.remove('password-match');
        } else {
          confirmInput.classList.add('password-match');
          confirmInput.classList.remove('password-mismatch');
        }
      } else {
        confirmInput.classList.remove('password-match', 'password-mismatch');
      }
    }
    
    passwordInput.addEventListener('input', checkPasswordMatch);
    confirmInput.addEventListener('input', checkPasswordMatch);
  }
});
</script>

@endsection