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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderItems as $item)
                            <tr>
                                <td>{{ optional($item->product)->name ?? $item->product_name}} 
                                    ({{optional($item->product)->sku ?? $item->sku}})
                                </td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ formatCurrency($item->price, 2) }}</td>
                                <td>{{ formatCurrency($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                    <p><strong>Subtotal:</strong> {{ formatCurrency($order->subtotal, 2) }}</p>
                    @if($order->discount > 0)
                    <p><strong>Discount:</strong> {{ formatCurrency($order->discount, 2) }}</p>
                    @endif
                    @if($order->wallet > 0)
                    <p><strong>Wallet:</strong> {{ formatCurrency($order->wallet, 2) }}</p>
                    @endif
                    @if($order->shipping > 0)
                    <p><strong>Shipping:</strong> {{ formatCurrency($order->shipping, 2) }}</p>
                    @endif
                    <p><strong>Total:</strong> {{ formatCurrency($order->total, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection