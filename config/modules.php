<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | PrestoWorld Modules
    |--------------------------------------------------------------------------
    |
    | Define the status of each module.
    | - true: Enabled
    | - false: Disabled
    | - null: Use module.json default
    |
    */
    'enabled' => [
        'database' => true,
        'cache' => true,
        'auth' => true,
        'wordpress' => env('WORDPRESS_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Paths
    |--------------------------------------------------------------------------
    */
    'paths' => [
        'modules' => base_path('modules'),
    ],
];
