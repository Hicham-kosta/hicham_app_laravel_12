@extends('front.layout.layout')
@section('content')

<!-- Page Header Start -->
    <div class="container-fluid bg-secondary mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 150px">
            <h1 class="font-weight-semi-bold text-uppercase mb-3">Shopping Cart</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="">Home</a></p>
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
                    <tbody class="align-middle">
                        @forelse($cartItems as $item)
                           <tr>
                             <td class="align-middle text-left">
                                <img src="{{$item['image']}}" alt="{{$item['product_name']}}" style="width: 50px;">
                                <a class="ml-2" href="{{url($item['product_url'])}}">{{$item['product_name']}}
                                </a>
                                <div class="small text-muted">Size: {{$item['size']}}</div>
                             </td>
                             <td class="align-middle">${{$item['unit_price']}}</td>
                             <td class="align-middle">
                                <div class="input-group-quantity mx-auto" style="width: 100px;">
                                    <div class="input-group-btn">
                                        <button type="button"
                                            class="btn btn-sm btn-primary btn-minus updateCartQty"
                                            data-cart-id="{{$item['cart_id']}}"
                                            data-dir="down">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                    <input type="text" 
                                        class="form-control form-control-sm bg-secondary text-center cart-qty"
                                        value="{{$item['qty']}}"
                                        data-cart-id="{{$item['cart_id']}}">
                                    <div class="input-group-btn">
                                        <button type="button"
                                        class="btn btn-sm btn-primary btn-plus updateCartQty"
                                        data-cart-id="{{$item['cart_id']}}"
                                        data-dir="up">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">${{$item['line_total']}}</td>
                            <td class="align-middle">
                                <button type="button" 
                                class="btn btn-sm btn-primary removeCartItem" 
                                data-cart-id="{{$item['cart_id']}}">
                                    <i class="fa fa-times"></i>
                                </button>
                            </td>    
                           </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No items in cart</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="col-lg-4">
                <form class="mb-5" action="">
                    <div class="input-group">
                        <input type="text" class="form-control p-4" placeholder="Coupon Code">
                        <div class="input-group-append">
                            <button class="btn btn-primary">Apply Coupon</button>
                        </div>
                    </div>
                </form>
                <div class="card border-secondary mb-5">
                    <div class="card-header bg-secondary border-0">
                        <h4 class="font-weight-semi-bold m-0">Cart Summary</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3 pt-1">
                            <h6 class="font-weight-medium">Subtotal</h6>
                            <h6 class="font-weight-medium">${{$subtotal}}</h6>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h6 class="font-weight-medium">Discount</h6>
                            <h6 class="font-weight-medium">${{$discount}}</h6>
                        </div>
                    </div>
                    <div class="card-footer border-secondary bg-transparent">
                        <div class="d-flex justify-content-between mt-2">
                            <h5 class="font-weight-bold">Total</h5>
                            <h5 class="font-weight-bold">${{$total}}</h5>
                        </div>
                        <button class="btn btn-block btn-primary my-3 py-3">Proceed To Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Cart End -->
@endsection