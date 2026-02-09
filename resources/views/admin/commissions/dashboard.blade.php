@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₹{{ number_format($dashboard['totals']->total_sales, 2) }}
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Commission</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₹{{ number_format($dashboard['totals']->total_commission, 2) }}
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Payout</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₹{{ number_format($pendingPayments->total_pending ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Vendors</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $activeVendors }}/{{ $totalVendors }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor Commission Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Vendor Commission Breakdown</h6>
            <div class="d-flex gap-2">
                <select id="periodFilter" class="form-control form-control-sm" style="width: auto;">
                    <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ $period == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ $period == 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="year" {{ $period == 'year' ? 'selected' : '' }}>This Year</option>
                    <option value="all">All Time</option>
                </select>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#bulkCommissionModal">
                    <i class="fas fa-edit"></i> Bulk Update
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="vendorCommissionTable">
                    <thead>
                        <tr>
                            <th>Vendor</th>
                            <th>Commission %</th>
                            <th>Total Sales</th>
                            <th>Commission</th>
                            <th>Vendor Earnings</th>
                            <th>Pending</th>
                            <th>Paid</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dashboard['vendor_breakdown'] as $vendor)
                        <tr>
                            <td>
                                <strong>{{ $vendor->name }}</strong><br>
                                <small class="text-muted">{{ $vendor->shop_name }}</small>
                            </td>
                            <td>
                                <span id="commissionDisplay_{{ $vendor->id }}">
                                    {{ $vendor->commission_percent }}%
                                </span>
                                <button class="btn btn-sm btn-outline-primary ms-2 edit-commission" 
                                        data-vendor-id="{{ $vendor->id }}"
                                        data-vendor-name="{{ $vendor->name }}"
                                        data-commission="{{ $vendor->commission_percent }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                            <td>₹{{ number_format($vendor->total_sales, 2) }}</td>
                            <td class="text-success">₹{{ number_format($vendor->total_commission, 2) }}</td>
                            <td>₹{{ number_format($vendor->vendor_earnings, 2) }}</td>
                            <td>
                                <span class="badge bg-warning">
                                    ₹{{ number_format($vendor->vendor_earnings - ($vendor->paid_amount ?? 0), 2) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    ₹{{ number_format($vendor->paid_amount ?? 0, 2) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.vendors.commission-history', $vendor->id) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="btn btn-sm btn-primary pay-vendor" 
                                            data-vendor-id="{{ $vendor->id }}"
                                            data-vendor-name="{{ $vendor->name }}">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </button>
                                    <a href="{{ route('admin.vendors.show', $vendor->id) }}" 
                                       class="btn btn-sm btn-secondary">
                                        <i class="fas fa-user"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Commission Trend Chart -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Commission Trend (Last 7 Days)</h6>
        </div>
        <div class="card-body">
            <canvas id="commissionChart" height="100"></canvas>
        </div>
    </div>
</div>

<!-- Commission Edit Modal -->
<div class="modal fade" id="editCommissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Commission for <span id="vendorName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateCommissionForm">
                <div class="modal-body">
                    <input type="hidden" id="vendor_id" name="vendor_id">
                    <div class="mb-3">
                        <label class="form-label">Commission Percentage</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="commission_percent" 
                                   name="commission_percent" min="0" max="100" step="0.01" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text">
                            Current average: <span id="currentAvg"></span>%
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Commission</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Commission Update Modal -->
<div class="modal fade" id="bulkCommissionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Update Vendor Commissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulkCommissionForm">
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Vendor</th>
                                <th>Current</th>
                                <th>New Commission %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dashboard['vendor_breakdown'] as $vendor)
                            <tr>
                                <td>{{ $vendor->name }}</td>
                                <td>{{ $vendor->commission_percent }}%</td>
                                <td>
                                    <input type="number" 
                                           name="commissions[{{ $vendor->id }}]" 
                                           class="form-control form-control-sm" 
                                           value="{{ $vendor->commission_percent }}"
                                           min="0" max="100" step="0.01">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update All</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="paymentForm">
                <div class="modal-body">
                    <input type="hidden" id="pay_vendor_id" name="vendor_id">
                    <div class="mb-3">
                        <label class="form-label">Vendor</label>
                        <input type="text" id="pay_vendor_name" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (₹)</label>
                        <input type="number" name="amount" class="form-control" required step="0.01">
                        <div class="form-text">
                            Pending amount: <span id="pendingAmount">₹0.00</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="">Select Method</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="upi">UPI</option>
                            <option value="paypal">PayPal</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reference Number</label>
                        <input type="text" name="reference" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Process Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Commission Chart
    const ctx = document.getElementById('commissionChart').getContext('2d');
    const dates = Object.keys(@json($dashboard['trend']));
    const amounts = Object.values(@json($dashboard['trend']));
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Commission (₹)',
                data: amounts,
                borderColor: 'rgba(78, 115, 223, 1)',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value;
                        }
                    }
                }
            }
        }
    });

    // Edit Commission
    $('.edit-commission').on('click', function() {
        const vendorId = $(this).data('vendor-id');
        const vendorName = $(this).data('vendor-name');
        const commission = $(this).data('commission');
        
        $('#vendor_id').val(vendorId);
        $('#vendorName').text(vendorName);
        $('#commission_percent').val(commission);
        $('#editCommissionModal').modal('show');
    });

    // Update Single Commission
    $('#updateCommissionForm').on('submit', function(e) {
        e.preventDefault();
        
        const vendorId = $('#vendor_id').val();
        const commissionPercent = $('#commission_percent').val();
        
        $.ajax({
            url: `/admin/vendors/${vendorId}/update-commission`,
            method: 'POST',
            data: {
                commission_percent: commissionPercent,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status) {
                    $('#commissionDisplay_' + vendorId).text(commissionPercent + '%');
                    $('#editCommissionModal').modal('hide');
                    showToast('success', response.message);
                } else {
                    showToast('error', response.message);
                }
            }
        });
    });

    // Bulk Update Commissions
    $('#bulkCommissionForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("admin.vendors.bulk-update-commissions") }}',
            method: 'POST',
            data: $(this).serialize() + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
            success: function(response) {
                if (response.status) {
                    $('#bulkCommissionModal').modal('hide');
                    showToast('success', response.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast('error', response.message);
                }
            }
        });
    });

    // Payment Modal
    $('.pay-vendor').on('click', function() {
        const vendorId = $(this).data('vendor-id');
        const vendorName = $(this).data('vendor-name');
        
        $('#pay_vendor_id').val(vendorId);
        $('#pay_vendor_name').val(vendorName);
        
        // Get pending amount
        $.ajax({
            url: `/admin/vendors/${vendorId}/pending-amount`,
            method: 'GET',
            success: function(response) {
                $('#pendingAmount').text('₹' + response.amount.toFixed(2));
            }
        });
        
        $('#paymentModal').modal('show');
    });

    // Process Payment
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("admin.commissions.process-payment") }}',
            method: 'POST',
            data: $(this).serialize() + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
            success: function(response) {
                if (response.success) {
                    $('#paymentModal').modal('hide');
                    showToast('success', response.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast('error', response.message);
                }
            }
        });
    });

    // Period Filter
    $('#periodFilter').on('change', function() {
        const period = $(this).val();
        window.location.href = `{{ route('admin.commissions.dashboard') }}?period=${period}`;
    });

    function showToast(type, message) {
        const toast = `<div class="toast align-items-center text-white bg-${type} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>`;
        $('.toast-container').append(toast);
        $('.toast').toast('show');
    }
});
</script>
@endsection