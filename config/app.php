<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    */
    'name' => env('APP_NAME', 'PrestoWorld'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    */
    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    */
    'debug' => env('APP_DEBUG', false) === 'true' || env('APP_DEBUG', false) === true,

    /*
    |--------------------------------------------------------------------------
    | Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */
    'providers' => [
        // Core Providers
        App\Providers\LogServiceProvider::class,
        App\Providers\HookServiceProvider::class,
        \App\Providers\DatabaseServiceProvider::class,
        \App\Foundation\Theme\ThemeServiceProvider::class,
        \App\Foundation\Debug\DebugServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        \App\Providers\ViewServiceProvider::class,
        
        // Add more providers here
    ],
];
