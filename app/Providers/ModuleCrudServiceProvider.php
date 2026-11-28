<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\ServiceProvider;
use App\Http\Routing\Contracts\RouterInterface;
use App\Foundation\Admin\ModuleCrudController;

/**
 * ModuleCrudServiceProvider — auto-registers generic dashboard CRUD + JSON API
 * routes for every enabled module that declares an `admin.crud` block in its
 * module.json. This is what lets a data module "manage its own data and show up
 * in the dashboard" without writing any routes or controllers.
 */
class ModuleCrudServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $router = $this->app->make(RouterInterface::class);

        // The {module} segment is resolved at request time by the controller,
        // which 404s if the module has no admin.crud block. One set of routes
        // covers all data modules.
        $router->get('/dashboard/{module}', [ModuleCrudController::class, 'index']);
        $router->get('/dashboard/{module}/create', [ModuleCrudController::class, 'create']);
        $router->post('/dashboard/{module}', [ModuleCrudController::class, 'store']);
        $router->get('/dashboard/{module}/{id}/edit', [ModuleCrudController::class, 'edit']);
        $router->post('/dashboard/{module}/{id}', [ModuleCrudController::class, 'update']);
        $router->post('/dashboard/{module}/{id}/delete', [ModuleCrudController::class, 'destroy']);

        $router->get('/api/{module}', [ModuleCrudController::class, 'apiList']);
    }
}