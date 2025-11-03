@extends('admin.layout.layout')
@section('content')
<main class="app-main">
    <!-- Your existing HTML content remains the same -->
    <div class="app-content-header">
        <!-- ... your existing content ... -->
    </div>
    <div class="content">
       <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                   <div class="card-header">
                    <h3 class="card-title">Reviews</h3>
                    </div>
                    <div class="card-body">
                        @if(Session::has('success_message'))
                    <div class="alert alert-success alert-dismissible fad show mx-1 my-3" role="alert">
                        <strong>Success: </strong>{{Session::get('success_message')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="reviews">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>User</th>
                                    <th>Rating</th>
                                    <th>Review</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reviews as $review)
                                <tr id="review-row-{{$review->id}}">
                                    <td>{{$review->product->product_name}}</td>
                                    <td>{{$review->user->name ?? 'Guest'}}</td>
                                    <td>{{$review->rating}}</td>
                                    <td>{{$review->review}}</td>
                                    <td>
                                        @if($reviewsModule['edit_access'] == 1 || $reviewsModule['full_access'] == 1)
                                        <a class="updateReviewStatus" href="javascript:void(0)" 
                                           data-review-id="{{$review->id}}" 
                                           data-current-status="{{$review->status ? 'Active' : 'Inactive'}}">
                                            <i class="fas fa-toggle-{{$review->status ? 'on' : 'off'}}"
                                               style="color: {{$review->status ? '#3f6ed3' : 'grey'}}"
                                               id="status-icon-{{$review->id}}"></i>
                                            <span id="status-text-{{$review->id}}">
                                                {{$review->status ? 'Active' : 'Inactive'}}
                                            </span>
                                        </a>
                                        @else
                                            <i class="fas fa-toggle-{{$review->status ? 'on' : 'off'}}"
                                               style="color: {{$review->status ? '#3f6ed3' : 'grey'}}"></i>
                                            {{$review->status ? 'Active' : 'Inactive'}}
                                        @endif
                                    </td>
                                    <td>
                                        @if($reviewsModule['edit_access'] == 1 || $reviewsModule['full_access'] == 1)
                                        <a href="{{route('reviews.edit', $review->id)}}"><i class="fa fa-edit"></i></a>&nbsp;
                                        @endif
                                        @if($reviewsModule['full_access'] == 1)
                                        <form action="{{route('reviews.destroy', $review->id)}}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="confirmDelete" name="review" data-module="review" 
                                            data-id="{{$review->id}}" style="border: none; background: none; 
                                            color: #3f6ed3;"><i class="fa fa-trash"></i></button>
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
   </div>
</main>

<!-- Move JavaScript to the bottom, right before closing body tag -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Document ready - review status script loaded');
    
    document.querySelectorAll('.updateReviewStatus').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const reviewId = this.getAttribute('data-review-id');
            const currentStatus = this.getAttribute('data-current-status');
            const icon = document.getElementById('status-icon-' + reviewId);
            const statusText = document.getElementById('status-text-' + reviewId);
            
            console.log('Clicked review:', reviewId, 'Current status:', currentStatus);
            
            if (!currentStatus || !reviewId) {
                console.error('Missing data attributes');
                alert('Error: Missing required data');
                return;
            }
            
            // Show loading state
            const originalIconClass = icon.className;
            icon.className = 'fas fa-spinner fa-spin';
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Prepare form data (some servers prefer this over JSON)
            const formData = new FormData();
            formData.append('status', currentStatus);
            formData.append('review_id', reviewId);
            formData.append('_token', csrfToken);
            
            console.log('Sending request to:', '{{ route("admin.updateReviewStatus") }}');
            
            fetch('{{ route("admin.updateReviewStatus") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status, response.statusText);
                console.log('Response headers:', Object.fromEntries(response.headers.entries()));
                
                if (!response.ok) {
                    // Get more details about the error
                    return response.text().then(text => {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}. Response: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Success:', data);
                
                if (data.status == 1) {
                    // Update to Active
                    icon.className = 'fas fa-toggle-on';
                    icon.style.color = '#3f6ed3';
                    statusText.textContent = 'Active';
                    link.setAttribute('data-current-status', 'Active');
                } else if (data.status == 0) {
                    // Update to Inactive
                    icon.className = 'fas fa-toggle-off';
                    icon.style.color = 'grey';
                    statusText.textContent = 'Inactive';
                    link.setAttribute('data-current-status', 'Inactive');
                } else {
                    console.error('Unexpected response data:', data);
                    alert('Unexpected response from server');
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                // Restore original icon on error
                icon.className = originalIconClass;
                alert('Error updating review status: ' + error.message);
            });
        });
    });
});
</script>
@endsection