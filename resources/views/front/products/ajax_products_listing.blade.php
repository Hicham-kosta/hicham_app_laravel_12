<!-- Shop Sidebar Start -->
    @include('front.products.filters')
 <!-- Shop Sidebar End -->

 <!-- Shop Product Start -->
            <div class="col-lg-9 col-md-12">
              <div class="row pb-3">
                 <div class="col-12 pb-1">
                    <div class="mb-3">
                         {!! $breadcrumbs ?? '' !!}
                        <div class="small text-muted">
                            (FOUND {{count($categoryProducts)}} PRODUCTS)
                        </div>    
                    </div>
                 </div>
            <div class="row pb-3">
                    <div class="col-12 pb-1">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <form action="">
                                <!-- <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search by name">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-transparent text-primary">
                                            <i class="fa fa-search"></i>
                                        </span>
                                    </div>
                                </div> -->
                            </form>
                            <form name="sortProducts" id="sortProducts">
                                <input type="hidden" name="url" id="url" value="{{$url}}">
                                <select class="form-control getsort" name="sort" id="sort">
                                    <option value="">Sort By</option>
                                    <option value="product_latest" @if(request()->get('sort') == 'lproduct_latest') selected 
                                        @endif>Sort By: Latest Items</option>
                                    <option value="lowest_price" @if(request()->get('sort') == 'lowest_price') selected 
                                        @endif>Sort By: Lowest Price</option>
                                    <option value="highest_price" @if(request()->get('sort') == 'highest_price') selected 
                                        @endif>Sort By: Highest Price</option>
                                    <option value="best_selling" @if(request()->get('sort') == 'best_selling') selected 
                                        @endif>Sort By: Best Selling</option>
                                    <option value="featured_items" @if(request()->get('sort') == 'featured_items') selected 
                                        @endif>Sort By: Featured Items</option>
                                    <option value="discounted_items" @if(request()->get('sort') == 'discounted_items') selected 
                                        @endif>Sort By: Discounted Items</option>
                                </select>                       
                            </form>
                        </div>
                    </div>
                    @foreach($categoryProducts as $product)
                     @php
                       $fallbackImage = asset('front/images/products/no-image.jpg');
                       $image = '';
                       if(!empty($product['main_image'])){
                            $image = asset('product-image/medium/' . $product['main_image']);
                        } elseif (!empty($product['product_images'][0]['image'])) {
                            $image = asset('product-image/medium/'. $product['product_images'][0]['image']);
                        } else {
                            $image = $fallbackImage;
                        }
                     @endphp
                      <div class="col-lg-4 col-md-6 col-sm-12 pb-1">
                        <div class="card product-item border-0 mb-4">
                            <div class="card-header product-img position-relative overflow-hidden 
                            bg-transparent border p-0">
                                <a href="#"><img class="img-fluid w-100" src="{{$image}}" alt="{{
                                $product['product_name']}}"></a>
                            </div>
                            <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                                <h6 class="text-truncate mb-3">{{$product['product_name']}}</h6>
                                <div class="d-flex justify-content-center">
                                    <h6>₹{{$product['final_price']}}</h6>
                                    @if($product['product_discount'] > 0)
                                       <h6 class="text-muted ml-2"><del>₹{{$product['product_price']}}</del></h6>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-between bg-light border">
                                <a href="#" class="btn btn-sm text-dark p-0">
                                    <i class="fas fa-eye text-primary mr-1"></i>View Detail</a>
                                <a href="javascript:void(0)" class="btn btn-sm text-dark p-0 addToCartBtn" data-id="{{$product['id']}}">
                                    <i class="fas fa-shopping-cart text-primary mr-1"></i>Add To Cart
                                </a>
                            </div>
                        </div>
                      </div>
                    @endforeach
                    <div class="col-12 pb-1">
                        @php
                          if(!isset($_GET['price'])){$_GET['price'] = '';}
                        @endphp
                        {{ $categoryProducts->appends([
                            'sort' => $_GET['sort'] ?? '',
                            'color' => $_GET['color'] ?? '',
                            'size' => $_GET['size'] ?? '',
                            'brand' => $_GET['brand'] ?? '',
                            'price' => $_GET['price'] ?? ''
                            ])->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        
            
            <!-- Shop Product End -->