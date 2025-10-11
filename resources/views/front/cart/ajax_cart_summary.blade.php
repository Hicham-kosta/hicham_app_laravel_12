<div class="d-flex justify-content-between mb-3 pt-1">
    <h6 class="font-weight-medium">Subtotal</h6>
    <h6 class="font-weight-medium">${{$subtotal}}</h6>
</div>
<div class="d-flex justify-content-between">
    <h6 class="font-weight-medium">Discount</h6>
    <h6 class="font-weight-medium">${{$discount}}</h6>
</div>
<div class="card-footer border-secondary bg-transparent">
    <div class="d-flex justify-content-between mt-2">
    <h5 class="font-weight-bold">Total</h5>
    <h5 class="font-weight-bold">${{$total}}</h5>
</div>
<a href="{{url('/checkout')}}" class="btn btn-block btn-primary my-3 py-3">Proceed To Checkout</a>