@extends('admin.layout.layout')
@section('content')
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-ms-6">
                    <h3 class="mb-0">Management</h1>
                </div>
                <div class="col-ms-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Orders
                        </li>
                    </ol>    
                </div>
            </div>
        </div>       
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card"></div>
               <div class="card-header">
                  <h3 class="card-title">Orders</h3>
               </div>
               <div class="card-body">
                  <table id="orders" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Total</th>
                            <th>Paiement</th>
                            <th>Status</th>
                            <th>Created at</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ optional($order->user)->name ?? 'Guest User' }}</td>
                                <td>{{ formatCurrency($order->total, 2) }}</td>
                                <td>{{ ucfirst($order->payment_method ?? 'N/A') }}</td>
                                <td>{{ ucfirst($order->status) }}</td>
                                <td>{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <a href="{{ url('admin/orders/' . $order->id) }}" class="btn btn-sm btn-primary">View</a>
                                    @if(strtolower($order->status) == 'shipped')
                                    <a href="{{route('admin.orders.invoice', $order->id)}}" target="_blank" 
                                    class="btn btn-sm btn-outline-secondary ms-1">
                                    <i class="fas fa-file-invoice"></i> Invoice </a>
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
</main>
@endsection