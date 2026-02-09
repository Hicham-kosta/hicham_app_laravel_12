@extends('vendor.layout.layout')

@section('content')
<div class="container-fluid">
    <!-- Vendor Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Products</h6>
                            <h3 class="mb-0">{{ $stats['total_products'] }}</h3>
                        </div>
                        <i class="fas fa-box fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Sales</h6>
                            <h3 class="mb-0">₹{{ number_format($stats['total_sales'], 2) }}</h3>
                        </div>
                        <i class="fas fa-rupee-sign fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Orders</h6>
                            <h3 class="mb-0">{{ $stats['total_orders'] }}</h3>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Pending Balance</h6>
                            <h3 class="mb-0">₹{{ number_format($stats['pending_balance'], 2) }}</h3>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('vendor.products.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-plus-circle me-2"></i> Add Product
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('vendor.products.index') }}" class="btn btn-success w-100">
                                <i class="fas fa-boxes me-2"></i> View Products
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('orders.index') }}" class="btn btn-info w-100">
                                <i class="fas fa-shopping-cart me-2"></i> View Orders
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('vendor.commissions') }}" class="btn btn-warning w-100">
                                <i class="fas fa-percentage me-2"></i> Commissions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Products -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Products</h5>
            <a href="{{ route('vendor.products.index') }}" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="card-body">
            @if($recentProducts->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Code</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentProducts as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($product->main_image)
                                    <img src="{{ asset('front/images/products/'.$product->main_image) }}" 
                                         alt="{{ $product->product_name }}" 
                                         width="40" class="me-2 rounded">
                                    @else
                                    <div class="bg-light rounded me-2" style="width:40px;height:40px;"></div>
                                    @endif
                                    <div>
                                        <strong>{{ $product->product_name }}</strong><br>
                                        <small class="text-muted">{{ $product->category->name ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->product_code }}</td>
                            <td>₹{{ number_format($product->product_price, 2) }}</td>
                            <td>
                                @if($product->stock > 0)
                                <span class="badge bg-success">{{ $product->stock }}</span>
                                @else
                                <span class="badge bg-danger">Out of Stock</span>
                                @endif
                            </td>
                            <td>
                                @if($product->status == 1)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('vendor.products.edit', $product->id) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('vendor.products.index', $product->id) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No products found. <a href="{{ route('vendor.products.create') }}">Add your first product</a>
            </div>
            @endif
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Orders</h5>
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="card-body">
            @if($recentOrders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->user->name ?? 'Guest' }}</td>
                            <td>₹{{ number_format($order->total, 2) }}</td>
                            <td>
                                @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'shipped' => 'primary',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger'
                                ];
                                $color = $statusColors[$order->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('vendor.orders.show', $order->id) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No orders found.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection