@extends('admin.layout.layout')
@section('content')
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-ms-6">
                    <h3 class="mb-0">Users Management</h1>
                </div>
                <div class="col-ms-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Users
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
                    <h3 class="card-title">Users</h3>
                    <!-- @if(isset($usersModule) && ($usersModule['edit_access'] == 1 || $usersModule['full_access'] == 1))
                      <a  style="max-width: 200px; float: right; display: inline-block;" 
                      href="{{url('admin/users/create') }}" class="btn btn-block btn-primary">
                      Add User
                      </a>
                    @endif -->
                  </div>
                  <div class="card-body">
                    @if(Session::has('success_message'))
                    <div class="alert alert-success alert-dismissible fad show mx-1 my-3" role="alert">
                        <strong>Success: </strong>{{Session::get('success_message')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="users">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>City</th>
                                    <th>Postcode</th>
                                    <th>Country</th>
                                    <th>Registred On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                  <tr>
                                    <td>{{$user->id}}</td>
                                    <td>{{$user->name ?? '-'}}</td>
                                    <td>{{$user->email}}</td>
                                    <td>{{$user->city ?? '-'}}
                                    <td>{{$user->postcode ?? '-'}}
                                    <td>{{$user->country ?? '-'}}
                                    <td>{{optional($user->created_at)->format('F j, Y g:i a') ?? '-'}}</td>
                                    <td>
                                        @if(isset($usersModule) && ($usersModule['edit_access'] == 1 || $usersModule['full_access'] == 1))
                                        {{-- Toggle Active/Inactive --}}
                                          @if($user->status == 1)
                                            <a class="updateUserStatus" data-user-id="{{$user->id}}" 
                                            style="color: 3fe6ed3;" href="javascript:void(0);"><i class="fas fa-toggle-on"
                                            data-status="Active"></i></a>
                                          @else
                                            <a class="updateUserStatus" data-user-id="{{$user->id}}" 
                                            style="color: grey;" href="javascript:void(0);"><i class="fas fa-toggle-off"
                                            data-status="Inactive"></i></a>
                                          @endif
                                        <!-- {{-- Edit User --}} 
                                         @if($usersModule['edit_access'] == 1 || $usersModule['full_access'] == 1)
                                         &nbsp;&nbsp;
                                         <a href{{url('admin/users/'.$user->id.'/edit')}}><i class="fas fa-edit"></i></a>
                                         @endif -->
                                         <!-- {{-- Delete User --}}
                                          @if($usersModule['full_access'] == 1)
                                          &nbsp;&nbsp;
                                          <form action="{{url('admin/users/'.$user->id)}}" 
                                          method="POST" style="display:inline-block;"> 
                                          @Csrf 
                                          @method('DELETE') 
                                          <button class="confirmDelete" type="button" 
                                          title="Delete User" style="border:none; background:none; color: 3fe6ed3"
                                          data-module="user" data-id="{{$user->id}}"><i class="fas fa-trash"></i>
                                          </button>
                                          </form>
                                          @endif -->
                                        @endif
                                      </td>
                                    </tr>
                                  @endforeach
                                </tbody>
                              </table>
                            </div>
                            {{-- Paginate --}}
                            <div class="mt-3">
                                @if(@method_exists($users, 'links'))
                                {{$users->links()}}
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