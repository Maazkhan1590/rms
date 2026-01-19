<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Academic Research Portal')</title>
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @if(request()->routeIs('welcome'))
    <link rel="stylesheet" href="{{ asset('css/slider.css') }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    @stack('styles')
</head>
<body>
    @include('partials.public-header')

    @if(session('success'))
    <div style="position: fixed; top: 80px; left: 50%; transform: translateX(-50%); z-index: 9999; background: #22c55e; color: white; padding: 1rem 2rem; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div style="position: fixed; top: 80px; left: 50%; transform: translateX(-50%); z-index: 9999; background: #ef4444; color: white; padding: 1rem 2rem; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif

    @yield('content')

    @include('partials.public-footer')

    <!-- Scripts -->
    <script src="{{ asset('js/script.js') }}"></script>
    @if(request()->routeIs('welcome'))
    <script src="{{ asset('js/slider.js') }}"></script>
    @endif
    @if(request()->routeIs('login') || request()->routeIs('register'))
    <script src="{{ asset('js/auth.js') }}"></script>
    @endif
    @if(request()->routeIs('publications.*'))
    <script src="{{ asset('js/publications.js') }}"></script>
    @endif
    
    @stack('scripts')
    
    <script>
        // Set base URL for JavaScript (handles subdirectory deployment)
        window.BASE_URL = '{{ url("/") }}';
        window.ASSET_URL = '{{ asset("") }}';
        
        // Debug logging
        console.log('=== URL DEBUG ===');
        console.log('window.BASE_URL:', window.BASE_URL);
        console.log('window.ASSET_URL:', window.ASSET_URL);
        console.log('Laravel route(publications.index):', '{{ route("publications.index") }}');
        console.log('Laravel url(/publications):', '{{ url("/publications") }}');
        console.log('Current location:', window.location.href);
        console.log('Current pathname:', window.location.pathname);
        console.log('==================');
        
        // Auto-hide success/error messages
        setTimeout(() => {
            const messages = document.querySelectorAll('[style*="position: fixed"]');
            messages.forEach(msg => {
                msg.style.opacity = '0';
                msg.style.transition = 'opacity 0.5s';
                setTimeout(() => msg.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
