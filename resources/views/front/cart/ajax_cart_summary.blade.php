@php
    $appliedWallet = session('applied_wallet_amount', 0);
@endphp

<div class="d-flex justify-content-between mb-3 pt-1">
    <h6 class="font-weight-medium">Subtotal</h6>
    <h6 class="font-weight-medium">{{ $subtotal ?? formatCurrency(0) }}</h6>
</div>

@if(($discount ?? 0) > 0)
<div class="d-flex justify-content-between">
    <h6 class="font-weight-medium">Coupon Discount</h6>
    <h6 class="font-weight-medium">{{ $discount_formatted ?? formatCurrency($discount ?? 0) }}</h6>
</div>
@endif

@if(($wallet ?? 0) > 0)
<div class="card-footer border-secondary bg-transparent">
    <div class="d-flex justify-content-between mt-2">
    <h5 class="font-weight-bold">Wallet Applied</h5>
    <h5 class="font-weight-bold">{{ $wallet_formatted ?? formatCurrency($wallet ?? 0) }}</h5>
</div>
@endif

<hr class="my-2">
<div class="d-flex justify-content-between mt-2">
    <h5 class="font-weight-bold">Total</h5>
    <h5 class="font-weight-bold">{{ $total ?? formatCurrency(0) }}</h5>
</div>

@if(($wallet ?? 0) > 0)
<div class="mt-2 text-end">
    <button id="removeWalletBtn" class="btn btn-sm btn-outline-danger">Remove Wallet</button>
</div>
@endif

@if(($discount ?? 0) > 0)
<div class="mt-2 text-end">
    <button id="removeCouponBtn" class="btn btn-sm btn-outline-danger">Remove Coupon</button>
</div>
@endif

   
<a href="{{ url('/checkout') }}" class="btn btn-block btn-primary my-3 py-3">Proceed To Checkout</a>