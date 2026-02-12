@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history"></i> Commission History – {{ $vendor->name }}
                @if($vendor->vendorDetails->shop_name ?? false)
                    <small class="text-muted">({{ $vendor->vendorDetails->shop_name }})</small>
                @endif
            </h5>
            <div>
                <span class="badge bg-info">Commission: {{ $vendor->vendorDetails->commission_percent ?? 0 }}%</span>
                <a href="{{ route('admin.vendors.show', $vendor->id) }}" class="btn btn-sm btn-secondary ml-2">
                    <i class="fas fa-arrow-left"></i> Back to Vendor
                </a>
            </div>
        </div>
        <div class="card-body">

            {{-- Summary Cards --}}
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Sales</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ${{ number_format($summary['summary']['total_amount'] ?? 0, 2) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Commission</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ${{ number_format($summary['summary']['total_commission'] ?? 0, 2) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Vendor Earnings</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ${{ number_format($summary['summary']['total_vendor_amount'] ?? 0, 2) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-wallet fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Orders Count</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $summary['summary']['total_orders'] ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-box fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pending / Paid Breakdown --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-light shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-warning font-weight-bold">Pending Payout</span>
                                <h4 class="mt-2">${{ number_format($summary['summary']['pending_amount'] ?? 0, 2) }}</h4>
                            </div>
                            <i class="fas fa-clock fa-3x text-warning opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-success font-weight-bold">Paid to Date</span>
                                <h4 class="mt-2">${{ number_format($summary['summary']['paid_amount'] ?? 0, 2) }}</h4>
                            </div>
                            <i class="fas fa-check-circle fa-3x text-success opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter Form --}}
            <form method="GET" action="{{ route('admin.vendors.commission-history', $vendor->id) }}" class="mb-4">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control form-control-sm">
                                <option value="">All</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>From Date</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>To Date</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-sm btn-block">Filter</button>
                        <a href="{{ route('admin.vendors.commission-history', $vendor->id) }}" class="btn btn-secondary btn-sm btn-block mt-1">Reset</a>
                    </div>
                </div>
            </form>

            {{-- Commission History Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Subtotal</th>
                            <th>Commission %</th>
                            <th>Commission</th>
                            <th>Vendor Receives</th>
                            <th>Status</th>
                            <th>Payment Date</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($summary['history'] as $record)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($record->commission_date)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('orders.show', $record->order_id) }}" target="_blank">
                                    #{{ $record->order_id }}
                                </a>
                            </td>
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
                                    <span class="badge bg-secondary">{{ ucfirst($record->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $record->payment_date ? \Carbon\Carbon::parse($record->payment_date)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $record->payment_reference ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">No commission records found for this vendor.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $summary['history']->withQueryString()->links() }}
            </div>

        </div>
    </div>
</div>
@endsection