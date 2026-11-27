<?php

declare(strict_types=1);

namespace Modules\News\Providers;

use Witals\Framework\Support\ServiceProvider;
use App\Http\Routing\Contracts\RouterInterface;
use Modules\News\Controllers\NewsController;

class NewsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $router = $this->app->make(RouterInterface::class);
        $router->get('/news', [NewsController::class, 'index']);
    }
}