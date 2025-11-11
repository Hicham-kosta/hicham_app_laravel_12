@extends('front.layout.layout')
@section('content')
<!-- Page Header -->
<div class="container-fluid bg-secondary mb-5">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 150px">
        <h1 class="font-weight-semi-bold text-uppercase mb-3">My Account</h1>
        <div class="d-inline-flex">
            <p class="m-0"><a href="{{ url('/') }}">Home</a></p>
            <p class="m-0 px-2">-</p>
            <p class="m-0">My Account</p>
        </div>
    </div>
</div>
<!-- Page Header End -->

<div class="container-fluid pt-2">
    <div class="text-center mb-4">
        <h2 class="section-title px-5"><span class="px-2">Account Details</span></h2>
    </div>
    <div class="row px-xl-5 justify-content-center">
        <div class="col-md-8">
            <!-- Success Message Container -->
            <div id="accountSuccess" class="mb-4"></div>
            
            <form id="accountForm" name="accountForm" novalidate>
                @csrf
                
                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name *</label>
                    <input type="text" class="form-control" name="name" id="name" 
                        placeholder="Full Name" value="{{ old('name', $user->name) }}" required />
                    <p class="help-block text-danger mt-1 mb-0" data-error-for="name"></p>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" class="form-control" name="email" id="email" 
                        placeholder="Email Address" value="{{ old('email', $user->email) }}" readonly />
                    <div class="form-text">*Email cannot be changed here</div>
                    <p class="help-block text-danger mt-1 mb-0" data-error-for="email"></p>
                </div>

                <!-- Company -->
                <div class="mb-3">
                    <label for="company" class="form-label">Company</label>
                    <input type="text" class="form-control" name="company" id="company" 
                        placeholder="Company (optional)" value="{{ old('company', $user->company) }}" />
                    <p class="help-block text-danger mt-1 mb-0" data-error-for="company"></p>
                </div>

                <!-- Phone -->
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" name="phone" id="phone" 
                        placeholder="Phone (optional)" value="{{ old('phone', $user->phone) }}" />
                    <p class="help-block text-danger mt-1 mb-0" data-error-for="phone"></p>
                </div>

                <!-- Address Line 1 -->
                <div class="mb-3">
                    <label for="address_line1" class="form-label">Address Line 1</label>
                    <input type="text" class="form-control" name="address_line1" id="address_line1" 
                        placeholder="Address line 1" value="{{ old('address_line1', $user->address_line1) }}" />
                    <p class="help-block text-danger mt-1 mb-0" data-error-for="address_line1"></p>
                </div>

                <!-- Address Line 2 -->
                <div class="mb-3">
                    <label for="address_line2" class="form-label">Address Line 2</label>
                    <input type="text" class="form-control" name="address_line2" id="address_line2"
                        placeholder="Address line 2" value="{{ old('address_line2', $user->address_line2) }}" />
                    <p class="help-block text-danger mt-1 mb-0" data-error-for="address_line2"></p>
                </div>

                <!-- City -->
                <div class="mb-3">
                    <label for="city" class="form-label">City / Town</label>
                    <input type="text" class="form-control" name="city" id="city" 
                        placeholder="City / Town" value="{{ old('city', $user->city) }}" />
                    <p class="help-block text-danger mt-1 mb-0" data-error-for="city"></p>
                </div>

                <!-- Country -->
                <div class="mb-3">
                    <label for="country" class="form-label">Country *</label>
                    <select id="country" name="country" class="form-control" required>
                        @foreach($countries as $c)
                        <option value="{{ $c->name }}" {{ (old('country', $user->country) ?? 'United States') == $c->name ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                        @endforeach
                    </select>
                    <p class="help-block text-danger mt-1 mb-0" data-error-for="country"></p>
                </div>
                <!-- County/State Dropdown (for US) -->
<div class="mb-3" id="county_select_wrapper">
    <label for="county_select" class="form-label">State</label>
    <select id="county_select" name="county" class="form-control">
        <option value="">-- Select State --</option>
        @foreach($usStates as $s)
        <option value="{{ $s->name }}" {{ old('county', $user->county) == $s->name ? 'selected' : '' }}>
            {{ $s->name }}
        </option>
        @endforeach
    </select>
    <p class="help-block text-danger mt-1 mb-0" data-error-for="county"></p>
</div>

<!-- County/State Text Input (for non-US countries) -->
<div class="mb-3" id="county_text_wrapper" style="display: none;">
    <label for="county_text" class="form-label">County / Province / State</label>
    <input id="county_text" name="county" class="form-control" type="text"
        placeholder="County / Province / State" value="{{ old('county', (old('country', $user->country) !== 'United States' ? old('county', $user->county) : '')) }}" />
    <p class="help-block text-danger mt-1 mb-0" data-error-for="county"></p>
</div>

                <!-- Postcode -->
                <div class="mb-4">
                    <label for="postcode" class="form-label">Postcode / ZIP</label>
                    <div class="input-group">
                        <input id="postcode" type="text" name="postcode" class="form-control"
                            placeholder="Postcode / ZIP" value="{{ old('postcode', $user->postcode) }}" />
                        <div class="input-group-append">
                            <span id="postcode_loader" class="input-group-text" 
                                style="display: none; background: white; border-left: 0;">
                                <span class="postcode-spinner" style="display: inline-block; width: 16px; height: 16px; border: 2px solid rgba(0,0,0,0.12); border-top-color: #333; border-radius: 50%;"></span>
                            </span>
                        </div>
                    </div>
                    <div class="form-text">For UK addresses, we'll try to auto-fill your city and county</div>
                    <p class="help-block text-danger mt-1 mb-0" data-error-for="postcode"></p>
                </div>

                <!-- Submit Button -->
                <div class="mb-3">
                    <button id="SaveBtn" type="submit" class="btn btn-primary py-2 px-4">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                    <a href="{{ url('/') }}" class="btn btn-secondary py-2 px-4 ms-2">
                        <i class="fas fa-home me-2"></i>Back to Home
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
/* ===== ROOT VARIABLES ===== */
:root {
    --primary-color: #007bff;
    --primary-dark: #0056b3;
    --secondary-color: #6c757d;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --light-color: #f8f9fa;
    --dark-color: #343a40;
    --border-radius: 12px;
    --transition: all 0.3s ease;
    --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* ===== CARD STYLES ===== */
.card {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    transition: var(--transition);
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.card-body {
    padding: 2.5rem !important;
}

/* ===== FORM CONTROLS ===== */
.form-control {
    border: 2px solid #e9ecef;
    border-radius: var(--border-radius);
    padding: 12px 16px;
    transition: var(--transition);
    font-size: 14px;
    background-color: #ffffff;
    height: auto;
    min-height: 48px;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
    background-color: #ffffff;
}

.form-control[readonly] {
    background-color: #f8f9fa;
    border-color: #e9ecef;
    color: #6c757d;
    cursor: not-allowed;
}

.form-control[readonly]:focus {
    border-color: #e9ecef;
    box-shadow: none;
}

/* ===== SELECT DROPDOWNS ===== */
.form-select {
    border: 2px solid #e9ecef;
    border-radius: var(--border-radius);
    padding: 12px 16px;
    transition: var(--transition);
    font-size: 14px;
    height: auto !important;
    min-height: 48px;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 12px center;
    background-repeat: no-repeat;
    background-size: 16px 12px;
    padding-right: 40px;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-color: #ffffff;
}

.form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
}

/* Style for select options */
select.form-control option,
select.form-select option {
    padding: 12px 16px;
    font-size: 14px;
    white-space: normal;
    word-wrap: break-word;
    border-bottom: 1px solid #f8f9fa;
}

select.form-control option:hover,
select.form-select option:hover {
    background-color: var(--primary-color) !important;
    color: white;
}

select.form-control option:checked,
select.form-select option:checked {
    background-color: var(--primary-color);
    color: white;
}

/* ===== BUTTON STYLES ===== */
.btn-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    border: none;
    padding: 12px 24px;
    font-weight: 600;
    transition: var(--transition);
    border-radius: var(--border-radius);
    font-size: 14px;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    background: linear-gradient(135deg, var(--primary-dark) 0%, #004085 100%);
}

.btn-secondary {
    background: linear-gradient(135deg, var(--secondary-color) 0%, #545b62 100%);
    border: none;
    padding: 12px 24px;
    font-weight: 600;
    transition: var(--transition);
    border-radius: var(--border-radius);
    font-size: 14px;
}

.btn-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
    background: linear-gradient(135deg, #545b62 0%, #4e555b 100%);
}

/* ===== FORM ELEMENTS ===== */
.form-label {
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 8px;
    font-size: 14px;
}

.form-text {
    font-size: 0.8rem;
    color: var(--secondary-color);
    margin-top: 4px;
}

.help-block {
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: block;
}

.text-danger {
    color: var(--danger-color) !important;
    font-weight: 500;
}

/* ===== ALERT STYLES ===== */
.alert {
    border-radius: var(--border-radius);
    border: none;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    font-weight: 500;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border-left: 4px solid var(--success-color);
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f1b0b7 100%);
    color: #721c24;
    border-left: 4px solid var(--danger-color);
}

.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    color: #856404;
    border-left: 4px solid var(--warning-color);
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #b6e7f1 100%);
    color: #0c5460;
    border-left: 4px solid #17a2b8;
}

/* ===== INPUT GROUP STYLES ===== */
.input-group {
    border-radius: var(--border-radius);
}

.input-group .form-control {
    border-radius: var(--border-radius) 0 0 var(--border-radius);
}

.input-group .input-group-text {
    background-color: #f8f9fa;
    border: 2px solid #e9ecef;
    border-left: 0;
    border-radius: 0 var(--border-radius) var(--border-radius) 0;
    padding: 12px 16px;
}

.input-group .form-control:focus + .input-group-text {
    border-color: var(--primary-color);
}

/* ===== LAYOUT & SPACING ===== */
.mb-3 {
    margin-bottom: 1.5rem !important;
}

.mb-4 {
    margin-bottom: 2rem !important;
}

/* County/State wrapper specific styles */
#county_select_wrapper,
#county_text_wrapper {
    width: 100%;
    transition: var(--transition);
}

#county_text_wrapper {
    display: none;
}

