@extends('front.layout.layout')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-lg rounded-4 border-0">
        <div class="card-body p-4">
          <h3 class="text-center mb-4">Login</h3>

          <div id="loginSuccess"></div>

          <form id="loginForm">
            @csrf

            {{-- Email --}}
            <div class="mb-3">
              <label for="loginEmail" class="form-label">Email</label>
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
            <div class="mb-3">
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

            {{-- Submit --}}
            <div class="d-grid">
              <button 
                type="submit" 
                id="loginButton" 
                class="btn btn-primary btn-lg rounded-pill"
              >
                Login
              </button>
            </div>
          </form>
          {{-- Forgot Password Link --}}
          <div class="text-center mt-3">
            <a href="{{ route('user.password.forgot.post') }}" class="text-decoration-none d-block mb-2">
              Forgot your password?
            </a>

          <div class="text-center mt-3">
            <a href="{{ route('user.register') }}" class="text-decoration-none">
              Don't have an account? Register here
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
