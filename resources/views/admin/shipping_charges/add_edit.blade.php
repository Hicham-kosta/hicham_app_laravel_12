@extends('admin.layout.layout')
@section('content')
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-ms-6">
                    <h3 class="mb-0">
                        Shipping Management
                    </h1>
                </div>
                <div class="col-ms-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{url('admin/shipping-charges')}}">Shipping Charges</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{isset($charge) ? 'Edit Shipping Charge' : 'Add New Shipping Charge'}}
                        </li>
                    </ol>    
                </div>
            </div>
        </div>       
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{isset($charge) ? 'Edit Shipping Charge' : 'Add New Shipping Charge'}}
                            </h3>
                        </div>
                        <form 
                        @if(isset($charge))
                        action="{{url('admin/shipping-charges/'.$charge->id)}}" 
                        @else
                        action="{{url('admin/shipping-charges')}}"
                        @endif
                        method="POST">
                        @csrf
                        @if(isset($charge))
                        @method('PUT')
                        @endif
                        <div class="card-body">
                            {{-- Country --}}
                            <div class="mb-3">
                                <label for="country_id" class="form-label">Country
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="country_id" id="country_id" class="form-control">
                                    <option value="">--Select Country--</option>
                                    @foreach($countries as $country)
                                    <option value="{{$country->id}}" {{(int)old('country_id', $charge->country_id ?? 0) == 
                                        $country->id ? 'selected' : ''}}>
                                        {{$country->name}}
                                    </option>
                                    @endforeach
                                </select>
                                @error('country_id')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            {{-- Method Name --}}
                            <div class="mb-3">
                                <label for="name" class="form label">Method Name</label>
                                <input type="text" name="name" id="name" class="form-control" 
                                value="{{old('name', $charge->name ?? 'Standard Shipping')}}" 
                                placeholder="Standard Shipping">
                                @error('name')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            {{-- Weight Range(grams) --}}
                            <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="min_weight_g" class="form label">Min Weight (grams)</label>
                                <input type="number" min="0" name="min_weight_g" id="min_weight_g" class="form-control" 
                                value="{{old('min_weight_g', $charge->min_weight_g ?? null)}}" 
                                placeholder="e.g 100(grams)">
                                @error('min_weight_g')
                                <div class="text-danger">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="max_weight_g" class="form label">Max Weight (grams)</label>
                                <input type="number" min="0" name="max_weight_g" id="max_weight_g" class="form-control" 
                                value="{{old('max_weight_g', $charge->max_weight_g ?? null)}}" 
                                placeholder="e.g 500(grams)">
                                @error('max_weight_g')
                                <div class="text-danger">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        {{-- Subtotal Range --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="min_subtotal" class="form label">Min Subtotal</label>
                                <input type="number" step="0.01" min="0" name="min_subtotal" id="min_subtotal" class="form-control" 
                                value="{{old('min_subtotal', $charge->min_subtotal ?? null)}}" 
                                placeholder="e.g 100">
                                @error('min_subtotal')
                                <div class="text-danger">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="max_subtotal" class="form label">Max Subtotal (Base)</label>
                                <input type="number" step="0.01" min="0" name="max_subtotal" id="max_subtotal" class="form-control" 
                                value="{{old('max_subtotal', $charge->max_subtotal ?? null)}}" 
                                placeholder="e.g 500">
                                @error('max_subtotal')
                                <div class="text-danger">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        {{-- Rate --}}
                        <div class="mb-3">
                            <label for="rate" class="form label">Shipping Rate (Base Currency)
                                <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.01" min="0" name="rate" id="rate" class="form-control" 
                            value="{{old('rate', $charge->rate ?? 0)}}" 
                            placeholder="e.g 10">
                            @error('rate')
                            <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        {{-- Sort Order --}}
                        <div class="mb-3">
                            <label for="sort_order" class="form label">Sort Order</label>
                            <input type="number" min="0" name="sort_order" id="sort_order" class="form-control" 
                            value="{{old('sort_order', $charge->sort_order ?? 0)}}">
                            @error('sort_order')
                            <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        {{-- Status --}}
                        <div class="mb-3">
                            <label class="form label">Status</label>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="status" id="statusActive" class="form-check-input" 
                                value="1" 
                                {{old('status', $charge->status ?? 1) == 1 ? 'checked' : ''}}>
                                <label for="statusActive" class="form-check-label">Active</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="status" id="statusInactive" class="form-check-input" 
                                value="0" 
                                {{old('status', $charge->status ?? 1) == 0 ? 'checked' : ''}}>
                                <label for="status_inactive" class="form-check-label">Inactive</label>
                            </div>
                            @error('status')
                            <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        {{-- Default --}}
                        <div class="mb-3">
                            <label class="form label">Default for Country</label><br>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" name="is_default" id="is_default" class="form-check-input" 
                                value="1" 
                                {{old('is_default', $charge->is_default ?? 1) ? 'checked' : ''}}>
                                <label for="is_default" class="form-check-label">Mark as Default</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            {{isset($charge) ? 'Update Shipping charge' : 'Add Shipping charge'}}</button>
                        <a href="{{url('admin/shipping-charges')}}" class="btn btn-secondary">Cancel</a>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
  </div>
</main>
@endsection