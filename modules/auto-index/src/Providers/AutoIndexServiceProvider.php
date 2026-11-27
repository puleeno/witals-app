<?php

declare(strict_types=1);

namespace Modules\AutoIndex\Providers;

use Witals\Framework\Support\ServiceProvider;
use App\Http\Routing\Contracts\RouterInterface;
use Modules\AutoIndex\Services\AutoIndexService;
use Modules\AutoIndex\Controllers\AutoIndexController;

/**
 * Wires the auto-index service and its JSON API routes.
 */
class AutoIndexServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AutoIndexService::class, function ($app) {
            return new AutoIndexService($app, $app->make(\Cycle\Database\DatabaseProviderInterface::class));
        });
    }

    public function boot(): void
    {
        $router = $this->app->make(RouterInterface::class);

        $router->post('/api/auto-index/submit', [AutoIndexController::class, 'submit']);
        $router->get('/api/auto-index/status', [AutoIndexController::class, 'status']);
    }
}