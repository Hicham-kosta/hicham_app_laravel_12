<!doctype html>
<html lang="en">
    <!--begin::Head-->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>AdminLTE v4 | Dashboard</title>
        <!--begin::Primary Meta Tags-->
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="title" content="AdminLTE v4 | Dashboard" />
        <meta name="author" content="ColorlibHQ" />
        <meta
            name="description"
            content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS."
            />
        <meta
            name="keywords"
            content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard"
            />
            <!-- Bootstrap 5 CSS -->
           <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!--end::Primary Meta Tags-->
        <!--begin::Fonts-->
        @include('admin.layout.styles')
    </head>
    <!--end::Head-->
    <!--begin::Body-->
    <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
        <!-- Bootstrap 5 JS + Popper -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        <!--begin::App Wrapper-->
        <div class="app-wrapper">
            <!--begin::Header-->
            @include('admin.layout.header')
            <!--end::Header-->
            <!--begin::Sidebar-->
            @include('admin.layout.sidebar')
            <!--end::Sidebar-->
            <!--begin::App Main-->
            @yield('content')
            <!--end::App Main-->
            <!--begin::Footer-->
            @include('admin.layout.footer')
            <!--end::Footer-->
        </div>
        <!--end::App Wrapper-->
        <!--begin::Script-->
        @include('admin.layout.scripts')

        @yield('scripts')

        <div class="toast-container position-fixed bottom-0 end-0 p-3"></div>

        <!--end::Script-->

    </body>
    <!--end::Body-->
</html>