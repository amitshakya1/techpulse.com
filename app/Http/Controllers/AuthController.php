<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SocialLoginRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\User;
use App\Jobs\MetalRateJob;
use Laravel\Socialite\Facades\Socialite;
use App\Notifications\ResetPasswordNotification;

class AuthController extends Controller
{
    protected string $subdomain;

    public function __construct()
    {
        // Cache subdomain for reuse in all methods
        $this->subdomain = getSubDomain();
    }

    /**
     * Render login view
     */
    public function showLogin()
    {
        return $this->renderAuthView('login');
    }

    /**
     * Render register view
     */
    public function showRegister()
    {
        return $this->renderAuthView('register');
    }

    /**
     * Render forgot password view
     */
    public function showForgotPassword()
    {
        return $this->renderAuthView('forgot-password');
    }

    /**
     * Render reset password view
     */
    public function showResetPassword(string $token)
    {
        return $this->renderAuthView('reset-password', compact('token'));
    }

    /**
     * Helper to resolve correct subdomain-based view path
     */
    protected function renderAuthView(string $page, array $data = [])
    {
        // Default fallback if no subdomain or view not found
        $sub = $this->subdomain ?: 'default';

        $path = "{$sub}.auth.{$page}";

        if (!view()->exists($path)) {
            $path = "auth.www.{$page}";
        }

        return view($path, $data);
    }
    /**
     * Register new admin user
     */
    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            Auth::login($user, $request->boolean('remember', false));

            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();

            return $this->successResponse([
                'user' => $user,
                'redirect' => route('admin.dashboard'),
            ], 'Registration successful');

        } catch (\Exception $e) {
            return $this->errorResponse('Registration failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Login with email & password
     */
    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return $this->errorResponse('Invalid credentials.', 401);
            }

            // Login the user with remember me option
            Auth::login($user, $request->boolean('remember', false));

            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();

            return $this->successResponse([
                'user' => $user,
                'redirect' => route('admin.dashboard'),
            ], 'Login successful');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Forgot Password - send reset link
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $validated = $request->validated();

            $status = Password::sendResetLink($validated, function ($user, $token) {
                $user->notify(new ResetPasswordNotification($token, $this->subdomain));
            });

            if ($status === Password::RESET_LINK_SENT) {
                return $this->successResponse(null, 'Password reset link sent to your email');
            } else {
                return $this->errorResponse(__($status), 400);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send reset link: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Reset password using token
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $validated = $request->validated();

            $status = Password::reset(
                $validated,
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return $this->successResponse(null, 'Password reset successful');
            } else {
                return $this->errorResponse(__($status), 400);
            }

        } catch (\Exception $e) {
            return $this->errorResponse('Password reset failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Social login (Google / Facebook example placeholder)
     */
    public function socialLogin(SocialLoginRequest $request)
    {
        try {
            $validated = $request->validated();

            // Example: verify token with social provider
            // You would use Socialite here:
            $socialUser = Socialite::driver($validated['provider'])->userFromToken($validated['access_token']);

            // For demo:
            $socialUser = [
                'email' => 'social@example.com',
                'name' => 'Social User'
            ];

            $user = User::firstOrCreate(
                ['email' => $socialUser['email']],
                ['name' => $socialUser['name'], 'password' => Hash::make(Str::random(16))]
            );

            Auth::login($user);

            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();

            return $this->successResponse([
                'user' => $user,
                'redirect' => route('admin.dashboard'),
            ], 'Social login successful');

        } catch (\Exception $e) {
            return $this->errorResponse('Social login failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Login with OTP (send)
     */
    public function sendOtp(SendOtpRequest $request)
    {
        try {
            $validated = $request->validated();

            $otp = rand(100000, 999999);

            // Store in DB or cache
            cache()->put('otp_' . $validated['phone'], $otp, now()->addMinutes(5));

            // Send SMS logic (Twilio / MSG91 / etc)
            // SmsService::send($validated['phone'], "Your OTP is $otp");

            return $this->successResponse(null, 'OTP sent successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send OTP: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Verify OTP & login
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        try {
            $validated = $request->validated();

            $storedOtp = cache()->get('otp_' . $validated['phone']);

            if ($storedOtp != $validated['otp']) {
                return $this->errorResponse('Invalid OTP', 422);
            }

            $user = User::firstOrCreate(
                ['phone' => $validated['phone']],
                ['name' => 'User ' . $validated['phone'], 'password' => Hash::make(Str::random(12))]
            );

            cache()->forget('otp_' . $validated['phone']);

            Auth::login($user);

            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();

            return $this->successResponse([
                'user' => $user,
                'redirect' => route('admin.dashboard'),
            ], 'Login successful');

        } catch (\Exception $e) {
            return $this->errorResponse('OTP verification failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        try {
            // Logout the user from session
            Auth::guard('web')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return redirect('/');

        } catch (\Exception $e) {
            return $this->errorResponse('Logout failed: ' . $e->getMessage(), 500);
        }
    }
}
