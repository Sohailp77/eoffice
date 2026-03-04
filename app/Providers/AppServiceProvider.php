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
        // View Composers are removed in favor of Component logic
        \Illuminate\Support\Facades\Auth::provider('multi_user', function ($app, array $config) {
            return new \App\Providers\MultiUserProvider();
        });
    }
}
