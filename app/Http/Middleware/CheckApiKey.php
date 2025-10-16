<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiKey;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('X-API-KEY');

        $apiKey = ApiKey::where('api_key', $key)
            ->where('status', 'active')
            ->first();

        if (!$apiKey) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Attach store_id to request for later use
        $request->merge(['store_id' => $apiKey->store_id]);
        session(['store_id' => $apiKey->store_id]);
        return $next($request);
    }
}
