@extends('admin.layout.layout')
@section('content')
<main class="app-main">
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Wallet / Credit Management</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <div class="app-content">
          <div class="container-fluid">
            <div class="row g-4">
              <div class="col-md-8">
                <div class="card card-primary card-outline mb-4">
                  <div class="card-header"><div class="card-title">{{$title}}</div></div>
                  <div class="card-body">
                    {{-- Error Messages --}}
                    @if(Session::has('error_message'))
                    <div class="alert alert-danger alert-dismissible fade show mx-1 my-3" role="alert">
                        <strong>Error: </strong>{{Session::get('error_message')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                    </div>
                    @endif

                    {{-- Success Messages --}}
                    @if(Session::has('success_message'))
                    <div class="alert alert-success alert-dismissible fade show mx-1 my-3" role="alert">
                        <strong>Success: </strong>{{Session::get('success_message')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                    </div>
                    @endif
                  
                    @foreach ($errors->all() as $error)
                    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                        <strong>Error</strong> {!!$error!!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endforeach
                  
                    <form id="walletCreditForm" action="{{isset($entry) ? route('wallet-credits.update', $entry->id) : route('wallet-credits.store')}}" method="post">
                    @csrf
                    @if(isset($entry)) @method('PUT') @endif
                    <div class="card-body">
                      <div class="mb-3">
                        <label class="form-label">User*</label>
                        <select name="user_id" class="form-control" required>
                            <option value="">-- Select User --</option>
                            @foreach($users as $u)
                              <option value="{{$u->id}}" {{old('user_id', $entry->user_id ?? '') == $u->id ? 'selected' : ''}}>
                                #{{$u->id}} - {{$u->name}} ({{$u->email}})
                              </option>
                            @endforeach
                        </select>
                      </div>

                      @php
                        $selectedUserId = old('user_id', $entry->user_id ?? '');
                        $currentBalance = (float)($balanceMap[$selectedUserId] ?? 0);
                      @endphp

                      <div class="mb-3">
                        <div class="alert alert-info py-2 px-3 mb-0" id="currentBalanceBox">
                            <strong>Current Balance:</strong>
                            <span id="currentBalanceValue">${{number_format($currentBalance, 2)}}</span>
                        </div>
                      </div>

                      @php
                        // FIXED: Removed $ from amount
                        $signed = old('amount', $entry->amount ?? null);
                        $isDebit = isset($signed) ? ((float)$signed < 0) : false;
                        $abs = isset($signed) ? number_format(abs((float)$signed), 2,'.','') : '';
                      @endphp

                      <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Action</label>
                            <select name="action" class="form-select">
                                <option value="credit" {{!$isDebit ? 'selected' : ''}}>Credit (+)</option>
                                <option value="debit" {{ $isDebit ? 'selected' : ''}}>Debit (-)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Amount*</label>
                            <input type="number" step="0.01" min="0" name="amount_abs" class="form-control" 
                            value="{{old('amount_abs', $abs)}}" required> {{-- FIXED: Removed $ from amount_abs --}}
                            <small class="text-muted">Choose Credit/Debit above</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Expiry Date</label> {{-- FIXED: Typo Expery to Expiry --}}
                            <input type="date" name="expires_at" class="form-control" 
                            value="{{old('expires_at', optional($entry->expires_at ?? null)->format('Y-m-d'))}}">
                            <small class="text-muted">Default: 1 year if left empty</small>
                        </div>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <input type="text" name="reason" class="form-control" maxlength="255" {{-- FIXED: maxLength to maxlength --}}
                        value="{{old('reason', $entry->reason ?? '')}}" 
                        placeholder="(Optional) e.g. Referral Bonus / Adjustment">
                      </div>
                      <div class="mb-3 d-flex align-items-center">
                        <input type="hidden" name="is_active" value="0">
                        <label for="is_active" class="me-2 mb-0">Active</label>
                        <input type="checkbox" id="is_active" name="is_active" value="1" 
                        {{old('is_active', $entry->is_active ?? 1) ? 'checked' : ''}}>
                      </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{route('wallet-credits.index')}}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
    </main>

<script>
    (function(){
        var balanceMap = @json($balanceMap ?? []);
        var sel = document.querySelector('select[name="user_id"]');
        var valueEl = document.getElementById('currentBalanceValue');

        function fmt(n){
            n = parseFloat(n || 0);
            return '$' + n.toFixed(2);
        }

        function update(){
            if(!sel || !valueEl) return;
            var uid = sel.value || '';
            // FIXED: Properly access balanceMap and ensure it's a number
            var bal = (uid && balanceMap.hasOwnProperty(uid)) ? parseFloat(balanceMap[uid]) : 0;
            valueEl.textContent = fmt(bal);
        }

        if(sel){
            sel.addEventListener('change', update);
            if(window.jQuery && jQuery.fn && jQuery.fn.select2) {
                jQuery(sel).on('change.select2', update);
            }
            update(); // Initial call
        }
    })();
</script>
@endsection