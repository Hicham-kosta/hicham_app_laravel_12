@extends('front.layout.layout')
@section('content')

<div class="container-fluid mt-2">
    <div class="text-center mb-4">
        <h2 class="section-title px-5"><span class="px-2">Reset Your Password</span></h2>
    </div>
    <div class="row px-xl-5 justify-content-center">
        <div class="col-lg-5 mb-5">
            <div class="contact-form">
                <div id="resetSuccess"></div>
                <form name="resetForm" id="resetForm" novalidate>
                    @csrf
                    <input type="hidden" name="token" value="{{ request()->route('token') }}">
                    
                    <div class="control-group mb-3">
                        <input type="email" class="form-control" name="email" 
                            value="{{ $email ?? old('email') }}" 
                            placeholder="Your Email" required>
                        <p class="help-block text-danger" data-error-for="email"></p>
                    </div>
                    
                    <div class="control-group mb-3">
                        <input type="password" class="form-control" name="password"  
                            placeholder="New Password" required />
                        <p class="help-block text-danger" data-error-for="password"></p>
                    </div>
                    
                    <div class="control-group mb-3">
                        <input type="password" class="form-control" name="password_confirmation" 
                            placeholder="Confirm New Password" required />
                        <p class="help-block text-danger" data-error-for="password_confirmation"></p>
                    </div>
                    
                    <div>
                        <button class="btn btn-primary py-2 px-4" type="submit" id="resetBtn">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection