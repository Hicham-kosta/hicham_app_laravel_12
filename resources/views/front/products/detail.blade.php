@extends('front.layout.layout')
@section('content')
<!-- Page Header Start -->
    <div class="container-fluid bg-secondary mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 150px">
            <h1 class="font-weight-semi-bold text-uppercase mb-3">Product Name</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{url('/')}}">Home</a></p>
                  @if($product->category)
                   {{-- if parent category exist --}}
                    @if($product->category->parentcategory)
                      <p class="m-0 px-2">-</p>
                      <p class="m-0">
                        <a href="{{url(url($product->category->parentcategory->url))}}">
                            {{$product->category->parentcategory->name}}
                        </a>
                      </p>
                    @endif
                    {{-- Current Category --}}
                     <p class="m-0 px-2">-</p>
                       <p class="m-0">
                        <a href="{{url(url($product->category->url))}}">
                            {{$product->category->name}}
                        </a>
                       </p>
                  @endif
                  {{-- Product --}}
                  <p class="m-0 px-2">-</p>
                    <p class="m-0">
                      {{$product->product_name}}
                    </p>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Shop Detail Start -->
    <div class="container-fluid py-5">
        <div class="row px-xl-5">
            <div class="col-lg-5 pb-5">
                <div id="product-carousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner border">
                        @php
                          $images = [];
                          if($product->main_image){
                            $images[] = $product->main_image;
                          }
                          foreach($product->product_images as $img){
                            $images[] = $img->image;
                          }
                        @endphp
                        @foreach($images as $key => $img)
                        <div class="carousel-item {{$key === 0 ? 'active' : ''}}">
                            <img class="w-100 h-100 zoom-image" 
                            src="{{asset('front/images/products/'.$img)}}"
                            data-zoom-image="{{asset('front/images/products/'.$img)}}" 
                            alt="{{$product->product_name}}">
                        </div>
                        @endforeach
                        
                        @if(!empty($product->product_video))
                         <div class="carousel-item">
                           <video class="w-100 h-100" controls>
                            <source src="{{ asset('front/videos/products/' . $product->product_video) }}" type="video/mp4">
                           </video>
                        </div>
                       @endif
                    </div>
                     <a class="carousel-control-prev" href="#product-carousel" data-slide="prev">
                        <i class="fa fa-2x fa-angle-left text-dark"></i>
                    </a>
                    <a class="carousel-control-next" href="#product-carousel" data-slide="next">
                        <i class="fa fa-2x fa-angle-right text-dark"></i>
                    </a>
              </div>
            </div>
            <div class="col-lg-7 pb-5">
                <h3 class="font-weight-semi-bold">{{$product->product_name}}</h3>
                <div class="d-flex mb-3">
                    <div class="text-primary mr-2">
                        <small class="fas fa-star"></small>
                        <small class="fas fa-star"></small>
                        <small class="fas fa-star"></small>
                        <small class="fas fa-star-half-alt"></small>
                        <small class="far fa-star"></small>
                    </div>
                    <small class="pt-1">(10 Reviews)</small>
                </div>
                 {{-- Price block --}}
                  <h3 class="font-weight-semi-bold mb-4 getAttributePrice">
                    @if($pricing['has_discount'])
                      <span class="text-danger final-price">${{$pricing['final_price']}} </span>
                      <del class="text-muted original-price"> ${{$pricing['base_price']}}</del>
                    @else
                      <span class="final-price">${{$pricing['base_price']}}</span>
                    @endif
                  </h3>
                @if(!empty($product->description))
                  <p class="mb-4"><?php echo $product->description; ?></p>          
                 @endif                
                {{-- Sizes (keep AJAX working + preselect + preselect first attribute) --}}
                <div class="d-flex mb-3 align-items-center">
                    <p class="text-dark font-weight-medium mb-0 mr-3">Sizes:</p>
                    <form>
                        @foreach($product->attributes as $loopAttr)
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio"
                            class="custom-control-input getPrice"
                            id="size-{{$loopAttr->id}}"
                            name="size"
                            value="{{$loopAttr->size}}"
                            data-product-id="{{$product->id}}" 
                            {{$pricing['preselected_size'] === $loopAttr->size ? 'checked' : ($loopAttr->first ? 'checked' : '')}}>
                            <label class="custom-control-label" for="size-{{$loopAttr->id}}">{{$loopAttr->size}}
                            </label>
                        </div>
                        @endforeach
                    </form>
                </div>       
                <div class="d-flex align-items-center mb-4">
                    <p class="text-dark font-weight-medium mb-0 mr-3">Colors:</p>
                    <div class="d-flex flex-wrap">
                        <a href="product-black.html" class="color-swatch" style="background-color: black;" title="Black"></a>
                        <a href="product-white.html" class="color-swatch" style="background-color: white; border: 1px solid #ccc;" title="White"></a>
                        <a href="product-red.html" class="color-swatch" style="background-color: red;" title="Red"></a>
                        <a href="product-blue.html" class="color-swatch" style="background-color: blue;" title="Blue"></a>
                        <a href="product-green.html" class="color-swatch" style="background-color: green;" title="Green"></a>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-4 pt-2">
                    <div class="input-group quantity mr-3" style="width: 130px;">
                        <div class="input-group-btn">
                            <button class="btn btn-primary btn-minus" >
                            <i class="fa fa-minus"></i>
                            </button>
                        </div>
                        <input type="text" class="form-control bg-secondary text-center" value="1">
                        <div class="input-group-btn">
                            <button class="btn btn-primary btn-plus">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <button class="btn btn-primary px-3"><i class="fa fa-shopping-cart mr-1"></i> Add To Cart</button>
                </div>
                <div class="d-flex pt-2">
                    <p class="text-dark font-weight-medium mb-0 mr-2">Share on:</p>
                    <div class="d-inline-flex">
                        <a class="text-dark px-2" href="">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a class="text-dark px-2" href="">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a class="text-dark px-2" href="">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a class="text-dark px-2" href="">
                            <i class="fab fa-pinterest"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row px-xl-5">
            <div class="col">
                <div class="nav nav-tabs justify-content-center border-secondary mb-4">
                    <a class="nav-item nav-link active" data-toggle="tab" href="#tab-pane-1">Description</a>
                    <a class="nav-item nav-link" data-toggle="tab" href="#tab-pane-2">Wash Care</a>
                    <a class="nav-item nav-link" data-toggle="tab" href="#tab-pane-3">Reviews (0)</a>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-pane-1">
                        
                        <p><?php echo $product->description; ?></p>
                    </div>
                    <div class="tab-pane fade" id="tab-pane-2">
                        
                        <p><?php echo $product->wash_care; ?></p>
                    </div>
                    <div class="tab-pane fade" id="tab-pane-3">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-4">1 review for "Product Name"</h4>
                                <div class="media mb-4">
                                    <img src="img/user.jpg" alt="Image" class="img-fluid mr-3 mt-1" style="width: 45px;">
                                    <div class="media-body">
                                        <h6>Amit Gupta<small> - <i>01 July 2025</i></small></h6>
                                        <div class="text-primary mb-2">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4 class="mb-4">Leave a review</h4>
                                <small>Your email address will not be published. Required fields are marked *</small>
                                <div class="d-flex my-3">
                                    <p class="mb-0 mr-2">Your Rating * :</p>
                                    <div class="text-primary">
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                                <form>
                                    <div class="form-group">
                                        <label for="message">Your Review *</label>
                                        <textarea id="message" cols="30" rows="5" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Your Name *</label>
                                        <input type="text" class="form-control" id="name">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Your Email *</label>
                                        <input type="email" class="form-control" id="email">
                                    </div>
                                    <div class="form-group mb-0">
                                        <input type="submit" value="Leave Your Review" class="btn btn-primary px-3">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Shop Detail End -->

    <!-- Products Start -->
    <div class="container-fluid py-5">
        <div class="text-center mb-4">
            <h2 class="section-title px-5"><span class="px-2">You May Also Like</span></h2>
        </div>
        <div class="row px-xl-5">
            <div class="col">
                <div class="owl-carousel related-carousel">
                    <div class="card product-item border-0">
                        <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
                            <img class="img-fluid w-100" src="{{asset('front/images/sitemakers.png')}}" alt="">
                        </div>
                        <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                            <h6 class="text-truncate mb-3">Product Name</h6>
                            <div class="d-flex justify-content-center">
                                <h6>₹1000</h6><h6 class="text-muted ml-2"><del>₹1500</del></h6>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between bg-light border">
                            <a href="" class="btn btn-sm text-dark p-0"><i class="fas fa-eye text-primary mr-1"></i>View Detail</a>
                            <a href="" class="btn btn-sm text-dark p-0"><i class="fas fa-shopping-cart text-primary mr-1"></i>Add To Cart</a>
                        </div>
                    </div>
                    <div class="card product-item border-0">
                        <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
                            <img class="img-fluid w-100" src="{{asset('front/images/sitemakers.png')}}" alt="">
                        </div>
                        <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                            <h6 class="text-truncate mb-3">Product Name</h6>
                            <div class="d-flex justify-content-center">
                                <h6>₹1000</h6><h6 class="text-muted ml-2"><del>₹1500</del></h6>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between bg-light border">
                            <a href="" class="btn btn-sm text-dark p-0"><i class="fas fa-eye text-primary mr-1"></i>View Detail</a>
                            <a href="" class="btn btn-sm text-dark p-0"><i class="fas fa-shopping-cart text-primary mr-1"></i>Add To Cart</a>
                        </div>
                    </div>
                    <div class="card product-item border-0">
                        <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
                            <img class="img-fluid w-100" src="{{asset('front/images/sitemakers.png')}}" alt="">
                        </div>
                        <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                            <h6 class="text-truncate mb-3">Product Name</h6>
                            <div class="d-flex justify-content-center">
                                <h6>₹1000</h6><h6 class="text-muted ml-2"><del>₹1500</del></h6>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between bg-light border">
                            <a href="" class="btn btn-sm text-dark p-0"><i class="fas fa-eye text-primary mr-1"></i>View Detail</a>
                            <a href="" class="btn btn-sm text-dark p-0"><i class="fas fa-shopping-cart text-primary mr-1"></i>Add To Cart</a>
                        </div>
                    </div>
                    <div class="card product-item border-0">
                        <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
                            <img class="img-fluid w-100" src="{{asset('front/images/sitemakers.png')}}" alt="">
                        </div>
                        <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                            <h6 class="text-truncate mb-3">Product Name</h6>
                            <div class="d-flex justify-content-center">
                                <h6>₹1000</h6><h6 class="text-muted ml-2"><del>₹1500</del></h6>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between bg-light border">
                            <a href="" class="btn btn-sm text-dark p-0"><i class="fas fa-eye text-primary mr-1"></i>View Detail</a>
                            <a href="" class="btn btn-sm text-dark p-0"><i class="fas fa-shopping-cart text-primary mr-1"></i>Add To Cart</a>
                        </div>
                    </div>
                    <div class="card product-item border-0">
                        <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
                            <img class="img-fluid w-100" src="{{asset('front/images/sitemakers.png')}}" alt="">
                        </div>
                        <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                            <h6 class="text-truncate mb-3">Product Name</h6>
                            <div class="d-flex justify-content-center">
                                <h6>₹1000</h6><h6 class="text-muted ml-2"><del>₹1500</del></h6>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between bg-light border">
                            <a href="" class="btn btn-sm text-dark p-0"><i class="fas fa-eye text-primary mr-1"></i>View Detail</a>
                            <a href="" class="btn btn-sm text-dark p-0"><i class="fas fa-shopping-cart text-primary mr-1"></i>Add To Cart</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Products End -->

@endsection