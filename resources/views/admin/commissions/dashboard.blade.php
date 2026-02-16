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
                                ${{ number_format($dashboard['totals']->total_sales ?? 0, 2) }}

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
                                ${{ number_format($dashboard['totals']->total_commission ?? 0, 2) }}
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
                                ${{ number_format($pendingPayments->total_pending ?? 0, 2) }}
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
                            <td class="text-success">${{ number_format($vendor->total_commission, 2) }}</td>
                            <td>₹{{ number_format($vendor->vendor_earnings, 2) }}</td>
                            <td>
                                <span class="badge bg-warning">
                                    ${{ number_format($vendor->pending_amount ?? 0, 2) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    ${{ number_format($vendor->paid_amount ?? 0, 2) }}
                                </span>
                            </td>
                            <td>
    <div class="btn-group">
        <a href="{{ route('admin.vendors.commission-history', $vendor->id) }}" 
           class="btn btn-sm btn-info" title="View History">
            <i class="fas fa-eye"></i>
        </a>
        {{-- ✅ PAY BUTTON --}}
        <button class="btn btn-sm btn-primary pay-vendor" 
                data-vendor-id="{{ $vendor->id }}"
                data-vendor-name="{{ $vendor->name }}"
                data-pending="{{ $vendor->pending_amount }}">
            <i class="fas fa-money-bill-wave"></i> Pay
        </button>
        <a href="{{ route('admin.vendors.show', $vendor->id) }}" 
           class="btn btn-sm btn-secondary" title="Vendor Details">
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
            <div id="commissionChart"></div>
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

<!-- Payment Modal (keep this one) -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="paymentForm">
            @csrf
            <input type="hidden" name="vendor_id" id="pay_vendor_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Process Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Vendor</label>
                        <input type="text" id="pay_vendor_name" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount ($)</label>
                        <input type="number" name="amount" class="form-control" step="0.01" required>
                        <small class="text-muted">Pending: $<span id="pendingAmount"></span></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Method</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="">Select</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="upi">UPI</option>
                            <option value="paypal">PayPal</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Reference / Transaction ID</label>
                        <input type="text" name="reference" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Notes (optional)</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle"></i> Process Payment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    console.log("Dashboard script loaded");

$(function () {

    /* =========================================
       CSRF SETUP (IMPORTANT FOR AJAX)
    ========================================== */
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    /* =========================================
       COMMISSION TREND CHART
    ========================================== */
    const trendData = @json($dashboard['trend']);
    const dates = Object.keys(trendData);
    const amounts = Object.values(trendData);

    if (document.querySelector("#commissionChart")) {

        const options = {
            chart: {
                type: 'area',
                height: 350,
                toolbar: { show: false }
            },
            series: [{
                name: 'Commission',
                data: amounts
            }],
            xaxis: {
                categories: dates
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            dataLabels: {
                enabled: false
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "₹ " + parseFloat(val).toFixed(2);
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1
                }
            }
        };

        const chart = new ApexCharts(
            document.querySelector("#commissionChart"),
            options
        );

        chart.render();
    }


    /* =========================================
       EDIT COMMISSION (Bootstrap 5 FIXED)
    ========================================== */
    $(document).on('click', '.edit-commission', function () {

        $('#vendor_id').val($(this).data('vendor-id'));
        $('#vendorName').text($(this).data('vendor-name'));
        $('#commission_percent').val($(this).data('commission'));

        const modal = new bootstrap.Modal(document.getElementById('editCommissionModal'));
        modal.show();
    });


    $('#updateCommissionForm').on('submit', function (e) {
        e.preventDefault();

        const vendorId = $('#vendor_id').val();
        const commissionPercent = $('#commission_percent').val();

        $.post(`/admin/vendors/${vendorId}/update-commission`, {
            commission_percent: commissionPercent
        })
        .done(function (response) {

            if (response.status) {

                $('#commissionDisplay_' + vendorId)
                    .text(commissionPercent + '%');

                bootstrap.Modal
                    .getInstance(document.getElementById('editCommissionModal'))
                    .hide();

                showToast('success', response.message);
            } else {
                showToast('danger', response.message);
            }
        })
        .fail(function () {
            showToast('danger', 'Something went wrong.');
        });
    });


    /* =========================================
       BULK UPDATE COMMISSIONS
    ========================================== */
    $('#bulkCommissionForm').on('submit', function (e) {
        e.preventDefault();

        $.post(
            '{{ route("admin.vendors.bulk-update-commissions") }}',
            $(this).serialize()
        )
        .done(function (response) {

            if (response.status) {

                bootstrap.Modal
                    .getInstance(document.getElementById('bulkCommissionModal'))
                    .hide();

                showToast('success', response.message);

                setTimeout(() => location.reload(), 1200);
            } else {
                showToast('danger', response.message);
            }
        })
        .fail(function () {
            showToast('danger', 'Bulk update failed.');
        });
    });


    /* =========================================
       PAY VENDOR (FIXED)
    ========================================== */
    $(document).on('click', '.pay-vendor', function () {

        const vendorId = $(this).data('vendor-id');
        const vendorName = $(this).data('vendor-name');

        $('#pay_vendor_id').val(vendorId);
        $('#pay_vendor_name').val(vendorName);

        // Reset amount field
        $('input[name="amount"]').val('');

        // Fetch pending amount
        $.get(`/admin/vendors/${vendorId}/pending-amount`)
        .done(function (response) {

            const amount = parseFloat(response.amount || 0).toFixed(2);
            $('#pendingAmount').text(amount);

        })
        .fail(function () {
            $('#pendingAmount').text('0.00');
        });

        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
    });


    /* =========================================
       PROCESS PAYMENT
    ========================================== */
    $('#paymentForm').on('submit', function (e) {
        e.preventDefault();

        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('Processing...');

        $.post(
            '{{ route("admin.commissions.process-payment") }}',
            $(this).serialize()
        )
        .done(function (response) {

            if (response.status) {

                bootstrap.Modal
                    .getInstance(document.getElementById('paymentModal'))
                    .hide();

                showToast('success', response.message);

                setTimeout(() => location.reload(), 1500);

            } else {
                showToast('danger', response.message);
            }
        })
        .fail(function () {
            showToast('danger', 'Payment failed.');
        })
        .always(function () {
            submitBtn.prop('disabled', false)
                     .html('<i class="fas fa-check-circle"></i> Process Payment');
        });
    });


    /* =========================================
       PERIOD FILTER
    ========================================== */
    $('#periodFilter').on('change', function () {
        window.location.href =
            `{{ route('admin.commissions.dashboard') }}?period=${$(this).val()}`;
    });


    /* =========================================
       CLEAN TOAST SYSTEM (Bootstrap 5)
    ========================================== */
    function showToast(type, message) {

        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type} border-0 mb-2"
                 role="alert" data-bs-delay="3000">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button"
                            class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        $('.toast-container').append(toastHtml);

        const toastEl = $('.toast').last()[0];
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }

});
</script>

@endsection