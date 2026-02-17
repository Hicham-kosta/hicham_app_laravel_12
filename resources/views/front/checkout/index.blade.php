@extends('front.layout.layout')
@section('title', 'Checkout')
@section('content')
<style>
/* ===== CHECKOUT PAGE STYLES (ISOLATED) ===== */

/* ----- Page Header ----- */
.checkout-page .bg-secondary {
  background-color: #f8f9fa !important;
  border-radius: 8px;
  margin-bottom: 2rem !important;
}
.checkout-page .bg-secondary h1 {
  font-size: 2.2rem;
  font-weight: 700;
  color: #2c3e50;
  letter-spacing: -0.5px;
}
.checkout-page .bg-secondary .d-inline-flex p {
  color: #6c757d;
  font-size: 1rem;
}
.checkout-page .bg-secondary .d-inline-flex a {
  color: #007bff;
  text-decoration: none;
  font-weight: 500;
}
.checkout-page .bg-secondary .d-inline-flex a:hover {
  text-decoration: underline;
}

/* ----- Saved Addresses Section ----- */
.checkout-page .mb-4 > .d-flex {
  margin-bottom: 1.5rem;
}
.checkout-page .mb-4 > .d-flex h4 {
  font-size: 1.3rem;
  font-weight: 700;
  color: #2c3e50;
}
.checkout-page .btn-sm.btn-primary {
  background: #007bff;
  border: none;
  padding: 0.4rem 1rem;
  font-size: 0.85rem;
  border-radius: 20px;
  transition: all 0.2s;
}
.checkout-page .btn-sm.btn-primary:hover {
  background: #0056b3;
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
}

/* Address Cards */
.checkout-page .border.p-3 {
  border: 1px solid #e9ecef !important;
  border-radius: 10px;
  background: white;
  transition: all 0.2s;
  position: relative;
}
.checkout-page .border.p-3:hover {
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  border-color: #c1d9f0 !important;
}
.checkout-page .custom-radio {
  margin-bottom: 0.5rem;
}
.checkout-page .custom-radio .custom-control-label {
  font-weight: 500;
  color: #495057;
  line-height: 1.5;
  padding-left: 0.5rem;
}
.checkout-page .custom-radio .custom-control-label strong {
  color: #2c3e50;
  font-size: 1rem;
}
.checkout-page .custom-radio .custom-control-input:checked ~ .custom-control-label::before {
  background-color: #007bff;
  border-color: #007bff;
}
/* Edit/Remove buttons */
.checkout-page .d-flex .btn-outline-primary,
.checkout-page .d-flex .btn-outline-danger {
  font-size: 0.8rem;
  padding: 0.2rem 1rem;
  border-radius: 20px;
  border-width: 1px;
  transition: all 0.2s;
}
.checkout-page .d-flex .btn-outline-primary:hover {
  background: #007bff;
  color: white;
  border-color: #007bff;
}
.checkout-page .d-flex .btn-outline-danger:hover {
  background: #dc3545;
  color: white;
  border-color: #dc3545;
}

/* Empty addresses message */
.checkout-page .mb-4 p {
  color: #6c757d;
  font-style: italic;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
  text-align: center;
}

/* ----- Add Address Collapse Form ----- */
.checkout-page .collapse {
  transition: all 0.3s ease;
}
.checkout-page .collapse.show {
  display: block;
}
.checkout-page #add-address-form {
  background: white;
  border-radius: 10px;
  padding: 1.5rem;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
  margin-top: 1rem;
  border: 1px solid #e9ecef;
}
.checkout-page #add-address-form h4 {
  font-size: 1.2rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 1.5rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #007bff;
  display: inline-block;
}
.checkout-page .form-group label {
  font-weight: 600;
  color: #495057;
  margin-bottom: 0.4rem;
  font-size: 0.9rem;
}
.checkout-page .form-control,
.checkout-page .custom-select {
  border: 1px solid #ced4da;
  border-radius: 6px;
  padding: 0.6rem 1rem;
  transition: all 0.2s;
}
.checkout-page .form-control:focus,
.checkout-page .custom-select:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
}
.checkout-page .form-control.is-invalid {
  border-color: #dc3545;
}
.checkout-page .text-danger.small {
  font-size: 0.8rem;
  margin-top: 0.25rem;
  margin-bottom: 0;
}

