@extends('admin.layout.layout')
@section('content')
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-ms-6">
                    <h3 class="mb-0">CMS Pages</h1>
                </div>
                <div class="col-ms-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Pages
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
                    <h3 class="card-title">Pages</h3>
                    @if(isset($pagesModule['edit_access']) && 
                    ($pagesModule['edit_access'] == 1 || $pagesModule['full_access'] == 1))
                        <a style="max-width: 150px; float:right; display: inline-block;"
                          href="{{url('admin/pages/create')}}" 
                          class="btn btn-block btn-primary">
                          Add Page
                        </a>
                    @endif
                    </div>
                  <div class="card-body">
                    @if(Session::has('error_message'))
                    <div class="alert alert-danger alert-dismissible fad show m-3" role="alert">
                        <strong>Error: </strong>{{Session::get('error_message')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                    </div>
                    @endif
                    @if(Session::has('success_message'))
                    <div class="alert alert-success alert-dismissible fad show m-3" role="alert">
                        <strong>Success: </strong>{{Session::get('success_message')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                    </div>
                    @endif
<div id="pages-container" class="table table-boredred table-striped">
    <table class="table table-bordered table-striped" id="pages">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>URL</th>
                <th>Status</th>
                <th>Sort</th>
                <th>Created On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pages as $page)
              <tr>
                <td>{{$page->id}}</td>
                <td>{{$page->title}}</td>
                <td>{{$page->url}}</td>
                <td>
                  @if($page->status == 1)
                    <a href="javascript:void(0)"
   class="updatePageStatus"
   data-page-id="{{ $page->id }}">
    <i class="fas fa-toggle-on" data-status="Active"></i>
</a>

                    @else
                    <a href="javascript:void(0)"
   class="updatePageStatus"
   data-page-id="{{ $page->id }}">
    <i class="fas fa-toggle-off" data-status="Inactive"></i>
</a>
                  @endif
                </td>
                <td>{{$page->sort_order}}</td>
                <td>{{optional($page->created_at)->format('F j, Y, g:ia')}}</td> <!-- Fixed date format -->
                <td>  
                  @if(isset($pagesModule['edit_access']) && 
                  ($pagesModule['edit_access'] == 1 || $pagesModule['full_access'] == 1))
                    <a href="{{url('admin/pages/'.$page->id.'/edit')}}">
                        <i class="fas fa-edit"></i></a>
                        &nbsp;&nbsp;
                  @endif
                  @if(isset($pagesModule['full_access']) && $pagesModule['full_access'] == 1)
                    <form action="{{route('pages.destroy', $page->id)}}" 
                    method="POST" style="display:inline-block;">
                      @csrf
                      @method('DELETE')
                      <button class="confirmDelete" name="page" title="Delete Page" 
                        type="button" style="border:none; background:none; color:#3f6ed3;" 
                        data-module="page" data-id="{{$page->id}}">
                        <i class="fas fa-trash"></i>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('Status toggle script active');

    // Event delegation - listen for clicks on elements with class 'updatePageStatus'
    document.addEventListener('click', function (e) {
        // Check if the clicked element or its parent has the class
        const button = e.target.closest('.updatePageStatus');
        if (!button) return;

        e.preventDefault();

        const pageId = button.dataset.pageId;
        const icon = button.querySelector('i');
        const currentStatus = icon.dataset.status; // "Active" or "Inactive"
        
        // Send the current status to the server
        fetch("{{ route('page.update.status') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            },
            body: JSON.stringify({
                page_id: pageId,
                status: currentStatus  // Send the current status string
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Response:', data);
            
            if (data.success) {
                // Toggle the status
                if (data.status == 1) {
                    icon.classList.remove('fa-toggle-off');
                    icon.classList.add('fa-toggle-on');
                    icon.dataset.status = 'Active';
                    button.style.color = '#3f6ed3';
                } else {
                    icon.classList.remove('fa-toggle-on');
                    icon.classList.add('fa-toggle-off');
                    icon.dataset.status = 'Inactive';
                    button.style.color = 'grey';
                }
            } else {
                alert('Error: ' + (data.message || 'Failed to update status'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the status');
        });
    });
});
</script>
