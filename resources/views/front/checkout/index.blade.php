@extends('front.layout.layout')
@section('title', 'Checkout')
@section('content')
    <!-- Page Header Start -->
    <div class="container-fluid bg-secondary mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 150px;">
            <h1 class="font-weight-semi-bold text-uppercase mb-3">Checkout</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{url('/')}}">Home</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0">Checkout</p>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Checkout Start -->
    <div class="container-fluid pt-2">
        <div class="row px-xl-5">
            <div class="col-lg-8">
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="font-weight-semi-bold mb-0">Saved Addresses</h4>
                        <button class="btn btn-sm btn-primary font-weight-bold" type="button" 
                            data-toggle="collapse" data-target="#add-address-form" id="add-address-btn">
                            + Add Delivery Address
                        </button>
                    </div>

                    <!-- Address List -->
                    @forelse($addresses ?? [] as $address)
                    <div class="border p-3 mb-3">
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" class="custom-control-input" 
                                id="address{{$address->id}}" name="selected_address" value="{{$address->id}}"
                                @if(old('address_id') == $address->id) checked @endif>
                            <label class="custom-control-label d-block" for="address{{$address->id}}">
                                <strong>{{$address->full_name ?? ($address->first_name . ' ' . ($address->last_name ?? ''))}}</strong><br>
                                Address : {{$address->address_line1 ?? ''}}<br>{{$address->address_line2 ?? ''}}<br>Mobile: {{$address->mobile ?? ''}}
                            </label>
                        </div>
                        <div class="d-flex">
                            <button type="button" class="btn btn-sm btn-outline-primary mr-2 edit-address-btn"
                            data-address='@json($address)' data-address-id="{{$address->id}}">
                            Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-address-btn"
                            data-address-id="{{$address->id}}">
                            Remove
                            </button>
                        </div>
                    </div>
                    @empty
                    <p>No Saved Addresses Found</p>
                    @endforelse
                </div>
                {{-- Add this at the top of your checkout.blade.php --}}
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Error:</strong> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

                {{-- Add Address collapse - will auto-open on validation errors --}}
                <div class="collapse mb-4 {{$errors->any() ? 'show' : ''}}" id="add-address-form">
                    <h4 class="font-weight-semi-bold mb-4">Add Delivery Address</h4>
                    <form action="{{ route('checkout.addAddress') }}" method="post" id="checkoutAddAddressForm">
    @csrf

    <div class="row">

        <div class="col-md-6 form-group">
            <label>First Name</label>
            <input class="form-control @error('first_name') is-invalid @enderror"
                   name="first_name" value="{{ old('first_name') }}">
            @error('first_name')<p class="text-danger small">{{ $message }}</p>@enderror
        </div>

        <div class="col-md-6 form-group">
            <label>Last Name</label>
            <input class="form-control @error('last_name') is-invalid @enderror"
                   name="last_name" value="{{ old('last_name') }}">
            @error('last_name')<p class="text-danger small">{{ $message }}</p>@enderror
        </div>

        <div class="col-md-6 form-group">
            <label>Mobile</label>
            <input class="form-control @error('mobile') is-invalid @enderror"
                   name="mobile" value="{{ old('mobile') }}">
            @error('mobile')<p class="text-danger small">{{ $message }}</p>@enderror
        </div>

        <div class="col-md-12 form-group">
            <label>Address Line 1</label>
            <input class="form-control @error('address_line1') is-invalid @enderror"
                   name="address_line1" value="{{ old('address_line1') }}">
            @error('address_line1')<p class="text-danger small">{{ $message }}</p>@enderror
        </div>

        <div class="col-md-12 form-group">
            <label>Address Line 2</label>
            <input class="form-control"
                   name="address_line2" value="{{ old('address_line2') }}">
        </div>

        <div class="col-md-6 form-group">
            <label>Country</label>
            <select id="country" name="country" class="custom-select">
                <option value="">-- Select Country --</option>
                @foreach($countries as $c)
                    <option value="{{ $c->name }}" {{ old('country') === $c->name ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
            @error('country')<p class="text-danger small">{{ $message }}</p>@enderror
        </div>

        <div class="col-md-6 form-group">
            <label>City</label>
            <input class="form-control @error('city') is-invalid @enderror"
                   name="city" id="city" value="{{ old('city') }}">
            @error('city')<p class="text-danger small">{{ $message }}</p>@enderror
        </div>

        <!-- COUNTY SELECT (UK only) -->
        <div class="col-md-6 form-group" id="county_select_wrapper">
            <label>County</label>
            <select id="county_select" name="county" class="custom-select">
                <option value="">-- Select County --</option>
                @foreach($ukStates as $s)
                    <option value="{{ $s->name }}" {{ old('county') === $s->name ? 'selected' : '' }}>
                        {{ $s->name }}
                    </option>
                @endforeach
                <option value="other" {{ old('county') === 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <!-- COUNTY TEXT (non-UK or “other”) -->
        <div class="col-md-6 form-group" id="county_text_wrapper" style="display:none;">
            <label>County / State</label>
            <input id="county_text" name="county_text" class="form-control"
                   value="{{ old('county_text') }}">
        </div>

        <div class="col-md-6 form-group">
            <label>Postcode</label>
            <input id="postcode" name="postcode" class="form-control"
                   value="{{ old('postcode') }}">
        </div>

        <div class="col-md-12 text-left mt-2">
            <button type="submit" id="checkoutSaveBtn" class="btn btn-primary 
            font-weight-bold px-4">Save Address</button>
            <button type="button" id="cancelEditBtn" class="btn btn-outline-secondary ml-2" 
            style="display:none;">Cancel</button>
        </div>

    </div>
</form>

                </div>
            </div>

            {{-- Right column: Order summary & Payment --}}
            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="card border-secondary mb-5">
                    <div class="card-header bg-secondary border-0">
                        <h4 class="font-weight-semi-bold m-0">Order Total</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="font-weight-medium mb-3">Products</h5>

                        @forelse($cart['items'] ?? [] as $item)
                        <div class="d-flex justify-content-between">
                            <p style="max-width:65%;">{{$item['product_name'] ?? ''}}<br>
                            <small class="text-muted">Size: {{$item['size'] ?? ''}} &times; {{$item['qty'] ?? 1}}</small></p>
                            <p>{{formatCurrency($item['line_total'] ?? (($item['unit_price'] ?? 0) * ($item['qty'] ?? 1)))}}</p>
                        </div>
                        @empty
                        <div class="text-muted">No Products in cart</div>
                        @endforelse
                        <hr class="mt-0">

                        <div class="d-flex justify-content-between mb-3 pt-1">
                            <h6 class="font-weight-medium">Subtotal</h6>
                            <h6 class="font-weight-medium">{{$cart['subtotal'] ?? formatCurrency($cart['subtotal_numeric'] ?? 0)}}</h6>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <h6 class="font-weight-medium">Discount</h6>
                            <h6 class="font-weight-medium">{{formatCurrency($cart['discount'] ?? 0)}}</h6>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <h6 class="font-weight-medium">Wallet</h6>
                            <h6 class="font-weight-medium">{{formatCurrency($cart['wallet'] ?? 0)}}</h6>
                        </div>

                        @if(isset($cart['shipping']))
                        <div class="d-flex justify-content-between">
                            <h6 class="font-weight-medium">Shipping</h6>
                            <h6 class="font-weight-medium">{{formatCurrency($cart['shipping'] ?? 0)}}</h6>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer border-secondary bg-transparent">
                        <div class="d-flex justify-content-between mt-2">
                            <h5 class="font-weight-bold">Total</h5>
                            <h5 class="font-weight-bold">{{$cart['total'] ?? formatCurrency($cart['total_numeric'] ?? 0)}}</h5>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="card border-secondary mb-5">
                    <div class="card-header bg-secondary border-0">
                        <h4 class="font-weight-semi-bold m-0">Payment Methods</h4>
                    </div>
                    <form action="{{route('checkout.placeOrder')}}" method="post" id="placeOrderForm">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" 
                                        name="payment_method" id="paypal" value="paypal" checked>
                                    <label class="custom-control-label" for="paypal">Paypal</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" 
                                        name="payment_method" id="directcheck" value="directcheck">
                                    <label class="custom-control-label" for="directcheck">Direct Check</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" 
                                        name="payment_method" id="banktransfer" value="banktransfer">
                                    <label class="custom-control-label" for="banktransfer">Bank Transfer</label>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" 
                                        name="payment_method" id="cod" value="cod">
                                    <label class="custom-control-label" for="cod">cash on Delevery (COD)</label>
                                </div>
                            </div>
                            {{-- Hidden input to carry selected address id to backend --}}
                            <input type="hidden" name="address_id" id="selected_address_input" value="{{old('address_id')}}">
                        </div>
                        <div class="card-footer border-secondary bg-transparent">
                            <button type="submit" class="btn btn-lg btn-block btn-primary font-weight-bold my-3 py-3">Place Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Checkout End -->
    

@endsection