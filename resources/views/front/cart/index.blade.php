@extends('front.layout.layout')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Page Header Start -->
    <div class="container-fluid bg-secondary mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 150px">
            <h1 class="font-weight-semi-bold text-uppercase mb-3">Shopping Cart</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{url('/')}}">Home</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0">Shopping Cart</p>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Cart Start -->
    <div class="container-fluid pt-2">
        <div class="row px-xl-5">
            <div class="col-lg-8 table-responsive mb-5">
                <table class="table table-bordered text-center mb-0">
                    <thead class="bg-secondary text-dark">
                        <tr>
                            <th>Products</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody id="cart-items-body" class="align-middle"> 
                       <tr><td colspan="5">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-4">
                <form id="applyCouponForm" action="javascript:;">
                    <div class="input-group mb-3">
                        <input type="text" name="coupon_code" id="coupon_code" class="form-control" 
                        placeholder="Coupon Code">
                        <div class="input-group-append">
                            <button class="btn btn-primary" id="applyCoupon">Apply Coupon/Credit</button>
                        </div>
                    </div>
                </form>
                <div id="coupon-msg"></div>
                <div class="card border-secondary mb-5">
                    <div class="card-header bg-secondary border-0">
                        <h4 class="font-weight-semi-bold m-0">Cart Summary</h4>
                    </div>
                    <div class="card-body" id="cart-summary-container">
                        <div class="d-flex justify-content-between mb-3 pt-1">
                            <h6 class="font-weight-medium">Subtotal</h6>
                            <h6 class="font-weight-medium">$0</h6>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h6 class="font-weight-medium">Discount</h6>
                            <h6 class="font-weight-medium">$0</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Cart End -->
@endsection