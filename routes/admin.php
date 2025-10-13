<?php
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // ----------------------------
// AUTH VIEW ROUTES
// ----------------------------
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('reset-password');

    // ----------------------------
// AUTH ACTION ROUTES
// ----------------------------
    Route::post('/', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password/{token}', [AuthController::class, 'resetPassword']);

    // ----------------------------
// OTP + SOCIAL AUTH
// ----------------------------
    Route::post('/send-otp', [AuthController::class, 'sendOtp'])->name('send-otp');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify-otp');
    Route::post('/social-login', [AuthController::class, 'socialLogin'])->name('social-login');
});

// ----------------------------
// LOGOUT (protected)
// ----------------------------
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});

