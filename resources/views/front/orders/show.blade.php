@extends('front.layout.layout')

@section('title', 'Order Details')

@section('content')
    <!-- Page Header -->
    <div class="container-fluid bg-dark mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
            <h1 class="font-weight-semi-bold text-uppercase mb-3 text-white">Order Details</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}" class="text-white">Home</a></p>
                <p class="m-0 px-2 text-white">-</p>
                <p class="m-0"><a href="{{ route('user.orders.index') }}" class="text-white">My Orders</a></p>
                <p class="m-0 px-2 text-white">-</p>
                <p class="m-0 text-white">Order #{{ $orders->order_number ?? $orders->id }}</p>
            </div>
        </div>
    </div>

    <!-- Order Details Section -->
    <div class="container-fluid py-5">
        <div class="container">
            <!-- Order Header -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-receipt fa-lg"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-dark mb-1">Order #{{ $orders->order_number ?? $orders->id }}</h4>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-calendar me-1"></i>
                                                Placed on {{ $orders->created_at->format('F d, Y \a\t h:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                    @php
                                        $statusConfig = [
                                            'pending' => ['class' => 'bg-warning text-dark', 'icon' => 'clock'],
                                            'processing' => ['class' => 'bg-info', 'icon' => 'sync'],
                                            'completed' => ['class' => 'bg-success', 'icon' => 'check'],
                                            'cancelled' => ['class' => 'bg-danger', 'icon' => 'times'],
                                            'shipped' => ['class' => 'bg-primary', 'icon' => 'shipping-fast']
                                        ];
                                        $status = strtolower($orders->status);
                                        $config = $statusConfig[$status] ?? ['class' => 'bg-secondary', 'icon' => 'question'];
                                    @endphp
                                    <span class="badge {{ $config['class'] }} rounded-pill px-4 py-2 fs-6">
                                        <i class="fas fa-{{ $config['icon'] }} me-2"></i>
                                        {{ ucfirst($orders->status) }}
                                    </span>
                                    @if($orders->payment_method === 'paypal')
                                    <h3 class="text-primary mt-2">
                                        {{ formatCurrency($orders->total ?? 0, 'USD') }}
                                    </h3>
                                    @else
                                    <h3 class="text-primary mt-2">
                                        {{ formatCurrency($orders->total ?? 0) }}
                                    </h3>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Order Items -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-lg h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h5 class="mb-0 text-dark">
                                <i class="fas fa-shopping-cart me-2"></i>Order Items ({{ $orders->orderItems->count() }})
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3">Product</th>
                                            <th class="text-center py-3">Price</th>
                                            <th class="text-center py-3">Quantity</th>
                                            <th class="text-end pe-4 py-3">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orders->orderItems as $item)
                                        <tr class="border-bottom">
                                            <td class="ps-4 py-4">
                                                <div class="d-flex align-items-center">
                                                    @if($item->product->product_image)
                                                    <img src="{{ asset('front/images/products/' . $item->product->product_image) }}" 
                                                         alt="{{ $item->product->name }}" 
                                                         class="img-fluid rounded me-3" 
                                                         style="width: 60px; height: 60px; object-fit: cover;">
                                                    @else
                                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center me-3" 
                                                         style="width: 60px; height: 60px;">No Image
                                                    </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-1 text-dark">{{ optional($item->product)->name ?? $item->product_name }} 
                                                            ({{optional($item->product)->sku ?? $item->sku}})
                                                        </h6>
                                                        @if($item->size || $item->product->product_color)
                                                        <p class="text-muted mb-0 small">
                                                            @if($item->size)<span class="me-2">Size: {{ $item->size }}</span>@endif
                                                            @if($item->product->product_color)<span>Color: {{ $item->product->product_color }}</span>@endif
                                                        </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <td class="text-center py-4">
                                                @if($orders->payment_method === 'paypal')
                                                <span class="text-dark">
                                                    {{ formatCurrency($item->price, 'USD') }}</span>
                                                @else
                                                <span class="text-dark">
                                                    {{ formatCurrency($item->price) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center py-4">
                                                <span class="badge bg-light text-dark border px-3 py-2">{{ $item->qty }}</span>
                                            </td>
                                            <td class="text-end pe-4 py-4">
                                                @if($orders->payment_method === 'paypal')
                                                <h6 class="text-dark mb-0">
                                                    {{ formatCurrency($item->qty * $item->price, 'USD') }}</h6>
                                                @else
                                                <h6 class="text-dark mb-0">
                                                    {{ formatCurrency($item->qty * $item->price) }}</h6>
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
                <div class="col-lg-8">
                  <div class="card-shadow-sm border-0 rounded-lg mt-4">
                   <div class="card-body">
                    <h5 class="mb-4">Order Tracking</h5>
                    @if($orders->logs && $orders->logs->count())
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Tracking</th>
                                    <th>Partner</th>
                                    <th>Remarks</th>
                                    <th>Updated By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders->logs as $log)
                                @php
                                $isShipped = strtolower($log->status->name ?? '') === 'shipped';
                                $trackLink = $log->tracking_link ?? $orders->tracking_link ?? null;
                                $trackNumber = $log->tracking_number ?? $orders->tracking_number ?? null; 
                                @endphp
                                <tr>
                                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ optional($log->status)->name ?? '--' }}</td>
                                    <td>{{ $trackNumber ?? '--' }}
                                    @if($trackLink)
                                    <div>
                                    <a href="{{ $trackLink }}" target="_blank" class="btn btn-sm btn-outline-primary">Track</a></td>
                                    </div>
                                    @elseif($trackNumber)
                                    @php
                                    $search = rawurlencode(($log->shipping_partner ?? '').' '. $trackNumber);
                                    $searchUrl = 'https://www.google.com/search?q='.$search;
                                    @endphp
                                    <div>
                                    <a href="{{ $searchUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary">Track</a>
                                    </div>
                                    @endif
                                    </td>
                                    <td>{{ $log->shipping_partner ?? '--' }}</td>
                                    <td style="max-width: 280px;">{!! nl2br(e($log->remarks ?? '--')) !!}</td>
                                    <td>{{ optional($log->updatedByAdmin)->name ?? optional('Admin #'.($log->updated_by ?? '--')) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted mb-0">No tracking information available.</p>
                    @endif 
                </div>
            </div>
            </div>  

                <!-- Order Summary & Shipping -->
                <div class="col-lg-4">
                    <!-- Billing Summary -->
                    <div class="card shadow-sm border-0 rounded-lg mb-4">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h5 class="mb-0 text-dark">
                                <i class="fas fa-receipt me-2"></i>Order Summary
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Subtotal</span>
                                @if($orders->payment_method === 'paypal')
                                <span class="text-dark">
                                    {{ formatCurrency($orders->subtotal, 'USD') }}</span>
                                @else
                                <span class="text-dark">
                                    {{ formatCurrency($orders->subtotal) }}</span>
                                @endif
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Shipping</span>
                                @if($orders->payment_method === 'paypal')
                                <span class="text-dark">
                                    {{ formatCurrency($orders->shipping, 'USD') }}</span>
                                @else
                                <span class="text-dark">
                                    {{ formatCurrency($orders->shipping) }}</span>
                                @endif
                            </div>
                            
                            @if(!empty($orders->discount) && $orders->discount > 0)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Discount</span>
                                @if($orders->payment_method === 'paypal')
                                <span class="text-success">-
                                    {{ formatCurrency($orders->discount, 'USD') }}</span>
                                @else
                                <span class="text-success">-
                                    {{ formatCurrency($orders->discount) }}</span>
                                @endif
                            </div>
                            @endif
                            
                            @if(!empty($orders->wallet) && $orders->wallet > 0)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Wallet Credit</span>
                                @if($orders->payment_method === 'paypal')
                                <span class="text-success">-
                                    {{ formatCurrency($orders->wallet, 'USD') }}</span>
                                @else
                                <span class="text-success">-
                                    {{ formatCurrency($orders->wallet) }}</span>
                                @endif
                            </div>
                            @endif
                            
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="text-dark">Grand Total</strong>
                                @if($orders->payment_method === 'paypal')
                                <strong class="text-primary fs-5">
                                    {{formatCurrency($orders->total, 'USD') }}</strong>
                                @else
                                <strong class="text-primary fs-5">
                                    {{formatCurrency($orders->total) }}</strong>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="card shadow-sm border-0 rounded-lg">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h5 class="mb-0 text-dark">
                                <i class="fas fa-truck me-2"></i>Shipping Address
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($orders->address)
                            <div class="d-flex align-items-start">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <h6 class="text-dark mb-1">{{ $orders->address->name ?? 'N/A' }}</h6>
                                    <p class="text-muted mb-1 small">{{ optional($orders->address)->address_line1 ?? '' }}</p>
                                    @if(optional($orders->address)->address_line2)
                                    <p class="text-muted mb-1 small">{{ $orders->address->address_line2 }}</p>
                                    @endif
                                    <p class="text-muted mb-1 small">
                                        {{ optional($orders->address)->city ?? '' }}, 
                                        {{ optional($orders->address)->state ?? '' }} 
                                        {{ optional($orders->address)->postcode ?? '' }}
                                    </p>
                                    <p class="text-muted mb-0 small">{{ optional($orders->address)->country ?? '' }}</p>
                                    @if(optional($orders->address)->phone)
                                    <p class="text-muted mb-0 small mt-2">
                                        <i class="fas fa-phone me-1"></i>{{ $orders->address->phone }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            @else
                            <div class="text-center py-3">
                                <i class="fas fa-map-marker-alt fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">No shipping address provided</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    

                    <!-- Payment Information -->
                    <div class="card shadow-sm border-0 rounded-lg mt-4">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h5 class="mb-0 text-dark">
                                <i class="fas fa-credit-card me-2"></i>Payment Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Method</span>
                                <span class="text-dark text-capitalize">{{ $orders->payment_method ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Status</span>
                                @php
                                        $statusConfig = [
                                            'pending' => ['class' => 'bg-warning text-dark', 'icon' => 'clock'],
                                            'processing' => ['class' => 'bg-info', 'icon' => 'sync'],
                                            'completed' => ['class' => 'bg-success', 'icon' => 'check'],
                                            'cancelled' => ['class' => 'bg-danger', 'icon' => 'times'],
                                            'shipped' => ['class' => 'bg-primary', 'icon' => 'shipping-fast']
                                        ];
                                        $status = strtolower($orders->status);
                                        $config = $statusConfig[$status] ?? ['class' => 'bg-secondary', 'icon' => 'question'];
                                    @endphp
                                    <span class="badge {{ $config['class'] }} rounded-pill px-4 py-2 fs-6">
                                        <i class="fas fa-{{ $config['icon'] }} me-2"></i>
                                        {{ ucfirst($orders->status) }}
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- Action Buttons -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                            <i class="fas fa-arrow-left me-2"></i>Back to Orders
                        </a>
                        <div>
                            @if($orders->status === 'pending')
                            <button class="btn btn-danger btn-lg px-4 me-2">
                                <i class="fas fa-times me-2"></i>Cancel Order
                            </button>
                            @endif
                            <button class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-print me-2"></i>Print Invoice
                            </button>
                        </div>
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
    
    .table tbody tr {
        transition: background-color 0.2s ease-in-out;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
</style>
@endpush