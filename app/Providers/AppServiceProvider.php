<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Auto-detect subdirectory for asset URLs (keep /public/ in URLs)
        if (empty(config('app.asset_url'))) {
            $request = request();
            if ($request) {
                $basePath = $request->getBasePath();
                if ($basePath && $basePath !== '/') {
                    config(['app.asset_url' => $basePath]);
                }
            }
        }
    }
}
