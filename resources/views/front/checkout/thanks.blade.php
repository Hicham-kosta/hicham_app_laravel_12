@extends('front.layout.layout')
@section('title', 'Order Placed')
@section('content')
<div class="container-fluid bg-secondary mb-5">
   <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 150px;">
        <h1 class="font-weight-semi-bold text-uppercase mb-3">Thank You</h1>
    <div class="d-inline-flex">
        <p class="m-0"><a href="{{url('/')}}">Home</a></p>
        <p class="m-0 px-2">-</p>
        <p class="m-0">Order Confirmation</p>
    </div>
   </div>
</div>
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="fa fa-check-circle text-success" style="font-size: 70px"></i>
                    </div>
                    <h2 class="font-weight-bold mb-2">Thank You --- Your Order is Confirmed</h2>
                    <p class="text-muted mb-4">
                        Your order has been placed successfully. We will process it shortly. Below are your details for your reference</p>
                    <div class="border-rounded p-4 mb-4 text-left bg-light">
                        <h5 class="mb-3">Order Details</h5>
                        <p class="mb-1"><strong>Order ID:</strong> {{$order->id}}</p>
                        <p class="mb-1"><strong>Order Status:</strong> {{ucfirst($order->status)}}</p>
                        <p class="mb-0"><strong>Grand Total:</strong> {{formatCurrency($order->total)}}</p>
                    </div>
                    <div class="d-flex justify-content-center">
                        <a href="{{url('/')}}" class="btn btn-primary py-2 px-4 mr-2">Continue Shopping</a>
                        <a href="{{route('user.account')}}" class="btn btn-secondary py-2 px-4">My Account</a>
                    </div>
                    <p class="text-muted small mt-3">A confirmation has been saved to your account order history. We'll also send aemail when it ships</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div aria-live="polite" aria-atomic="true" style="position: fixed; bottom: 20px; right: 20px; z-index: 1060;">
    <div id="orderToast" class="toast" data-delay="4000">
        <div class="toast-header">
            <strong class="mr-auto text-success">Order Placed</strong>
            <small>Now</small>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">&times;</button>
        </div>
        <div class="toast-body">
            Order #{{$order->id}} has been placed successfully.
            Grand Total: {{formatCurrency($order->total)}}
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toastEl = document.getElementById('orderToast');
        if(toastEl && typeof jQuery !== 'undefined' && $(toastEl).toast) {
            $(toastEl).toast('show');
        } 
    });
</script>
@endpush
@endsection