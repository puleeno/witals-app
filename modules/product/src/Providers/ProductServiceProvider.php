<?php

declare(strict_types=1);

namespace Modules\Product\Providers;

use Witals\Framework\Support\ServiceProvider;
use App\Http\Routing\Contracts\RouterInterface;
use Modules\Product\Controllers\ProductController;

class ProductServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $router = $this->app->make(RouterInterface::class);
        $router->get('/san-pham', [ProductController::class, 'index']);
        $router->get('/san-pham/{id}', [ProductController::class, 'show']);
    }
}