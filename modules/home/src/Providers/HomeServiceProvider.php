<?php

declare(strict_types=1);

namespace Modules\Home\Providers;

use Witals\Framework\Support\ServiceProvider;
use App\Http\Routing\Contracts\RouterInterface;
use Modules\Home\Controllers\HomeController;
use Modules\Home\Sections\SectionRegistry;

class HomeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SectionRegistry::class, static fn ($app) => new SectionRegistry($app));
    }

    public function boot(): void
    {
        $router = $this->app->make(RouterInterface::class);
        $router->get($this->path(), [HomeController::class, 'index']);
    }

    private function path(): string
    {
        $file = dirname(__DIR__, 2) . '/module.json';
        if (is_file($file)) {
            $meta = json_decode((string) file_get_contents($file), true);
            $p = $meta['config']['path'] ?? null;
            if (is_string($p) && $p !== '') {
                return '/' . trim($p, '/');
            }
        }

        return '/';
    }
}