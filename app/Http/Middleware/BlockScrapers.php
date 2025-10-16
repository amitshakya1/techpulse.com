<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockScrapers
{
    protected $blockedAgents = [
        'HTTrack',
        'Wget',
        'curl',
        'python-requests',
        'libwww-perl',
        'Java/',
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $agent = $request->userAgent() ?? '';

        // Block known scraper UAs
        foreach ($this->blockedAgents as $blocked) {
            if (stripos($agent, $blocked) !== false) {
                abort(403, 'Scrapers not allowed');
            }
        }

        // Optional: block headless browsers
        if (!empty($request->header('Sec-Ch-Ua-Platform')) && $request->header('Sec-Ch-Ua-Platform') === 'Headless') {
            abort(403, 'Headless browsers blocked');
        }
        return $next($request);
    }
}
