<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Research Management System')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon/favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon/favicon.ico') }}">
    
    <!-- Stylesheets -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    <!-- Local fonts (replaces Google Fonts import in CSS) -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/inter/400.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/inter/500.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/inter/600.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/inter/700.css') }}">
    @yield('styles')
</head>
<body>
    <div id="app">
        @yield('content')
    </div>
    
    <!-- Custom Scripts -->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/axios/axios.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <!-- Form Submit Blocker - Prevents duplicate submissions -->
    <script src="{{ asset('js/form-blocker.js') }}"></script>
    @yield('scripts')
</body>
</html>
