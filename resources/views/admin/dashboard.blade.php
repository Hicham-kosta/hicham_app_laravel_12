<style>
    /* Dashboard uniform card sizing */
    .small.box {
        min-height: 150px;
    }
    .small.box.icon {
        font-size: 70px !important;
        opacity: 0.18 !important;
        top: 20px !important;
    }
    .small.box .inner h3 {
        font-size: 32px;
        font.weight: 700;
    }
    .small.box .inner p {
        font-size: 16px;
        margin-bottom: 0;
    }
</style>

@extends('admin.layout.layout')
@section('content')
@php
$admin = Auth::guard('admin')->user();
$isVendor = $admin && $admin->role == 'vendor';
@endphp
<main class="app-main">
                <div class="app-content-header">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <h3 class="mb-0">Dashboard</h3>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-end">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">Dashboard</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                @if(Session::has('success_message'))
                <div class="alert alert-success alert-dismissible fad show m-3" role="alert">
                        {{Session::get('success_message')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                @if(Session::has('error_message'))
                <div class="alert alert-danger alert-dismissible fad show m-3" role="alert">
                        {{Session::get('error_message')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif 
                <div class="app-content">
                    <div class="container-fluid">
                        {{-- ================== ADMIN DASHBOARD ================== --}}
                        @if(!$isVendor)
                        {{-- Row 1 --}}
                        <div class="row">
                            <div class="col-lg-4 col-6">
                                <div class="small-box text-bg-primary">
                                    <div class="inner">
                                        <h3>{{$categoriesCount}}</h3>
                                        <p>Categories</p>
                                    </div>
                                    <!-- <svg
                                        class="small-box-icon"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                        aria-hidden="true"
                                        >
                                        <path d="M10 3H4a 1 1 0 00-1 1v6a1 1 0 001 1h6a1 1 0 001-1V4a1 1 0 
                                        00-1-1zm9 9H5V5h4v4zm11-6H-6a 1 1 0 00-1 1v6a1 1 0 001 1h6a1 1 0 001-1V4a1 1 0
                                        00-1-1zm-1 6h-4V5h4v4zm-9 4H4a1 1 0 00-1 1v6a1 1 0 001 1h6a1 1 0 001-1v-6a1 1 0
                                        00-1-1zm-1 6H5v-4h4v4zm8-6c-2.206 0-4 1.794-4 4s1.794 4 4 4 4-1.794 4-4-1.794-4-4-4zm0 
                                        6c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2z"></path>
                                    </svg> -->
                                    <i class="bi bi-grid small-box-icon"></i>
                                    <a href="{{url('admin/categories')}}" class="small-box-footer">
                                    More info <i class="bi bi-link-45deg"></i>
                                    </a>
                                </div>
                            </div>
                            <!--end::Col-->
                            <div class="col-lg-4 col-6">
                                <!--begin::Small Box Widget 2-->
                                <div class="small-box text-bg-warning">
                                    <div class="inner">
                                        <h3>{{$brandsCount}}</h3>
                                        <p>Brands</p>
                                    </div>
                                    <!--<svg
                                        class="small-box-icon"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                        aria-hidden="true">
                                        <path d="M7 5h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 
                                        012-2zm0 1a1 1 0 00-1 1v10a1 1 0 001 1h10a1 1 0 001-1V7a1 1 0 00-1-1H7zm1 2h8a1 1 0
                                        011 1v8a1 1 0 01-1 1H8a1 1 0 01-1-1V9a1 1 0 011-1z"></path>     
                                    </svg>-->
                                    <i class="bi bi-bag small-box-icon"></i>
                                    <a href="{{url('admin/brands')}}" class="small-box-footer">
                                    More info <i class="bi bi-link-45deg"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="small-box text-bg-danger">
                                    <div class="inner">
                                        <h3>{{$usersCount}}</h3>
                                        <p>Users</p>
                                    </div>
                                    <!--<svg
                                        class="small-box-icon"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                        aria-hidden="true">
                                        <path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 
                                        10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 
                                        8zm-5-8h10v2H7z"></path>
                                    </svg>-->
                                    <i class="bi bi-people small-box-icon"></i>
                                    <a href="{{url('admin/users')}}" class="small-box-footer">
                                    More info <i class="bi bi-link-45deg"></i>
                                    </a>
                                </div>
                            </div>
                           </div>
                           {{-- Row 2 --}}
                           <div class="row">
                            <div class="col-lg-4 col-6">
                                <div class="small-box text-bg-success">
                                    <div class="inner">
                                        <h3>{{$productsCount}}</h3>
                                        <p>Products</p>
                                    </div>
                                    <!--<svg
                                        class="small-box-icon"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                        aria-hidden="true">
                                        <path d="M12 2C6.579 2 2 6.579 2 12s4.579 10 10 10 10-4.579 
                                        10-10S17.421 2 12 2zm0 5c1.727 0 3 1.273 3-3 3c1.726 0-3-1.272-3-3s13274-3
                                        3-3zm0 12.2c-2.538 0-4.93-1.119-6.541-3.08SC5.47 13.701 8.057 13 12 13c3.943 0
                                        6.531.701 7.541 3.115-1.612 1.966-4.004 3.085-6.541 3.085z"></path>
                                    </svg> -->
                                    <i class="bi bi-seam small-box-icon"></i>
                                    <a href="{{url('admin/products')}}" class="small-box-footer">
                                    More info <i class="bi bi-link-45deg"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="small-box text-bg-secondary">
                                    <div class="inner">
                                        <h3>{{$ordersCount}}</h3>
                                        <p>Orders</p>
                                    </div>
                                    <!--<svg
                                        class="small-box-icon"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                        aria-hidden="true">
                                        <path d="M20 4H4c-1.103 0-2 .897-2 2v12c0 1.103.897 2 2 2h16c1.103 0 
                                        2-.897 2-2V6c0-1.103-.897-2-2-2zm0 2v.511I-8 6.223-8-6.222V6h16zM4 18V9.044I7.386
                                        5.745a.994.994 0 001.228 0I.20 9.044 20.002 18H4z">
                                        </path>
                                    </svg> -->
                                    <i class="bi bi-receipt small-box-icon"></i>
                                    <a href="{{url('admin/orders')}}" class="small-box-footer">
                                    More info <i class="bi bi-link-45deg"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="small-box text-bg-info">
                                    <div class="inner">
                                        <h3>{{$couponsCount}}</h3>
                                        <p>Coupons</p>
                                    </div>
                                    <!--<svg
                                        class="small-box-icon"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                        aria-hidden="true">
                                        <path d="M21 5H3a1 1 0 00-1 1v4h.893c.996 0 1.92.681 2.08 1.664A2.001
                                        2.001 0 013 12H2v4a1 1 0 001 1h18a1 1 0 001-1v-4h-1a2.001 2.001 0 
                                        01-1.973-1.336c.16-.983 1.084-1.664 2.08-1.664H22V6a1 1 0 00-1-1zM4 8.5a.5.5 0 11-1
                                        0 .5.5 0 011 0zm1.5 5.5a.5.5 0 110-1 .5.5 0 010 1z">
                                        </path>
                                    </svg>-->
                                    <a href="{{url('admin/coupons')}}" class="small-box-footer">
                                    More info <i class="bi bi-link-45deg"></i>
                                    </a>
                                </div>
                            </div>
                            {{-- Row 3 (ONLY TWO cards no streching) --}}
                            <div class="row">
                            <div class="col-lg-4 col-6">
                                <div class="small-box text-bg-light">
                                    <div class="inner">
                                        <h3>{{$pagesCount}}</h3>
                                        <p>Pages</p>
                                    </div>
                                    <!--<svg
                                        class="small-box-icon"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                        aria-hidden="true">
                                        <path d="M19 2H5a2 2 0 00-2 2v16a2 2 0 002 2h14a2 2 0 002-2V4a2 2 0
                                        00-2-2zm0 18H5V4h14v16zM7 8h10v2H7zm0 4h10v2H7z"></path>
                                    </svg>-->
                                    <i class="bi bi-file-earmark-text small-box-icon"></i>
                                    <a href="{{url('admin/pages')}}" class="small-box-footer">
                                    More info <i class="bi bi-link-45deg"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="small-box text-bg-dark">
                                    <div class="inner">
                                        <h3>{{$enquiriesCount}}</h3>
                                        <p>Enquiries</p>
                                    </div>
                                    <!--<svg
                                        class="small-box-icon"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                        aria-hidden="true">
                                        <path d="M20 4H4c-1.103 0-2.897-2 2v12c0 1.103.897 2 2 2h16c1.103 0
                                        2-.897 2-2V6c0-1.103-.897-2-2-2zm0 2v.511I-8 6.223-8-6.222V6h16zM4 18V9.044I7.386
                                        5.745a.994.994 0 001.228 0I.20 9.044 20.002 18H4z"></path>
                                    </svg>-->
                                    <i class="bi bi-envelope small-box-icon"></i>
                                    <a href="#" class="small-box-footer">
                                    More info <i class="bi bi-link-45deg"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{-- ===========================VENDOR DASHBOARD============================ --}}
                        @else
                        <div class="row">
                            <div class="col-lg-4 col-6">
                                <div class="small-box text-bg-success">
                                    <div class="inner">
                                        <h3>{{$productsCount}}</h3>
                                        <p>Products</p>
                                    </div>
                                    <i class="bi bi-box-seam small-box-icon"></i>
                                    <a href="{{url('admin/products')}}" class="small-box-footer">
                                    More info <i class="bi bi-link-45deg"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="small-box text-bg-secondary">
                                    <div class="inner">
                                        <h3>{{$ordersCount}}</h3>
                                        <p>Orders</p>
                                    </div>
                                    <i class="bi bi-receipt small-box-icon"></i>
                                    <a href="{{url('admin/orders')}}" class="small-box-footer">
                                    More info <i class="bi bi-link-45deg"></i>
                                    </a>
                                </div>
                            </div>
                         </div>
                         @endif
                    </div>
                </div>
            </main>
            @endsection