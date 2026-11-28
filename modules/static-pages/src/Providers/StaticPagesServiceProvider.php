<?php

declare(strict_types=1);

namespace Modules\StaticPages\Providers;

use App\Http\Routing\Contracts\RouterInterface;
use Modules\StaticPages\Controllers\StaticPagesController;
use Witals\Framework\Support\ServiceProvider;

class StaticPagesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $router = $this->app->make(RouterInterface::class);
        $router->get('/static-pages/{alias}', [StaticPagesController::class, 'show']);
    }
}