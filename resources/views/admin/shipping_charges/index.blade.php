@extends('admin.layout.layout')
@section('content')
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-ms-6">
                    <h3 class="mb-0">Shipping Management</h1>
                </div>
                <div class="col-ms-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Shipping Charges
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
                            <h3 class="card-title">Shipping Charges</h3>
                            @if(!empty($shippingModule) && ($shippingModule['edit_access'] == 1 || $shippingModule['full_access'] == 1))
                            <a style="max-width: 220px; float: right; display: inline-block;" 
                            href="{{url('admin/shipping-charges/create')}}"
                            class="btn btn-block btn-primary">
                            Add New Shipping Charge
                            </a>
                            @endif
                        </div>
                        <div class="card-body">
                            @if(Session::has('success_message'))
                            <div class="alert alert-success alert-dismissible fade show mx-1 my-3" role="alert">
                                <strong>Success: </strong> {{Session::get('success_message')}}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @endif
                            @if(Session::has('error_message'))
                            <div class="alert alert-danger alert-dismissible fade show mx-1 my-3" role="alert">
                                <strong>Error: </strong> {{Session::get('error_message')}}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @endif
                            <table id="shipping_charges" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Country</th>
                                        <th>Name</th>
                                        <th>Weight Range (g)</th>
                                        <th>Subtotal Range (Base)</th>
                                        <th>Rate (Base)</th>
                                        <th>Default</th>
                                        <th>Status</th>
                                        <th>Created On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($charges as $charge)
                                    <tr id="shipping-row-{{$charge->id}}">
                                        <td>{{$charge->id}}</td>
                                        <td>{{$charge->country?->name ?? '-'}}</td>
                                        <td>{{$charge->name}}</td>
                                        <td>
                                        @php
                                        $minW = $charge->min_weight_g;
                                        $maxW = $charge->max_weight_g;
                                        @endphp
                                        @if(is_null($minW) && is_null($maxW))
                                        <span class="text-muted">Any</span>
                                        @else
                                        {{$minW ?? 0}}g - {{$maxW ? $maxW.'g' : 'Unlimited'}}
                                        @endif
                                        </td>
                                        <td>
                                        @php
                                        $minS = $charge->min_subtotal;
                                        $maxS = $charge->max_subtotal;
                                        @endphp
                                        @if(is_null($minS) && is_null($maxS))
                                        <span class="text-muted">Any</span>
                                        @else
                                        {{$minS ?? 0}} - {{$maxS ?? 'Unlimited'}}
                                        @endif
                                        </td>
                                        <td>{{number_format($charge->rate, 2)}}</td>
                                        <td>
                                            @if($charge->is_default)
                                            <span class="badge bg-success text-white">Default</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($charge->status)
                                            <a class="updateShippingChargeStatus" data-shipping-charge-id="{{$charge->id}}"
                                            style="color: #3f6ed3" href="javascript:void(0)">
                                            <i class="fas fa-toggle-on" data-status="Active"></i>
                                            </a>
                                            @else
                                            <a class="updateShippingChargeStatus" data-shipping-charge-id="{{$charge->id}}"
                                            style="color: #3f6ed3" href="javascript:void(0)">
                                            <i class="fas fa-toggle-off" data-status="Inactive"></i>
                                            </a>
                                            @endif
                                        </td>
                                        <td>{{optional($charge->created_at)->format('d M Y h:i A')}}</td>
                                        <td>
                                            @if($shippingModule['edit_access'] ==1 || $shippingModule['full_access'] == 1)
                                            <a href="{{url('admin/shipping-charges/'.$charge->id.'/edit')}}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                            @if($shippingModule['full_access'] == 1)
                                            &nbsp;&nbsp;
                                            <form action="{{route('shipping-charges.destroy', $charge->id)}}" 
                                            method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="confirmDelete" 
                                            nam="ShippingCharge" 
                                            title="Delete Shipping Charge" 
                                            type="button" 
                                            style="border:none; background:none; color: #3f6ed3;" 
                                            data-module="Shipping_charge" 
                                            data-id="{{$charge->id}}">
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