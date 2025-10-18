<?php

use App\Http\Controllers\AuthController;

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
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password-action');

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
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});