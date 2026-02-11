@extends('vendor.layout.layout')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Commission History</h5>
            <a href="{{ route('vendor.commissions.export') }}" class="btn btn-sm btn-success">
                <i class="fas fa-download"></i> Export CSV
            </a>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <form method="GET" action="{{ route('vendor.commissions.history') }}" class="form-inline">
                        <div class="form-group mr-2">
                            <label for="status" class="mr-1">Status:</label>
                            <select name="status" id="status" class="form-control form-control-sm">
                                <option value="">All</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group mr-2">
                            <label for="start_date" class="mr-1">From:</label>
                            <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                        </div>
                        <div class="form-group mr-2">
                            <label for="end_date" class="mr-1">To:</label>
                            <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="{{ route('vendor.commissions.history') }}" class="btn btn-sm btn-secondary ml-2">Reset</a>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Total Sales</h6>
                            <h4 class="card-title">${{ number_format($summary['total_sales'], 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Total Commission</h6>
                            <h4 class="card-title">${{ number_format($summary['total_commission'], 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Your Earnings</h6>
                            <h4 class="card-title">${{ number_format($summary['total_earnings'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- History Table -->
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
                            <th>You Received</th>
                            <th>Status</th>
                            <th>Payment Date</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->commission_date)->format('d/m/Y') }}</td>
                            <td>#{{ $item->order_id }}</td>
                            <td>{{ Str::limit($item->product_name, 30) }}</td>
                            <td>${{ number_format($item->subtotal, 2) }}</td>
                            <td>{{ $item->commission_percent }}%</td>
                            <td class="text-danger">-${{ number_format($item->commission_amount, 2) }}</td>
                            <td class="text-success">${{ number_format($item->vendor_amount, 2) }}</td>
                            <td>
                                @if($item->status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($item->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $item->payment_date ? \Carbon\Carbon::parse($item->payment_date)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $item->payment_reference ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">No commission records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $history->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection