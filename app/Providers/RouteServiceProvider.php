<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\ServiceProvider;

/**
 * Route Service Provider
 */
class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register routes here
        // For now, routes are handled in HTTP Kernel
    }
}
