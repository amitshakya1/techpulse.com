<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponseTrait;

class RedirectIfAuthenticated
{
    use ApiResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $guard = null): Response
    {
        if (Auth::guard($guard)->check()) {
            $host = $request->getHost();

            if (str_starts_with($host, 'admin.')) {
                return redirect(config('app.url_admin') . '/dashboard');
            } elseif (str_starts_with($host, 'api.') || $request->expectsJson()) {
                return $this->errorResponse(
                    'You are already authenticated.',
                    403
                );
            } else {
                return redirect(config('app.url') . '/');
            }
        }

        return $next($request);
    }
}
