@extends('vendor.layout.layout')
@section('content')
<!--begin::App Main-->
<main class="app-main">
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">My Products</h3></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Products</li>
                    </ol>
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content Header-->
    <!--begin::App Content-->
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            @if(Session::has('error_message'))
                <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                    <strong>Error</strong> {{ Session::get('error_message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (Session::has('success_message'))
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    <strong>Success</strong> {{ Session::get('success_message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <!-- Product Management Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="card-title">
                        <a style="max-width: 150px; float:right; display: inline-block;" 
                        href="{{ route('vendor.products.create') }}" class="btn btn-block btn-primary">
                            <i class="fas fa-plus"></i> Add Product
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($productsModule['view_access'] == 1)
                        <div class="table-responsive">
                            <table class="table table-bordered" id="productsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Product Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Approval</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $product)
                                        <tr>
                                            <td>{{ $product->id }}</td>
                                            <td>{{ $product->product_name }}</td>
                                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                                            <td>${{ number_format($product->product_price, 2) }}</td>
                                            <td>{{ $product->stock }}</td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input updateProductStatus" 
                                                           type="checkbox" 
                                                           data-product-id="{{ $product->id }}"
                                                           {{ $product->status == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        {{ $product->status == 1 ? 'Active' : 'Inactive' }}
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                @if($product->is_approved == 1)
                                                    <span class="badge bg-success">Approved</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($productsModule['edit_access'] == 1)
                                                    <a href="{{ route('vendor.products.edit', $product->id) }}" 
                                                       class="btn btn-sm btn-info" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                
                                                @if($productsModule['full_access'] == 1)
                                                    <a href="javascript:void(0)" 
                                                       class="btn btn-sm btn-danger confirmDelete" 
                                                       data-module="product" 
                                                       data-id="{{ $product->id }}" 
                                                       title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No products found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            You don't have access to view products.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content-->
</main>
<!--end::App Main-->

@push('scripts')
<script>
    $(document).ready(function() {
        // Update product status
        $('.updateProductStatus').change(function() {
            var product_id = $(this).data('product-id');
            var status = $(this).is(':checked') ? 1 : 0;
            
            $.ajax({
                type: 'POST',
                url: '{{ route("vendor.products.update-status") }}',
                data: {
                    product_id: product_id,
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                }
            });
        });
        
        // Delete confirmation
        $('.confirmDelete').click(function() {
            var module = $(this).data('module');
            var id = $(this).data('id');
            
            if(confirm('Are you sure you want to delete this ' + module + '?')) {
                window.location.href = '/vendor/products/' + id;
            }
        });
    });
</script>
@endpush
@endsection