<?php
use App\Models\Category;
// Get Categories their Subcategories
$categories = Category::getCategories('Front');
/*echo '<pre>'; print_r($categories); die;*/
$totalCartItems = totalCartItems();
?>
<!-- Topbar Start -->
    <div class="container-fluid">
        <div class="row bg-secondary py-2 px-xl-5">
            <div class="col-lg-6 d-none d-lg-block">
                <div class="d-inline-flex align-items-center">
                    <a class="text-dark" href="#">FAQs</a>
                    <span class="text-muted px-2">|</span>
                    <a class="text-dark" href="#">Help</a>
                    <span class="text-muted px-2">|</span>
                    <a class="text-dark" href="#">Support</a>
                </div>
            </div>
            <div class="col-lg-6 text-center text-lg-right">
                <div class="d-inline-flex align-items-center">
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
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a class="text-dark pl-2" href="">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
        </div>
        </div>
        <div class="row align-items-center py-3 px-xl-5">
            <div class="col-lg-3 d-none d-lg-block">
                <a href="" class="text-decoration-none">
                    <h1 class="m-0 display-5 font-weight-semi-bold"><span class="text-primary font-weight-bold border px-1 mr-0">S</span>ite&nbsp;<span class="text-primary font-weight-bold border px-1 mr-0">M</span>akers</h1>
                </a>
            </div>
            <div class="col-lg-6 col-6 text-left">
                <!-- Search Form with live search container -->
                 <div class="search-wrapper" style="position: relative; width: 100%;">
                  <form action="javascript:void(0);">
                    <div class="input-group">
                        <input type="text" 
                        class="form-control"
                        id="search_input"
                        name="q"
                        placeholder="Search for products">
                        <div class="input-group-append">
                            <span class="input-group-text bg-transparent text-primary">
                                <i class="fa fa-search"></i>
                            </span>
                        </div>
                    </div>
                </form>
                <!-- live search results -->
                <div id="search_result" 
                style="position: absolute; top:100%; left:0; right:0; background: #fff; border: 
                1px solid #ddd; border-top: none; z-index: 999;">
                </div>
            </div>
          </div> 
            <div class="col-lg-3 col-6 text-right">
                <a href="" class="btn border">
                    <i class="fas fa-heart text-primary"></i>
                    <span class="badge">0</span>
                </a>
                <a href="{{route('cart.index')}}" class="btn border">
                    <i class="fas fa-shopping-cart text-primary"></i>
                    <span class="badge totalCartItems">{{$totalCartItems}}</span>
                </a>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <div class="container-fluid mb-2">
        <div class="row border-top px-xl-5">
            <!-- Left: Categories -->
            <div class="col-lg-3 d-none d-lg-block">
                <a class="btn shadow-none d-flex align-items-center justify-content-between bg-primary text-white w-100"
                   data-toggle="collapse" href="#navbar-vertical"
                   style="height: 65px; margin-top: -1px; padding: 0 30px;">
                    <h6 class="m-0">Categories</h6>
                    <i class="fa fa-angle-down text-dark"></i>
                </a>
                <nav class="collapse position-absolute navbar navbar-vertical navbar-light align-items-start p-0 border border-top-0 border-bottom-0 bg-light"
                     id="navbar-vertical" style="width: calc(100% - 30px); z-index: 9;">
                    <div class="navbar-nav w-100">
                        @foreach($categories as $category)
                          @if($category['menu_status'] == 1)
                            @if(count($category['subcategories']) > 0)
                          <div class="nav-item dropdown">
                            <a href="{{url($category['url'])}}" class="nav-link" data-toggle="dropdown">
                                {{$category['name']}} <i class="fa fa-angle-down float-right mt-1"></i>
                            </a>
                            <div class="dropdown-menu position-absolute bg-secondary border-0 rounded-0 w-100 m-0">
                                @foreach($category['subcategories'] as $subcategory)
                                  @if($subcategory['menu_status'] == 1)
                                  <a href="{{url($subcategory['url'])}}" class="dropdown-item">
                                    {{$subcategory['name']}}</a>
                                  @endif
                                @endforeach
                              </div>
                            </div>
                           @else
                            <a href="{{url($category['url'])}}" class="nav-item nav-link">
                                {{$category['name']}}</a>
                           @endif
                         @endif
                        @endforeach
                    </div>
                </nav>
            </div>

            <!-- Right: Main Navbar -->
            <div class="col-lg-9">
                <nav class="navbar navbar-expand-lg bg-light navbar-light py-3 py-lg-0 px-0">
                    <a href="" class="text-decoration-none d-block d-lg-none">
                        <h1 class="m-0 display-5 font-weight-semi-bold">
                            <span class="text-primary font-weight-bold border px-1 mr-1">S</span>ite&nbsp;
                            <span class="text-primary font-weight-bold border px-1 mr-1">M</span>akers
                        </h1>
                    </a>
                    <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                        <div class="navbar-nav mr-auto py-0">
                            <a href="{{url('/')}}" class="nav-item nav-link">Home</a>
                            @foreach($categories as $category)
                              @if($category['menu_status'] == 1)
                                 @if(count($category['subcategories']) > 0)
                                   <div class="nav-item dropdown">
                                    <a href="{{url($category['url'])}}" class="nav-link dropdown-toggle" data-toggle="dropdown">
                                        {{$category['name']}}
                                    </a>
                                    <div class="dropdown-menu rounded-0 m-0">
                                        @foreach($category['subcategories'] as $subcategory)
                                          @if($subcategory['menu_status'] == 1)
                                            @if(count($subcategory['subcategories']) > 0)
                                               <div class="dropdown-submenu">
                                                 <a href="{{url($subcategory['url'])}}" class="dropdown-item">
                                                   {{$subcategory['name']}}
                                                   
                                                </a>
                                                <div class="dropdown-menu">
                                                  @foreach($subcategory['subcategories'] as $subsubcategory)
                                                    @if($subsubcategory['menu_status'] == 1)
                                                      <a href="{{url($subsubcategory['url'])}}" class="dropdown-item">
                                                        {{$subsubcategory['name']}}
                                                      </a>
                                                    @endif
                                                  @endforeach
                                               </div>
                                             </div>
                                            @else
                                              <a href="{{url($subcategory['url'])}}" class="dropdown-item">
                                                {{$subcategory['name']}}
                                              </a>
                                            @endif
                                          @endif
                                        @endforeach
                                    </div>    
                                   </div>
                                    @else
                                    <a href="{{url($category['url'])}}" class="nav-item nav-link">
                                        {{$category['name']}}
                                 @endif
                              @endif
                            @endforeach
                            <a href="contact.html" class="nav-item nav-link">Contact</a>
                        </div>
                        <div class="navbar-nav ml-auto py-0">
                        @auth
                         <div class="nav-item dropdown">
                            
                          <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                          <i class="fa fa-user text-primary mr-1"></i>
                          <span class="text-dark mr-3">
                            👋 Welcome, <br><strong>{{ Auth::user()->name }}</strong>
                           </span>
                          </a>
                        <div class="dropdown-menu dropdown-menu-right rounded-0 m-0">
                          <a href="{{ url('/') }}" class="dropdown-item">Home</a>
                          <a href="#" class="dropdown-item disabled">
                            Role: {{ ucfirst(Auth::user()->user_type) }}
                           </a>
                          <form action="{{ route('user.logout') }}" method="POST" class="m-0">
                          @csrf
                            <button type="submit" class="dropdown-item text-danger">
                             Logout
                            </button>
                          </form>
                        </div>
                     </div>
                     @else
                     <a href="{{ url('user/login') }}" class="nav-item nav-link">Login</a>
                     <a href="{{ url('user/register') }}" class="nav-item nav-link">Register</a>
                    @endauth
                   </div>
                  </div>
                </nav>
            </div>
        </div>
    </div>
    <!-- Navbar End -->