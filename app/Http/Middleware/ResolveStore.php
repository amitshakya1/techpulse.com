<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Store;
use App\Models\BaseModel;

class ResolveStore
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        if (str_starts_with($host, 'www.')) {
            // Find store by hostname or subdomain
            $store = Store::where('shop_domain', $host)->where('status', BaseModel::STATUS_ACTIVE)->first();

            if ($store) {
                // Store current store in session or globally
                session(['store_id' => $store->id]);
                // Optional: bind globally
                // app()->instance('current_store', $store);
            }
        }

        return $next($request);
    }
}

