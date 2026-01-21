@php
$admin = Auth::guard('admin')->user();
$isAdmin = $admin && $admin->role == 'admin';
$isVendor = $admin && $admin->role == 'vendor';
@endphp
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!-- Brand -->
     <div class="sidebar-brand">
        <a href="{{url('admin/dashboard')}}" class="brand-link d-flex align-items-center gap-2">
            <img src="{{asset('admin/images/AdminLTELogo.png')}}" class="brand-image opacity-75 shadow">
            <span class="brand-text">Admin Panel</span>
        </a>
     </div>
     <div class="sidebar-wrapper">
     <!-- User Panel -->
      <div class="sidebar-user">
        <img style="width: 50px;" class="avatar" 
        src="{{!empty($admin->image) ? asset('admin/images/profiles/'.$admin->image) 
        : asset('admin/images/no-image.png')}}">
       <div>
        <div class="name" style="color: white;">{{$admin->name}}</div>
        <div class="role" style="color: white;">{{ucfirst($admin->role)}}</div>
      </div>
     </div>
     <nav class="mt-2">
        <div class="sidebar-heading " style="color: white;">Management</div>
        <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview">

        {{-- ==========================
             ADMIN / VENDOR Management
             ========================== --}}
            <li class="nav-item {{in_array(Session::get('page'), ['dashboard','update-password',
            'update-details','vendor-details']) ? 'menu-open' : ''}}">
            <a href="#" class="nav-link {{in_array(Session::get('page'),['dashboard','update-password',
            'update-details','vendor-details']) ? 'active' : ''}}">
            <i class="nav-icon bi bi-speedometer2"></i>
            <p>Account Management <i class="nav-arrow bi bi-chevron-right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                {{-- Dashboard (Admin + Vendor) --}}
                <li class="nav-item">
                    <a href="{{url('admin/dashboard')}}" class="nav-link 
                    {{Session::get('page') == 'dashboard' ? 'active' : ''}}">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                {{-- Update Password (Admin + Vendor) --}}
                <li class="nav-item">
                    <a href="{{url('admin/update-password')}}" class="nav-link 
                    {{Session::get('page') == 'update-password' ? 'active' : ''}}">
                        <i class="nav-icon bi bi-shield-lock"></i>
                        <p>Update Password</p>
                    </a>
                </li>
                {{-- Update Details (Admin + Vendor) --}}
                
                <li class="nav-item">
                    <a href="{{url('admin/update-details')}}" class="nav-link 
                    {{Session::get('page') == 'update-details' ? 'active' : ''}}">
                        <i class="nav-icon bi bi-person-gear"></i>
                        <p>{{$isVendor ? 'Vendor Details' : 'Admin Details'}}</p>
                    </a>
                </li>
                
                {{-- Vendor KYC / Business Details (Vendor only) --}}
                @if($isVendor)
                <li class="nav-item">
                    <a href="{{route('admin.vendor.update-details')}}" class="nav-link 
                    {{Session::get('page') == 'vendor-details' ? 'active' : ''}}">
                        <i class="nav-icon bi bi-shop"></i>
                        <p>Shop Details</p>
                    </a>
                </li>
                @endif
                {{-- Subadmins (Admin only) --}}
                @if($isAdmin)
                <li class="nav-item">
                    <a href="{{url('admin/subadmins')}}" class="nav-link 
                    {{Session::get('page') == 'subadmins' ? 'active' : ''}}">
                        <i class="nav-icon bi bi-people"></i>
                        <p>Subadmins</p>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        {{-- ==========================
             CATALOGUE MANAGEMENT
             ========================== --}}
             <li class="nav-item {{Session::get('page')=='products' ? 'menu-open' : ''}}">
                <a href="#" class="nav-link {{Session::get('page') == 'products' ? 'active' : ''}}">
                    <i class="nav-icon bi bi-box-seam"></i>
                    <p>Products <i class="nav-arrow bi bi-chevron-right"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    {{-- Categories & Brands (Admin Only) --}}
                    @if($isAdmin)
                    <li class="nav-item">
                        <a href="{{url('admin/categories')}}" class="nav-link 
                        {{Session::get('page') == 'categories' ? 'active' : ''}}">
                            <i class="nav-icon bi bi-tags"></i>
                            <p>Categories</p>
                        </a>
                    </li>
                    {{-- Brands (Admin) --}}
                    <li class="nav-item">
                        <a href="{{url('admin/brands')}}" class="nav-link 
                        {{Session::get('page') == 'brands' ? 'active' : ''}}">
                            <i class="nav-icon bi bi-bookmark"></i>
                            <p>Brands</p>
                        </a>
                    </li>
                    @endif
                    {{-- Products (Admin + Vendor) --}}
                    <li class="nav-item">
                        <a href="{{url('admin/products')}}" class="nav-link 
                        {{Session::get('page') == 'products' ? 'active' : ''}}">
                            <i class="nav-icon bi bi-box"></i>
                            <p>Products</p>
                        </a>
                    </li>
                  </ul>
                </li>
                {{-- ==========================
                     ORDERS (Admin + Vendor)
                     ========================== --}}
                     <li class="nav-item {{Session::get('page')=='orders' ? 'menu-open' : ''}}">
                        <a href="{{url('admin/orders')}}" class="nav-link 
                        {{Session::get('page') == 'orders' ? 'active' : ''}}">
                            <i class="nav-icon bi bi-receipt"></i>
                            <p>Orders</p>
                        </a>
                    </li>

                    {{-- ==========================
                     ADMIN ONLY MODULES
                     ========================== --}}
                     @if($isAdmin)
                     {{-- Users --}}
                     <li class="nav-item">
                        <a href="{{url('admin/users')}}" class="nav-link
                        {{Session::get('page') == 'users' ? 'active' : ''}}">
                            <i class="nav-icon bi bi-people-fill"></i>
                            <p>Users</p>
                        </a>
                     </li>
                     {{-- Coupons --}}
                     <li class="nav-item">
                        <a href="{{url('admin/coupons')}}" class="nav-link
                        {{Session::get('page') == 'coupons' ? 'active' : ''}}">
                            <i class="nav-icon bi bi-ticket-perforated"></i>
                            <p>Coupons</p>
                        </a>
                     </li>
                     {{-- CMS Pages --}}
                     <li class="nav-item">
                        <a href="{{url('admin/pages')}}" class="nav-link
                        {{Session::get('page') == 'pages' ? 'active' : ''}}">
                            <i class="nav-icon bi bi-file-earmark-text"></i>
                            <p>CMS Pages</p>
                        </a>
                     </li>
                     {{-- Wallet --}}
                     <li class="nav-item">
                        <a href="{{route('wallet-credits.index')}}" class="nav-link
                        {{Session::get('page') == 'wallet-credits' ? 'active' : ''}}">
                            <i class="nav-icon bi bi-wallet"></i>
                            <p>Wallet / Credits</p>
                        </a>
                     </li>
                     @endif
                    <!--@if($isAdmin)
                    <li class="nav-item">
                        <a href="{{url('admin/attributes')}}" class="nav-link 
                        {{Session::get('page') == 'attributes' ? 'active' : ''}}">
                            <i class="nav-icon bi bi-sliders"></i>
                            <p>Attributes</p>
                        </a>
                    </li>
                    @endif-->
                    {{-- Filters (Admin Only) --}}
                    @if($isAdmin)
                    <li class="nav-item">
                        <a href="{{url('admin/filters')}}" class="nav-link 
                        {{Session::get('page') == 'filters' ? 'active' : ''}}">
                            <i class="nav-icon bi bi-funnel"></i>
                            <p>Filters</p>
                        </a>
                    </li>
                    @endif
                    <!--{{-- Units (Admin Only) --}}
                    @if($isAdmin)
                    <li class="nav-item">
                        <a href="{{url('admin/units')}}" class="nav-link 
                        {{Session::get('page') == 'units' ? 'active' : ''}}">
                            <i class="nav-icon bi bi-ruler"></i>
                            <p>Units</p>
                        </a>
                    </li>
                    @endif-->
            </ul>
        </nav>
    </div>
</aside>