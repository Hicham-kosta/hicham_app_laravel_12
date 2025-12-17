@extends('front.layout.layout')

@section('title', 'My Orders')

@section('content')
    <!-- Page Header -->
    <div class="container-fluid bg-dark mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
            <h1 class="font-weight-semi-bold text-uppercase mb-3 text-white">My Orders</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}" class="text-white">Home</a></p>
                <p class="m-0 px-2 text-white">-</p>
                <p class="m-0 text-white">Order History</p>
            </div>
        </div>
    </div>

    <!-- Order List Section -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-header bg-gradient-primary text-white py-4">
                            <h4 class="mb-0 font-weight-semi-bold">
                                <i class="fas fa-clipboard-list me-2"></i>Order History
                            </h4>
                        </div>
                        <div class="card-body p-0">
                            @if($orders->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-borderless mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-4 py-3 border-bottom">Order Details</th>
                                                <th class="text-center py-3 border-bottom">Items</th>
                                                <th class="text-center py-3 border-bottom">Total Amount</th>
                                                <th class="text-center py-3 border-bottom">Status</th>
                                                <th class="text-center pe-4 py-3 border-bottom">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($orders as $order)
                                            <tr class="border-bottom">
                                                <td class="ps-4 py-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                                            <i class="fas fa-shopping-bag"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1 text-dark">Order #{{ $order->order_number ?? $order->id }}</h6>
                                                            <p class="text-muted mb-0 small">
                                                                <i class="fas fa-calendar me-1"></i>
                                                                {{ $order->created_at->format('M d, Y') }}
                                                            </p>
                                                            <p class="text-muted mb-0 small">
                                                                <i class="fas fa-clock me-1"></i>
                                                                {{ $order->created_at->format('h:i A') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center py-4">
                                                    <span class="badge bg-secondary rounded-pill px-3 py-2">
                                                        {{ $order->orderItems->sum('qty') }} Items
                                                    </span>
                                                </td>
                                                <td class="text-center py-4">
                                                    <h6 class="text-dark mb-0">
                                                        {{ formatCurrency($order->total) }}
                                                    </h6>
                                                </td>
                                                <td class="text-center py-4">
                                                    @php
                                                        $latest = $order->latestLog ?? null;
                                                        $displayStatus = $latest?->status?->name ?? $order->status ?? 'Pending';
                                                    @endphp
                                                    <span class="badge 
                                                    @if(strtolower($displayStatus) === 'pending') bg-warning text-dark 
                                                    @elseif(strtolower($displayStatus) === 'completed') bg-success
                                                    @elseif(strtolower($displayStatus) === 'cancelled') bg-danger 
                                                    @else bg-secondary 
                                                    @endif">
                                                    {{ $displayStatus }} 
                                                    </span>
                                                    @if($latest)
                                                    <div class="small text-muted mt-1">
                                                        Updated at: {{ $latest->created_at->diffForHumans() }}
                                                        @if(!empty($latest->tracking_link) || !empty($latest->tracking_number))
                                                        <br>&nbsp;&nbsp;
                                                        @if(!empty($latest->tracking_link))
                                                        <a href="{{ $latest->tracking_link }}" target="_blank" class="small">
                                                            Track</a>
                                                            @else
                                                            <a href="https://www.google.com/search?q={{ rawurlencode(($latest->
                                                            shipping_partner ?? '').' '.$latest->tracking_number) }}" target="_blank" 
                                                            class="small">Track</a>
                                                        @endif
                                                        @endif
                                                    </div>
                                                    @endif
                                                </td>
                                                <td class="text-center pe-4 py-4">
                                                    <a href="{{ route('user.orders.show', $order->id) }}" 
                                                       class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                                        <i class="fas fa-eye me-1"></i>View Details
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Pagination -->
                                <div class="card-footer bg-white border-0 py-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="text-muted mb-0">
                                            Showing {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders
                                        </p>
                                        <nav>
                                            {{ $orders->links('pagination::bootstrap-5') }}
                                        </nav>
                                    </div>
                                </div>
                            @else
                                <!-- Empty State -->
                                <div class="text-center py-5">
                                    <div class="empty-state-icon mb-4">
                                        <i class="fas fa-shopping-bag fa-4x text-muted"></i>
                                    </div>
                                    <h4 class="text-muted mb-3">No Orders Yet</h4>
                                    <p class="text-muted mb-4">You haven't placed any orders. Start shopping to see your order history here.</p>
                                    <a href="{{ url('/') }}" class="btn btn-primary btn-lg rounded-pill px-5">
                                        <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    @if($orders->count() > 0)
    <div class="container-fluid py-4 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <i class="fas fa-clipboard-list fa-2x text-primary mb-3"></i>
                            <h3 class="text-dark">{{ $orders->total() }}</h3>
                            <p class="text-muted mb-0">Total Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                            <h3 class="text-dark">{{ $orders->where('status', 'completed')->count() }}</h3>
                            <p class="text-muted mb-0">Completed</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <i class="fas fa-clock fa-2x text-warning mb-3"></i>
                            <h3 class="text-dark">{{ $orders->where('status', 'pending')->count() }}</h3>
                            <p class="text-muted mb-0">Pending</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <i class="fas fa-shipping-fast fa-2x text-info mb-3"></i>
                            <h3 class="text-dark">{{ $orders->where('status', 'shipped')->count() }}</h3>
                            <p class="text-muted mb-0">Shipped</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
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
    
    .empty-state-icon {
        opacity: 0.7;
    }
</style>
@endpush