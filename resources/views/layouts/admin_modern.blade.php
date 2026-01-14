<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'RMS') }} - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Charts CDN (lightweight for demo) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
    <!-- Bootstrap Icons (for simple icons without setup) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
    @stack('scripts')
</body>
</html>