/* Save/Cancel buttons */
.checkout-page .btn-primary.font-weight-bold {
  background: linear-gradient(135deg, #007bff, #0056b3);
  border: none;
  padding: 0.6rem 2rem;
  border-radius: 30px;
  font-weight: 600;
  transition: all 0.2s;
}
.checkout-page .btn-primary.font-weight-bold:hover {
  background: linear-gradient(135deg, #0056b3, #003d80);
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
}
.checkout-page .btn-outline-secondary {
  border: 1px solid #ced4da;
  color: #6c757d;
  padding: 0.6rem 2rem;
  border-radius: 30px;
}
.checkout-page .btn-outline-secondary:hover {
  background: #6c757d;
  color: white;
  border-color: #6c757d;
}

/* ----- Alert Messages ----- */
.checkout-page .alert {
  border-radius: 8px;
  border: none;
  padding: 1rem 1.5rem;
  margin-bottom: 1.5rem;
}
.checkout-page .alert-danger {
  background: #f8d7da;
  color: #721c24;
}
.checkout-page .alert ul {
  margin: 0;
  padding-left: 1.2rem;
}

/* ----- Order Summary Card ----- */
.checkout-page .card.border-secondary {
  border: 1px solid #e9ecef !important;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  margin-bottom: 1.5rem;
}
.checkout-page .card-header {
  background: #f8f9fa;
  border-bottom: 2px solid #007bff;
  padding: 1rem 1.5rem;
}
.checkout-page .card-header h4 {
  font-size: 1.2rem;
  font-weight: 700;
  color: #2c3e50;
  margin: 0;
}
.checkout-page .card-body {
  padding: 1.5rem;
}
.checkout-page .card-body h5 {
  font-size: 1rem;
  font-weight: 700;
  color: #495057;
  margin-bottom: 1rem;
}
.checkout-page .d-flex.justify-content-between {
  margin-bottom: 0.8rem;
  align-items: center;
}
.checkout-page .d-flex p {
  margin: 0;
  color: #495057;
  font-size: 0.95rem;
  line-height: 1.4;
}
.checkout-page .d-flex p small {
  color: #6c757d;
  font-size: 0.85rem;
}
.checkout-page .d-flex p:last-child {
  font-weight: 600;
  color: #007bff;
}
.checkout-page hr {
  border-top: 1px dashed #dee2e6;
  margin: 1rem 0;
}
.checkout-page .card-footer {
  background: transparent;
  border-top: 1px solid #e9ecef;
  padding: 1rem 1.5rem;
}
.checkout-page .card-footer .d-flex h5 {
  font-size: 1.2rem;
  font-weight: 800;
  color: #2c3e50;
}
.checkout-page .card-footer .d-flex h5:last-child {
  color: #28a745;
}

/* ----- Payment Methods Card ----- */
.checkout-page .custom-radio .custom-control-label {
  font-weight: 600;
  color: #2c3e50;
}
.checkout-page .custom-radio + small {
  margin-left: 1.8rem;
  color: #6c757d;
  font-size: 0.85rem;
  margin-top: -0.2rem;
  margin-bottom: 0.8rem;
}
/* PayPal conversion box */
.checkout-page .paypal-conversion-box {
  background: #f1f3f5 !important;
  border-left: 3px solid #007bff;
  margin-top: 0.5rem;
  margin-left: 1.8rem;
  font-size: 0.9rem;
}
.checkout-page .paypal-conversion-box .fa-paypal {
  color: #003087;
}
.checkout-page .paypal-conversion-box .converted-amount {
  font-weight: 700;
  color: #007bff;
}

/* ----- Place Order Button ----- */
.checkout-page .btn-block.btn-primary {
  background: linear-gradient(135deg, #28a745, #218838);
  border: none;
  border-radius: 40px;
  padding: 1rem;
  font-size: 1.1rem;
  font-weight: 800;
  letter-spacing: 1px;
  text-transform: uppercase;
  box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
  transition: all 0.2s;
}
.checkout-page .btn-block.btn-primary:hover {
  background: linear-gradient(135deg, #218838, #1e7e34);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(40, 167, 69, 0.3);
}
.checkout-page .btn-block.btn-primary:active {
  transform: translateY(0);
}

/* ----- Responsive Layout ----- */
@media (min-width: 992px) {
  .checkout-page .col-lg-8 {
    padding-right: 2rem;
  }
  .checkout-page .col-lg-4 {
    padding-left: 0;
  }
}
@media (max-width: 991px) {
  .checkout-page .col-lg-4 {
    margin-top: 2rem;
  }
}
@media (max-width: 767px) {
  .checkout-page .bg-secondary {
    min-height: 120px !important;
  }
  .checkout-page .bg-secondary h1 {
    font-size: 1.8rem;
  }
  .checkout-page .mb-4 > .d-flex {
    flex-direction: column;
    align-items: flex-start !important;
  }
  .checkout-page .mb-4 > .d-flex button {
    margin-top: 0.5rem;
  }
  .checkout-page .border.p-3 .d-flex {
    flex-wrap: wrap;
    gap: 0.5rem;
  }
  .checkout-page .card-body .d-flex {
    flex-wrap: wrap;
  }
}
</style>
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
<div class="container-fluid pt-2 checkout-page">
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
                  @if(old('address_id', $selectedAddressId ?? null) == $address->id) checked @endif>
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
                     @error('first_name')
                     <p class="text-danger small">{{ $message }}</p>
                     @enderror
                  </div>
                  <div class="col-md-6 form-group">
                     <label>Last Name</label>
                     <input class="form-control @error('last_name') is-invalid @enderror"
                        name="last_name" value="{{ old('last_name') }}">
                     @error('last_name')
                     <p class="text-danger small">{{ $message }}</p>
                     @enderror
                  </div>
                  <div class="col-md-6 form-group">
                     <label>Mobile</label>
                     <input class="form-control @error('mobile') is-invalid @enderror"
                        name="mobile" value="{{ old('mobile') }}">
                     @error('mobile')
                     <p class="text-danger small">{{ $message }}</p>
                     @enderror
                  </div>
                  <div class="col-md-12 form-group">
                     <label>Address Line 1</label>
                     <input class="form-control @error('address_line1') is-invalid @enderror"
                        name="address_line1" value="{{ old('address_line1') }}">
                     @error('address_line1')
                     <p class="text-danger small">{{ $message }}</p>
                     @enderror
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
                     @error('country')
                     <p class="text-danger small">{{ $message }}</p>
                     @enderror
                  </div>
                  <div class="col-md-6 form-group">
                     <label>City</label>
                     <input class="form-control @error('city') is-invalid @enderror"
                        name="city" id="city" value="{{ old('city') }}">
                     @error('city')
                     <p class="text-danger small">{{ $message }}</p>
                     @enderror
                  </div>
                  <!-- STATE SELECT (US only) -->
                  <div class="col-md-6 form-group" id="state_select_wrapper">
                     <label>State</label>
                     <select id="state_select" name="state" class="custom-select">
                        <option value="">-- Select State --</option>
                        @foreach($usStates as $s)
                        <option value="{{ $s->name }}" {{ old('state') === $s->name ? 'selected' : '' }}>
                        {{ $s->name }}
                        </option>
                        @endforeach
                        <option value="other" {{ old('state') === 'other' ? 'selected' : '' }}>Other</option>
                     </select>
                  </div>
                  <!-- STATE TEXT (non-US or “other”) -->
                  <div class="col-md-6 form-group" id="state_text_wrapper" style="display:none;">
                     <label>State / Province</label>
                     <input id="state_text" name="state_text" class="form-control"
                        value="{{ old('state_text') }}">
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
                     <small class="text-muted">Size: {{$item['size'] ?? ''}} &times; {{$item['qty'] ?? 1}}</small>
                  </p>
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
                  <h6 class="font-weight-medium" id="shippingAmount">
                     {{formatCurrency($cart['shipping'], $currCode)}}
                  </h6>
               </div>
               @endif
               <div class="d-flex justify-content-between">
                  <h6 class="font-weight-medium">Tax (GST)</h6>
                  <h6 class="font-weight-medium">{{formatCurrency($cart['taxes_total'] ?? 0, $currCode)}}</h6>
               </div>
            </div>
            <div class="card-footer border-secondary bg-transparent">
               <div class="d-flex justify-content-between mt-2">
                  <h5 class="font-weight-bold">Total</h5>
                  <h5 class="font-weight-bold" id="orderTotalAmount">
                     {{formatCurrency($cart['total_numeric'], $currCode)}}
                  </h5>
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
                        {{-- Paypal USD Conversion Box --}}
                        @if(isset($paypalPreview) && is_array($paypalPreview))
                        <div class="mt-2 p-2 bg-light rounded paypal-conversion-box">
                           <div class="d-flex align-items-center">
                              <i class="fa fa-paypal fa-2x mr-3" style="color: #003087;"></i>
                              <div>
                                 <div style="font-size:14px; color:#333;">
                                    PayPal will charge in (<strong>USD</strong>)
                                 </div>
                                 <div style="font-size:16px; font-weight:600;">
                                    <span class="converted-amount">{{formatCurrency($paypalPreview['converted_amount'], 'USD')}}</span>
                                    <small class="text-muted">USD</small>
                                    <span class="ml-2 text-muted" style="font-size:13px;">
                                    (1 {{$paypalPreview['original_currency']}} = 
                                    {{number_format($paypalPreview['conversion_rate'], 6)}} USD)</span>
                                 </div>
                                 <div style="font-size:12px;" class="text-muted">
                                    Original: {{formatCurrency($paypalPreview['original_amount'], 
                                    $paypalPreview['original_currency'])}}
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endif
                     </div>
                  </div>
                  {{-- Direct Check --}}
                  <div class="form-group">
                     <div class="custom-control custom-radio">
                        <input type="radio" 
                        class="custom-control-input"  
                        name="payment_method" 
                        id="directcheck"
                        value="directcheck">
                        <label class="custom-control-label" for="directcheck">Direct Check</label>
                     </div>
                     <small class="text-muted d-block ml-4 mt-1">
                        Please send your check to our official business address. 
                        Your order will be processed after payment confirmation
                     </small>
                  </div>
                  {{-- Bank Transfer --}}
                  <div class="form-group">
                     <div class="custom-control custom-radio">
                        <input type="radio" 
                        class="custom-control-input"  
                        name="payment_method" 
                        id="banktransfer"
                        value="banktransfer">
                        <label class="custom-control-label" for="banktransfer">Bank Transfer</label>
                     </div>
                     <small class="text-muted d-block ml-4 mt-1">
                        Please send your bank transfer to our official business account. 
                        Your order will be processed after payment confirmation
                     </small>
                  </div>
                  <div class="form-group mt-2">
                     <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" 
                           name="payment_method" id="cod" value="cod">
                        <label class="custom-control-label" for="cod">cash on Delevery (COD)</label>
                     </div>
                  </div>
                  {{-- Hidden input to carry selected address id to backend --}}
                  <input type="hidden" name="address_id" id="selected_address_input" 
                     value="{{old('address_id', $selectedAddressId ?? null)}}">
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