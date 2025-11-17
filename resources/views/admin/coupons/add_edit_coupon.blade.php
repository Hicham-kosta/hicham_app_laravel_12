@extends('admin.layout.layout')
@section('content')
<main class="app-content">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="mb-0">{{$title}}</h3>
                </div>
                <div class="col-md-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{route('coupons.index')}}">Coupons</a></li>
                        <li class="breadcrumb-item active">{{$title}}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card mb-4">
                <div class="card-body">
                    @if(Session::has('success_message'))
                      <div class="alert alert-success">{{Session::get('success_message')}}</div>
                    @endif
                    <form action="{{isset($coupon->id) ? route('coupons.update', $coupon->id) 
                    : route('coupons.store')}}" method="POST">
                    @csrf
                    @if(isset($coupon->id))
                     @method('PUT')
                    @endif
                    {{-- Coupon Option & Code --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Coupon Option</label><br>
                                <div class="form-check form-check-inline">
                                    <input id="couponOptionAutomatic" class="form-check-input" 
                                    type="radio" name="coupon_option" value="Automatic" 
                                    {{old('coupon_option', $coupon->coupon_option ?? 
                                        'Automatic') === 'Automatic' ? 'checked' : ''}}>
                                    <label class="form-check-label" for="couponOptionAutomatic">Automatic</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input id="couponOptionManual" class="form-check-input" 
                                    type="radio" name="coupon_option" value="Manual" 
                                    {{old('coupon_option', $coupon->coupon_option ?? 
                                        '') === 'Manual' ? 'checked' : ''}}>
                                    <label class="form-check-label" for="couponOptionManual">Manual</label>
                                </div>
                            </div>                 
                        </div>
                        <div class="col-md-6" id="couponCodeWrapper">
                            <div class="form-group">
                                <label for="coupon_code">Coupon Code</label>
                                <div class="input-group">
                                    <input type="text" name="coupon_code" id="coupon_code" 
                                    class="form-control"  value="{{old('coupon_code', $coupon->coupon_code ?? '')}}"
                                    placeholder="Enter Coupon Code">
                                    <div class="input-group-append">
                                        &nbsp;<button class="btn btn-outline-secondary" id="regenCoupon" type="button" 
                                        title="Regenerate"><i class="fas fa-sync-alt"></i></button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Automatic will generate a code - you may customize it here </small>
                            </div>
                        </div>
                    </div>
                    {{-- Coupon Type amount Type (radio button) --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Coupon Type</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" 
                                    name="coupon_type" id="couponTypeMultiple" 
                                    value="Multiple" {{old('coupon_type', $coupon->coupon_type ?? 'Multiple') == 
                                        'Multiple' ? 'checked' : ''}}>
                                        <label class="form-check-label" for="couponTypeMultiple">Multiple Times</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" 
                                    name="coupon_type" id="couponTypeSingle" 
                                    value="Single" {{old('coupon_type', $coupon->coupon_type ?? '') == 
                                        'Single' ? 'checked' : ''}}>
                                        <label class="form-check-label" for="couponTypeSingle">Single Time</label> 
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Amount Type</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" 
                                    name="amount_type" id="amountTypePercent" 
                                    value="percentage" {{old('amount_type', $coupon->amount_type ?? 'percentage') == 
                                        'percentage' ? 'checked' : ''}}>
                                        <label class="form-check-label" for="amountTypePercent">Percentage (%)</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" 
                                    name="amount_type" id="amountTypeFixed" 
                                    value="fixed" {{old('amount_type', $coupon->amount_type ?? '') == 
                                        'fixed' ? 'checked' : ''}}>
                                        <label class="form-check-label" for="amountTypeFixed">Fixed ($)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Amount_min / max qty & expiry --}}
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input type="number" step="0.01" min="0" name="amount" id="amount" 
                                class="form-control" value="{{old('amount', $coupon->amount ?? '')}}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="min_qty">Min Quantity</label>
                                <select name="min_qty" id="min_qty" class="form-control">
                                    <option value="">Select Min Quantity</option>
                                    @for($i = 1; $i <= 10; $i++)
                                    <option value="{{$i}}" {{old('min_qty', $coupon->min_qty ?? 
                                        '') == $i ? 'selected' : ''}}>{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                               <label for="max_qty">Max Quantity</label>
                                <select name="max_qty" id="max_qty" class="form-control">
                                    <option value="">Select Max Quantity</option>
                                    @for($i = 1; $i <= 100; $i++)
                                    <option value="{{$i}}" {{old('max_qty', $coupon->max_qty ?? 
                                        '') == $i ? 'selected' : ''}}>{{$i}}</option>
                                    @endfor
                                    @for($i = 150; $i <= 1000; $i+= 50)
                                    <option value="{{$i}}" {{old('max_qty', $coupon->max_qty ?? 
                                        '') == $i ? 'selected' : ''}}>{{$i}}</option>
                                    @endfor
                                </select> 
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="expiry_date">Expiry Date</label>
                                <input type="date" name="expiry_date" id="expiry_date" class="form-control" 
                                value="{{old('expiry_date', isset($coupon->expiry_date) ? 
                                date('Y-m-d', strtotime($coupon->expiry_date)) : '')}}" required>
                            </div>
                        </div>
                    </div>
                    {{-- Range Price --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="min_cart_value">Min Price Range</label>
                            <input type="number" step="0.01" name="min_cart_value" id="min_cart_value" 
                                class="form-control" value="{{old('min_cart_value', $coupon->min_cart_value ?? '')}}">
                        </div>
                        <div class="col-md-6">
                            <label for="max_cart_value">Max Price Range</label>
                            <input type="number" step="0.01" name="max_cart_value" id="max_cart_value" 
                                class="form-control" value="{{old('max_cart_value', $coupon->max_cart_value ?? '')}}">
                        </div>
                    </div>
                    {{-- Select Categories Brands w / Select All / Deselect All --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="categoriesSelect">Categories</label>
                                <select name="categories[]" id="categoriesSelect" 
                                class="form-control select2" multiple data-actions-box="true">
                                @foreach($categories as $category)
                                <option value="{{$category['id']}}" 
                                @if(in_array($category['id'],$selCats)) selected @endif>
                                   {{$category['name'] ?? $category['category_name'] ?? ''}}
                                </option>
                                   @foreach($category['subcategories'] ?? [] as $subCategory)
                                    <option value="{{$subCategory['id']}}" 
                                    @if(in_array($subCategory['id'],$selCats)) selected @endif>
                                    &nbsp;&nbsp;--{{$subCategory['name'] ?? $subCategory['category_name'] ?? ''}}
                                    </option>
                                      @foreach($subCategory['subcategories'] ?? [] as $subsubcategory)
                                       <option value="{{$subsubcategory['id']}}" 
                                        @if(in_array($subsubcategory['id'],$selCats)) selected @endif>
                                        &nbsp;&nbsp;&nbsp;&nbsp;--{{$subsubcategory['name'] ?? $subsubcategory['category_name'] ?? ''}}
                                        </option>
                                      @endforeach
                                    @endforeach
                                @endforeach
                                </select>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-success select-all" 
                                    data-target="#categoriesSelect">Select All</button>
                                    <button type="button" class="btn btn-sm btn-danger deselect-all" 
                                    data-target="#categoriesSelect">Deselect All</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="brandsSelect">Brands</label>
                                <select name="brands[]" id="brandsSelect" class="form-control select2" multiple>
                                    @foreach($brands as $id => $name)
                                      <option value="{{$id}}" {{in_array($id, old('brands', $selBrands ?? [])) ? 'selected' : ''}}>
                                        {{$name}}
                                      </option>
                                    @endforeach
                                </select>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-success select-all" 
                                    data-target="#brandsSelect">Select All</button>
                                    <button type="button" class="btn btn-sm btn-danger deselect-all" 
                                    data-target="#brandsSelect">Deselect All</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Users multiselect (optional) --}}
                    <div class="row mt-3">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="usersSelect">Users</label>
                            <select name="users[]" id="usersSelect" class="form-control select2-tags" multiple>
                                @foreach($users as $email)
                                  <option value="{{$email}}" {{ in_array($email, $selUsers ?? []) ? 'selected' : '' }}>
                                    {{$email}}
                                  </option>
                                @endforeach
                            </select>
                            <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-success select-all" 
                                    data-target="#usersSelect">Select All</button>
                                    <button type="button" class="btn btn-sm btn-danger deselect-all" 
                                    data-target="#usersSelect">Deselect All</button>
                            </div>
                          </div>
                       </div>
                   </div>
                   {{-- Visibility checkbox --}}
                   <div class="row mb-3">
                     <div class="col-md-4">
                        <div class="form-group d-flex align-items-center">
                            <input type="checkbox" name="visible" id="visible" 
                            class="form-check-input mr-2" value="1" {{old('visible', $coupon->visible ?? 0) == 1 ? 'checked' : ''}}>
                            <label for="visible" class="mb-0">&nbsp;Visible in Cart</label>
                        </div>
                     </div>
                   </div>
                   <div class="mt-3">
                    <button class="btn btn-primary">{{isset($coupon->id) ? 'Update Coupon' : 'Add Coupon'}}</button>
                    <a href="{{route('coupons.index')}}" class="btn btn-secondary">Cancel</a>
                   </div>
                </form>
              </div>
           </div>
        </div>
    </div>
</main>
@endsection
<script>
    $(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: "Select options",
        allowClear: true
    });

    $('.select2-tags').select2({
        theme: 'bootstrap4',
        tags: true,
        placeholder: "Select or enter emails",
        allowClear: true
    });

    // Select All / Deselect All buttons
    $('.select-all').on('click', function() {
        var target = $(this).data('target');
        $(target).find('option').prop('selected', true);
        $(target).trigger('change');
    });

    $('.deselect-all').on('click', function() {
        var target = $(this).data('target');
        $(target).find('option').prop('selected', false);
        $(target).trigger('change');
    });

    // Regenerate coupon code
    $('#regenCoupon').on('click', function() {
        var randomCode = Math.random().toString(36).substring(2, 10).toUpperCase();
        $('#coupon_code').val(randomCode);
    });
});
</script>