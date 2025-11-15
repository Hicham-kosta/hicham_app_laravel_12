@extends('admin.layout.layout')
@section('content')
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"> {{-- FIXED: col-ms-6 to col-sm-6 --}}
                    <h3 class="mb-0">Wallet / Credits Management</h3> {{-- FIXED: </h1> to </h3> --}}
                </div>
                <div class="col-sm-6"> {{-- FIXED: col-ms-6 to col-sm-6 --}}
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Wallet Credits {{-- FIXED: Changed from "Products" to "Wallet Credits" --}}
                        </li>
                    </ol>    
                </div>
            </div>
        </div>       
    </div>
    <div class="app-content">
       <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                   <div class="card-header">
                    <h3 class="card-title">Wallet Credits</h3>
                    @if($walletCreditsModule['edit_access'] == 1 || $walletCreditsModule['full_access'] == 1)
                        <a style="max-width: 210px; float:right; display: inline-block;"
                          href="{{url('admin/wallet-credits/create')}}" 
                          class="btn btn-block btn-primary">
                          Add Credit / Debit
                        </a>
                    @endif
                    </div>
                    <div class="card-body">
                        {{-- FIXED: Correct alert sections --}}
                        @if(Session::has('error_message'))
                        <div class="alert alert-danger alert-dismissible fade show mx-1 my-3" role="alert">
                            <strong>Error: </strong>{{Session::get('error_message')}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                        </div>
                        @endif

                        @if(Session::has('success_message'))
                        <div class="alert alert-success alert-dismissible fade show mx-1 my-3" role="alert">
                            <strong>Success: </strong>{{Session::get('success_message')}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                        </div>
                        @endif

                        <table id="walletCredits" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>User Balance</th>
                                    <th>Expires</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Added By</th>
                                    <th>Created On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($credits as $c)
                                  <tr>
                                    <td>{{$c->id}}</td>
                                    <td>
                                        @if($c->user)
                                          #{{$c->user->id}} - {{$c->user->name}}<br>
                                          <small>{{$c->user->email}}</small>
                                          @else
                                          -
                                        @endif
                                    </td>
                                    <td class="{{$c->amount < 0 ? 'text-danger' : 'text-success'}}">
                                        {{$c->amount < 0 ? '-' : '+'}}{{number_format(abs($c->amount), 2)}}
                                    </td>
                                    <td>
                                        ${{number_format((float)($runningMap[$c->id] ?? 0), 2)}}
                                    </td>
                                    <td>
                                        {{$c->expires_at ? $c->expires_at->format('F j, Y') : '-'}} {{-- FIXED: Date format --}}
                                    </td>
                                    <td>
                                        {{$c->reason ? : '-'}}
                                    </td>
                                    <td>
                                        @if($walletCreditsModule['edit_access'] == 1 || $walletCreditsModule['full_access'] == 1)
                                        @if($c->is_active == 1)
                                        <a class="updateWalletCreditStatus" data-wallet-credit-id="{{$c->id}}" 
                                        style="color: #3f6ed3" href="javascript:void(0)">
                                        <i class="fas fa-toggle-on" data-status="Active"></i>
                                        </a>
                                        @else
                                        <a class="updateWalletCreditStatus" data-wallet-credit-id="{{$c->id}}" 
                                        style="color: grey" href="javascript:void(0)">
                                        <i class="fas fa-toggle-off" data-status="Inactive"></i>
                                        </a>
                                        @endif
                                        @else
                                        <span class="badge {{$c->is_active ? 'bg-success' : 'bg-secondary'}}">
                                            {{$c->is_active ? 'Active' : 'Inactive'}}
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{$c->added_by ?? '-'}}
                                    </td>
                                    <td>
                                        {{$c->created_at->format('F j, Y, g:i a')}} {{-- FIXED: Date format --}}
                                    </td>
                                    <td>
                                        @if($walletCreditsModule['edit_access'] == 1 || $walletCreditsModule['full_access'] == 1)
                                        <a href="{{url('/admin/wallet-credits/'.$c->id.'/edit')}}">
                                            <i class="fas fa-edit"></i></a>
                                            &nbsp;&nbsp;
                                        @endif
                                        @if($walletCreditsModule['full_access'] == 1)
                                        <form action="{{route('wallet-credits.destroy', $c->id)}}" 
                                        method="POST" style="display: inline-block;">
                                        @csrf @method('DELETE')
                                        <button class="confirmDelete" name="Wallet Entry" 
                                        title="Delete Entry" type="submit" style="border: none; background: none; {{-- FIXED: type="button" to type="submit" --}}
                                        color: #3f6ed3" data-module="wallet-credit" data-id="{{$c->id}}">
                                        <i class="fas fa-trash" ></i>
                                        </button>
                                        </form>
                                        @endif
                                    </td>
                                  </tr>
                                @endforeach
                            </tbody>
                          </table>
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
