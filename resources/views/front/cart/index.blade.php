@extends('front.layout.layout')
@section('content')
<style>
/* ===== CART PAGE STYLES (ISOLATED) ===== */

/* Main container – all rules are prefixed with .cart-page */
#cart-page {
  /* No extra styles needed here */
}

/* ----- Page Header ----- */
#cart-page .bg-secondary {
  background-color: #f8f9fa !important;
  border-radius: 8px;
  margin-bottom: 2rem !important;
}
#cart-page .bg-secondary h1 {
  font-size: 2.2rem;
  font-weight: 700;
  color: #2c3e50;
  letter-spacing: -0.5px;
}
#cart-page .bg-secondary .d-inline-flex p {
  color: #6c757d;
  font-size: 1rem;
}
#cart-page .bg-secondary .d-inline-flex a {
  color: #007bff;
  text-decoration: none;
  font-weight: 500;
}
#cart-page .bg-secondary .d-inline-flex a:hover {
  text-decoration: underline;
}

/* ----- Cart Table ----- */
#cart-page .table-responsive {
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  background: white;
}
#cart-page .table {
  margin-bottom: 0;
  border-collapse: separate;
  border-spacing: 0;
  width: 100%;
}
#cart-page .table thead th {
  background-color: #f8f9fa;
  border-bottom: 2px solid #dee2e6;
  color: #495057;
  font-weight: 700;
  text-transform: uppercase;
  font-size: 0.9rem;
  padding: 1rem 0.75rem;
  border-top: none;
}
#cart-page .table tbody td {
  vertical-align: middle;
  padding: 1.2rem 0.75rem;
  border-bottom: 1px solid #e9ecef;
  color: #2c3e50;
  font-weight: 500;
}
#cart-page .table tbody tr:hover {
  background-color: #f8f9fa;
  transition: background-color 0.2s;
}
#cart-page .table tbody tr:last-child td {
  border-bottom: none;
}

/* Product column */
#cart-page .table tbody td:first-child {
  font-weight: 600;
  color: #007bff;
}

/* Price & Total */
#cart-page .table tbody td:nth-child(2),
#cart-page .table tbody td:nth-child(4) {
  font-weight: 600;
  color: #28a745;
}

/* Remove button */
#cart-page .table tbody td:last-child {
  text-align: center;
}
#cart-page .btn-remove {
  background: none;
  border: none;
  color: #dc3545;
  font-size: 1.2rem;
  cursor: pointer;
  transition: all 0.2s;
  padding: 5px 10px;
  border-radius: 4px;
}
#cart-page .btn-remove:hover {
  background-color: #dc3545;
  color: white;
}
#cart-page .btn-remove i {
  pointer-events: none;
}

/* Quantity input (if you add one later) */
#cart-page .quantity-input {
  width: 70px;
  text-align: center;
  border: 1px solid #ced4da;
  border-radius: 4px;
  padding: 6px 10px;
  font-weight: 600;
}

/* ----- Coupon Form ----- */
#cart-page .input-group {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
  border-radius: 6px;
  overflow: hidden;
}
#cart-page #coupon_code {
  border: 1px solid #ced4da;
  border-right: none;
  padding: 0.75rem 1rem;
  font-size: 0.95rem;
}
#cart-page #coupon_code:focus {
  border-color: #007bff;
  box-shadow: none;
}
#cart-page .btn-primary {
  background: linear-gradient(135deg, #007bff, #0056b3);
  border: none;
  padding: 0.75rem 1.5rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  transition: all 0.2s;
}
#cart-page .btn-primary:hover {
  background: linear-gradient(135deg, #0056b3, #003d80);
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
}
#cart-page .btn-primary:active {
  transform: translateY(0);
}

/* Coupon message */
#coupon-msg {
  margin: 1rem 0;
  padding: 0.75rem 1rem;
  border-radius: 6px;
  font-size: 0.9rem;
  display: none;
}
#coupon-msg.success {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
  display: block;
}
#coupon-msg.error {
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
  display: block;
}

/* ----- Cart Summary Card ----- */
#cart-page .card {
  border: none;
  border-radius: 10px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
  margin-top: 1.5rem;
}
#cart-page .card-header {
  background-color: #f8f9fa;
  border-bottom: 2px solid #dee2e6;
  padding: 1rem 1.5rem;
}
#cart-page .card-header h4 {
  font-size: 1.3rem;
  font-weight: 700;
  color: #2c3e50;
  margin: 0;
}
#cart-page .card-body {
  padding: 1.5rem;
}
#cart-page .d-flex.justify-content-between {
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px dashed #dee2e6;
}
#cart-page .d-flex.justify-content-between:last-of-type {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}
#cart-page .d-flex h6 {
  font-size: 1rem;
  font-weight: 600;
  color: #495057;
}
.cart-page .d-flex .font-weight-medium {
  font-weight: 600;
}
#cart-page .d-flex .font-weight-medium:last-child {
  color: #007bff;
}

/* Loading state */
#cart-page #cart-items-body tr td[colspan="5"] {
  text-align: center;
  padding: 3rem;
  color: #6c757d;
  font-style: italic;
  background: #f8f9fa;
}

/* ----- Responsive Adjustments ----- */
@media (max-width: 767px) {
  #cart-page .bg-secondary {
    min-height: 120px !important;
  }
  #cart-page .bg-secondary h1 {
    font-size: 1.8rem;
  }
  #cart-page .table-responsive {
    overflow-x: auto;
  }
  #cart-page .table {
    min-width: 600px;
  }
  #cart-page .col-lg-4 {
    margin-top: 2rem;
  }
  #cart-page .input-group {
    flex-wrap: wrap;
  }
  #cart-page .input-group-append {
    width: 100%;
    margin-top: 0.5rem;
  }
  #cart-page .btn-primary {
    width: 100%;
  }
}
</style>

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Page Header Start -->
    <div class="container-fluid bg-secondary mb-5" id="cart-page">
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
    <div class="container-fluid pt-2" id="cart-page">
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
                    <tbody id="cart-items-body" data-cart-container="true" class="align-middle"> 
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