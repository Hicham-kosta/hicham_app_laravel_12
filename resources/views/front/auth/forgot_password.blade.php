@extends('front.layout.layout')
@section('content')
  <!-- Page Header ... same as other pages -->
   <div class="containe-fluid mt-2">
      <div class="text-center mb-4">
        <h2 class="section-title px-5"><span class="px-2">Reset Your Password</span></h2>
      </div>
      <div class="row px-xl-5 justify-content-center">
        <div class="col-lg-5 mb-5">
            <div class="contact-form">
               <div id="forgotSuccess"></div>
                 <form name="forgotForm" id="forgotForm" novalidate>
                    @csrf
                    <div class="control-group mb-3">
                        <input type="email" class="form-control" name="email" id="forgotEmail" 
                        placeholder="Enter your account Email" required />
                        <p class="help-block text-danger" data-error-for="email"></p>
                    </div>
                    <div>
                        <button class="btn btn-primary py-2 px-4" type="submit" id="forgotBtn">Send Reset Link</button>
                    </div>
                 </form>
                 <p class="mt-3">Remembered your password? <a href="{{ route('user.login') }}">Back to Login</a></p>
            </div>
        </div>
      </div>
   </div>
@endsection