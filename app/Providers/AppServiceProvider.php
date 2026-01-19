<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix URL generation for subdirectory deployment
        $appUrl = env('APP_URL');
        
        \Log::info('=== URL DEBUG START ===');
        \Log::info('APP_URL from .env: ' . ($appUrl ?: 'NOT SET'));
        \Log::info('config(app.url): ' . config('app.url'));
        \Log::info('config(app.asset_url): ' . config('app.asset_url'));
        
        // Always use APP_URL from .env if set (most reliable)
        if ($appUrl) {
            \Log::info('Setting URL::forceRootUrl to: ' . $appUrl);
            URL::forceRootUrl($appUrl);
            
            // Extract base path from APP_URL for asset URLs
            $parsedUrl = parse_url($appUrl);
            \Log::info('Parsed URL:', $parsedUrl);
            
            if (isset($parsedUrl['path']) && $parsedUrl['path'] !== '/') {
                $basePath = rtrim($parsedUrl['path'], '/');
                \Log::info('Extracted basePath: ' . $basePath);
                if (empty(config('app.asset_url'))) {
                    config(['app.asset_url' => $basePath]);
                    \Log::info('Set asset_url to: ' . $basePath);
                }
            }
        } else {
            // Fallback: auto-detect from request
            $request = request();
            if ($request) {
                $basePath = $request->getBasePath();
                \Log::info('Auto-detected basePath from request: ' . $basePath);
                \Log::info('Request URI: ' . $request->getRequestUri());
                \Log::info('Request Path: ' . $request->getPathInfo());
                \Log::info('Request Root: ' . $request->root());
                
                // If we're in a subdirectory, force the root URL to include it
                if ($basePath && $basePath !== '/') {
                    // Get the full URL including the subdirectory
                    $scheme = $request->getScheme();
                    $host = $request->getHttpHost();
                    $rootUrl = $scheme . '://' . $host . $basePath;
                    \Log::info('Auto-detected rootUrl: ' . $rootUrl);
                    
                    // Force Laravel to use this as the root URL for all URL generation
                    URL::forceRootUrl($rootUrl);
                    
                    // Also set asset URL if not already configured
                    if (empty(config('app.asset_url'))) {
                        config(['app.asset_url' => $basePath]);
                        \Log::info('Set asset_url to: ' . $basePath);
                    }
                }
            }
        }
        
        // Log final state
        \Log::info('Final config(app.url): ' . config('app.url'));
        \Log::info('Final config(app.asset_url): ' . config('app.asset_url'));
        try {
            $testRootUrl = url('/');
            \Log::info('Final url("/"): ' . $testRootUrl);
        } catch (\Exception $e) {
            \Log::error('Error getting root URL: ' . $e->getMessage());
        }
        
        // Test route generation
        try {
            $testRoute = route('publications.index', [], false);
            \Log::info('Test route(publications.index): ' . $testRoute);
            $testUrl = url('/publications');
            \Log::info('Test url(/publications): ' . $testUrl);
        } catch (\Exception $e) {
            \Log::error('Error generating test routes: ' . $e->getMessage());
        }
        
        \Log::info('=== URL DEBUG END ===');
    }
}
