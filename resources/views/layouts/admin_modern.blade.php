<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'RMS') }} - @yield('title', 'Dashboard')</title>
    <!-- Custom Admin Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    <!-- Charts CDN (lightweight for demo) -->
    <script src="{{ asset('assets/vendor/chartjs/chart.umd.js') }}" defer></script>
    <!-- Bootstrap Icons (for simple icons without setup) -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-icons/font/bootstrap-icons.css') }}">
</head>
<body class="h-100" data-theme>
    <header class="topbar">
        @include('partials.topbar')
    </header>

    <div class="layout">
        <aside class="sidebar" id="sidebar">
            @include('partials.sidebar')
        </aside>
        <main class="p-lg">
            @yield('content')
        </main>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="flex" style="justify-content: space-between; align-items: center;">
                <span>© {{ date('Y') }} {{ config('app.name', 'RMS') }}</span>
                <span class="text-muted">Version {{ env('APP_VERSION', 'v1.0.0') }}</span>
            </div>
        </div>
    </footer>
    <!-- Custom Admin Scripts -->
    <script src="{{ asset('assets/vendor/axios/axios.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
