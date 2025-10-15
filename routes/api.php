<?php

use App\Http\Controllers\Api\N8N\AuthController;
use App\Http\Controllers\Api\N8N\NewsController;
use Illuminate\Support\Facades\Route;

// Public API routes (no authentication required)
Route::prefix('v1')->group(function () {
    Route::get('health', function () {
        return response()->json(['status' => 'ok', 'timestamp' => now()]);
    });
});

// N8N Authentication Routes (Public)
Route::prefix('n8n/v1/auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('send-otp', [AuthController::class, 'sendOtp']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('social-login', [AuthController::class, 'socialLogin']);
});

// N8N Protected Routes (Requires Authentication)
Route::prefix('n8n/v1')->middleware('auth:sanctum')->group(function () {
    // News endpoints
    Route::get('news', [NewsController::class, 'index']);

    // Token management
    Route::get('tokens', [AuthController::class, 'tokens']);
    Route::post('tokens', [AuthController::class, 'createToken']);
    Route::delete('tokens/{tokenId}', [AuthController::class, 'revokeToken']);
    Route::post('tokens/revoke-others', [AuthController::class, 'revokeOtherTokens']);

    // Logout
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('logout-all', [AuthController::class, 'logoutAll']);
});

// Protected API routes v1 (Requires Sanctum authentication)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // User info endpoint
    Route::get('user', function () {
        return response()->json([
            'success' => true,
            'data' => auth()->user(),
        ]);
    });

    // Add your protected routes here
    // Route::apiResource('posts', PostController::class);
});