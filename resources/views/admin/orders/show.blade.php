@extends('admin.layout.layout')
@section('content')
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h3 class="mb-0">Order #{{ $order->id }} Details</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{url('admin/orders')}}">Orders</a></li>
                        <li class="breadcrumb-item active">
                            Detail
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card mb-4">
                <div class="card-body">
                    <h5><strong>Order Information</strong></h5>
                    <p><strong>User: </strong>{{optional($order->user)->name ?? 'Guest User'}}</p>
                    <p><strong>Date: </strong>{{$order->created_at->format('Y-m-d H:i:s') ?? 'N/A'}}</p>
                    <p><strong>Status: </strong>{{ucfirst($order->status)}}</p>
                    <p><strong>Payment Method: </strong>{{$order->payment_method ?? 'N/A'}}</p>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <h5><strong>Order Items</strong></h5>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                                <th>GST (%)</th>
                                <th>GST Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderItems as $item)
                            <tr>
                                <td>{{ optional($item->product)->name ?? $item->product_name}} 
                                    ({{optional($item->product)->sku ?? $item->sku}})
                                </td>
                                <td>{{ $item->qty }}</td>
                                @if($order->payment_method === 'paypal')
                                <td>{{ formatCurrency($item->price, 'USD') }}</td>
                                @else
                                <td>{{ formatCurrency($item->price) }}</td>
                                @endif
                                @if($order->payment_method === 'paypal')
                                <td>{{ formatCurrency($item->qty * $item->price, 'USD') }}</td>
                                @else
                                <td>{{ formatCurrency($item->qty * $item->price) }}</td>
                                @endif
                                <td>{{ number_format($item->product_gst ?? 0, 2) }}%</td>
                                @if($order->payment_method === 'paypal')
                                <td>{{ formatCurrency($item->product_gst_amount ?? 0, 'USD') }}</td>
                                @else
                                <td>{{ formatCurrency($item->product_gst_amount ?? 0, 2) }}</td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- In resources/views/admin/orders/show.blade.php, add this section: -->

@php
use App\Services\Admin\VendorCommissionService;
$commissionService = new VendorCommissionService();
$commissionData = $commissionService->calculateOrderCommissions($order->id);
@endphp

