<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Academic Research Portal')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon/favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon/favicon.ico') }}">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @if(request()->routeIs('welcome'))
        <link rel="stylesheet" href="{{ asset('css/slider.css') }}">
    @endif
    <!-- Local fonts (replaces Google Fonts) -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/playfair-display/400.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/playfair-display/500.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/playfair-display/600.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/playfair-display/700.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/playfair-display/800.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/inter/300.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/inter/400.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/inter/500.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/inter/600.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/inter/700.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/cormorant-garamond/400.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/cormorant-garamond/500.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/cormorant-garamond/600.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/cormorant-garamond/700.css') }}">
    <!-- Icon fonts -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome6/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-icons/font/bootstrap-icons.css') }}">

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
<script src="{{ asset('js/script.js') }}" defer></script>

<script>
    // Load heavier form libraries only when a form exists on the page
    (function () {
        function hasForms() {
            return !!document.querySelector('form.auth-form, form.needs-validation');
        }

        function loadScript(src) {
            return new Promise((resolve, reject) => {
                const s = document.createElement('script');
                s.src = src;
                s.defer = true;
                s.onload = resolve;
                s.onerror = reject;
                document.body.appendChild(s);
            });
        }

        async function bootFormValidation() {
            try {
                // jQuery + jQuery Validate (required by form-validation.js)
                await loadScript('{{ asset('assets/vendor/jquery/jquery.min.js') }}');
                await loadScript('{{ asset('assets/vendor/jquery-validation/jquery.validate.min.js') }}');
                await loadScript('{{ asset('assets/vendor/jquery-validation/additional-methods.min.js') }}');

                // Local helpers
                await loadScript('{{ asset('js/form-blocker.js') }}');
                await loadScript('{{ asset('js/form-validation.js') }}');
            } catch (e) {
                // Silent fail: avoid breaking page render if CDN blocked
                console.warn('Form validation scripts failed to load', e);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
                if (hasForms()) bootFormValidation();
            });
        } else {
            if (hasForms()) bootFormValidation();
        }
    })();
</script>
@if(request()->routeIs('welcome'))
    <script src="{{ asset('js/slider.js') }}" defer></script>
@endif
@if(request()->routeIs('login') || request()->routeIs('register'))
    <script src="{{ asset('js/auth.js') }}" defer></script>
@endif
@if(request()->routeIs('publications.*'))
    <script src="{{ asset('js/publications.js') }}" defer></script>
@endif

@stack('scripts')

<script>
    // Set base URL for JavaScript (handles subdirectory deployment)
    window.BASE_URL = '{{ url("/") }}';
    window.ASSET_URL = '{{ asset("") }}';

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
