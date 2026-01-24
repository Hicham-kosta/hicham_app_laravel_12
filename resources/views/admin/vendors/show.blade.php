@extends('admin.layout.layout')
@section('content')
@php
$detail = $vendor->vendordetails ?? null;
@endphp
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Admin Management</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{url('admin/vendors')}}">Vendors</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            View Vendor
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            @if(Session::has('success_message'))
                <div class="alert alert-success alert-dismissible fad show m-3" role="alert">
                    <strong>Success: </strong>{{Session::get('success_message')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif
            @if(Session::has('error_message'))
                <div class="alert alert-danger alert-dismissible fad show m-3" role="alert">
                    <strong>Error: </strong>{{Session::get('error_message')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif
            <div class="row">
                <!-- left: Vendor Summary -->
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle" 
                                src="{{ !empty($vendor->image) ? asset('admin/images/profiles/'.$vendor->image) : asset('admin/images/profiles/no-image.png') }}" 
                                alt="Vendor profile picture">
                            </div>
                            <h3 class="profile-username text-center mb-1">{{ $vendor->name }}</h3>
                            <p class="text-muted text-center mb-2">Vendor Account</p>
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Email</b> <span class="float-end">{{ $vendor->email }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Mobile</b> <span class="float-end">{{ $vendor->mobile ?? '-' }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Account Status</b> <span class="float-end">
                                        @if($vendor->status == 1)
                                        <span class="badge bg-success">Active</span>
                                        @else
                                        <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>KYC Status</b>
                                    <span class="float-end">
                                        @if(($detail->is_verified ?? 0) == 1)
                                        <span class="badge bg-success">Approved</span>
                                        @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>Commission</b>
                                    <span class="float-end">
                                        {{ isset($detail->commission_percent) ? number_format($detail->commission_percent, 2) : '0.00' }}%
                                    </span>
                                </li>
                            </ul>
                            <a href="{{url('admin/vendors')}}" class="btn btn-secondary btn-block">
                                <b>Back to Vendors</b>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Right: Detailed Tabs -->
                <div class="col-md-8">
                    <div class="card card-outline card-primary">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link" href="#store" data-bs-toggle="tab">Shop Details</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#kyc" data-bs-toggle="tab">KYC Details</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#bank" data-bs-toggle="tab">Bank Details</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Shop Details Tab -->
                                <div class="tab-pane active" id="store">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Shop Name</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->shop_name ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Shop Email</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->shop_email ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Shop Mobile</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->shop_mobile ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Website</label>
                                            <div class="form-control bg-light">
                                                @if(!empty($detail->shop_website))
                                                <a href="{{ $detail->shop_website }}" target="_blank">
                                                    {{ $detail->shop_website }}
                                                </a>
                                                @else
                                                -
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Address</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->shop_address ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">City</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->shop_city ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">State</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->shop_state ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Pincode</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->shop_pincode ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Country</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->shop_country ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- KYC Details Tab -->
                                <div class="tab-pane" id="kyc">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">GST Number</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->gst_number ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Pan Number</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->pan_number ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Business License</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->business_license_number ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Address Proof</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->address_proof ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Address Proof Image</label>
                                            <div class="form-control bg-light">
                                                @if(!empty($detail->address_proof_image))
                                                <a href="{{ asset('front/images/vendor-docs/'.$detail->address_proof_image) }}" target="_blank">
                                                    View Document
                                                </a>
                                                @else
                                                -
                                                @endif
                                            </div>
                                            <small class="text-muted">
                                                Note: Update the document path above if you store it elsewhere
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <!-- Bank Details Tab -->
                                <div class="tab-pane" id="bank">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Account Holder Name</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->account_holder_name ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Bank Name</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->bank_name ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Account Number</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->account_number ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Bank IFSC Code</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->bank_ifsc_code ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection