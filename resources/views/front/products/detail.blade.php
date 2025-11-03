@extends('front.layout.layout')
@section('content')

<style>
    .color-swatch {
        display: inline-block;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        margin-right: 6px;
        cursor: pointer;
        border: 2px solid transparent;
    }
    .color-swatch.active {
        border-color: 2px solid #000; /* Highlight active swatch */
        cursor: default;
        pointer-events: none; /* Disable click on active swatch */
    }
</style>
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
                      $fallbackImage = 'no-image.jpg';

                      if (!empty($product->main_image)) {
                       $images[] = $product->main_image;
                       }

                     if (!empty($product->product_images) && $product->product_images->count() > 0) {
                           foreach ($product->product_images as $img) {
                          $images[] = $img->image;
                         }
                     }

                    if (empty($images)) {
                    $images[] = $fallbackImage;
                    }

                    // Approuved reviews count and average
                    $approuvedReviewsQuery = $product->reviews()->where('status', 1);
                    $approuvedCount = $approuvedReviewsQuery->count();
                    $averageRating = $approuvedCount ? round($approuvedReviewsQuery->avg('rating'), 1) : 0;
                   @endphp

                   @foreach($images as $key => $img)
                    <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                     <img class="w-100 h-100 zoom-image"
                     src="{{ asset('front/images/products/' . $img) }}"
                     data-zoom-image="{{ asset('front/images/products/' . $img) }}"
                    alt="{{ $product->product_name }}">
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
               <form id="addToCart" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{$product->id}}">
                 <h3 class="font-weight-semi-bold">{{$product->product_name}}</h3>
                  <div class="d-flex mb-3 align-items-center">
                    <div class="product-stars text-primary mr-2">
                        @for($i = 1; $i <= 5; $i++)
                          @if($i <= floor($averageRating))
                            <small class="fa fa-star"></small>
                            @else(i == ceil($averageRating) && $averageRating - floor($averageRating) >= 0.5)
                               <small class="fa fa-star-half-alt"></small>
                               
                          @endif
                        @endfor
                    </div>
                    <small class="pt-1">({{$approuvedCount}} Reviews)</small>
                  </div>
                 {{-- Price block --}}
                  <h3 class="font-weight-semi-bold mb-4 getAttributePrice">
                    @if(!empty($pricing['has_discount']) && $pricing['has_discount'])
                      <span class="text-danger final-price">{!! formatCurrency($pricing['final_price']) !!} </span>
                      <del class="text-muted original-price"> {!! formatCurrency($pricing['base_price']) !!}</del>
                    @else
                      <span class="final-price">{!! formatCurrency($pricing['base_price']) !!}</span>
                    @endif
                  </h3>
                 @if(!empty($product->description))
                  <p class="mb-4">{!! $product->description !!}</p>         
                 @endif
                 @if($product->attributes->count() > 0)              
                {{-- Sizes --}}
                <div class="d-flex mb-3 align-items-center">
                    <p class="text-dark font-weight-medium mb-0 mr-3">Sizes:</p>
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
                   </div>
                    @endif
                   {{-- Colors --}}     
                    @if($product->group_products->count() > 0)
                     <div class="d-flex align-items-center mb-4">
                       <p class="text-dark font-weght-medium mb-0 mr-3">Colors</p>
                        <div class="d-flex flex-wrap">
                        @foreach($product->group_products as $gp)
                          @if($gp->id == $product->id)
                            {{-- Current product swatch (no link) --}}
                              <span class="color-swatch active"
                              style="background-color: {{strtolower($gp->family_color)}};
                              @if(strtolower($gp->family_color) == 'white') border: 1px solid #ccc; @endif"
                              title="{{ucfirst($gp->family_color)}}">
                              </span>
                              @else
                              {{-- Other product swatch (with link) --}}
                               <a href="{{ url($gp->product_url) }}"
                                 class="color-swatch"
                                 style="background-color: {{ strtolower($gp->family_color) }};
                                 @if(strtolower($gp->family_color) == 'white') border: 1px solid #ccc; @endif"
                                 title="{{ ucfirst($gp->family_color) }}">
                               </a>
                          @endif
                        @endforeach
                    </div>
                  </div>
                @endif
                @if($product->attributes->count() > 0)
                {{-- Quantity & Add to cart --}}
                  <div class="d-flex align-items-center mb-4 pt-2">
                    <div class="input-group quantity mr-3" style="width: 130px;">
                        <div class="input-group-btn">
                            <button class="btn btn-primary btn-minus" >
                            <i class="fa fa-minus"></i>
                            </button>
                        </div>
                        <input type="text" name="qty" class="form-control bg-secondary text-center" value="1">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-primary btn-plus">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary px-3">
                        <i class="fa fa-shopping-cart mr-1"></i> Add To Cart</button>
                  </div>
                @else
                  <p class="text-danger">This product is not availlable for sale</p>
                @endif
                {{-- success & error message --}}
                <div class="print-success-msg" style="display:none; font-size: 14px;"></div>
                <div class="print-error-msg" style="display:none; font-size: 14px;"></div>
                </form>
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
                    <a class="nav-item nav-link" data-toggle="tab" href="#tab-pane-3">Reviews ({{$approuvedCount}})</a>
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
                                <h4 class="mb-4">{{$approuvedCount}} Review(s) for "{{$product->product_name}}"</h4>
                                @forelse($product->reviews()->where('status', 1)->latest()->get() as $review)
                                <div class="media mb-4">
                                  @if(!empty($review->user->avatar))
                                    <img src="{{$review->user && $review->user->avatar ? 
                                    asset('storage/'.$review->user->avatar) : asset('assets/images/default-user.jpg')}}" 
                                    alt="Image" class="img-fluid mr-3 mt-1" style="width: 45px;">
                                  @endif  
                                    <div class="media-body">
                                        <h6>{{$review->user->name ?? 'Guest'}}<small> - <i>
                                          {{$review->created_at->format('d M Y')}}</i></small></h6>
                                        <div class="text-primary mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                              @if($i <= $review->rating)
                                              <i class="fas fa-star"></i>
                                              @else
                                              <i class="far fa-star"></i>
                                              @endif
                                            @endfor
                                        </div>
                                        <p>{{$review->review}}.</p>
                                    </div>
                                </div>
                                @empty
                                <p>No Review yet. Be the first to review this product!</p>
                                @endforelse
                            </div>
                            <div class="col-md-6">
                                <h4 class="mb-4">Leave a review</h4>
                                <small>Your email address will not be published. Required fields are marked *</small>
                                {{-- Flash messages --}}
                                @if(session('success_message'))
                                <div class="alert alert-success mt-3">
                                 {{session('success_message')}}
                                </div>
                                @endif
                                @if(session('error_message'))
                                <div class="alert alert-danger mt-3">
                                  {{session('error_message')}}
                                </div>
                                @endif
                                @auth
                                  @php
                                    $hasReviewed = $product->reviews()
                                                    ->where('user_id', auth()->id())
                                                    ->exists();
                                  @endphp
                                  @if($hasReviewed)
                                    <div class="alert alert-info mt-3">
                                      You have already reviewed this product. Thank you.
                                    </div>
                                    @else
                                    <div class="d-flex my-3">
                                      <p class="mb-0 mr-2">Your Rating * :</p>
                                      <div id="star-rating" class="text-primary" style="font-size: 20px; cursor: pointer;">
                                        <i class="far fa-star" data-value="1"></i>
                                        <i class="far fa-star" data-value="2"></i>
                                        <i class="far fa-star" data-value="3"></i>
                                        <i class="far fa-star" data-value="4"></i>
                                        <i class="far fa-star" data-value="5"></i>
                                    </div>
                                </div>
                                <form id="reviewForm" action="{{ route('product.review.store') }}" method="POST">
                                  @csrf
                                  <input type="hidden" name="product_id" value="{{ $product->id }}">
                                  <input type="hidden" name="rating" id="ratingInput" value="0">
                                    <div class="form-group">
                                        <label for="message">Your Review *</label>
                                        <textarea id="message" name="review" cols="30" rows="5" class="form-control" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Your Name *</label>
                                        <input type="text" class="form-control" id="name" value="{{auth()->user()->name}}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Your Email *</label>
                                        <input type="email" class="form-control" id="email" value="{{auth()->user()->email}}" readonly>
                                    </div>
                                    <div class="form-group mb-0">
                                        <input type="submit" value="Leave Your Review" class="btn btn-primary px-3">
                                    </div>
                                </form>
                                @endif
                              @else
                               <div class="alert alert-warning mt-3">Please <a href="{{url('user/login')}}">Login</a>
                                to leave a review.
                               </div>
                               @endauth
                           </div>
                        </div>
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
                <div class="row px-xl-5">
                    @foreach($product->similar_products as $similar)
                       @php
                         $fallbackImage = asset('front/images/products/no-image.jpg');
                         $image = '';
                         if(!empty($similar->main_image)){
                            $image = asset('product-image/medium/' . $similar->main_image);
                         }elseif(!empty($similat->product_images[0]['image'])){
                            $image = asset('product-image/medium/' . $similar->product_images[0]['image']);
                         }else{
                            $image = $fallbackImage;
                         }
                       @endphp
                       <div class="col-lg-4 col-md-6 col-sm-6 pb-1">
                         <div class="card product-item border-0 mb-4">
                            <!-- Product image -->
                             <div class="card-header product-img positio-relative overflow-hidden bg-transparent border p-0">
                                <a href="{{url($similar->product_url)}}">
                                  <img class="img-fluid w-100" src="{{$image}}" alt="{{$similar->product_name}}">
                                </a>
                             </div>
                             <!-- Product details -->
                              <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                                <h6 class="text-truncate mb-3">{{$similar->product_name}}</h6>
                                <div class="d-flex justify-content-center">
                                    <h6>${{$similar->final_price ?? $simolar->product_price}}</h6>
                                    @if(!empty($similar->product_discount) && $similar->product_discount > 0)
                                     <h6 class="text-muted ml-2">
                                        <del>${{$similar->product_price}}</del>
                                     </h6>
                                    @endif
                                </div>
                             </div>
                             <!-- Product Actions -->
                              <div class="card-footer d-flex justify-content-between bg-light border">
                                <a href="{{url($similar->product_url)}}" class="btn btn-sm text-dark p-0">
                                  <i class="fas fa-eye text-primary mr-1"></i>View Details
                                </a>
                                <a href="{{url($similar->product_url)}}" class="btn btn-sm text-dark p-0 addToCartBtn"
                                data-id="$similar->id">
                                  <i class="fas fa-shopping-cart text-primary mr-1"></i>Add To Cart
                                </a>
                              </div>
                          </div>
                       </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <!-- Products End -->

@endsection