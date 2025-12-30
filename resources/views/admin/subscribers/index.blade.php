@extends('admin.layout.layout')
@section('content')
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-ms-6">
                    <h3 class="mb-0">Subscribers Management</h1>
                </div>
                <div class="col-ms-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Subscribers
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
                    <h3 class="card-title">Subscribers</h3>
                    {{-- Optionaly add any header actions here --}}
                    </div>
                    <div class="card-body">
                    @if(Session::has('success_message'))
                    <div class="alert alert-success alert-dismissible fad show m-3" role="alert">
                        <strong>Success: </strong>{{Session::get('success_message')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                    </div>
                    @endif
                     <table class="table table-bordered table-striped" id="subscribers">
                        <thead>
                          <tr>
                             <th>Email</th>
                             <th>Status</th>
                             <th>Subscribed At</th>
                             <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($subscribers as $subscriber)
                          <tr>
                             <td>{{$subscriber->email}}</td>
                             <td>
                                @if(isset($subscribersModule) && 
                                ($subscribersModule['edit_access'] == 1 || $subscribersModule['full_access'] == 1))
                                <a class="updateSubscriberStatus" id="subscriber-{{$subscriber->id}}" 
                                data-subscriber-id="{{$subscriber->id}}" 
                                data-status="{{$subscriber->status}}" 
                                href="javascript:void(0)">
                                <i class="fas fa-toggle-{{$subscriber->status ? 'on' : 'off'}}" 
                                data-status="{{$subscriber->status ? 'Active' : 'Inactive'}}" 
                                style="color:{{$subscriber->status ? '#3f6ed3' : 'grey'}}"></i>
                                </a>
                                @else
                                <i class="fas fa-toggle-{{$subscriber->status ? 'on' : 'off'}}" 
                                style="color:{{$subscriber->status ? '#3f6ed3' : 'grey'}}"></i>
                                @endif
                                </td>
                                <td>{{$subscriber->created_at ? $subscriber->created_at->format('F j, Y, g:ia') : '-'}}</td>
                                <td>{{-- No editfor subscribers in normal flow only status and delete --}}
                                @if(isset($subscribersModule) && $subscribersModule['full_access'] == 1)
                                <form action="{{route('subscribers.destroy', $subscriber->id)}}" 
                                method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="confirmDelete" name="subscriber" title="Delete Subscriber" 
                                type="submit" style="border:none; background:none; color:#3f6ed3;" 
                                data-module="subscriber" data-id="{{$subscriber->id}}">
                                <i class="fas fa-trash"></i>
                                </button>
                                </form>
                                @else
                                @if(isset($subscribersModule) && $subscribersModule['edit_access'] == 1)
                                {{-- Show disabled trash icon --}}
                                <i class="fas fa-trash" style="color:#ccc"></i>
                                @endif
                                @endif
                                </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                    @if(@method_exists($subscribers, 'links'))
                    {{$subscribers->links()}}
                    @endif
                    </div>
                 </div>
              </div>
           </div>
        </div>
    </div>
</div>
</main>
@endsection


                                
    
