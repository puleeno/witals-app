<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\ServiceProvider;
use App\Http\Routing\Router;
use App\Http\Routing\RouterFactory;
use App\Http\Routing\RouteRegistry;
use App\Http\Routing\Contracts\RouterInterface;
use App\Http\Routing\Contracts\RouteRegistryInterface;

class RouteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // RouteRegistry — single source of truth for all routes
        $this->app->singleton(RouteRegistryInterface::class, function ($app) {
            $registry = new RouteRegistry($app);
            if (!$app->isLongRunning()) {
                $cachePath = $app->basePath('storage/framework/cache/routes.php');
                $registry->enableCache($cachePath);
            }
            return $registry;
        });

        // Router — facade over RouteRegistry with fallback chain
        $this->app->singleton(RouterInterface::class, function ($app) {
            return RouterFactory::create(
                $app,
                $app->make(\Psr\Log\LoggerInterface::class)
            );
        });

        $this->app->singleton(Router::class, function ($app) {
            return $app->make(RouterInterface::class);
        });
    }

    public function boot(): void
    {
        $router = $this->app->make(RouterInterface::class);

        $this->loadRoutes($router);
    }

    protected function loadRoutes(RouterInterface $router): void
    {
        $webFile = $this->app->basePath('routes/web.php');
        if (file_exists($webFile)) {
            require $webFile;
        }

        $apiFile = $this->app->basePath('routes/api.php');
        if (file_exists($apiFile)) {
            require $apiFile;
        }
    }
}
