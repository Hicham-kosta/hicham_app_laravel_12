@extends('admin.layout.layout')
@section('content')
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-ms-6">
                    <h3 class="mb-0">Catalogue Management</h1>
                </div>
                <div class="col-ms-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Brands
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
                    <h3 class="card-title">Brands</h3>
                    @if($brandsModule['edit_access'] == 1 || $brandsModule['full_access'] == 1)
                        <a style="max-width: 150px; float:right;"
                          href="{{url('admin/brands/create')}}" 
                          class="btn btn-block btn-primary">
                          Add Brand
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
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="brands">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>URL</th>
                                    <th>Created On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($brands as $brand)
                                  <tr>
                                    <td>{{$brand->id}}</td>
                                    <td>{{$brand->name}}</td>
                                    <td>{{$brand->url}}</td>
                                    <td>{{$brand->created_at->format('F j, Y, g:i a')}}</td>
                                    <td>
                                    @if($brandsModule['edit_access'] == 1 || $brandsModule['full_access'] == 1)
                                    <!-- Actions: Enable, Disable, Edit, Delete...-->
                                        @if($brand->status == 1)
                                          <a class="updateBrandStatus" data-brand-id="{{
                                          $brand->id}}"style='color:#3f6ed3' title="Desactivate brand" href="
                                          javasript:(0)"><i class="fas fa-toggle-on" 
                                          data-status="Active"></i></a>
                                        @else
                                         <a class="updateBrandStatus" data-brand-id="{{
                                         $brand->id}}"style='color:grey' title="Activate Brand" href="
                                         javasript:(0)"><i class="fas fa-toggle-off" 
                                         data-status="Inactive"></i></a>   
                                        @endif
                                        @if($brandsModule['edit_access'] == 1 || $brandsModule['full_access'] == 1)
                                    <!-- Actions: Enable, Disable, Edit, Delete...-->
                                        &nbsp;&nbsp;
                                        <a title="Edit Brand" href="{{url('admin/brands/'.$brand->id.'/edit')}}">
                                            <i class="fas fa-edit"></i></a>&nbsp;&nbsp;
                                        @endif
                                            @if($brandsModule['full_access'] == 1)
                                            <form action="{{route('brands.destroy', $brand->id)}}" method="POST" style="display:
                                               inline-block;" >@csrf
                                                @method('DELETE')
                                                <button class="confirmDelete" name="brand" 
                                                title="Delete brand" type="button" style=
                                                "border:none;background:none; color:#3f6ed3;" 
                                                href="javascript:void(0)" data-module="brand" 
                                                data-id="{{$brand->id}}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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