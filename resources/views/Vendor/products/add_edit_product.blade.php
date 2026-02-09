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
                        <li class="breadcrumb-item"><a href="{{ route('vendor.products.index') }}">Products</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
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
            <!--begin::Row-->
            <div class="row g-4">
                <!--begin::Col-->
                <div class="col-md-12">
                    <!--begin::Quick Example-->
                    <div class="card card-primary card-outline mb-4">
                        <!--begin::Header-->
                        <div class="card-header">
                            <div class="card-title">{{ $title }}</div>
                        </div>
                        <!--end::Header-->
                        
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
                        
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                <strong>Error</strong> {!! $error !!}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endforeach
                        
                        <!-- Approval Notice for Vendor -->
                        <div class="alert alert-info m-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> All vendor products require admin approval before they become visible to customers.
                            @if(isset($product) && $product->is_approved == 0)
                                <br><span class="badge bg-warning mt-1">Pending Approval</span>
                            @elseif(isset($product) && $product->is_approved == 1)
                                <br><span class="badge bg-success mt-1">Approved</span>
                            @endif
                        </div>
                        
                        <!--begin::Form-->
                        <form name="productForm" id="productForm" 
                              action="{{ isset($product) ? route('vendor.products.update', $product->id) : route('vendor.products.store') }}" 
                              method="post" enctype="multipart/form-data">
                            @csrf
                            @if(isset($product)) @method('PUT') @endif

                            @if(isset($product))
    <input type="hidden" name="id" value="{{ $product->id }}">
    <input type="hidden" name="product_url" value="{{ $product->product_url ?? '' }}">
