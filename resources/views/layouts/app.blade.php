<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ trans('panel.site_title') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon/favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon/favicon.ico') }}">
    
    <link href="{{ asset('assets/vendor/bootstrap4/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/coreui/css/coreui.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/fontawesome4/css/font-awesome.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/fontawesome5/css/all.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/datatables/1.10.19/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/dropzone/dropzone.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
    @yield('styles')
</head>

<body class="header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden login-page">
    <div class="c-app flex-row align-items-center">
        <div class="container">
            @yield("content")
        </div>
    </div>
    <!-- Vendor Scripts required by legacy admin pages -->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/popper/umd/popper.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap4/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/coreui/js/coreui.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/1.10.19/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/1.10.19/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/dropzone/dropzone.min.js') }}"></script>
    <!-- Form Submit Blocker - Prevents duplicate submissions -->
    <script src="{{ asset('js/form-blocker.js') }}"></script>

    @yield('scripts')
</body>

</html>
