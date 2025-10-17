<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\ResolveStore;
use App\Http\Middleware\EnsureStoreSelected;
use App\Http\Middleware\CheckApiKey;
use App\Http\Middleware\BlockScrapers;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        // api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'guest' => RedirectIfAuthenticated::class,
            'api.key' => CheckApiKey::class,
        ]);

        // Append to web middleware group (don't replace it!)
        $middleware->appendToGroup('web', [
                // ResolveStore::class,
                // EnsureStoreSelected::class,
            BlockScrapers::class,
        ]);

        // Ensure HandleCors is in the API middleware group (it's included by default, but being explicit)
        // Note: HandleCors is already in the global middleware stack in Laravel 11
        // This configuration ensures CORS works for your API subdomain routing
        $middleware->api(prepend: [
            // HandleCors is automatically included via global middleware
            // You can add additional API-specific middleware here if needed
        ]);

        // Redirect guests based on subdomain
        $middleware->redirectGuestsTo(function (Request $request) {
            $host = $request->getHost();
            // Redirect based on subdomain
            if (str_starts_with($host, 'admin.')) {
                return route('admin.login');
            } elseif (str_starts_with($host, 'api.') || $request->expectsJson()) {
                return null;
            } else {
                return '/';
            }
        });
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('backup:run')->dailyAt('02:00');
        $schedule->command('sitemap:generate ' . config('app.domain'))->dailyAt('03:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        if (app()->environment('production')) {
            // Comprehensive HTTP Exception Handler
            $exceptions->render(function (Throwable $e, $request) {
                // Get status code from exception
                $statusCode = 500; // Default to 500
    
                if ($e instanceof HttpException) {
                    $statusCode = $e->getStatusCode();
                } elseif ($e instanceof NotFoundHttpException || $e instanceof ModelNotFoundException) {
                    $statusCode = 404;
                } elseif ($e instanceof AccessDeniedHttpException) {
                    $statusCode = 403;
                } elseif ($e instanceof TokenMismatchException) {
                    $statusCode = 419;
                }

                $host = $request->getHost();

                // Determine subdomain
                $subdomain = 'www'; // Default to www
                if (str_starts_with($host, 'admin.')) {
                    $subdomain = 'admin';
                } elseif (str_starts_with($host, 'api.')) {
                    $subdomain = 'api';
                }

                // Handle API requests with JSON response
                if ($subdomain === 'api' || $request->expectsJson()) {
                    $message = match ($statusCode) {
                        404 => 'Resource not found',
                        403 => 'Access forbidden',
                        419 => 'CSRF token mismatch',
                        500 => 'Internal server error',
                        503 => 'Service unavailable',
                        default => $e->getMessage() ?: 'An error occurred',
                    };
                    return ApiResponseTrait::errorResponse($message, $statusCode);
                }

                // Check if custom error view exists for this subdomain and status code
                $errorView = "errors.{$subdomain}.{$statusCode}";

                if (view()->exists($errorView)) {
                    try {
                        return response()->view($errorView, [
                            'exception' => $e
                        ], $statusCode);
                    } catch (\Throwable $viewException) {
                        // If view rendering fails, return simple HTML
                        $errorTitle = match ($statusCode) {
                            404 => '404 - Not Found',
                            403 => '403 - Forbidden',
                            419 => '419 - Session Expired',
                            500 => '500 - Server Error',
                            503 => '503 - Service Unavailable',
                            default => "{$statusCode} - Error",
                        };

                        return response()->make("
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>{$errorTitle}</title>
                            <style>
                                body { font-family: sans-serif; text-align: center; padding: 50px; }
                                h1 { font-size: 4rem; margin: 0; }
                                p { font-size: 1.2rem; color: #666; }
                            </style>
                        </head>
                        <body>
                            <h1>{$statusCode}</h1>
                            <p>{$errorTitle}</p>
                            <a href='/'>Go to Homepage</a>
                        </body>
                        </html>
                    ", $statusCode);
                    }
                }

                // Fallback to Laravel's default error handling
                return null;
            });
        }

    })->create();
