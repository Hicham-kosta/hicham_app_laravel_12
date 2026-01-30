@extends('admin.layout.layout')
@section('content')
@php
$detail = $vendor->vendorDetails ?? null;
@endphp
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Vendor Management</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{url('admin/vendors')}}">Vendors</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            View Vendor
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="app-content">
        <div class="container-fluid">
            <!-- Success/Error Messages -->
            @if(Session::has('success_message'))
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    <strong>Success: </strong>{{Session::get('success_message')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif
            @if(Session::has('error_message'))
                <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                    <strong>Error: </strong>{{Session::get('error_message')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif
            
            <!-- AJAX Response Messages -->
            <div id="ajaxResponse" class="d-none"></div>

            <!-- Rejection Modal -->
            <div class="modal fade" id="rejectVendorModal" tabindex="-1" aria-labelledby="rejectVendorModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectVendorModalLabel">Reject Vendor KYC</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="rejectVendorForm">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" id="vendor_id" name="vendor_id">
                                <div class="mb-3">
                                    <label for="rejection_reason" class="form-label">Reason for Rejection *</label>
                                    <textarea class="form-control" id="rejection_reason" name="rejection_reason" 
                                              rows="4" placeholder="Please provide specific reason for rejection..." 
                                              required></textarea>
                                    <small class="form-text text-muted">This message will be included in the email sent to the vendor.</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger" id="rejectSubmitBtn">Reject Vendor</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left: Vendor Summary -->
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle" 
                                src="{{ !empty($vendor->image) ? asset('admin/images/profiles/'.$vendor->image) 
                                : asset('admin/images/profiles/no-image.png') }}" 
                                alt="Vendor profile picture">
                            </div>
                            <h3 class="profile-username text-center mb-1">{{ $vendor->name }}</h3>
                            <p class="text-muted text-center mb-2">Vendor Account</p>
                            
                            <!-- Status Badges -->
                            <div class="text-center mb-3">
                                @if($vendor->status == 1)
                                    <span class="badge bg-success">Account Active</span>
                                @else
                                    <span class="badge bg-danger">Account Inactive</span>
                                @endif
                                
                                @if(($detail->is_verified ?? 0) == 1)
                                    <span class="badge bg-success ms-1">KYC Approved</span>
                                @elseif(($detail->is_verified ?? 0) == 2)
                                    <span class="badge bg-danger ms-1">KYC Rejected</span>
                                @else
                                    <span class="badge bg-warning ms-1">KYC Pending</span>
                                @endif
                            </div>
                            
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Email</b> <span class="float-end">{{ $vendor->email }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Mobile</b> <span class="float-end">{{ $vendor->mobile ?? '-' }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>KYC Status</b>
                                    <span class="float-end">
                                        @if(($detail->is_verified ?? 0) == 1)
                                            <span class="badge bg-success">Approved</span>
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-warning rejectVendor" 
                                                        data-id="{{ $vendor->id }}"
                                                        data-name="{{ $vendor->name }}">
                                                    Revoke Approval
                                                </button>
                                            </div>
                                        @elseif(($detail->is_verified ?? 0) == 2)
                                            <span class="badge bg-danger">Rejected</span>
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-success approveVendor" 
                                                        data-id="{{ $vendor->id }}">
                                                    Approve Now
                                                </button>
                                            </div>
                                            @if($detail && $detail->rejection_reason)
                                            <div class="mt-1">
                                                <small class="text-danger">
                                                    <i class="fas fa-info-circle"></i> 
                                                    Reason: {{ Str::limit($detail->rejection_reason, 50) }}
                                                </small>
                                            </div>
                                            @endif
                                        @else
                                            @if($detail)
                                            <div class="d-flex flex-column gap-2">
                                                <button class="btn btn-sm btn-success approveVendor" 
                                                        data-id="{{ $vendor->id }}">
                                                    Approve KYC
                                                </button>
                                                <button class="btn btn-sm btn-danger rejectVendor" 
                                                        data-id="{{ $vendor->id }}"
                                                        data-name="{{ $vendor->name }}">
                                                    Reject KYC
                                                </button>
                                            </div>
                                            @else
                                            <span class="text-warning">No KYC details submitted</span>
                                            @endif
                                        @endif
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>Commission</b>
                                    <span class="float-end">
                                        {{ isset($detail->commission_percent) ? number_format($detail->commission_percent, 2) : '0.00' }}%
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>Last Updated</b>
                                    <span class="float-end">
                                        {{ $detail ? $detail->updated_at->format('M d, Y H:i') : 'N/A' }}
                                    </span>
                                </li>
                            </ul>
                            
                            <!-- Action Buttons -->
                            <div class="d-grid gap-2">
                                <a href="{{url('admin/vendors')}}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Back to Vendors
                                </a>
                                
                                @if($detail)
                                <div class="btn-group" role="group">
                                    @if(($detail->is_verified ?? 0) != 1)
                                    <button class="btn btn-success approveVendor" 
                                            data-id="{{ $vendor->id }}">
                                        <i class="fas fa-check-circle me-1"></i> Approve
                                    </button>
                                    @endif
                                    
                                    @if(($detail->is_verified ?? 0) != 2)
                                    <button class="btn btn-danger rejectVendor" 
                                            data-id="{{ $vendor->id }}"
                                            data-name="{{ $vendor->name }}">
                                        <i class="fas fa-times-circle me-1"></i> Reject
                                    </button>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Detailed Tabs -->
                <div class="col-md-8">
                    <div class="card card-outline card-primary">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#store" data-bs-toggle="tab">
                                        <i class="fas fa-store me-1"></i> Shop Details
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#kyc" data-bs-toggle="tab">
                                        <i class="fas fa-id-card me-1"></i> KYC Details
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#bank" data-bs-toggle="tab">
                                        <i class="fas fa-university me-1"></i> Bank Details
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Shop Details Tab -->
                                <div class="tab-pane active" id="store">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Shop Name</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->shop_name ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Shop Email</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->shop_email ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Shop Mobile</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->shop_mobile ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Website</label>
                                            <div class="form-control bg-light">
                                                @if(!empty($detail->shop_website))
                                                <a href="{{ $detail->shop_website }}" target="_blank">
                                                    {{ $detail->shop_website }}
                                                </a>
                                                @else
                                                -
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Address</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->shop_address ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">City</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->shop_city ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">State</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->shop_state ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Pincode</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->shop_pincode ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Country</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->shop_country ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- KYC Details Tab -->
                                <div class="tab-pane" id="kyc">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">GST Number</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->gst_number ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">PAN Number</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->pan_number ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Business License</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->business_license_number ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Address Proof Type</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->address_proof ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Address Proof Document</label>
                                            <div class="form-control bg-light">
                                                @if(!empty($detail->address_proof_image))
                                                <div class="d-flex align-items-center">
                                                    <a href="{{ asset('front/images/vendor-docs/'.$detail->address_proof_image) }}" 
                                                       target="_blank" class="me-3">
                                                        <i class="fas fa-eye"></i> View Document
                                                    </a>
                                                    <a href="{{ asset('front/images/vendor-docs/'.$detail->address_proof_image) }}" 
                                                       download class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </div>
                                                @else
                                                -
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($detail && $detail->rejection_reason)
                                        <div class="col-md-12">
                                            <div class="alert alert-warning">
                                                <h6><i class="fas fa-exclamation-triangle"></i> Previous Rejection Reason</h6>
                                                <p class="mb-0">{{ $detail->rejection_reason }}</p>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Bank Details Tab -->
                                <div class="tab-pane" id="bank">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Account Holder Name</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->account_holder_name ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Bank Name</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->bank_name ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Account Number</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->account_number ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Bank IFSC Code</label>
                                            <div class="form-control bg-light">
                                                {{ $detail->ifsc_code ?? '-' }}
                                            </div>
                                        </div>
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
    // CSRF Token
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    // Show AJAX message
    function showMessage(type, message) {
        $('#ajaxResponse').removeClass('d-none alert-success alert-danger')
                         .addClass('alert alert-' + type + ' alert-dismissible fade show')
                         .html('<strong>' + (type === 'success' ? 'Success:' : 'Error:') + '</strong> ' + message + 
                               '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>');
        
        setTimeout(function() {
            $('#ajaxResponse').addClass('d-none');
        }, 5000);
    }
    
    // Approve Vendor
    $(document).on('click', '.approveVendor', function () {
        if (!confirm("Are you sure you want to approve this vendor's KYC?")) return false;
        
        let vendorId = $(this).data('id');
        let $btn = $(this);
        let originalText = $btn.html();
        
        // Show loading
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
        
        $.ajax({
            url: '/admin/vendors/' + vendorId + '/approve',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function (response) {
                if (response.status === true) {
                    showMessage('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showMessage('danger', response.message);
                    $btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                showMessage('danger', xhr.responseJSON?.message || 'An error occurred. Please try again.');
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Reject Vendor - Show Modal
    $(document).on('click', '.rejectVendor', function () {
        let vendorId = $(this).data('id');
        let vendorName = $(this).data('name') || 'Vendor';
        
        // Set vendor ID in modal
        $('#vendor_id').val(vendorId);
        
        // Update modal title
        if ($(this).hasClass('btn-warning')) {
            $('#rejectVendorModalLabel').text('Revoke Approval - ' + vendorName);
        } else {
            $('#rejectVendorModalLabel').text('Reject KYC - ' + vendorName);
        }
        
        // Show modal
        $('#rejectVendorModal').modal('show');
    });
    
    // Handle reject form submission
    $('#rejectVendorForm').on('submit', function(e) {
        e.preventDefault();
        
        let vendorId = $('#vendor_id').val();
        let rejectionReason = $('#rejection_reason').val().trim();
        let $submitBtn = $('#rejectSubmitBtn');
        let originalText = $submitBtn.html();
        
        if (!rejectionReason) {
            alert('Please enter a reason for rejection.');
            return false;
        }
        
        // Show loading
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
        
        $.ajax({
            url: '/admin/vendors/' + vendorId + '/reject',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: {
                rejection_reason: rejectionReason
            },
            success: function (response) {
                if (response.status === true) {
                    // Close modal
                    $('#rejectVendorModal').modal('hide');
                    
                    // Reset form
                    $('#rejectVendorForm')[0].reset();
                    
                    showMessage('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showMessage('danger', response.message);
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                showMessage('danger', xhr.responseJSON?.message || 'An error occurred. Please try again.');
                $submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Reset modal when closed
    $('#rejectVendorModal').on('hidden.bs.modal', function () {
        $('#rejectVendorForm')[0].reset();
        $('#rejectSubmitBtn').prop('disabled', false).html('Reject Vendor');
    });
});
</script>

<style>
.profile-user-img {
    width: 100px;
    height: 100px;
    object-fit: cover;
}

.list-group-item {
    border-left: 0;
    border-right: 0;
}

.list-group-item:first-child {
    border-top: 0;
}

.list-group-item:last-child {
    border-bottom: 0;
}

.nav-pills .nav-link.active {
    background-color: #0d6efd;
}

.tab-content {
    padding-top: 15px;
}

.form-control.bg-light {
    min-height: 38px;
    display: flex;
    align-items: center;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.2em;
}

.btn-group .btn {
    flex: 1;
}
</style>
@endsection