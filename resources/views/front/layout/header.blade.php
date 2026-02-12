<?php
use App\Models\Category;
// Get Categories their Subcategories
$categories = Category::getCategories('Front');
/*echo '<pre>'; print_r($categories); die;*/
$totalCartItems = totalCartItems();
?>
<style>
/* ===== HEADER & NAVIGATION STYLES ===== */

/* Topbar General Styles */
.bg-secondary {
    background-color: #f8f9fa !important;
}

.bg-secondary .text-dark:hover {
    color: #007bff !important;
    text-decoration: none;
}

/* Currency Dropdown Styling */
.currency-dropdown {
    position: relative;
    display: inline-block;
    margin-left: 8px;
}

#current-currency-btn {
    border: 1px solid #dee2e6;
    padding: 4px 12px;
    font-size: 14px;
    border-radius: 4px;
    transition: all 0.3s ease;
    background: white;
}

#current-currency-btn:hover {
    border-color: #007bff;
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.1);
}

#currency-list {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    min-width: 180px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    margin-top: 4px;
    padding: 8px 0;
}

.currency-dropdown:hover #currency-list {
    display: block;
}

.currency-item {
    display: flex;
    align-items: center;
    padding: 8px 16px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.currency-item:hover {
    background-color: #f8f9fa;
}

.currency-item img {
    margin-right: 10px;
    width: 20px;
    height: 15px;
    object-fit: cover;
}

/* Social Icons */
.d-inline-flex a {
    transition: all 0.3s ease;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.d-inline-flex a:hover {
    background-color: #007bff;
    color: white !important;
    transform: translateY(-2px);
}

/* Logo Styling */
.text-decoration-none h1 {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    letter-spacing: -0.5px;
}

.text-primary {
    color: #007bff !important;
}

.border {
    border-color: #007bff !important;
}

/* Search Bar Enhancement */
.search-wrapper {
    max-width: 600px;
    margin: 0 auto;
}

#search_input {
    border: 2px solid #e9ecef;
    border-right: none;
    padding: 12px 20px;
    font-size: 16px;
    transition: all 0.3s ease;
    border-radius: 4px 0 0 4px;
}

#search_input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    outline: none;
}

.input-group-append .input-group-text {
    border: 2px solid #e9ecef;
    border-left: none;
    background: white;
    padding: 12px 20px;
    border-radius: 0 4px 4px 0;
    transition: all 0.3s ease;
}

#search_input:focus + .input-group-append .input-group-text {
    border-color: #007bff;
}

.fa-search {
    font-size: 18px;
}

/* Live Search Results */
#search_result {
    border-radius: 0 0 8px 8px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    max-height: 400px;
    overflow-y: auto;
    display: none;
}

.search-wrapper:focus-within #search_result {
    display: block;
}

/* Cart & Wishlist Icons */
.btn.border {
    border: 2px solid #e9ecef !important;
    border-radius: 8px;
    padding: 10px 16px;
    margin-left: 10px;
    transition: all 0.3s ease;
    position: relative;
    background: white;
}

.btn.border:hover {
    border-color: #007bff !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.15);
}

.badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ff4757;
    color: white;
    border-radius: 50%;
    min-width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
}

/* Categories Dropdown */
.bg-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
    border: none !important;
}

.bg-primary:hover {
    background: linear-gradient(135deg, #0056b3 0%, #003d82 100%) !important;
}

.navbar-vertical {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border-radius: 0 0 8px 8px;
}

.nav-item.dropdown:hover > .dropdown-menu {
    display: block;
    margin-top: 0;
    animation: fadeIn 0.3s ease;
}

.dropdown-menu {
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    padding: 10px 0;
}

.dropdown-item {
    padding: 10px 20px;
    transition: all 0.2s;
    color: #495057;
    font-weight: 500;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    color: #007bff;
    padding-left: 25px;
}

/* Main Navigation */
.navbar-light .navbar-nav .nav-link {
    color: #495057;
    font-weight: 600;
    padding: 12px 20px;
    transition: all 0.3s;
    position: relative;
}

.navbar-light .navbar-nav .nav-link:hover {
    color: #007bff;
}

.navbar-light .navbar-nav .nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 20px;
    right: 20px;
    height: 2px;
    background: #007bff;
    transform: scaleX(0);
    transition: transform 0.3s;
}

.navbar-light .navbar-nav .nav-link:hover::after {
    transform: scaleX(1);
}

/* Dropdown Submenu */
.dropdown-submenu {
    position: relative;
}

.dropdown-submenu > .dropdown-menu {
    top: 0;
    left: 100%;
    margin-left: 0;
    margin-top: -10px;
}

.dropdown-submenu:hover > .dropdown-menu {
    display: block;
}