/* Hidden inputs */
input[type="hidden"] {
    display: none;
}

/* ===== SPINNER & LOADING ===== */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.spinner-border {
    animation: spin 0.75s linear infinite;
    display: inline-block;
    width: 1rem;
    height: 1rem;
    border: 0.15em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
}

.postcode-spinner {
    animation: spin 0.8s linear infinite;
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(0, 0, 0, 0.12);
    border-top-color: #333;
    border-radius: 50%;
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
    .card-body {
        padding: 2rem 1.5rem !important;
    }
    
    .btn {
        padding: 10px 20px;
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .btn-group {
        width: 100%;
    }
    
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .form-control,
    .form-select {
        font-size: 16px; /* Prevent zoom on iOS */
    }
    
    .input-group {
        flex-direction: column;
    }
    
    .input-group .form-control {
        border-radius: var(--border-radius);
        margin-bottom: 0.5rem;
    }
    
    .input-group .input-group-text {
        border-radius: var(--border-radius);
        border: 2px solid #e9ecef;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .card-body {
        padding: 1.5rem 1rem !important;
    }
    
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}

/* ===== ACCESSIBILITY & INTERACTION ===== */
.form-control:invalid:not(:focus) {
    border-color: var(--danger-color);
}

.form-control:valid:not(:focus) {
    border-color: var(--success-color);
}

/* Focus styles for accessibility */
.form-control:focus,
.form-select:focus,
.btn:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

/* Smooth transitions for all interactive elements */
.form-control,
.form-select,
.btn,
.card {
    transition: var(--transition);
}

/* ===== CUSTOM SCROLLBAR FOR SELECTS ===== */
.form-select::-webkit-scrollbar {
    width: 8px;
}

.form-select::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.form-select::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 4px;
}

