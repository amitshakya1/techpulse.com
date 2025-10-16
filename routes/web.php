<?php
// use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
Route::middleware(['throttle:30,1'])->group(function () {
    // Website
    Route::domain('www.' . config('app.domain'))
        ->middleware('web')
        ->group(base_path('routes/www.php'));

    // Admin panel
    Route::domain('admin.' . config('app.domain'))
        ->middleware('web')
        ->name('admin.')
        ->group(base_path('routes/admin.php'));

    // API
    Route::domain('api.' . config('app.domain'))
        ->middleware('api')
        ->name('api.')
        ->group(base_path('routes/api.php'));
});
