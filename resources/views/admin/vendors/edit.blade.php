@extends('admin.layout.layout')
@section('content')

@php
use Illuminate\Support\Facades\Auth;
$admin = Auth::guard('admin')->user();
$vendorDetails = $admin->vendorDetails ?? null;
@endphp

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <h3 class="mb-0">Vendor Details</h3>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            {{-- Success Message --}}
            @if(session('success_message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{session('success_message')}}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            {{-- Validation Errors (GLOBAL) --}}
            @if($errors->any())
            <div class="alert alert-danger">
                <strong>Please fix the following errors</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <form method="POST" action="{{route('admin.vendor.update-details.request')}}" enctype="multipart/form-data">
                @csrf
            <!-- =================Business Details================ -->
             <div class="card-mb-4">
                <div class="card-header">
                    <h5>Business Details</h5>
                </div>
                <div class="card-body row">
                    <div class="col-md-6 mb-3">
                        <label>Shop Name*</label>
                        <input type="text" 
                        name="shop_name" 
                        value="{{old('shop_name', $vendorDetails->shop_name ?? '')}}"
                        class="form-control @error('shop_name') is-invalid @enderror">
                        @error('shop_name')
                        <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Shop Mobile*</label>
                        <input type="text" 
                        name="shop_mobile" 
                        value="{{old('shop_mobile', $vendorDetails->shop_mobile ?? '')}}"
                        class="form-control @error('shop_mobile') is-invalid @enderror">
                        @error('shop_mobile')
                        <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Shop Address</label>
                        <textarea name="shop_address" 
                        class="form-control">{{old('shop_address', $vendorDetails->shop_address ?? '')}}</textarea>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>City</label>
                        <input type="text" name="shop_city" 
                        value="{{old('shop_city', $vendorDetails->shop_city ?? '')}}"
                        class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>State</label>
                        <input type="text" name="shop_state" 
                        value="{{old('shop_state', $vendorDetails->shop_state ?? '')}}"
                        class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Pincode</label>
                        <input type="text" name="shop_pincode" 
                        value="{{old('shop_pincode', $vendorDetails->shop_pincode ?? '')}}"
                        class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Country</label>
                        <input type="text" name="shop_country" 
                        value="{{old('shop_country', $vendorDetails->shop_country ?? '')}}"
                        class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Email</label>
                        <input type="text" name="shop_email" 
                        value="{{old('shop_email', $vendorDetails->shop_email ?? '')}}"
                        class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Website</label>
                        <input type="text" name="shop_website" 
                        value="{{old('shop_website', $vendorDetails->shop_website ?? '')}}"
                        class="form-control">
                    </div>
                </div>
             </div>
             <!-- =================Bank Details================ -->
             <div class="card-mb-4">
                <div class="card-header">
                    <h5>Bank Details</h5>
                </div>
                <div class="card-body row">
                    <div class="col-md-6 mb-3">
                        <label>Account Holder Name*</label>
                        <input type="text" 
                        name="account_holder_name" 
                        value="{{old('account_holder_name', $vendorDetails->account_holder_name ?? '')}}"
                        class="form-control @error('account_holder_name') is-invalid @enderror">
                        @error('account_holder_name')
                        <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Account Number*</label>
                        <input type="text" 
                        name="account_number" 
                        value="{{old('account_number', $vendorDetails->account_number ?? '')}}"
                        class="form-control @error('account_number') is-invalid @enderror">
                        @error('account_number')
                        <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Bank Name*</label>
                        <input type="text" 
                        name="bank_name" 
                        value="{{old('bank_name', $vendorDetails->bank_name ?? '')}}"
                        class="form-control @error('bank_name') is-invalid @enderror">
                        @error('bank_name')
                        <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>IFSC Code*</label>
                        <input type="text" 
                        name="ifsc_code" 
                        value="{{old('ifsc_code', $vendorDetails->ifsc_code ?? '')}}"
                        class="form-control @error('ifsc_code') is-invalid @enderror">
                        @error('ifsc_code')
                        <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                </div>
             </div>
             <!-- =================KYC Details================ -->
             <div class="card-mb-4">
                <div class="card-header">
                    <h5>KYC Details</h5>
                </div>
                <div class="card-body row">
                    <div class="col-md-6 mb-3">
                        <label>GST Number</label>
                        <input type="text" 
                        name="gst_number" 
                        value="{{old('gst_number', $vendorDetails->gst_number ?? '')}}"
                        class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>PAN Number</label>
                        <input type="text" 
                        name="pan_number" 
                        value="{{old('pan_number', $vendorDetails->pan_number ?? '')}}"
                        class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Business License Number</label>
                        <input type ="text" 
                        name="business_license_number" 
                        value="{{old('business_license_number', $vendorDetails->business_license_number ?? '')}}" 
                        class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Address Proof Type</label>
                        <input type ="text" 
                        name="address_proof" 
                        value="{{old('address_proof', $vendorDetails->address_proof ?? '')}}" 
                        class="form-control" 
                        placeholder="Voter ID / Passport">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Address Proof Image</label>
                        <input type="file" 
                        name="address_proof_image" 
                        class="form-control">
                        @if(!empty($vendorDetails?->address_proof_image))
                        <div class="mt-2">
                            <a target="_blank" href="{{asset('front/images/vendor-docs/'
                            .$vendorDetails->address_proof_image)}}">
                            View Uploaded Document
                            </a>
                            &nbsp;&nbsp;
                            <a href="javascript:void(0)" class="text-danger deleteAdrressProof">
                                Delete
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
             </div>
             <!-- Submit -->
              <button type="submit" class="btn btn-primary">
                Save Vendor Details
              </button>
          </form>
        </div>
    </div>
</main>


@endsection