<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Comprehensive HTTP Exception Handler
        $exceptions->render(function (Throwable $e, $request) {
            // Get status code from exception
            $statusCode = 500; // Default to 500
    
            if ($e instanceof HttpException) {
                $statusCode = $e->getStatusCode();
            } elseif ($e instanceof NotFoundHttpException) {
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

                return response()->json([
                    'message' => $message,
                    'status' => $statusCode
                ], $statusCode);
            }

            // Check if custom error view exists for this subdomain and status code
            $errorView = "errors.{$subdomain}.{$statusCode}";

            if (view()->exists($errorView)) {
                return response()->view($errorView, [
                    'exception' => $e
                ], $statusCode);
            }

            // Fallback to Laravel's default error handling
            return null;
        });
    })->create();
