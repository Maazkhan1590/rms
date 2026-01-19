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
        
        // Always use APP_URL from .env if set (most reliable)
        if ($appUrl) {
            URL::forceRootUrl($appUrl);
            
            // Extract base path from APP_URL for asset URLs
            $parsedUrl = parse_url($appUrl);
            if (isset($parsedUrl['path']) && $parsedUrl['path'] !== '/') {
                $basePath = rtrim($parsedUrl['path'], '/');
                if (empty(config('app.asset_url'))) {
                    config(['app.asset_url' => $basePath]);
                }
            }
        } else {
            // Fallback: auto-detect from request
            $request = request();
            if ($request) {
                $basePath = $request->getBasePath();
                
                // If we're in a subdirectory, force the root URL to include it
                if ($basePath && $basePath !== '/') {
                    // Get the full URL including the subdirectory
                    $scheme = $request->getScheme();
                    $host = $request->getHttpHost();
                    $rootUrl = $scheme . '://' . $host . $basePath;
                    
                    // Force Laravel to use this as the root URL for all URL generation
                    URL::forceRootUrl($rootUrl);
                    
                    // Also set asset URL if not already configured
                    if (empty(config('app.asset_url'))) {
                        config(['app.asset_url' => $basePath]);
                    }
                }
            }
        }
    }
}
