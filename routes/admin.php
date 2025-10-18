<?php
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PageController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

// ----------------------------
// LOGOUT (protected)
// ----------------------------
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::resource('pages', PageController::class);

    // Additional routes for AJAX, bulk, import/export
    Route::prefix('pages')->name('pages.')->group(function () {

        // Get paginated items (for Alpine.js or API)
        Route::get('items', [PageController::class, 'getItems'])->name('items');

        // Export pages to Excel
        Route::get('export', [PageController::class, 'export'])->name('export');

        // Import pages from Excel/CSV
        Route::post('import', [PageController::class, 'import'])->name('import');

        // Bulk actions (status update or delete)
        Route::post('bulk-action', [PageController::class, 'bulkAction'])->name('bulk-action');

        // Restore soft-deleted pages
        Route::post('restore', [PageController::class, 'restore'])->name('restore');
    });

});