<!-- After order items table, add: -->
<div class="card mt-4">
    <div class="card-header bg-light">
        <h5 class="card-title mb-0">
            <i class="fas fa-percentage me-2"></i>Commission Breakdown
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Vendor</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Commission %</th>
                        <th>Commission Amount</th>
                        <th>Vendor Receives</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($commissionData['commission_data'] as $item)
                    <tr>
                        <td>{{ $item['product_name'] }}</td>
                        <td>
                            {{ $item['vendor_name'] }}
                            <small class="text-muted d-block">ID: {{ $item['vendor_id'] }}</small>
                        </td>
                        <td>₹{{ number_format($item['price'], 2) }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>₹{{ number_format($item['subtotal'], 2) }}</td>
                        <td>{{ number_format($item['commission_percent'], 2) }}%</td>
                        <td class="text-danger">₹{{ number_format($item['commission_amount'], 2) }}</td>
                        <td class="text-success">₹{{ number_format($item['vendor_payable'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="4" class="text-end">Totals:</th>
                        <th>₹{{ number_format($commissionData['summary']['total_order_amount'], 2) }}</th>
                        <th></th>
                        <th class="text-danger">₹{{ number_format($commissionData['summary']['total_commission'], 2) }}</th>
                        <th class="text-success">₹{{ number_format($commissionData['summary']['total_vendor_payable'], 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Commission Summary</h6>
                    <ul class="mb-0">
                        <li>Order Total: ₹{{ number_format($commissionData['summary']['total_order_amount'], 2) }}</li>
                        <li>Total Commission: ₹{{ number_format($commissionData['summary']['total_commission'], 2) }}</li>
                        <li>Total Vendor Payout: ₹{{ number_format($commissionData['summary']['total_vendor_payable'], 2) }}</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Notes</h6>
                    <small>
                        • Commission is calculated per product based on vendor's commission rate<br>
                        • GST and other charges are included in subtotal before commission calculation<br>
                        • Vendor receives: Subtotal - Commission<br>
                        • This is for informational purposes only
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
            <div class="card mb-4">
                <div class="card-body">
                    <h5><strong>Update Order Status</strong></h5>
                    @if(Session('success_message'))
                    <div class="alert alert-success">{{session('success_message')}}</div>
                    @endif
                    @if(Session('error_message'))
                    <div class="alert alert-danger">{{session('error_message')}}</div>
                    @endif
                    <form action="{{route('orders.updateStatus', $order->id)}}" method="post">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="order_status_id">Order Status</label>
                                <select name="order_status_id" id="order_status_id" class="form-control">
                                    <option value="">--Select Status--</option>
                                    @foreach($statuses as $s)
                                    <option value="{{$s->id}}" {{ strtolower($order->status) === strtolower($s->name) ? 'selected' : '' }}>
                                    {{$s->name}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 shipped-field" style="display: none;">
                                <label for="tracking_number">Order Tracking</label>
                                <input type="text" name="tracking_number" id="tracking_number" class="form-control" 
                                    value="{{$order->tracking_number}}">
                            </div>
                            <div class="col-md-4 shipped-field" style="display: none;">
                                <label for="shipping_partner">Shipping Partner</label>
                                <input type="text" name="shipping_partner" id="shipping_partner" class="form-control" 
                                    value="{{$order->shipping_partner}}">
                            </div>
                            <div class="col-md-12 shipped-field" style="display: none; margin-top: 8px;">
                                <label for="tracking_link">Tracking Link (Optional)</label>
                                <input type="url" name="tracking_link" id="tracking_link" class="form-control"
                                placeholder="https://track.example.com/parcel/ABC123456" 
                                    value="{{$order->tracking_link}}">
                                    <small class="form-text text-muted">Enter the tracking link for the order</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="remarks">Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </form>
                    <hr>
                    <h6><strong>Order Logs</strong></h6>
                    <table class="table table-sm mt-3">
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
                            @forelse ($logs as $log)
                            <tr>
                                <td>{{$log->created_at->format('Y-m-d')}}</td>
                                <td>{{optional($log->status)->name ?? '--'}}</td>
                                <td>{{$log->tracking_number ?? '--'}}
                                    @if(!empty($log->tracking_link))
                                    <br>
                                    <a href="{{$log->tracking_link}}" target="_blank">Open Tracking</a>
                                    @endif
                                </td>
                                <td>{{$log->shipping_partner ?? '--'}}</td>
                                <td>{{$log->remarks ?? '--'}}</td>
                                <td>{{optional($log->updatedByAdmin)->name ?? 'Admin #'.$log->updated_by}}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No logs available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5><strong>Delivery Address</strong></h5>
                    @if($order->address)
                    <p>{{$order->address->name ?? ''}}</p>
                    <p>{{$order->address->address_line1 ?? ''}}
                    <p>{{$order->address->city ?? ''}}, {{$order->address->state ?? ''}}, {{$order->address->postcode ?? ''}}</p>
                    <p>{{$order->address->country ?? ''}}</p>
                    @else
                    <p>No shipping address available</p>
                    @endif
                    <hr>
                    @if($order->payment_method === 'paypal')
                    <p><strong>Subtotal:</strong> {{ formatCurrency($order->subtotal, 'USD') }}</p>
                    @else
                    <p><strong>Subtotal:</strong> {{ formatCurrency($order->subtotal) }}</p>
                    @endif
                    @if($order->discount > 0)
                    @if($order->payment_method === 'paypal')
                    <p><strong>Discount:</strong> {{ formatCurrency($order->discount, 'USD') }}</p>
                    @else
                    <p><strong>Discount:</strong> {{ formatCurrency($order->discount) }}</p>
                    @endif
                    @endif
                    @if($order->wallet > 0)
                    @if($order->payment_method === 'paypal')
                    <p><strong>Wallet:</strong> {{ formatCurrency($order->wallet, 'USD') }}</p>
                    @else
                    <p><strong>Wallet:</strong> {{ formatCurrency($order->wallet) }}</p>
                    @endif
                    @endif
                    @if($order->shipping > 0)
                    @if($order->payment_method === 'paypal')
                    <p><strong>Shipping:</strong> {{ formatCurrency($order->shipping, 'USD') }}</p>
                    @else
                    <p><strong>Shipping:</strong> {{ formatCurrency($order->shipping) }}</p>
                    @endif
                    @endif
                    @if($order->taxes > 0)
                    @if($order->payment_method === 'paypal')
                    <p><strong>Tax (GST):</strong> {{ formatCurrency($order->taxes, 'USD') }}</p>
                    @else
                    <p><strong>Tax (GST):</strong> {{ formatCurrency($order->taxes) }}</p>
                    @endif  
                    @endif
                    @if($order->payment_method === 'paypal')
                    <p><strong>Total:</strong> {{ formatCurrency($order->total, 'USD') }}</p>
                    @else
                    <p><strong>Total:</strong> {{ formatCurrency($order->total) }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>
@endsection