<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'domain' => env('APP_DOMAIN', 'website.test'),
    'url' => env('APP_URL', 'http://www.website.test'),
    'url_admin' => env('APP_URL_ADMIN', 'http://admin.website.test'),
    'url_api' => env('APP_URL_API', 'http://api.website.test'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    'brand' => [
        'favicon' => env('APP_FAVICON'),
        'logo' => env('APP_LOGO'),
        'logo2' => env('APP_LOGO2'),

        'email' => env('APP_CONTACT_EMAIL'),
        'email2' => env('APP_CONTACT_EMAIL2'),
        'phone' => env('APP_CONTACT_PHONE'),
        'phone2' => env('APP_CONTACT_PHONE2'),
        'address' => env('APP_CONTACT_ADDRESS'),
        'address2' => env('APP_CONTACT_ADDRESS2'),

        'gst' => [
            'name' => env('APP_GST_NAME'),
            'number' => env('APP_GST_NUMBER'),
            'address' => env('APP_GST_ADDRESS'),
        ],

        'social' => [
            'facebook' => env('APP_SOCIAL_FACEBOOK'),
            'instagram' => env('APP_SOCIAL_INSTAGRAM'),
            'twitter' => env('APP_SOCIAL_TWITTER'),
            'linkedin' => env('APP_SOCIAL_LINKEDIN'),
            'youtube' => env('APP_SOCIAL_YOUTUBE'),
            'whatsapp' => env('APP_SOCIAL_WHATSAPP'),
            'telegram' => env('APP_SOCIAL_TELEGRAM'),
        ],
    ],

];
