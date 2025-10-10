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
        if ($this->app->runningInConsole() && $this->app->environment('local')) {
            $urls = [
                'Website' => config('app.url'),
                'Admin site' => config('app.url_admin'),
                'API site' => config('app.url_api'),
            ];

            foreach ($urls as $name => $url) {
                echo "[{$name}] => {$url}\n";
            }
        }
    }
}
