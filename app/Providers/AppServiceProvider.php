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
        // Register global currency formatting helper
        if (!function_exists('formatCurrency')) {
            function formatCurrency($amount, $decimals = 2) {
                return \App\Models\SystemSetting::formatCurrency($amount, $decimals);
            }
        }
    }
}
