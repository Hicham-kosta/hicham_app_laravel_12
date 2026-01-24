@extends('admin.layout.layout')
@section('content')
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-ms-6">
                    <h3 class="mb-0">Admin Management</h1>
                </div>
                <div class="col-ms-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Vendors
                        </li>
                    </ol>    
                </div>
            </div>
        </div>       
    </div>
    <div class="app-content">
       <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                   <div class="card-header">
                    <h3 class="card-title">Vendors</h3>
                    {{-- Optional: if later you add admin-side create vendor --}}
                    {{-- <a style="max-width: 150px; float:right; display:inline-block;" 
                        href="{{url('admin/vendors/create')}}" class="btn btn-block btn-primary">
                        Add Vendor
                         </a> --}}
                    </div>
                    <div class="card-body">
                        @if(Session::has('success_message'))
                        <div class="alert alert-success alert-dismissible fad show m-3" role="alert">
                            <strong>Success: </strong> {{Session::get('success_message')}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                        </div>
                        @endif
                        @if(Session::has('error_message'))
                        <div class="alert alert-danger alert-dismissible fad show m-3" role="alert">
                            <strong>Error: </strong> {{Session::get('error_message')}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                        </div>
                        @endif
                        <table id="vendors" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Vendor</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Shop Name</th>
                                    <th>City</th>
                                    <th>Commission</th>
                                    <th>KYC Status</th>
                                    <th>Account</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
<tbody>
    @foreach($vendors as $vendor)
    @php
    $detail = $vendor->vendorDetails;
    @endphp
    <tr>
        <td>{{$vendor->id}}</td>
        <td>{{$vendor->name}}</td>
        <td>{{$vendor->mobile}}</td>
        <td>{{$vendor->email}}</td>
        <td>{{$detail->shop_name ?? '- '}}</td>
        <td>{{$detail->shop_city ?? '- '}}</td>
        <td>{{isset($detail->commission_percent) ? 
            number_format($detail->commission_percent, 2) : '0.00'}}</td>
        <td>
            @if(($detail->is_verified ?? 0) == 1)
                <span class="badge bg-success">Approved</span>
            @else
                <span class="badge bg-warning text-dark">Pending</span>
            @endif
        </td>
        <td>
            @if($vendor->status == 1)
                <span class="badge bg-success">Active</span>
            @else
                <span class="badge bg-danger">Inactive</span>
            @endif
        </td>
        <td>
            <a title="view Vendor" href="{{url('admin/vendors/'.$vendor->id)}}" 
            style="color: #3f6ed3;"><i class="fas fa-eye"></i></a>&nbsp;&nbsp;
            {{-- Toggle vendor status (same UX Subadmin) --}}
            @if($vendor->status == 1)
                <a class="updateVendorStatus" data-vendor-id="{{$vendor->id}}" 
                style="color: #3f6ed3;" href="javascript:void(0)">
                <i class="fas fa-toggle-on" data-status="Active"></i></a>
            @else
                <a class="updateVendorStatus" data-vendor-id="{{$vendor->id}}" 
                style="color: grey;" href="javascript:void(0)">
                <i class="fas fa-toggle-on" data-status="Inactive"></i></a>
            @endif
        </td>
    </tr>
    @endforeach
</tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>
@endsection
