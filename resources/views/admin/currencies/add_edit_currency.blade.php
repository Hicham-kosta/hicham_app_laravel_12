@extends('admin.layout.layout')
@section('content')
<main class="app-content">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="m-0">Catalogue Management</h3></div>
                
                <div class="col-md-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('admin/currencies') }}">Currencies</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ isset($currency) && $currency->id ? 'Edit Currency' : 'Add Currency' }}
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
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header">
                            <h3 class="card-title">{{ isset($currency) && $currency->id ? 'Edit Currency' : 'Add Currency' }}</h3>
                        </div>
                    <form 
                        action="{{ isset($currency->id) ? route('currencies.update', $currency->id) : route('currencies.store') }}"

                        method="POST"
                        enctype="multipart/form-data">

                        @csrf
                        @if(isset($currency) && $currency->id)
                            @method('PUT')
                        @else
                            @method('POST')
                        @endif

                        <div class="card-body">

                            {{-- Currency Code --}}
                            <div class="mb-3">
                                <label for="code" class="form-label">Currency Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code" class="form-control" 
                                    value="{{ old('code', optional($currency)->code) }}" placeholder="e.g. GBP" required>
                                @error('code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Currency Symbol --}}
                            <div class="mb-3">
                                <label for="symbol" class="form-label">Currency Symbol</label>
                                <input type="text" name="symbol" id="symbol" class="form-control" 
                                    value="{{ old('symbol', optional($currency)->symbol) }}" placeholder="e.g. £">
                                @error('symbol')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Currency Name --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Currency Name</label>
                                <input type="text" name="name" id="name" class="form-control" 
                                    value="{{ old('name', optional($currency)->name) }}" placeholder="e.g. British Pound">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Currency Rate --}}
                            <div class="mb-3">
                                <label for="rate" class="form-label">Currency Rate <span class="text-danger">*</span></label>
                                <input type="number" step="0.000000001" name="rate" id="rate" class="form-control"
                                    value="{{ old('rate', optional($currency)->rate) }}"
                                    {{ isset($currency) && $currency->is_base ? 'readonly' : '' }}>
                                @if(isset($currency) && $currency->is_base)
                                    <small class="form-text text-muted">Base currency always has rate = 1</small>
                                @endif
                                @error('rate')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusActive" value="1"
                                        {{ old('status', optional($currency)->status) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusActive">Active</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusInactive" value="0"
                                        {{ old('status', optional($currency)->status) == 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusInactive">Inactive</label>
                                </div>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Base Currency --}}
                            <div class="mb-3">
                                <label class="form-label">Base Currency</label>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" name="is_base" id="is_base" value="1"
                                        class="form-check-input"
                                        {{ old('is_base', optional($currency)->is_base ?? 0) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_base">Mark as Base</label>
                                </div>
                                @if(isset($currency) && $currency->is_base)
                                    <small class="form-text text-muted">This currency is already marked as base currency.</small>
                                @endif
                            </div>

                            {{-- Flag Upload --}}
                            <div class="mb-3">
                                <label for="flag" class="form-label">Flag</label>
                                <input type="file" name="flag" id="flag" class="form-control">
                                @if(isset($currency) && $currency->flag)
                                    <p class="mt-2">
                                        <img src="{{ asset('front/images/flags/' . $currency->flag) }}" alt="Flag" style="height: 24px;">
                                        <small class="text-muted">{{ $currency->flag }}</small>
                                    </p>
                                @endif
                                @error('flag')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($currency) && $currency->id ? 'Update Currency' : 'Add Currency' }}
                            </button>
                            <a href="{{ url('admin/currencies') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                  </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
