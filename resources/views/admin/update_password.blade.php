@extends('admin.layout.layout')
@section('content')
<main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Admin Management</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Update Pssword</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row g-4">
              
              <!--begin::Col-->
              <div class="col-md-6">
                <!--begin::Quick Example-->
                <div class="card card-primary card-outline mb-4">
                  <!--begin::Header-->
                  <div class="card-header"><div class="card-title">Update Password</div></div>
                  <!--end::Header-->
                  @if(Session::has('error_message'))
                  <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                    <strong>Error</strong> {{Session::get('error_message')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                  @endif
                  @if (Session::has('success_message'))
                  <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    <strong>Success</strong> {{Session::get('success_message')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                  @endif
                  @foreach ($errors->all() as $error)
                  <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                    <strong>Error: </strong>{!!$error!!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                  @endforeach
                  <!--begin::Form-->
                  <form method="post" action="{{route('admin.update-password.request')}}">@csrf
                    <!--begin::Body-->
                    <div class="card-body">
                      <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input
                          type="email"
                          class="form-control"
                          id="email"
                          aria-describedby="emailHelp" value="{{Auth::guard('admin')->user()->email}}" redonly
                           style="background-color:#ccc" />
                        <div id="emailHelp" class="form-text">
                          We'll never share your email with anyone else.
                        </div>
                      </div>
                      <div class="mb-3">
                        <label for="current_pwd" class="form-label">Current Password </label>
                        <input type="password" class="form-control" id="current_pwd" name="current_pwd" />
                        <span id="verifyPwd"></span>
                      </div>
                      <div class="mb-3">
                        <label for="new_pwd" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_pwd" name="new_pwd">
                      </div>
                      <div class="mb-3">
                        <label for="confirm_pwd" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_pwd" name="confirm_pwd">
                      </div>
                      <!--<div class="input-group mb-3">
                        <input type="file" class="form-control" id="inputGroupFile02" />
                        <label class="input-group-text" for="inputGroupFile02">Upload</label>
                      </div>
                      <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1" />
                        <label class="form-check-label" for="exampleCheck1">Check me out</label>
                      </div>-->
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    <div class="card-footer">
                      <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                    <!--end::Footer-->
                  </form>
                  <!--end::Form-->
                </div>
                <!--end::Horizontal Form-->
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>
      @endsection