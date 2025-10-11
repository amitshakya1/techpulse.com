<?php

use Illuminate\Support\Facades\Route;

// Website
Route::domain('www.' . config('app.domain'))
    ->group(base_path('routes/www.php'));

// Admin panel
Route::domain('admin.' . config('app.domain'))->as('admin.')->group(base_path('routes/admin.php'));

// API
Route::domain('api.' . config('app.domain'))->as('api.')->group(base_path('routes/api.php'));