/* User Dropdown */
.nav-link.dropdown-toggle {
    display: flex;
    align-items: center;
}

.nav-link.dropdown-toggle span {
    text-align: left;
    line-height: 1.3;
}

.dropdown-menu-right {
    right: 0;
    left: auto;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Adjustments */
@media (max-width: 992px) {
    .search-wrapper {
        margin-top: 15px;
    }
    
    .btn.border {
        margin: 5px;
    }
    
    .navbar-vertical {
        position: static !important;
        width: 100% !important;
    }
    
    .dropdown-menu {
        position: static !important;
        float: none !important;
        width: 100% !important;
    }
    
    .dropdown-submenu > .dropdown-menu {
        left: 0;
    }
}

@media (max-width: 768px) {
    .d-none.d-lg-block {
        text-align: center;
    }
    
    .col-lg-6.text-center.text-lg-right {
        text-align: center !important;
        margin-top: 10px;
    }
    
    #search_input {
        font-size: 14px;
        padding: 10px 15px;
    }
}

/* Smooth Scroll Behavior */
html {
    scroll-behavior: smooth;
}

/* Custom Scrollbar for Search Results */
#search_result::-webkit-scrollbar {
    width: 6px;
}

#search_result::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#search_result::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

#search_result::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
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
                    <span class="text-muted px-2">|</span>
                    @php
                      use App\Models\Currency;
                      $currencies = Currency::where('status', 1)->get();
                      $currentCurrency = getCurrentCurrency();
                    @endphp
                     <div class="currency-dropdown">
                       <button id="current-currency-btn" class="btn btn-light">
                        <img src="{{ asset('front/images/flags/' . $currentCurrency->flag) }}" alt="{{ $currentCurrency->code }}" style="height:16px;">
                        {{ $currentCurrency->code }}
                       </button>
                       <ul id="currency-list" class="dropdown-menu">
                         @foreach($currencies as $currency)
                           <li class="currency-item" data-code="{{ $currency->code }}">
                           <img src="{{ asset('front/images/flags/' . $currency->flag) }}" alt="{{ $currency->code }}" style="height:16px;">
                             {{ $currency->symbol }} {{ $currency->code }}
                           </li>
                         @endforeach
                      </ul>
                    </div>
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
                    <h1 class="m-0 display-5 font-weight-semi-bold"><span class="text-primary font-weight-bold border px-1 mr-0">
                      S</span>ite&nbsp;<span class="text-primary font-weight-bold border px-1 mr-0">E</span>-Commerce</h1>
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
                            <span class="text-primary font-weight-bold border px-1 mr-1">E</span>Commerce
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
                          <a href="{{ url('user/account') }}" class="dropdown-item">My Account</a>
                          <a href="{{ url('user/orders') }}" class="dropdown-item">My Orders</a>
                          <a class="dropdown-item" href="{{ url('user/change-password') }}">
                            Change Password
                           </a>
                          <!--<a href="#" class="dropdown-item disabled">
                            Role: {{ ucfirst(Auth::user()->user_type) }}
                           </a>-->
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
     <script>
// Currency dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    // Currency selection
    document.querySelectorAll('.currency-item').forEach(item => {
        item.addEventListener('click', function() {
            const currencyCode = this.getAttribute('data-code');
            
            // Update button
            const btn = document.getElementById('current-currency-btn');
            const flag = this.querySelector('img').src;
            const text = this.textContent.trim();
            
            btn.innerHTML = `<img src="${flag}" alt="${currencyCode}" style="height:16px;"> ${currencyCode}`;
            
            // Hide dropdown
            document.getElementById('currency-list').style.display = 'none';
            
            // Send AJAX request to update currency
            fetch('/change-currency', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({currency: currencyCode})
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                }
            });
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('search_input');
    const searchResult = document.getElementById('search_result');
    
    if(searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value;
            
            if(query.length < 2) {
                searchResult.innerHTML = '';
                searchResult.style.display = 'none';
                return;
            }
            
            fetch(`/search?q=${encodeURIComponent(query)}`)
                .then(response => response.text())
                .then(html => {
                    searchResult.innerHTML = html;
                    searchResult.style.display = 'block';
                });
        });
        
        // Close search results when clicking outside
        document.addEventListener('click', function(e) {
            if(!searchWrapper.contains(e.target)) {
                searchResult.style.display = 'none';
            }
        });
    }
    
    // Add hover effects for dropdowns on desktop
    if(window.innerWidth > 992) {
        document.querySelectorAll('.nav-item.dropdown').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.querySelector('.dropdown-menu').style.display = 'block';
            });
            
            item.addEventListener('mouseleave', function() {
                this.querySelector('.dropdown-menu').style.display = 'none';
            });
        });
    }
});
</script>
     