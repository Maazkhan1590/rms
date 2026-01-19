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
        $request = request();
        if ($request) {
            $basePath = $request->getBasePath();
            
            // If we're in a subdirectory, force the root URL to include it
            if ($basePath && $basePath !== '/') {
                // Get the full URL including the subdirectory
                $scheme = $request->getScheme();
                $rootUrl = $scheme . '://' . $host . $basePath;
                
                // Force Laravel to use this as the root URL for all URL generation
                URL::forceRootUrl($rootUrl);
                
                // Also set asset URL if not already configured
                if (empty(config('app.asset_url'))) {
                    config(['app.asset_url' => $basePath]);
                }
            } elseif (env('APP_URL')) {
                // Fallback: use APP_URL from .env if set
                URL::forceRootUrl(env('APP_URL'));
            }
        }
    }
}
