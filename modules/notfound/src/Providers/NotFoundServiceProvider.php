<?php

declare(strict_types=1);

namespace Modules\NotFound\Providers;

use App\Http\Routing\Contracts\RouterInterface;
use Modules\NotFound\Controllers\NotFoundController;
use Witals\Framework\Support\ServiceProvider;

class NotFoundServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $router = $this->app->make(RouterInterface::class);
        $router->get('/404', [NotFoundController::class, 'index']);
    }
}