@endif
                            
                            <!-- Hidden Vendor ID -->
                            <input type="hidden" name="vendor_id" value="{{ auth('admin')->user()->id }}">
                            
                            <!--begin::Body-->
                            <div class="card-body">
                                <!-- Category Selection -->
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category *</label>
                                    <select name="category_id" id="category_id" class="form-control" required>
                                        <option value="">Select Category</option>
                                        @foreach($getCategories as $cat)
                                            <option value="{{ $cat['id'] }}" 
                                                {{ (old('category_id', $product->category_id ?? '') == $cat['id']) ? 'selected' : '' }}>
                                                {{ $cat['name'] }}
                                            </option>
                                            @if(!empty($cat['subcategories']))
                                                @foreach($cat['subcategories'] as $subcat)
                                                    <option value="{{ $subcat['id'] }}" 
                                                        {{ (old('category_id', $product->category_id ?? '') == $subcat['id']) ? 'selected' : '' }}>
                                                        &nbsp;&nbsp;› {{ $subcat['name'] }}
                                                    </option>
                                                    @if(!empty($subcat['subcategories']))
                                                        @foreach($subcat['subcategories'] as $subsubcat)
                                                            <option value="{{ $subsubcat['id'] }}" 
                                                                {{ (old('category_id', $product->category_id ?? '') == $subsubcat['id']) ? 'selected' : '' }}>
                                                                &nbsp;&nbsp;&nbsp;&nbsp;›› {{ $subsubcat['name'] }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <!-- Show in other categories -->
                                <div class="mb-3">
                                    <label for="other_categories">Show in other categories</label>
                                    <select name="other_categories[]" id="other_categories" class="form-control" multiple style="height: 150px;">
                                        @foreach($getCategories as $cat)
                                            <option value="{{ $cat['id'] }}"
                                                @if(!empty($product) && isset($product->otherCategories) && 
                                                in_array($cat['id'], $product->otherCategories->pluck('category_id')->toArray())) 
                                                selected @endif>
                                                {{ $cat['name'] }}
                                            </option>
                                            @if(!empty($cat['subcategories']))
                                                @foreach($cat['subcategories'] as $subcat)
                                                    <option value="{{ $subcat['id'] }}"
                                                        @if(!empty($product) && isset($product->otherCategories) && 
                                                        in_array($subcat['id'], $product->otherCategories->pluck('category_id')->toArray())) 
                                                        selected @endif>
                                                        &nbsp;&nbsp;› {{ $subcat['name'] }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </select>
                                    <div class="mt-2">
                                        <button type="button" id="selectAll" class="btn btn-sm btn-primary">
                                            <i class="fas fa-check-circle"></i> Select All
                                        </button>
                                        <button type="button" id="deselectAll" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-times-circle"></i> Deselect All
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Brand Selection -->
                                <div class="mb-3">
                                    <label class="form-label" for="brand_id">Brand</label>
                                    <select name="brand_id" id="brand_id" class="form-control">
                                        <option value="">Select Brand</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand['id'] }}" 
                                                {{ (old('brand_id', $product->brand_id ?? '') == $brand['id']) ? 'selected' : '' }}>
                                                {{ $brand['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Product Name -->
                                <div class="mb-3">
                                    <label class="form-label" for="product_name">Product Name *</label>
                                    <input type="text" class="form-control" id="product_name" name="product_name"
                                           placeholder="Enter Product Name" 
                                           value="{{ old('product_name', $product->product_name ?? '') }}" required>
                                    @error('product_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <!-- Product URL (Only for edit) -->
                                @if(!empty($product->id))
                                    <div class="mb-3">
                                        <label class="form-label" for="product_url">Product URL *</label>
                                        <input type="text" class="form-control" id="product_url" name="product_url"
                                               placeholder="Enter Product URL" 
                                               value="{{ old('product_url', $product->product_url ?? '') }}" required>
                                    </div>
                                @endif
                                
                                <!-- Product Code -->
                                <div class="mb-3">
                                    <label class="form-label" for="product_code">Product Code *</label>
                                    <input type="text" class="form-control" id="product_code" name="product_code"
                                           placeholder="Enter Product Code" 
                                           value="{{ old('product_code', $product->product_code ?? '') }}" required>
                                    @error('product_code')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <!-- Product Color -->
                                <div class="mb-3">
                                    <label class="form-label" for="product_color">Product Color</label>
                                    <input type="text" class="form-control" id="product_color" name="product_color"
                                           placeholder="Enter Product Color" 
                                           value="{{ old('product_color', $product->product_color ?? '') }}">
                                </div>
                                
                                <!-- Family Color -->
                                <?php $familyColors = \App\Models\Color::colors(); ?>
                                <div class="mb-3">
                                    <label class="form-label" for="family_color">Family Color</label>
                                    <select name="family_color" id="family_color" class="form-control">
                                        <option value="">Select Family Color</option>
                                        @foreach($familyColors as $color)
                                            <option value="{{ $color->name }}" 
                                                {{ (old('family_color', $product->family_color ?? '') == $color->name) ? 'selected' : '' }}>
                                                {{ $color->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Group Code -->
                                <div class="mb-3">
                                    <label class="form-label" for="group_code">Group Code</label>
                                    <input type="text" class="form-control" id="group_code" name="group_code"
                                           placeholder="Enter Group Code" 
                                           value="{{ old('group_code', $product->group_code ?? '') }}">
                                </div>
                                
                                <!-- Product Price -->
                                <div class="mb-3">
                                    <label class="form-label" for="product_price">Product Price *</label>
                                    <input type="number" step="0.01" class="form-control" id="product_price" name="product_price"
                                           placeholder="Enter Product Price" 
                                           value="{{ old('product_price', $product->product_price ?? '') }}" required>
                                    @error('product_price')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <!-- Product Discount -->
                                <div class="mb-3">
                                    <label class="form-label" for="product_discount">Product Discount (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="product_discount" name="product_discount"
                                           placeholder="Enter Discount Percentage" 
                                           value="{{ old('product_discount', $product->product_discount ?? '') }}">
                                </div>
                                
                                <!-- Product GST -->
                                <div class="mb-3">
                                    <label class="form-label" for="product_gst">Product GST (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="product_gst" name="product_gst"
                                           placeholder="Enter GST Percentage" 
                                           value="{{ old('product_gst', $product->product_gst ?? '') }}">
                                </div>
                                
                                <!-- Product Weight -->
                                <div class="mb-3">
                                    <label class="form-label" for="product_weight">Product Weight (g)</label>
                                    <input type="number" step="0.01" class="form-control" id="product_weight" name="product_weight"
                                           placeholder="Enter Weight in Grams" 
                                           value="{{ old('product_weight', $product->product_weight ?? '') }}">
                                </div>
                                
                                <!-- Product Attributes Section -->
                                <div class="mb-3">
                                    <label class="form-label mb-1">Product Attributes *</label>
                                    <div class="alert alert-warning py-2 mb-2">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        At least one attribute (size, SKU, price) is required.
                                    </div>
                                    
                                    <!-- Header Row -->
                                    <div class="d-none d-md-flex fw-semibold bg-light border rounded px-2 py-1 mb-2">
                                        <div class="flex-fill col-2">Size</div>
                                        <div class="flex-fill col-2 ms-2">SKU *</div>
                                        <div class="flex-fill col-2 ms-2">Price *</div>
                                        <div class="flex-fill col-2 ms-2">Stock</div>
                                        <div class="flex-fill col-2 ms-2">Sort</div>
                                        <div style="width:50px"></div>
                                    </div>
                                    
                                    <!-- Dynamic Rows for New Attributes -->
                                    <div class="field_wrapper" id="new-attributes-container">
                                        <div class="d-flex align-items-center gap-2 mb-2 attribute-row">
                                            <input type="text" name="size[]" class="form-control flex-fill col-2" placeholder="Size (e.g., M)">
                                            <input type="text" name="sku[]" class="form-control flex-fill col-2" placeholder="SKU">
                                            <input type="number" step="0.01" name="price[]" class="form-control flex-fill col-2" placeholder="Price">
                                            <input type="number" name="stock[]" class="form-control flex-fill col-2" placeholder="Stock" value="0">
                                            <input type="number" name="sort[]" class="form-control flex-fill col-2" placeholder="Sort" value="0">
                                            <button type="button" class="btn btn-sm btn-success add-button" title="Add More">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Add More Button -->
                                    <div class="mt-2">
                                        <button type="button" id="add-more-attributes" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus-circle"></i> Add More Attributes
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Existing Product Attributes (Edit Mode) -->
                                @if(isset($product) && $product->attributes->count() > 0)
                                    <div class="mb-3">
                                        <label class="form-label mb-1">Existing Product Attributes</label>
                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle mb-0">
                                                <thead class="table-light text-center">
                                                    <tr>
                                                        <th style="width:15%">Size</th>
                                                        <th style="width:20%">SKU</th>
                                                        <th style="width:15%">Price</th>
                                                        <th style="width:15%">Stock</th>
                                                        <th style="width:15%">Sort</th>
                                                        <th style="width:20%">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($product['attributes'] as $index => $attribute)
                                                        <tr class="text-center">
                                                            <td>
                                                                <input type="text" name="update_size[]" value="{{ $attribute['size'] }}" 
                                                                       class="form-control form-control-sm">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="update_sku[]" value="{{ $attribute['sku'] }}" 
                                                                       class="form-control form-control-sm">
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" name="update_price[]" value="{{ $attribute['price'] }}" 
                                                                       class="form-control form-control-sm">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="update_stock[]" value="{{ $attribute['stock'] }}" 
                                                                       class="form-control form-control-sm">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="update_sort[]" value="{{ $attribute['sort'] }}" 
                                                                       class="form-control form-control-sm">
                                                            </td>
                                                            <td>
                                                                <input type="hidden" name="attrId[]" value="{{ $attribute->id }}">
                                                                @if($attribute['status'] == 1)
                                                                    <a class="updateAttributeStatus btn btn-sm btn-outline-success" 
                                                                       data-attribute-id="{{ $attribute->id }}" 
                                                                       href="javascript:void(0)" title="Active">
                                                                        <i class="fas fa-toggle-on"></i>
                                                                    </a>
                                                                @else
                                                                    <a class="updateAttributeStatus btn btn-sm btn-outline-secondary" 
                                                                       data-attribute-id="{{ $attribute->id }}" 
                                                                       href="javascript:void(0)" title="Inactive">
                                                                        <i class="fas fa-toggle-off"></i>
                                                                    </a>
                                                                @endif
                                                                <a href="javascript:void(0)" class="btn btn-sm btn-outline-danger delete-attribute"
                                                                   data-id="{{ $attribute['id'] }}" title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Product Main Image -->
                                <!-- Main Image Dropzone -->
<div class="mb-3">
    <label class="form-label" for="main_image_dropzone">Product Main Image (Max 500KB)</label>
    <div class="dropzone" id="mainImageDropzone">
        <div class="dz-message" data-dz-message>
            <i class="fas fa-cloud-upload-alt"></i><br>
            <span>Drop main image here or click to upload</span>
        </div>
    </div>
    <div id="mainImageDropzoneError" style="color:red; display:none;"></div>
    <br />
    <!-- Hidden Input to send uploaded image -->
    <input type="hidden" name="main_image_hidden" id="main_image_hidden" 
           value="{{ old('main_image_hidden', $product->main_image ?? '') }}">
    
    @if(!empty($product['main_image']))
        <div class="mt-2">
            <a target="_blank" href="{{ url('front/images/products/'.$product['main_image']) }}">
                <img style="width:80px; margin:10px; border: 1px solid #ddd; border-radius: 4px; padding: 2px;" 
                     src="{{ url('product-image/thumbnail/'.$product->main_image) }}">
            </a>
            <a style='color:#3f6ed3;' class="confirmDelete btn btn-sm btn-outline-danger"
               title="Delete Product Image" href="javascript:void(0)" data-module="product-main-image"
               data-id="{{ $product['id'] }}">
                <i class="fas fa-trash"></i> Delete
            </a>
        </div>
    @endif
</div>

<!-- Alternative Images Dropzone -->
<div class="mb-3">
    <label class="form-label" for="product_images_dropzone">Alternate Product Images (Max 500KB each)</label>
    <div class="dropzone" id="productImagesDropzone">
        <div class="dz-message" data-dz-message>
            <i class="fas fa-cloud-upload-alt"></i><br>
            <span>Drop multiple images here or click to upload</span>
        </div>
    </div>
    <!-- Hidden Input to send uploaded images -->
    <input type="hidden" name="product_images_hidden" id="product_images_hidden" 
           value="{{ old('product_images_hidden', '') }}">
    
    @if(isset($product->product_images) && $product->product_images->count() > 0)
        @if($product->product_images->count() > 1)
            <p class="drag-instruction mt-2 text-muted">
                <i class="fas fa-arrows-alt"></i> Drag and drop to reorder images
            </p>
        @endif
        
        <!-- Container for sortable images -->
        <div id="sortable-images" class="sortable-wrapper d-flex gap-2 overflow-auto mt-2">
            @foreach($product->product_images as $img)
                <div class="sortable-item position-relative" data-id="{{ $img->id }}" style="cursor: move;">
                    <a target="_blank" href="{{ url('front/images/products/'.$img->image) }}">
                        <img src="{{ url('product-image/thumbnail/'.$img->image) }}" 
                             style="width:80px; border: 1px solid #ddd; border-radius: 4px; padding: 2px;">
                    </a>
                    <a href="javascript:void(0)" class="position-absolute top-0 end-0 btn btn-sm btn-danger confirmDelete" 
                       data-module="product-image" data-id="{{ $img->id }}">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Product Video Dropzone -->
<div class="mb-3">
    <label class="form-label" for="product_video_dropzone">Product Video (Max 2MB)</label>
    <div class="dropzone" id="productVideoDropzone">
        <div class="dz-message" data-dz-message>
            <i class="fas fa-cloud-upload-alt"></i><br>
            <span>Drop video here or click to upload</span>
        </div>
    </div>
    <!-- Hidden Input to send uploaded video -->
    <input type="hidden" name="product_video_hidden" id="product_video_hidden" 
           value="{{ old('product_video_hidden', $product->product_video ?? '') }}">
    
    @if(!empty($product['product_video']))
        <div class="mt-2">
            <div class="d-flex align-items-center">
                <i class="fas fa-video me-2 text-primary"></i>
                <a target="_blank" href="{{ url('front/videos/products/'.$product['product_video']) }}" 
                   class="me-3">
                    View Video
                </a>
                <a href="javascript:void(0)" class="btn btn-sm btn-outline-danger confirmDelete" 
                   data-module="product-video" data-id="{{ $product['id'] }}">
                    <i class="fas fa-trash"></i> Delete Video
                </a>
            </div>
        </div>
    @endif
</div>
                                
                                <!-- Wash Care -->
                                <div class="mb-3">
                                    <label class="form-label" for="wash_care">Wash Care Instructions</label>
                                    <textarea class="form-control" name="wash_care" id="wash_care" rows="2" 
                                              placeholder="Enter wash care instructions">{{ old('wash_care', $product->wash_care ?? '') }}</textarea>
                                </div>
                                
                                <!-- Product Description -->
                                <div class="mb-3">
                                    <label class="form-label" for="description">Product Description *</label>
                                    <textarea class="form-control" name="description" id="description" rows="4" 
                                              placeholder="Enter detailed product description" required>{{ old('description', $product->description ?? '') }}</textarea>
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <!-- Search Keywords -->
                                <div class="mb-3">
                                    <label class="form-label" for="search_keywords">Search Keywords</label>
                                    <textarea class="form-control" name="search_keywords" id="search_keywords" rows="2" 
                                              placeholder="Enter comma separated keywords">{{ old('search_keywords', $product->search_keywords ?? '') }}</textarea>
                                    <div class="form-text">Separate keywords with commas</div>
                                </div>
                                
                                <!-- Filters (if applicable) -->
                                @php
                                    $filters = \App\Models\Filter::with(['values' => function($q) {
                                        $q->where('status', 1)->orderBy('sort', 'asc');
                                    }])->where('status', 1)->orderBy('sort', 'asc')->get();
                                    
                                    $selectedValues = isset($product) ? $product->filterValues->pluck('id')->toArray() : [];
                                @endphp
                                
                                @if($filters->count() > 0)
                                    <div class="mb-3">
                                        <label class="form-label">Product Filters</label>
                                        @foreach($filters as $filter)
                                            <div class="mb-2">
                                                <label class="form-label-sm">{{ ucwords($filter->filter_name) }}</label>
                                                <select name="filter_values[{{ $filter->id }}]" class="form-control form-control-sm">
                                                    <option value="">-- Select {{ ucwords($filter->filter_name) }} --</option>
                                                    @foreach($filter->values as $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ in_array($value->id, $selectedValues) ? 'selected' : '' }}>
                                                            {{ ucfirst($value->value) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                
                                <!-- Meta Title -->
                                <div class="mb-3">
                                    <label class="form-label" for="meta_title">Meta Title</label>
                                    <input type="text" class="form-control" id="meta_title" name="meta_title"
                                           placeholder="Enter meta title for SEO" 
                                           value="{{ old('meta_title', $product->meta_title ?? '') }}">
                                    <div class="form-text">Recommended: 50-60 characters</div>
                                </div>
                                
                                <!-- Meta Description -->
                                <div class="mb-3">
                                    <label class="form-label" for="meta_description">Meta Description</label>
                                    <textarea class="form-control" name="meta_description" id="meta_description" rows="2"
                                              placeholder="Enter meta description for SEO">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
                                    <div class="form-text">Recommended: 150-160 characters</div>
                                </div>
                                
                                <!-- Meta Keywords -->
                                <div class="mb-3">
                                    <label class="form-label" for="meta_keywords">Meta Keywords</label>
                                    <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                                           placeholder="Enter meta keywords for SEO" 
                                           value="{{ old('meta_keywords', $product->meta_keywords ?? '') }}">
                                    <div class="form-text">Separate keywords with commas</div>
                                </div>
                                
                                <!-- Sort Order -->
                                <div class="mb-3">
                                    <label class="form-label" for="sort">Sort Order</label>
                                    <input type="number" class="form-control" id="sort" name="sort"
                                           value="{{ old('sort', $product->sort ?? 0) }}">
                                    <div class="form-text">Lower numbers appear first</div>
                                </div>
                                
                                <!-- Featured Product -->
                                <div class="mb-3">
                                    <label class="form-label" for="is_featured">Featured Product?</label>
                                    <select name="is_featured" id="is_featured" class="form-control">
                                        <option value="No" {{ (old('is_featured', $product->is_featured ?? 'No') == 'No') ? 'selected' : '' }}>
                                            No
                                        </option>
                                        <option value="Yes" {{ (old('is_featured', $product->is_featured ?? 'No') == 'Yes') ? 'selected' : '' }}>
                                            Yes (Highlight this product)
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <!--end::Body-->
                            
                            <!--begin::Footer-->
                            <div class="card-footer d-flex justify-content-between">
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        {{ isset($product) ? 'Update Product' : 'Add Product' }}
                                    </button>
                                    @if(isset($product) && $product->is_approved == 0)
                                        <span class="badge bg-warning ms-2 align-middle">
                                            <i class="fas fa-clock me-1"></i>Pending Approval
                                        </span>
                                    @endif
                                </div>
                                <a href="{{ route('vendor.products.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                            </div>
                            <!--end::Footer-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Quick Example-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content-->
</main>
<!--end::App Main-->
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Add CSRF token to all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Select All / Deselect All for categories
    $('#selectAll').click(function() {
        $('#other_categories option').prop('selected', true);
    });
    
    $('#deselectAll').click(function() {
        $('#other_categories option').prop('selected', false);
    });
    
    // Add more attribute rows
    let attributeRowCount = 1;
    
    // Function to add new attribute row
    function addAttributeRow() {
        const template = `
            <div class="d-flex align-items-center gap-2 mb-2 attribute-row">
                <input type="text" name="size[]" class="form-control flex-fill col-2" placeholder="Size">
                <input type="text" name="sku[]" class="form-control flex-fill col-2" placeholder="SKU">
                <input type="number" step="0.01" name="price[]" class="form-control flex-fill col-2" placeholder="Price">
                <input type="number" name="stock[]" class="form-control flex-fill col-2" placeholder="Stock" value="0">
                <input type="number" name="sort[]" class="form-control flex-fill col-2" placeholder="Sort" value="0">
                <button type="button" class="btn btn-sm btn-danger remove-row" title="Remove">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        `;
        $('#new-attributes-container').append(template);
        attributeRowCount++;
    }
    
    // Add more attributes button
    $('#add-more-attributes').click(function() {
        addAttributeRow();
    });
    
    // Add button in existing rows
    $(document).on('click', '.add-button', function() {
        addAttributeRow();
    });
    
    // Remove attribute row
    $(document).on('click', '.remove-row', function() {
        if ($('.attribute-row').length > 1) {
            $(this).closest('.attribute-row').remove();
        } else {
            alert('At least one attribute row is required!');
        }
    });
    
    // Delete existing attribute via AJAX
    $(document).on('click', '.delete-attribute', function(e) {
        e.preventDefault();
        const attributeId = $(this).data('id');
        const row = $(this).closest('tr');
        
        if (confirm('Are you sure you want to delete this attribute? This action cannot be undone.')) {
            $.ajax({
                type: 'DELETE',
                url: '{{ route("vendor.products.delete-attribute", ":id") }}'.replace(':id', attributeId),
                dataType: 'json',
                beforeSend: function() {
                    row.css('opacity', '0.5');
                },
                success: function(response) {
                    row.fadeOut(300, function() {
                        $(this).remove();
                        showNotification('Attribute deleted successfully!', 'success');
                    });
                },
                error: function(xhr) {
                    row.css('opacity', '1');
                    showNotification('Error deleting attribute. Please try again.', 'error');
                }
            });
        }
    });
    
    // Update attribute status
    $(document).on('click', '.updateAttributeStatus', function(e) {
        e.preventDefault();
        const attributeId = $(this).data('attribute-id');
        const button = $(this);
        const icon = button.find('i');
        
        $.ajax({
            type: 'POST',
            url: '{{ route("vendor.products.update-attribute-status") }}',
            data: {
                attribute_id: attributeId
            },
            dataType: 'json',
            beforeSend: function() {
                button.prop('disabled', true);
            },
            success: function(response) {
                // Toggle icon and class
                if (icon.hasClass('fa-toggle-on')) {
                    icon.removeClass('fa-toggle-on text-success').addClass('fa-toggle-off text-secondary');
                    button.removeClass('btn-outline-success').addClass('btn-outline-secondary');
                } else {
                    icon.removeClass('fa-toggle-off text-secondary').addClass('fa-toggle-on text-success');
                    button.removeClass('btn-outline-secondary').addClass('btn-outline-secondary');
                }
                button.prop('disabled', false);
            },
            error: function() {
                button.prop('disabled', false);
                showNotification('Error updating attribute status', 'error');
            }
        });
    });
    
    // Form validation
    $('#productForm').submit(function(e) {
        // Check if at least one attribute is filled
        const hasAttributes = $('input[name="size[]"]').filter(function() {
            return $(this).val().trim() !== '';
        }).length > 0 || $('input[name="update_size[]"]').filter(function() {
            return $(this).val().trim() !== '';
        }).length > 0;
        
        if (!hasAttributes) {
            e.preventDefault();
            showNotification('Please add at least one product attribute (size, SKU, price).', 'error');
            return false;
        }
        
        return true;
    });
    
    // Show notification
    function showNotification(message, type = 'success') {
        // Remove existing notifications
        $('.custom-notification').remove();
        
        const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
        const icon = type === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';
        
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show custom-notification" 
                 style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="${icon} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(notification);
        
        // Auto remove after 5 seconds
        setTimeout(function() {
            notification.alert('close');
        }, 5000);
    }
    
    // Handle delete confirmation
    $(document).on('click', '.confirmDelete', function(e) {
        e.preventDefault();
        const module = $(this).data('module');
        const id = $(this).data('id');
        
        if (confirm(`Are you sure you want to delete this ${module}?`)) {
            let url = '';
            switch(module) {
                case 'product-main-image':
                    url = '{{ route("vendor.products.delete-main-image", ":id") }}'.replace(':id', id);
                    break;
                case 'product-image':
                    url = '{{ route("vendor.products.delete-image", ":id") }}'.replace(':id', id);
                    break;
                case 'product-video':
                    url = '{{ route("vendor.products.delete-video", ":id") }}'.replace(':id', id);
                    break;
            }
            
            if (url) {
                window.location.href = url;
            }
        }
    });
});

$(document).ready(function() {
    // Handle main image upload
    $('input[name="main_image"]').change(function() {
        var file = this.files[0];
        if (file) {
            var formData = new FormData();
            formData.append('main_image', file);
            formData.append('_token', '{{ csrf_token() }}');
            
            $.ajax({
                url: '{{ route("vendor.products.upload-image") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#main_image_hidden').val(response.fileName);
                    $('#main_image_preview').attr('src', '/temp/' + response.fileName);
                }
            });
        }
    });

    // Handle multiple images upload
    $('input[name="product_images[]"]').change(function() {
        var files = this.files;
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        
        for(var i = 0; i < files.length; i++) {
            formData.append('product_images[]', files[i]);
        }
        
        $.ajax({
            url: '{{ route("vendor.products.upload-images") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                var currentValue = $('#product_images_hidden').val();
                var newValue = currentValue ? currentValue + ',' + response.fileName : response.fileName;
                $('#product_images_hidden').val(newValue);
                
                // Show preview
                $('#product_images_preview').append(
                    '<div class="image-preview-item">' +
                    '<img src="/temp/' + response.fileName + '" style="max-width: 100px;">' +
                    '</div>'
                );
            }
        });
    });

    // Handle product video upload
    $('input[name="product_video"]').change(function() {
        var file = this.files[0];
        if (file) {
            var formData = new FormData();
            formData.append('product_video', file);
            formData.append('_token', '{{ csrf_token() }}');
            
            $.ajax({
                url: '{{ route("vendor.products.upload-video") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#product_video_hidden').val(response.fileName);
                }
            });
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.attribute-row {
    padding: 8px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background-color: #f8f9fa;
}

.attribute-row:hover {
    background-color: #e9ecef;
}

.sortable-wrapper {
    min-height: 100px;
    padding: 10px;
    border: 1px dashed #dee2e6;
    border-radius: 4px;
}

.sortable-item {
    cursor: move;
}

.sortable-item img {
    transition: transform 0.2s;
}

.sortable-item:hover img {
    transform: scale(1.1);
}

.form-text {
    font-size: 0.85rem;
    color: #6c757d;
}

.custom-notification {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.input-group-text {
    cursor: pointer;
}
</style>
@endpush