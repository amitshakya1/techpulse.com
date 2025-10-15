<?php

namespace App\Http\Controllers\Api\N8N;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Register new admin user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
        ]);

        $token = $user->createToken('admin-auth')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Registration successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * Login with email & password - API Version (Token-based)
     */
    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return $this->errorResponse('Invalid credentials.', 401);
            }

            // Delete old tokens (optional - for single device login)
            // $user->tokens()->delete();

            // Create a new token
            $token = $user->createToken('api-token')->plainTextToken;

            return $this->successResponse([
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ], 'Login successful');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Forgot Password - send reset link
     */
    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink($validated);

        return response()->json([
            'status' => $status === Password::RESET_LINK_SENT,
            'message' => __($status),
        ]);
    }

    /**
     * Reset password using token
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $validated,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return response()->json([
            'status' => $status === Password::PASSWORD_RESET,
            'message' => __($status),
        ]);
    }

    /**
     * Social login (Google / Facebook example placeholder)
     */
    public function socialLogin(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|in:google,facebook',
            'access_token' => 'required|string',
        ]);

        // Example: verify token with social provider
        // You would use Socialite here:
        // $socialUser = Socialite::driver($validated['provider'])->userFromToken($validated['access_token']);

        // For demo:
        $socialUser = [
            'email' => 'social@example.com',
            'name' => 'Social User'
        ];

        $user = User::firstOrCreate(
            ['email' => $socialUser['email']],
            ['name' => $socialUser['name'], 'password' => Hash::make(Str::random(16))]
        );

        $token = $user->createToken('admin-auth')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Social login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * Login with OTP (send)
     */
    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|min:10|max:15',
        ]);

        $otp = rand(100000, 999999);

        // Store in DB or cache
        cache()->put('otp_' . $validated['phone'], $otp, now()->addMinutes(5));

        // Send SMS logic (Twilio / MSG91 / etc)
        // SmsService::send($validated['phone'], "Your OTP is $otp");

        return response()->json([
            'status' => true,
            'message' => 'OTP sent successfully',
        ]);
    }

    /**
     * Verify OTP & login
     */
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|min:10|max:15',
            'otp' => 'required|digits:6',
        ]);

        $storedOtp = cache()->get('otp_' . $validated['phone']);

        if ($storedOtp != $validated['otp']) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP',
            ], 422);
        }

        $user = User::firstOrCreate(
            ['phone' => $validated['phone']],
            ['name' => 'User ' . $validated['phone'], 'password' => Hash::make(Str::random(12))]
        );

        cache()->forget('otp_' . $validated['phone']);

        $token = $user->createToken('admin-auth')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * Logout - revoke current token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logged out successfully');
    }

    /**
     * Logout from all devices - revoke all tokens
     */
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->successResponse(null, 'Logged out from all devices successfully');
    }

    /**
     * Get all user tokens
     */
    public function tokens(Request $request)
    {
        $tokens = $request->user()->tokens->map(function ($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'abilities' => $token->abilities,
                'created_at' => $token->created_at,
                'last_used_at' => $token->last_used_at,
            ];
        });

        return $this->successResponse($tokens, 'Tokens retrieved successfully');
    }

    /**
     * Create a new token (for generating additional tokens)
     */
    public function createToken(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abilities' => 'sometimes|array',
            'expires_at' => 'sometimes|date|after:now',
        ]);

        $token = $request->user()->createToken(
            $validated['name'],
            $validated['abilities'] ?? ['*'],
            $validated['expires_at'] ?? null
        )->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'type' => 'Bearer',
        ], 'Token created successfully');
    }

    /**
     * Revoke a specific token
     */
    public function revokeToken(Request $request, $tokenId)
    {
        $request->user()->tokens()->where('id', $tokenId)->delete();

        return $this->successResponse(null, 'Token revoked successfully');
    }

    /**
     * Revoke all tokens except current one
     */
    public function revokeOtherTokens(Request $request)
    {
        $request->user()->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

        return $this->successResponse(null, 'All other tokens revoked successfully');
    }
}
