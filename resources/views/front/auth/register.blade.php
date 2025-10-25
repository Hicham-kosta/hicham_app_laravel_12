@extends('front.layout.layout')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4">
          <h3 class="text-center mb-4">Create an Account</h3>

          <div id="registerSuccess"></div>

          <form id="registerForm">
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
            </div>

            {{-- Confirm Password --}}
            <div class="mb-3">
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

            {{-- User Type --}}
            <div class="mb-3">
              <label class="form-label d-block">Register as</label>
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

            {{-- Submit Button --}}
            <div class="d-grid">
              <button 
                type="submit" 
                id="registerButton" 
                class="btn btn-success btn-lg rounded-pill"
              >
                Register
              </button>
            </div>
          </form>

          <div class="text-center mt-3">
            <a href="{{ route('user.login') }}" class="text-decoration-none">
              Already have an account? Login here
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
