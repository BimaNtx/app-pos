<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
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
        // Share restaurant settings globally to all views
        View::composer('*', function ($view) {
            $settingsPath = storage_path('app/settings.json');
            $restaurantName = 'Kasir App';
            $restaurantAddress = '';

            if (File::exists($settingsPath)) {
                $settings = json_decode(File::get($settingsPath), true);
                $restaurantName = $settings['restaurant_name'] ?? 'Kasir App';
                $restaurantAddress = $settings['restaurant_address'] ?? '';
            }

            $view->with('restaurantName', $restaurantName);
            $view->with('restaurantAddress', $restaurantAddress);
        });
    }
}
