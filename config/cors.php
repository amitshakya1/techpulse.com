<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    */

    'paths' => ['v1/*', 'n8n/*', 'sanctum/csrf-cookie'],
    // This applies CORS to all routes under v1/ and n8n/

    'allowed_methods' => ['*'], // or ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS']

    'allowed_origins' => [
        // Development
        'http://localhost:3000',
        'http://localhost:8000',
        'http://127.0.0.1:3000',
        'http://127.0.0.1:8000',
        'http://www.techpulse.test:8000',
        'http://admin.techpulse.test:8000',
        'http://api.techpulse.test:8000',
        // Add your production domains here
        // 'https://yourdomain.com',
        // 'https://www.yourdomain.com',
    ],

    'allowed_origins_patterns' => [
        // Use patterns for dynamic subdomains in production
        // '/^https:\/\/.*\.yourdomain\.com$/',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [
        'Authorization',
        'Content-Type',
    ],

    'max_age' => 86400, // Cache preflight response for 24 hours

    'supports_credentials' => true,
];
