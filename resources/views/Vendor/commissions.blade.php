@extends('vendor.layout.layout')
@section('content')
<div class="container-fluid">
    @php
        // Set default values if variables don't exist
        $summary = $summary ?? [
            'total_amount' => 0,
            'pending_amount' => 0,
            'paid_amount' => 0,
            'total_orders' => 0,
            'total_commission' => 0,
            'total_vendor_amount' => 0,
        ];
        
        $history = $history ?? collect([]);
        $commissionPercent = $commissionPercent ?? 0;
    @endphp
    
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Sales</h6>
                            <h3 class="mb-0">${{ number_format($summary['total_amount'], 2) }}</h3>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Commission Rate</h6>
                            <h3 class="mb-0">{{ $commissionPercent }}%</h3>
                        </div>
                        <i class="fas fa-percentage fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Pending Amount</h6>
                            <h3 class="mb-0">${{ number_format($summary['pending_amount'], 2) }}</h3>
                        </div>
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Received</h6>
                            <h3 class="mb-0">${{ number_format($summary['paid_amount'], 2) }}</h3>
                        </div>
                        <i class="fas fa-wallet fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission History Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Commission History</h5>
        </div>
        <div class="card-body">
            @if($history->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="commissionTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Amount</th>
                            <th>Commission %</th>
                            <th>Commission</th>
                            <th>You Receive</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $record)
                        <tr>
                            <td>{{ $record->created_at->format('d/m/Y') }}</td>
                            <td>#{{ $record->order_id }}</td>
                            <td>{{ Str::limit($record->product_name, 30) }}</td>
                            <td>${{ number_format($record->subtotal, 2) }}</td>
                            <td>{{ $record->commission_percent }}%</td>
                            <td class="text-danger">-${{ number_format($record->commission_amount, 2) }}</td>
                            <td class="text-success">${{ number_format($record->vendor_amount, 2) }}</td>
                            <td>
                                @if($record->status == 'paid')
                                <span class="badge bg-success">Paid</span>
                                @elseif($record->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                                @else
                                <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No commission history found. Your commissions will appear here after sales.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection