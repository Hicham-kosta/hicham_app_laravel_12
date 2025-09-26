@extends('admin.layout.layout')
@section('content')
<!--begin::App Main-->
<main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Catalogue Management</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
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
              <div class="col-md-8">
                <!--begin::Quick Example-->
                <div class="card card-primary card-outline mb-4">
                  <!--begin::Header-->
                  <div class="card-header"><div class="card-title">{{$title}}</div></div>
                  <!--end::Header-->
                  @if(Session::has('error_message'))
                  <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                    <strong>Error</strong> {{Session::get('error_message')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                  @endif
                  @if (Session::has('success_message'))
                  <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    <strong>Success</strong> {{Session::get('success_message')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                  @endif
                  @foreach ($errors->all() as $error)
                  <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                    <strong>Error</strong> {!!$error!!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                  @endforeach
                  <!--begin::Form-->
                  <form name="productForm" id="productForm" action="{{isset($product) ? 
                  route('products.update', $product->id) : route('products.store')}}" 
                  method="post" enctype="multipart/form-data">@csrf
                  @if(isset($product)) @method('PUT') @endif
                    <!--begin::Body-->
                    <div class="card-body">
                      <div class="mb-3">
                        <label for="category_id">Category Level(Select Category)*</label>
                         <select name="category_id" class="form-control">
                          <option value="">Select</option>
                          <option >Main Category</option>
                            @foreach($getCategories as $cat)
                             <option value="{{$cat['id']}}" @if(old('category_id', $product->
                              category_id ?? '') == $cat['id']) selected @endif>{{$cat['name']}}</option>
                              @if(!empty($cat['subcategories']))
                                @foreach($cat['subcategories'] as $subcat)
                                  <option value="{{$subcat['id']}}" @if(old('category_id', $product->
                                    category_id ?? '') == $subcat['id']) selected @endif>
                                   &nbsp;&nbsp;&nbsp;&nbsp;&raquo;&raquo;{{$subcat['name']}}</option>
                                    @if(!empty($subcat['subcategories']))
                                      @foreach($subcat['subcategories'] as $subsubcat)
                                        <option value="{{$subsubcat['id']}}" @if(old('category_id', $product->
                                          category_id ?? '')  == $subsubcat['id']) selected @endif>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&raquo;
                                        &raquo;{{$subsubcat['name']}}</option>
                                      @endforeach
                                    @endif
                                @endforeach
                              @endif 
                            @endforeach
                         </select>  
                      </div>
                      <div class="mb-3">
                        <label for="other_categories">Show in other categories</label>
                        <select name="other_categories[]" id="other_categories" class="form-control" multiple>
                          @foreach($getCategories as $cat)
                            <option value="{{$cat['id']}}"
                            @if(!empty($product) && isset($product->otherCategories) && 
                            in_array($cat['id'], $product->otherCategories->pluck('category_id')->toArray())) 
                            selected @endif>
                            {{$cat['name']}}
                            </option>
                            @if(!empty($cat['subcategories']))
                              @foreach($cat['subcategories'] as $subcat)
                                <option value="{{$subcat['id']}}"
                                @if(!empty($product) && isset($product->otherCategories) && 
                            in_array($subcat['id'], $product->otherCategories->pluck('category_id')->toArray())) 
                            selected @endif>
                            &nbsp;&nbsp;&raquo;{{$subcat['name']}}
                            </option>
                              @endforeach
                            @endif
                          @endforeach
                        </select>
                        <small class="form-text text-muted">Hold Ctrl (Windows) or Command (Mac) to select multiple options.</small>
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="brand_id">Brand*</label>
                        <select name="brand_id" class="form-control">
                          <option value="">Select</option>
                          @foreach($brands as $brand)
                            <option value="{{$brand['id']}}" 
                            {{old('brand_id', $product['brand_id'] ?? '') == $brand['id']? 'selected': ''}}>
                              {{$brand['name']}}
                            </option>
                          @endforeach
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="product_name">Product Name*</label>
                        <input type="text" class="form-control" id="product_name" name="product_name"
                        placeholder="Enter product Name" value="{{old('product_name', $product->product_name ?? '')}}" >
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="product_code">Product Code*</label>
                        <input type="text" class="form-control" id="product_code" name="product_code"
                        placeholder="Enter product Code" value="{{old('product_code', $product->product_code ?? '')}}" >
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="product_color">Product Color</label>
                        <input type="text" class="form-control" id="product_color" name="product_color"
                        placeholder="Enter product color" value="{{old('product_color', $product->product_color ?? '')}}" >
                      </div>
                      <?php $familyColors = \App\Models\Color::colors(); ?>
                      <div class="mb-3">
                        <label class = "form-label" for="family_color" >Family Color</label>
                        <select class="form-control" name="family_color">
                          <option value="">Select Family Color</option>
                          @foreach($familyColors as $color)
                            <option value="{{$color->name}}" @if(isset($product['family_color']) && 
                          $product['family_color'] == $color->name) selected @endif>
                        {{$color->name}}
                            </option>
                          @endforeach
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="group_code">Group Code</label>
                        <input type="text" class="form-control" id="group_code" name="group_code"
                        placeholder="Enter Group Code" value="{{old('group_code', $product->group_code ?? '')}}" >
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="product_price">Product Price</label>
                        <input type="text" class="form-control" id="product_price" name="product_price"
                        placeholder="Enter Product Price" value="{{old('product_price', $product->product_price ?? '')}}" >
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="product_discount">Product Discount(%)</label>
                        <input type="number" step="0.01" class="form-control" name="product_discount" value ="{{old('product_discount', $product->product_discount ?? '')}}">
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="product_gst">Product GST</label>
                        <input type="number" step="0.01" class="form-control" name="product_gst"
                        value ="{{old('product_gst', $product->product_gst ?? '')}}">
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="product_weight">Product Weight(g)</label>
                        <input type="number" step="0.01" class="form-control" name="product_weight"
                        value ="{{old('product_weight', $product->product_weight ?? '')}}">
                      </div>
                      <div class="mb-3">
                        <label class="form-label mb-1">Product Attributes</label>
                        {{--header row--}}
                        <div class="d-none d-md-flex fw-semibold bg-light border rounded px-2 py-1 mb-2">
                          <div class="flex-fill col-2">Size</div>
                          <div class="flex-fill col-2 ms-4">SKU</div>
                          <div class="flex-fill col-2 ms-4">Price</div>
                          <div class="flex-fill col-2 ms-4">Stock</div>
                          <div class="flex-fill col-2 ms-4">Sort</div>
                          <div style="width:60px"></div>
                        </div>
                        {{--dynamic rows--}}
                        <div class="field_wrapper">
                          {{--first row--}}
                          <div class="d-flex align-items-center gap-2 mb-2 attribute-row">
                            <input name="size[]" class="form-control flex-fill col-2" placeholder="Size">
                            <input name="sku[]" class="form-control flex-fill col-2" placeholder="SKU">
                            <input name="price[]" class="form-control flex-fill col-2" placeholder="Price">
                            <input name="stock[]" class="form-control flex-fill col-2" placeholder="Stock">
                            <input name="sort[]" class="form-control flex-fill col-2" placeholder="Sort">
                            <a href="javascript:void(0);" class="btn btn-sm btn-success add_button" title="Add Row">
                              <i class="fas fa-plus"></i>
                            </a>
                          </div>
                        </div> 
                      </div>
                      @if(isset($product->attributes) && $product->attributes->count() > 0)
                      <div class="mb-3">
                        <label class="form-label mb-1">Existing Product Attributes</label>
                        <div class="table-responsive">
                          <table class="table table-bordered align-middle mb-0" >
                            <thead class="table-light text-center">
                              <tr>
                                <th style="width:15%">Size</th>
                                <th style="width:20%">SKU</th>
                                <th style="width:15%">Price</th>
                                <th style="width:15%">Stock</th>
                                <th style="width:15%">Sort</th>
                                <th style="width:15%">Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              
                              @foreach($product['attributes'] as $attribute)
                              <input type="hidden" name="attrId[]" value="{{$attribute->id}}">
                              <tr class="text-center">
                                <td>{{$attribute['size']}}</td>
                                <td>{{$attribute['sku']}}</td>
                                <td>
                                <input type="number" name="update price[]" value="{{$attribute['price']}}" class="form-control" required>
                                </td>
                                <td>
                                  <input type="number" name="update stock[]" value="{{$attribute['stock']}}" class="form-control" required>
                                </td>
                                <td>
                                  <input type="number" name="update sort[]" value="{{$attribute['sort']}}" class="form-control" required>
                                </td>
                                <td>
                                  @if($attribute['status'] == 1)
                                  <a class="updateAttributeStatus" 
                                  data-attribute-id="{{$attribute->id}}" 
                                  style='color:#3f6ed3' href="javascript:void
                                  (0)"><i class="fas fa-toggle-on"
                                  data-status="Active"></i></a>&nbsp;&nbsp;
                                  @else
                                  <a class="updateAttributeStatus" 
                                  data-attribute-id="{{$attribute->id}}" 
                                  style='color:grey' href="javascript:void(0)
                                  "><i class="fas fa-toggle-off" data-status=
                                  "Inactive"></i>
                                  </a>&nbsp;&nbsp;
                                  @endif
                                  <a title="Delete Attribute" href="
                                  javascript:void(0)" class="confirmDelete 
                                  text-danger" data-module="product-attribute" 
                                  data-id="{{$attribute['id']}}"><i class="
                                  fas fa-trash"></i>
                                  </a>
                                </td>
                             </tr>
                             @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                      @endif
                      <div class="mb-3">
                        <!-- Product Main Image Upload Field-->
                        <label class="form-label" for="main_image_dropzone">Product Main Image (Max 500KB)</label>
                        <div class="dropzone" id="mainImageDropzone"></div>
                        <br />
                        <!-- Hidden Input to send uploaded image --> 
                        <input type="hidden" name="main_image" id="main_image_hidden">
                        @if(!empty($product['main_image']))
                        <a target="_blank" href="{{url('front/images/products/'.$product['main_image'])}}">
                          <img style="width:50px" margin="10px" src="{{url('product-image/thumbnail/'.$product->main_image)}}">
                        </a>&nbsp;&nbsp;<a style='color:#3f6ed3;' class="confirmDelete"
                          title="Delete Product Image" href="javascript:void(0)" data-module="product-main-image"
                          data-id="{{$product['id']}}"><i class="fas fa-trash"></i></a>
                        @endif                      
                      </div>
                      <div id="mainImageDropzoneError" style="color:red; display:none;"></div>

                      <!-- Product Additional Images Upload Field -->
                      <div class="mb-3">
                        <label class="form-label" for="product_images_dropzone">Alternate Product Images (Max 500KB each)</label>
                        <div class="dropzone" id="productImagesDropzone"></div>
                          @if(isset($product->product_images) && $product->product_images->count() > 0)
                            @if($product->product_images->count() > 1)
                             <!-- Instuction line -->
                              <p class="drag-instruction">
                                <i class="fas fa-arrows-alt"></i> Drag and drop to reorder images
                              </p>
                            @endif
                          <!-- Container for sortable images -->
                           <div id="sortable-images" class="sortable-wrapper d-flex gap-2 overflow-auto">
                            @foreach($product->product_images as $img)
                             <div class="sortable-item" data-id="{{$img->id}}">
                              <a traget="_blank" href="{{url('front/images/products/'
                              .$img->image)}}"><img src="{{url('product-image/thumbnail/'.$img->image)}}" 
                              style="width:50px;"></a>&nbsp;&nbsp;<a href="javascript:void(0)" 
                              class="confirmDelete" data-module="product-image"
                              data-id="{{$img->id}}" data-image="{{$img->image}}"><i class="fas fa-trash"></i>
                              </a>
                             </div>
                            @endforeach
                           </div>  
                          @endif
                          <!-- Hidden Input to send uploaded images -->
                          <input type="hidden" name="product_images" id="product_images_hidden">
                      </div>


                      <!-- Product Video Upload Field -->
                      <div class="mb-3">
                        <label class="form-label" for="product_video_dropzone">Product Video (Max 2MB)</label>
                        <div class="dropzone" id="productVideoDropzone"></div>
                        <!-- Hidden Input to send uploaded video -->
                        <input type="hidden" name="product_video" id="product_video_hidden">
                        @if(!empty($product['product_video']))
                        <a target="_blank" href="{{url('front/videos/products/'.
                        $product['product_video'])}}">View Video</a>
                        <a href="javascript:void(0)" class="confirmDelete" data-module="product-video"
                        data-id="{{$product['id']}}">Delete Video</a>
                        @endif
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="wash_care">Wash Care</label>
                        <textarea class="form-control" name="wash_care">
                        {{old('wash_care', $product->wash_care ?? '')}}</textarea>
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="product_description">Product Description</label>
                        <textarea class="form-control" name="product_description" id="product_description" 
                        rows="3" placeholder="Enter Product Description">
                          {{old('product_description', $product->product_description ?? '')}}</textarea>
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="search_keywords">Search Keywords</label>
                        <textarea class="form-control" name="search_keywords" >
                          {{old('search_keywords', $product->search_keywords ?? '')}}</textarea>
                      </div>
                      @php
                        $filters = \App\Models\Filter::with(['values' => function($q) {
                          $q->where('status', 1)->orderBy('sort', 'asc');
                        }])->where('status', 1)->orderBy('sort', 'asc')->get();

                        $selectedValues = isset($product) ? $product->filterValues->pluck('id')->toArray() : [];
                      @endphp
                      @foreach($filters as $filter)
                        <div class="mb-3">
                          <label>{{ucwords($filter->filter_name)}}</label>
                          <select name="filter_values[{{$filter->id}}]" class="form-control">
                              <option value="">--Select {{ucwords($filter->filter_name)}}--</option>
                              @foreach($filter->values as $value)
                               <option value="{{$value->id}}"
                                {{ in_array($value->id, $selectedValues) ? 'selected' : '' }}>
                                {{ucfirst($value->value)}}
                                </option>
                            @endforeach
                          </select>
                        </div>
                      @endforeach
                      <div class="mb-3">
                        <label class="form-label" for="meta_title">Meta Title</label>
                        <input type="text" class="form-control" name="meta_title"
                        value="{{old('meta_title', $product->meta_title ?? '')}}" >
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="meta_description">Meta Description</label>
                        <input type="text" class="form-control" name="meta_description"
                        value="{{old('meta_description', $product->meta_description ?? '')}}" >
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="meta_keyword">Meta Keywords</label>
                        <input type="text" class="form-control" name="meta_keyword"
                        value="{{old('meta_keyword', $product->meta_keyword ?? '')}}" >
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="meta_keyword">Sort</label>
                        <input type="number" class="form-control" name="sort"
                        value="{{old('sort', $product->sort ?? '')}}" >
                      </div>
                      <div class="mb-3">
                        <label class="form-label" for="is_featured">Featured?</label>
                        <select name="is_featured" class="form-select">
                          <option value="No" {{old('is_featured', $product->is_featured ?? '') == 'No' ? 'selected' : ''}}>No</option>
                          <option value="Yes" {{old('is_featured', $product->is_featured ?? '') == 'Yes' ? 'selected' : ''}}>Yes</option>
                        </select>
                      </div>   
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    <div class="card-footer">
                      <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                    <!--end::Footer-->
                  </form>
                  <!--end::Form-->
                </div>
                <!--end::Quick Example-->
                
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
</main>
      <!--end::App Main-->
@endsection