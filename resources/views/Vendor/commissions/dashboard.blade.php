@extends('vendor.layout.layout')

@section('content')
<div class="container-fluid">

    <!-- Period Filter -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold text-primary">Commission Dashboard</h5>
                    <div class="d-flex gap-2">
                        <select id="periodFilter" class="form-control form-control-sm" style="width: auto;">
                            <option value="today"  {{ $dashboard['period'] == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="week"   {{ $dashboard['period'] == 'week' ? 'selected' : '' }}>This Week</option>
                            <option value="month"  {{ $dashboard['period'] == 'month' ? 'selected' : '' }}>This Month</option>
                            <option value="year"   {{ $dashboard['period'] == 'year' ? 'selected' : '' }}>This Year</option>
                            <option value="all"    {{ $dashboard['period'] == 'all' ? 'selected' : '' }}>All Time</option>
                        </select>
                        <a href="{{ route('vendor.commissions.export') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-download"></i> Export
                        </a>
                        <a href="{{ route('vendor.commissions.history') }}" class="btn btn-sm btn-info">
                            <i class="fas fa-list"></i> Full History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($dashboard['totals']->total_sales, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Your Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $dashboard['commission_percent'] }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Commission</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($dashboard['totals']->total_commission, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Your Earnings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($dashboard['totals']->total_earned, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending / Paid Row -->
    <div class="row mb-4">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 border-left-warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Payout</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($pending, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 border-left-success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid to Date</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($paid, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 7-Day Trend Chart -->
    @if(count($dashboard['trend']) > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Commission Trend (Last 7 Days)</h6>
        </div>
        <div class="card-body">
            <canvas id="commissionChart"></canvas>
        </div>
    </div>
    @endif

    <!-- Recent Commissions -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Recent Commission History</h6>
            <a href="{{ route('vendor.commissions.history') }}" class="btn btn-sm btn-primary">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="card-body">
            @if($dashboard['recent']->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Subtotal</th>
                            <th>Commission %</th>
                            <th>Commission</th>
                            <th>You Receive</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dashboard['recent'] as $item)
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle"></i> No commission records for this period.
            </div>
            @endif
        </div>
    </div>

    <!-- Monthly Breakdown -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Monthly Breakdown</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Year-Month</th>
                            <th>Sales</th>
                            <th>Commission</th>
                            <th>Earnings</th>
                            <th>Pending</th>
                            <th>Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dashboard['monthly'] as $month)
                        <tr>
                            <td>{{ $month->year }}-{{ str_pad($month->month, 2, '0', STR_PAD_LEFT) }}</td>
                            <td>${{ number_format($month->total_sales, 2) }}</td>
                            <td>${{ number_format($month->total_commission, 2) }}</td>
                            <td>${{ number_format($month->total_earnings, 2) }}</td>
                            <td>${{ number_format($month->pending, 2) }}</td>
                            <td>${{ number_format($month->paid, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    @if($dashboard['topProducts']->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Top 10 Selling Products</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Orders</th>
                            <th>Qty Sold</th>
                            <th>Sales</th>
                            <th>Commission</th>
                            <th>Earnings</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dashboard['topProducts'] as $product)
                        <tr>
                            <td>{{ $product->product_name }}</td>
                            <td>{{ $product->order_count }}</td>
                            <td>{{ $product->total_qty }}</td>
                            <td>${{ number_format($product->total_sales, 2) }}</td>
                            <td>${{ number_format($product->total_commission, 2) }}</td>
                            <td>${{ number_format($product->total_earnings, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@section('scripts')
@if(count($dashboard['trend']) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('commissionChart').getContext('2d');
    const trend = @json($dashboard['trend']);
    const labels = Object.keys(trend);
    const earnedData = labels.map(date => trend[date].earned);
    const commissionData = labels.map(date => trend[date].commission);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Your Earnings ($)',
                    data: earnedData,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Commission ($)',
                    data: commissionData,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += '$' + context.raw.toFixed(2);
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endif

<script>
$(document).ready(function() {
    $('#periodFilter').on('change', function() {
        const period = $(this).val();
        window.location.href = "{{ route('vendor.commissions.dashboard') }}?period=" + period;
    });
});
</script>
@endsection