.form-select::-webkit-scrollbar-thumb:hover {
    background: var(--primary-dark);
}

/* ===== PRINT STYLES ===== */
@media print {
    .btn,
    .input-group-text,
    .postcode-spinner {
        display: none !important;
    }
    
    .form-control,
    .form-select {
        border: 1px solid #000 !important;
        background: transparent !important;
    }
}

/* ===== DARK MODE SUPPORT ===== */
@media (prefers-color-scheme: dark) {
    .card {
        background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
        color: #e2e8f0;
    }
    
    .form-control,
    .form-select {
        background-color: #2d3748;
        border-color: #4a5568;
        color: #e2e8f0;
    }
    
    .form-control:focus,
    .form-select:focus {
        background-color: #2d3748;
        color: #e2e8f0;
    }
}

/* ===== ANIMATIONS ===== */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card,
.alert {
    animation: fadeIn 0.5s ease-out;
}

/* ===== ICON STYLES ===== */
.fas, .far, .fal, .fab {
    margin-right: 0.5rem;
}

/* Ensure proper contrast for disabled states */
.form-control:disabled,
.form-select:disabled {
    background-color: #e9ecef;
    color: #6c757d;
    cursor: not-allowed;
    opacity: 0.6;
}
</style>

{{-- Font Awesome for icons --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection