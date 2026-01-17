@extends('front.layout.layout')

@section('title', 'Order Confirmed')

@section('content')
    <!-- Page Header -->
    <div class="container-fluid bg-dark mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 150px;">
            <h1 class="font-weight-semi-bold text-uppercase mb-3 text-white">Order Confirmed</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}" class="text-white">Home</a></p>
                <p class="m-0 px-2 text-white">-</p>
                <p class="m-0"><a href="{{ route('orders.index') }}" class="text-white">My Orders</a></p>
                <p class="m-0 px-2 text-white">-</p>
                <p class="m-0 text-white">Order Confirmation</p>
            </div>
        </div>
    </div>

    <!-- Confirmation Section -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Success Card -->
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-body p-5">
                            <!-- Success Header -->
                            <div class="text-center mb-5">
                                <div class="success-animation mb-4">
                                    <div class="success-checkmark">
                                        <div class="check-icon">
                                            <span class="icon-line line-tip"></span>
                                            <span class="icon-line line-long"></span>
                                            <div class="icon-circle"></div>
                                            <div class="icon-fix"></div>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="font-weight-bold text-success mb-3">Order Confirmed!</h1>
                                <p class="lead text-muted mb-4">
                                    Thank you for your purchase. Your order has been received and is being processed.
                                </p>
                            </div>

                            <!-- Order Summary -->
                            <div class="card border-0 bg-light rounded-lg mb-5">
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-receipt"></i>
                                                </div>
                                                <div>
                                                    <h6 class="text-dark mb-1">Order Reference</h6>
                                                    <p class="text-muted mb-0">#{{ $order->order_number ?? $order->id }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-calendar-check"></i>
                                                </div>
                                                <div>
                                                    <h6 class="text-dark mb-1">Order Date</h6>
                                                    <p class="text-muted mb-0">{{ $order->created_at->format('F d, Y') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-4">
                                    <div class="row">
                                        <div class="col-md-4 mb-3 mb-md-0">
                                            <h6 class="text-muted mb-2">Total Amount</h6>
                                            <h4 class="text-primary mb-0">{{ formatCurrency($order->total) }}</h4>
                                        </div>
                                        <div class="col-md-4 mb-3 mb-md-0">
                                            <h6 class="text-muted mb-2">Payment Method</h6>
                                            <p class="text-dark mb-0 text-capitalize">{{ $order->payment_method ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="text-muted mb-2">Order Status</h6>
                                            @php
                                                $statusConfig = [
                                                    'pending' => ['class' => 'bg-warning text-dark', 'icon' => 'clock'],
                                                    'processing' => ['class' => 'bg-info', 'icon' => 'sync'],
                                                    'completed' => ['class' => 'bg-success', 'icon' => 'check'],
                                                    'cancelled' => ['class' => 'bg-danger', 'icon' => 'times'],
                                                    'confirmed' => ['class' => 'bg-primary', 'icon' => 'check-circle']
                                                ];
                                                $status = strtolower($order->status);
                                                $config = $statusConfig[$status] ?? ['class' => 'bg-secondary', 'icon' => 'question'];
                                            @endphp
                                            <span class="badge {{ $config['class'] }} rounded-pill px-3 py-2">
                                                <i class="fas fa-{{ $config['icon'] }} me-1"></i>
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="row mb-5">
                                <div class="col-md-4 mb-3">
                                    <div class="card border-0 text-center h-100">
                                        <div class="card-body py-4">
                                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                                <i class="fas fa-shipping-fast fa-lg"></i>
                                            </div>
                                            <h6 class="text-dark mb-2">Track Your Order</h6>
                                            <p class="text-muted small mb-0">We'll send you tracking information once your order ships</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card border-0 text-center h-100">
                                        <div class="card-body py-4">
                                            <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                                <i class="fas fa-envelope fa-lg"></i>
                                            </div>
                                            <h6 class="text-dark mb-2">Email Confirmation</h6>
                                            <p class="text-muted small mb-0">A detailed confirmation has been sent to your email address</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card border-0 text-center h-100">
                                        <div class="card-body py-4">
                                            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                                <i class="fas fa-headset fa-lg"></i>
                                            </div>
                                            <h6 class="text-dark mb-2">Need Help?</h6>
                                            <p class="text-muted small mb-0">Our support team is here to help with any questions</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- BANK TRANSFER DETAILS --}}
                            @if($order->payment_method == 'banktransfer')
                            <div class="alert alert-info text-left mb-4">
                                <h5 class="mb-2">Bank Transfer Details</h5>
                                <p class="mb-1">
                                    Please transfer the total amount to the following bank account: 
                                </p>
                                <ul class="mb-0">
                                    <li><strong>Account Holder</strong>Your Company Name</li>
                                    <li><strong>Bank Name</strong>Your Bank Name</li>
                                    <li><strong>Account Number</strong>Your Account Number</li>
                                    <li><strong>IBAN</strong>Your IBAN</li>
                                    <li><strong>SWIFT/BIC</strong>Your SWIFT/BIC</li>
                                    <li><strong>Amount</strong>{{ $order->total_amount }}</li>
                                </ul>
                                <p class="mt-3 mb-0">
                                    Please include your <strong>order ID number</strong> in the transfer description: 
                                    
                                </p>
                            </div>
                            @endif

                            {{-- DIRECT CHECK DETAILS --}}
                            @if($order->payment_method == 'directcheck')
                            <div class="alert alert-info text-left mb-4">
                                <h5 class="mb-2">Direct Check Details</h5>
                                <p class="mb-1">
                                    Please send your check to the following address: 
                                </p>
                                <address class="mb-0">
                                    <strong>Your Company Name</strong><br>
                                    Your Street Address<br>
                                    Your City, State, Zip Code<br>
                                    Your Country
                                </address>
                                <p class="mt-3 mb-0">
                                    Please include your <strong>order ID number</strong> in the check description: 
                                </p>
                            </div>
                            @endif

                            {{-- COD MESSAGE --}}
                            @if($order->payment_method == 'cod')
                            <div class="alert alert-info text-left mb-4">
                                <h5 class="mb-1">Cash on Delivery</h5>
                                <p class="mb-1">
                                    Please pay the total amount to the delivery person when you receive your order: 
                                </p>
                                <p class="mt-3 mb-0">
                                    Please include your <strong>order ID number</strong> in the payment description: 
                                </p>
                            </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="text-center">
                                <div class="row justify-content-center">
                                    <div class="col-lg-8">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <a href="{{ url('/') }}" class="btn btn-outline-primary btn-lg w-100 py-3">
                                                    <i class="fas fa-shopping-cart me-2"></i>Continue Shopping
                                                </a>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="{{ route('user.orders.show', $order->id) }}" class="btn btn-primary btn-lg w-100 py-3">
                                                    <i class="fas fa-eye me-2"></i>View Order Details
                                                </a>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <a href="{{ route('orders.index') }}" class="btn btn-link text-muted">
                                                <i class="fas fa-list me-1"></i>View All Orders
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Additional Information -->
                            <div class="mt-5 pt-4 border-top">
                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <h6 class="text-dark mb-3">
                                            <i class="fas fa-info-circle me-2 text-primary"></i>What happens next?
                                        </h6>
                                        <ul class="list-unstyled text-muted">
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2 small"></i>
                                                Order confirmation and payment verification
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2 small"></i>
                                                Order processing and preparation for shipping
                                            </li>
                                            <li class="mb-2">
                                                <i class="far fa-clock text-warning me-2 small"></i>
                                                Shipping and delivery (2-5 business days)
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-dark mb-3">
                                            <i class="fas fa-question-circle me-2 text-primary"></i>Need assistance?
                                        </h6>
                                        <p class="text-muted mb-3">
                                            If you have any questions about your order, please contact our customer support team.
                                        </p>
                                        <div class="d-flex flex-wrap gap-2">
                                            <a href="#" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-phone me-1"></i>Contact Support
                                            </a>
                                            <a href="#" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-comment me-1"></i>Live Chat
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div aria-live="polite" aria-atomic="true" style="position: fixed; bottom: 20px; right: 20px; z-index: 1060;">
        <div id="orderToast" class="toast shadow-lg" data-bs-delay="5000">
            <div class="toast-header bg-success text-white">
                <i class="fas fa-check-circle me-2"></i>
                <strong class="me-auto">Order Placed Successfully</strong>
                <small>Just now</small>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Order #{{ $order->order_number ?? $order->id }}</h6>
                        <p class="mb-0 text-muted">Total: {{ formatCurrency($order->total) }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-shopping-bag text-success fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    /* Success Animation */
    .success-animation {
        margin: 0 auto;
    }
    
    .check-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        position: relative;
        border-radius: 50%;
        box-sizing: content-box;
        border: 4px solid #4CAF50;
    }
    
    .check-icon::before {
        top: 3px;
        left: -2px;
        width: 30px;
        transform-origin: 100% 50%;
        border-radius: 100px 0 0 100px;
    }
    
    .check-icon::after {
        top: 0;
        left: 30px;
        width: 60px;
        transform-origin: 0 50%;
        border-radius: 0 100px 100px 0;
        animation: rotate-circle 4.25s ease-in;
    }
    
    .check-icon::before, .check-icon::after {
        content: '';
        height: 100px;
        position: absolute;
        background: #FFFFFF;
        transform: rotate(-45deg);
    }
    
    .icon-line {
        height: 5px;
        background-color: #4CAF50;
        display: block;
        border-radius: 2px;
        position: absolute;
        z-index: 10;
    }
    
    .icon-line.line-tip {
        top: 46px;
        left: 14px;
        width: 25px;
        transform: rotate(45deg);
        animation: icon-line-tip 0.75s;
    }
    
    .icon-line.line-long {
        top: 38px;
        right: 8px;
        width: 47px;
        transform: rotate(-45deg);
        animation: icon-line-long 0.75s;
    }
    
    .icon-circle {
        top: -4px;
        left: -4px;
        z-index: 10;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        position: absolute;
        box-sizing: content-box;
        border: 4px solid rgba(76, 175, 80, .5);
    }
    
    .icon-fix {
        top: 8px;
        width: 5px;
        left: 26px;
        z-index: 1;
        height: 85px;
        position: absolute;
        transform: rotate(-45deg);
        background-color: #FFFFFF;
    }
    
    @keyframes rotate-circle {
        0% { transform: rotate(-45deg); }
        5% { transform: rotate(-45deg); }
        12% { transform: rotate(-405deg); }
        100% { transform: rotate(-405deg); }
    }
    
    @keyframes icon-line-tip {
        0% { width: 0; left: 1px; top: 19px; }
        54% { width: 0; left: 1px; top: 19px; }
        70% { width: 50px; left: -8px; top: 37px; }
        84% { width: 17px; left: 21px; top: 48px; }
        100% { width: 25px; left: 14px; top: 45px; }
    }
    
    @keyframes icon-line-long {
        0% { width: 0; right: 46px; top: 54px; }
        65% { width: 0; right: 46px; top: 54px; }
        84% { width: 55px; right: 0px; top: 35px; }
        100% { width: 47px; right: 8px; top: 38px; }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Show toast notification
        var orderToast = document.getElementById('orderToast');
        if (orderToast) {
            var toast = new bootstrap.Toast(orderToast);
            toast.show();
        }
        
        // Add animation class to success checkmark
        const checkIcon = document.querySelector('.check-icon');
        if (checkIcon) {
            setTimeout(() => {
                checkIcon.style.animation = 'rotate-circle 4.25s ease-in';
            }, 100);
        }
    });
</script>
@endpush