<?php

declare(strict_types=1);

namespace Modules\Search\Providers;

use App\Http\Routing\Contracts\RouterInterface;
use Modules\Search\Controllers\SearchController;
use Witals\Framework\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $router = $this->app->make(RouterInterface::class);
        $router->get('/tim-kiem', [SearchController::class, 'index']);
    }
}