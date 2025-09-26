@extends('admin.layout.layout')
@section('content')
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-ms-6">
                    <h3 class="mb-0">Admin Management</h1>
                </div>
                <div class="col-ms-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Subadmins
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
                    <h3 class="card-title">Subadmins</h3>
                        <a style="max-width: 150px; float:right; display: inline-block;"
                          href="{{url('admin/add-edit-subadmin')}}" 
                          class="btn btn-block btn-primary">
                          Add Subadmin
                        </a>
                    </div>
                  <div class="card-body">
                    @if(Session::has('success_message'))
                    <div class="alert alert-success alert-dismissible fad show m-3" role="alert">
                        <strong>Success: </strong>{{Session::get('success_message')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                    </div>
                    @endif    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="subadmins">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subadmins as $subadmin)
                                  <tr>
                                    <td>{{$subadmin->id}}</td>
                                    <td>{{$subadmin->name}}</td>
                                    <td>{{$subadmin->mobile}}</td>
                                    <td>{{$subadmin->email}}</td>
                                    <td>
                                        @if($subadmin->status==1)
                                        <a class="updateSubadminStatus" data-subadmin_id="{{
                                        $subadmin->id}}" style='color:#3f6ed3' title="Inactive Subadmin" href="
                                        javascript:void(0)"><i class="fas fa-toggle-on" data-status
                                        ="Active"></i></a>
                                        @else
                                        <a class="updateSubadminStatus" data-subadmin_id="{{
                                        $subadmin->id}}" style='color:grey' title="Active Subadmin" href="javascript:void(
                                        0)"><i class="fas fa-toggle-off" data-status="Inactive"></i
                                        ></a>@endif&nbsp;&nbsp;<a style='color:#3f6ed3;' title="edit Subadmin"
                                        href="{{url('admin/add-edit-subadmin/'.$subadmin->id)}}"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;<a style='color:#3f6ed3;' title="Set Permisions for Sub_admins" href="{{url('admin/update-role/'.$subadmin->id)}}"><i class="fas fa-unlock"></i></a>&nbsp;&nbsp;<a class="confirmDelete" name="Subadmin" title="Delete Subadmin" style='color:#3f6ed3;'
                                        data-module="subadmin" data-id="{{$subadmin->id}}" <?php /* href="{{url('admin/delete-subadmin/'.$subadmin->id)}}" */ ?>><i class="fas fa-trash"></i></a>   
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