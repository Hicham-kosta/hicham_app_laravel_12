@extends('front.layout.layout')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-lg rounded-4 border-0">
        <div class="card-body p-4">
          <h3 class="text-center mb-4">Create New Password</h3>
          <p class="text-muted text-center mb-4">Enter your new password below and confirm it to reset your password.</p>

          {{-- Success/Error Messages --}}
          <div id="resetSuccess"></div>

          <form name="resetForm" id="resetForm" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            {{-- Email --}}
            <div class="mb-3">
              <label for="resetEmail" class="form-label">Email Address</label>
              <input 
                type="email" 
                id="resetEmail" 
                class="form-control" 
                name="email" 
                value="{{ $email ?? old('email') }}"
                placeholder="Enter your email" 
                required
              >
              <small class="help-block text-danger" data-error-for="email"></small>
            </div>

            {{-- New Password --}}
            <div class="mb-3">
              <label for="resetPassword" class="form-label">New Password</label>
              <input 
                type="password" 
                id="resetPassword" 
                class="form-control" 
                name="password" 
                placeholder="Enter new password" 
                required
              >
              <small class="help-block text-danger" data-error-for="password"></small>
              <small class="form-text text-muted">Password must be at least 8 characters long.</small>
            </div>

            {{-- Confirm Password --}}
            <div class="mb-4">
              <label for="resetConfirm" class="form-label">Confirm New Password</label>
              <input 
                type="password" 
                id="resetConfirm" 
                class="form-control" 
                name="password_confirmation" 
                placeholder="Confirm new password" 
                required
              >
              <small class="help-block text-danger" data-error-for="password_confirmation"></small>
            </div>

            {{-- Password Requirements --}}
            <div class="alert alert-info border-0 rounded-3 mb-4">
              <small class="d-block mb-1"><strong>Password Requirements:</strong></small>
              <small class="d-block mb-1">• At least 8 characters</small>
              <small class="d-block">• Use a mix of letters, numbers, and symbols</small>
            </div>

            {{-- Submit Button --}}
            <div class="d-grid">
              <button 
                type="submit" 
                id="resetBtn" 
                class="btn btn-primary btn-lg rounded-pill"
              >
                Reset Password
              </button>
            </div>
          </form>

          {{-- Back to Login Link --}}
          <div class="text-center mt-4">
            <a href="{{ route('user.login') }}" class="text-decoration-none">
              <i class="fas fa-arrow-left me-2"></i>Back to Login
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

.form-text {
  font-size: 0.8rem;
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
</style>

{{-- Font Awesome for icons --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
  const passwordInput = document.getElementById('resetPassword');
  const confirmInput = document.getElementById('resetConfirm');
  
  // Real-time password confirmation check
  if (passwordInput && confirmInput) {
    function checkPasswordMatch() {
      if (passwordInput.value && confirmInput.value) {
        if (passwordInput.value !== confirmInput.value) {
          confirmInput.style.borderColor = '#dc3545';
        } else {
          confirmInput.style.borderColor = '#28a745';
        }
      } else {
        confirmInput.style.borderColor = '#e9ecef';
      }
    }
    
    passwordInput.addEventListener('input', checkPasswordMatch);
    confirmInput.addEventListener('input', checkPasswordMatch);
  }
});
</script>

@endsection