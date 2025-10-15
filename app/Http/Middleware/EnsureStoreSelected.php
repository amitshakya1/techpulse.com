<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureStoreSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $guard = null): Response
    {
        if (Auth::guard($guard)->check() && str_starts_with($request->getHost(), 'admin.')) {
            if (!session()->has('store_id')) {
                return redirect()->route('admin.stores.select')->with('warning', 'Please select a store first.');
            }
        }
        return $next($request);
    }

}
