<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class LayoutServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer(['layouts.admin.header', 'layouts.admin.footer', 'layouts.admin.sidebar', 'admin.dashboard', 'admin.profile', 'admin.change-password'], function ($view) {
            $view->with('auth', auth()->user());
        });
    }
}
