<?php

declare(strict_types=1);

namespace Modules\Pages\Providers;

use App\Http\Routing\Contracts\RouteRegistryInterface;
use App\Http\Routing\Contracts\RouterInterface;
use Modules\Pages\Controllers\PagesController;
use Witals\Framework\Support\ServiceProvider;

class PagesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        /** @var RouteRegistryInterface $registry */
        $registry = $this->app->make(RouteRegistryInterface::class);
        // Catch-all single-segment, lowest priority — chỉ khớp khi không route native nào khớp.
        $registry->addRoute(
            'GET',
            '/{alias}',
            [PagesController::class, 'show'],
            RouteRegistryInterface::PRIORITY_FALLBACK
        );
        // Fallback cuối cùng cho path không khớp (multi-segment) → 404.
        $registry->addRoute(
            'GET',
            '/{path:.*}',
            [PagesController::class, 'fallback'],
            RouteRegistryInterface::PRIORITY_FALLBACK
        );
    }
}