@extends('admin.layout.layout')
@section('content')
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-ms-6">
                    <h3 class="mb-0">Coupons Management</h1>
                </div>
                <div class="col-ms-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Coupons
                        </li>  
                    </ol>    
                </div>
            </div>
        </div>       
    </div>
    <div class="content">
       <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                   <div class="card-header">
                    <h3 class="card-title">Coupons</h3>
                    @if($couponsModule['edit_access'] == 1 || $couponsModule['full_access'] == 1)
                     <a href="{{route('coupons.create')}}" class="btn btn-primary float-end">Add Coupon</a>
                    @endif
                   </div>
                   <div class="card-body">
                    @if(Session::has('success_message'))
                    <div class="alert alert-success alert-dismissible fad show m-3" role="alert">
                        <strong>Success: </strong>{{Session::get('success_message')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                    </div>
                    @endif
                    <table class="table table-bordered table-striped" id="coupons">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Coupon Type</th>
                                    <th>Amount</th>
                                    <th>Expiry Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($coupons as $coupon)
                                <tr>
                                    <td>{{$coupon->coupon_code}}</td>
                                    <td>{{ucfirst($coupon->coupon_type)}}</td>
                                    <td>
                                      {{$coupon->amount}}
                                     @if($coupon->amount_type === 'percentage')
                                      %
                                     @else
                                      INR
                                     @endif
                                    </td>
                                    <td>
                                        @if($coupon->expiry_date)
                                        {{$coupon->expiry_date->format('Fj, Y')}}
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        @if($couponsModule['edit_access'] == 1 || $couponsModule['full_access'] == 1)
                                        <a class="updateCouponStatus"
                                        id="coupon-{{$coupon->id}}" 
                                        data-coupon-id="{{$coupon->id}}" 
                                        href="javascript:void(0)">
                                        <i class="fas fa-toggle-{{$coupon->status ? 'on' : 'off'}}"
                                        data-status="{{$coupon->status ? 'Active' : 'Inactive'}}" 
                                        style="color: {{$coupon->status ? '#3f6ed3' : 'grey'}}"></i>
                                        </a>
                                        @else
                                        <i class="fas fa-toggle-{{($coupon->status ? 'on' : 'off')}}" 
                                        style="color: {{$coupon->status ? '#3f6ed3' : 'grey'}}"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($couponsModule['edit_access'] == 1 || $couponsModule['full_access'] == 1)
                                        <a href="{{route('coupons.edit', $coupon->id)}}"><i class="fas fa-edit"></i></a>
                                        &nbsp;&nbsp;
                                        @endif
                                        @if($couponsModule['full_access'] == 1)
                                            <form action="{{route('coupons.destroy', $coupon->id)}}" method="POST" style="display:
                                               inline-block;" >@csrf
                                                @method('DELETE')
                                                <button class="confirmDelete" name="coupon" 
                                                title="Delete coupon" type="button" style=
                                                "border:none;background:none; color:#3f6ed3;" 
                                                href="javascript:void(0)" data-module="coupon" 
                                                data-id="{{$coupon->id}}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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
 