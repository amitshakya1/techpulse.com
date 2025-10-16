<?php

use App\Http\Controllers\Api\N8N\AuthController;
use App\Http\Controllers\Api\N8N\NewsController;
use Illuminate\Support\Facades\Route;

// ============================================
// PUBLIC API ROUTES (No authentication)
// ============================================
Route::prefix('v1')->group(function () {
    Route::get('health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now(),
            'message' => 'API is running with CORS enabled',
        ]);
    });

    // CORS test endpoint
    Route::get('test-cors', function () {
        return response()->json([
            'success' => true,
            'message' => 'CORS is working correctly!',
            'origin' => request()->header('Origin'),
            'timestamp' => now(),
        ]);
    });
});

// ============================================
// API KEY PROTECTED ROUTES
// ============================================
Route::prefix('v1')->middleware('api.key')->group(function () {
    // These routes require X-API-KEY header

    // Test endpoint - shows store_id from API key
    Route::get('secure/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'API Key authentication successful!',
            'store_id' => request()->get('store_id'), // From middleware
            'timestamp' => now(),
        ]);
    });

    // Add your API key protected routes here
    // Example:
    // Route::get('products', [ProductController::class, 'index']);
    // Route::post('orders', [OrderController::class, 'store']);
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

// ============================================
// SANCTUM AUTHENTICATION ROUTES
// ============================================
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

// ============================================
// COMBINED: API KEY + SANCTUM AUTHENTICATION
// ============================================
// Use this for routes that need both store identification (via API key)
// and user authentication (via Sanctum token)
Route::prefix('v1')->middleware(['api.key', 'auth:sanctum'])->group(function () {
    // These routes require BOTH X-API-KEY header AND Bearer token

    Route::get('secure/user-store', function () {
        return response()->json([
            'success' => true,
            'message' => 'Authenticated with both API key and user token',
            'store_id' => request()->get('store_id'),
            'user' => auth()->user(),
            'timestamp' => now(),
        ]);
    });

    // Example: Store-specific user resources
    // Route::get('store/products', [StoreProductController::class, 'index']);
    // Route::post('store/orders', [StoreOrderController::class, 'store']);
});