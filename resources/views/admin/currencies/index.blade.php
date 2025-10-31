@extends('admin.layout.layout')
@section('content')
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-ms-6">
                    <h3 class="mb-0">Settings</h1>
                </div>
                <div class="col-ms-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Currencies
                        </li>  
                    </ol>    
                </div>
            </div>
        </div>       
    </div>
    <div class="content">
       <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                   <div class="card-header">
                     <h3 class="card-title">Currencies</h3>
                       @if(!empty($currenciesModule) && $currenciesModule['edit_access'] == 1 || $currenciesModule['full_access'] == 1)
                          <a style="max-width: 180px; float: right; display: inline-block;" 
                          href="{{ url('admin/currencies/create') }}" class="btn btn-block btn-primary">
                           Add Currency
                          </a>
                       @endif
                  </div>
                  <div class="card-body">
                    @if(Session::has('success_message'))
                    <div class="alert alert-success alert-dismissible fad show m-3" role="alert">
                        <strong>Success: </strong>{{Session::get('success_message')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                    </div>
                    @endif
                    <table id="currencies" class="table table-bordered table-striped">
                        <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Flag</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Symbol</th>
                                    <th>Rate</th>
                                    <th>Base</th>
                                    <th>Created On</th>
                                    <th>Actions</th>
                                </tr>
                        </thead>
                        <tbody>
                            @foreach($currencies as $currency)
                              <tr id="currency-row-{{ $currency->id }}">
                                <td>{{ $currency->id }}</td>
                                <td>
                                    @if($currency->flag)
                                       <img src="{{ asset('front/images/flags/'.$currency->flag) }}" 
                                       alt="{{$currency->code}}" style="height: 16px;">
                                       @else
                                       <span class="text-muted">--</span>
                                    @endif
                                <td>{{$currency->name}}</td>
                                <td>{{$currency->code}}</td>
                                <td>{{$currency->symbol}}</td>
                                <td>{{number_format($currency->rate, 8)}}</td>
                                <td>
                                    @if($currency->is_base)
                                     <span class="badge bg-success text-white">Base</span>
                                    @endif
                                </td> 
                                <td>{{optional($currency->created_at)->format('F,j,Y,g:ia')}}</td>
                                <td>
                                    @if($currenciesModule['edit_access'] == 1 || $currenciesModule['full_access'] == 1)
                                     <!-- Actions(Enable/desable, Edit, Delete) will be added here -->
                                      @if($currency->status == 1)
                                       <a class="updateCurrencyStatus" data-currency-id="{{$currency->id}}" 
                                       style="color: 3f6ed3" href="javascript:void(0)"><i class="fas fa-toggle-on" 
                                       data-status="Active"></i></a>
                                      @else
                                       <a class="updateCurrencyStatus" data-currency-id="{{$currency->id}}" 
                                       style="color: grey" href="javascript:void(0)"><i class="fas fa-toggle-off" 
                                       data-status="Inactive"></i></a>
                                      @endif
                                      @if($currenciesModule['edit_access'] == 1 || $currenciesModule['full_access'] == 1)
                                      &nbsp;&nbsp;
                                      <a href="{{url('admin/currencies/'.$currency->id.'/edit')}}">
                                        <i class="fas fa-edit"></i></a>&nbsp;&nbsp;
                                      @endif
                                      @if($currenciesModule['full_access'] == 1)
                                      <form action="{{(route('currencies.destroy', $currency->id))}}" method="POST" 
                                      style="display: inline-block">
                                      @csrf
                                      @method('DELETE')
                                      <button class="confirmDelete" name="currency" title="Delete Currency" 
                                      type="button" style="border: none; background: none; color: #3f6ed3;" 
                                      href="javascript:void(0)" data-module="currency" data-id="{{$currency->id}}">
                                      <i class="fas fa-trash"></i></button>
                                      </form>
                                      @endif
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
                         
