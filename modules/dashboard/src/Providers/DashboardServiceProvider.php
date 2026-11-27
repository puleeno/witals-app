<?php

declare(strict_types=1);

namespace Modules\Dashboard\Providers;

use Witals\Framework\Support\ServiceProvider;
use App\Http\Routing\Contracts\RouterInterface;
use App\Http\Controllers\AuthController;
use Modules\Dashboard\Controllers\DashboardController;

/**
 * Wires the dashboard + auth routes into the unified RouteRegistry.
 *
 * Registered from boot() so routes exist before RouteServiceProvider loads
 * routes/web.php and before the registry is dispatched.
 */
class DashboardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Dashboard context is shared with other modules (seo, schema-org, ...).
        $this->app->singleton('dashboard.context', static fn () => new \ArrayObject([
            'title' => 'Bảng điều khiển',
        ]));
    }

    public function boot(): void
    {
        $router = $this->app->make(RouterInterface::class);

        // Auth (login / logout) — reuse the existing AuthController.
        $router->get('/login', [AuthController::class, 'showLogin']);
        $router->post('/login', [AuthController::class, 'handleLogin']);
        $router->post('/logout', [AuthController::class, 'handleLogout']);

        // Dashboard SPA shell.
        $router->get('/dashboard', [DashboardController::class, 'index']);
        $router->get('/dashboard/', [DashboardController::class, 'index']);

        // Headless JSON API consumed by the SolidJS SPA.
        $router->get('/api/dashboard/stats', [DashboardController::class, 'stats']);
    }
}