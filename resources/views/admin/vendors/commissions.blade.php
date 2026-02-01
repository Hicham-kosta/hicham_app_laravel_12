@extends('admin.layout.layout')
@section('content')
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Vendor Commission Management</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.vendors') }}">Vendors</a></li>
                        <li class="breadcrumb-item active">Commissions</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="app-content">
        <div class="container-fluid">
            @if(Session::has('success_message'))
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    <strong>Success: </strong>{{ Session::get('success_message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-percentage me-2"></i>Vendor Commission Rates
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="vendorCommissionsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Vendor Name</th>
                                    <th>Shop Name</th>
                                    <th>Email</th>
                                    <th>KYC Status</th>
                                    <th>Account Status</th>
                                    <th>Commission %</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vendors as $index => $vendor)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $vendor['name'] }}</td>
                                    <td>{{ $vendor['shop_name'] }}</td>
                                    <td>{{ $vendor['email'] }}</td>
                                    <td>
                                        @if($vendor['is_verified'] == 1)
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($vendor['is_verified'] == 2)
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $vendor['status'] == 'Active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $vendor['status'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <input type="number" 
                                               class="form-control form-control-sm commission-input" 
                                               data-vendor-id="{{ $vendor['id'] }}"
                                               value="{{ $vendor['commission_percent'] }}" 
                                               min="0" 
                                               max="100" 
                                               step="0.1"
                                               style="width: 80px;">
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary update-single-commission"
                                                data-vendor-id="{{ $vendor['id'] }}">
                                            <i class="fas fa-save"></i> Save
                                        </button>
                                        <a href="{{ route('admin.vendors.show', $vendor['id']) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <button class="btn btn-success" id="bulkUpdateCommissions">
                            <i class="fas fa-save me-1"></i> Save All Changes
                        </button>
                        <button class="btn btn-secondary" id="resetCommissions">
                            <i class="fas fa-undo me-1"></i> Reset Changes
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calculator me-2"></i>Commission Calculator
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Order Amount (₹)</label>
                                <input type="number" class="form-control" id="calcOrderAmount" value="1000">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Commission Percentage (%)</label>
                                <input type="number" class="form-control" id="calcCommissionPercent" value="15">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button class="btn btn-primary d-block" id="calculateCommission">
                                    <i class="fas fa-calculator me-1"></i> Calculate
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3" id="calculationResult" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert alert-success">
                                <h6><i class="fas fa-chart-pie me-1"></i> Commission Breakdown</h6>
                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>Order Amount:</strong></p>
                                        <h4 id="calcOrderTotal">₹0.00</h4>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>Commission:</strong></p>
                                        <h4 id="calcCommissionAmount" class="text-danger">₹0.00</h4>
                                        <small id="calcCommissionPercentText">0%</small>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>Vendor Receives:</strong></p>
                                        <h4 id="calcVendorReceives" class="text-success">₹0.00</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    let changedCommissions = {};
    
    // Track commission changes
    $('.commission-input').on('change', function() {
        const vendorId = $(this).data('vendor-id');
        const commission = $(this).val();
        
        if (commission >= 0 && commission <= 100) {
            changedCommissions[vendorId] = commission;
            $(this).addClass('is-valid');
        } else {
            $(this).addClass('is-invalid');
            delete changedCommissions[vendorId];
        }
    });
    
    // Update single commission
    $('.update-single-commission').on('click', function() {
        const vendorId = $(this).data('vendor-id');
        const commission = $(this).closest('tr').find('.commission-input').val();
        const $btn = $(this);
        const originalText = $btn.html();
        
        if (commission < 0 || commission > 100) {
            alert('Commission must be between 0 and 100%');
            return;
        }
        
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        
        $.ajax({
            url: '/admin/vendors/' + vendorId + '/update-commission',
            type: 'POST',
            headers: {'X-CSRF-TOKEN': csrfToken},
            data: {commission_percent: commission},
            success: function(response) {
                if (response.status) {
                    showAlert('success', 'Commission updated successfully');
                    $btn.closest('tr').find('.commission-input').removeClass('is-valid');
                    delete changedCommissions[vendorId];
                } else {
                    showAlert('danger', response.message);
                }
                $btn.prop('disabled', false).html(originalText);
            },
            error: function(xhr) {
                showAlert('danger', xhr.responseJSON?.message || 'Error updating commission');
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Bulk update commissions
    $('#bulkUpdateCommissions').on('click', function() {
        if (Object.keys(changedCommissions).length === 0) {
            alert('No changes to save');
            return;
        }
        
        if (!confirm(`Update commissions for ${Object.keys(changedCommissions).length} vendor(s)?`)) {
            return;
        }
        
        const $btn = $(this);
        const originalText = $btn.html();
        
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
        
        $.ajax({
            url: '/admin/vendors/bulk-update-commissions',
            type: 'POST',
            headers: {'X-CSRF-TOKEN': csrfToken},
            data: {commissions: changedCommissions},
            success: function(response) {
                if (response.status) {
                    showAlert('success', response.message);
                    $('.commission-input').removeClass('is-valid');
                    changedCommissions = {};
                } else {
                    showAlert('danger', response.message);
                }
                $btn.prop('disabled', false).html(originalText);
            },
            error: function(xhr) {
                showAlert('danger', xhr.responseJSON?.message || 'Error updating commissions');
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Reset changes
    $('#resetCommissions').on('click', function() {
        $('.commission-input').each(function() {
            const vendorId = $(this).data('vendor-id');
            // Reset to original value (you might need to store original values)
            $(this).removeClass('is-valid is-invalid');
        });
        changedCommissions = {};
        location.reload();
    });
    
    // Commission calculator
    $('#calculateCommission').on('click', function() {
        const orderAmount = parseFloat($('#calcOrderAmount').val()) || 0;
        const commissionPercent = parseFloat($('#calcCommissionPercent').val()) || 0;
        
        if (orderAmount <= 0) {
            alert('Please enter a valid order amount');
            return;
        }
        
        const commissionAmount = (orderAmount * commissionPercent) / 100;
        const vendorReceives = orderAmount - commissionAmount;
        
        $('#calcOrderTotal').text('₹' + orderAmount.toFixed(2));
        $('#calcCommissionAmount').text('₹' + commissionAmount.toFixed(2));
        $('#calcCommissionPercentText').text(commissionPercent + '%');
        $('#calcVendorReceives').text('₹' + vendorReceives.toFixed(2));
        
        $('#calculationResult').show();
    });
    
    function showAlert(type, message) {
        // Remove existing alerts
        $('.dynamic-alert').remove();
        
        // Create new alert
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show dynamic-alert m-3" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.app-content').prepend(alertHtml);
        
        setTimeout(() => {
            $('.dynamic-alert').alert('close');
        }, 5000);
    }
});
</script>

<style>
.commission-input.is-valid {
    border-color: #198754;
    background-color: rgba(25, 135, 84, 0.1);
}

.commission-input.is-invalid {
    border-color: #dc3545;
    background-color: rgba(220, 53, 69, 0.1);
}

#vendorCommissionsTable tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}
</style>
@endsection