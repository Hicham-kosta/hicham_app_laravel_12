@extends('front.layout.layout')
@section('content')
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
<div class="container-fluid pt-2">
    <div class="text-center mb-4">
        <h2 class="section-title px-5"><span class="px-2">Change Your Password</span></h2>
    </div>
    <div class="row px-xl-5 justify-content-center">
        <div class="col-lg-5 mb-5">
           <div id="changePasswordSuccess">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
           </div>
           <form id="changePasswordForm" novalidate>
            @csrf
            <div class="control-group mb-3">
                <input type="email" class="form-control" value="{{$user->email}}" readonly />
                <small class="text-muted">Your Email (read-only)</small>
            </div>
            <div class="control-group mb-3">
                <input type="password" class="form-control" name="current_password" 
                id="current_password" placeholder="Current Password" required />
                <p class="help-block text-danger" data-error-for="current_password"></p>
            </div>
            <div class="control-group mb-3">
             <input type="password" class="form-control" name="password" 
             id="password" placeholder="New Password" required />
             <small class="text-muted d-block">Password must be at least 8 characters long, 
                mixed case, letters, numbers and symbols</small>
             <p class="help-block text-danger" data-error-for="password"></p>
            </div>
            <div class="control-group mb-4">
                <input type="password" class="form-control" name="password_confirmation" 
                id="password_confirmation" placeholder="Confirm New Password" required />
                <p class="help-block text-danger" data-error-for="password_confirmation"></p>
            </div>
        <div>
            <button class="btn btn-primary py-2 px-4" 
            type="submit" id="changePasswordBtn">Change Password</button>
        </div>
        </form>
        <div class="mt-3">
            <a href="{{ route('user.account') }}">
                <-Back to Account
            </a>
        </div>
    </div>
</div>

@endsection