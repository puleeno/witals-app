<?php

declare(strict_types=1);

namespace Modules\Seo\Providers;

use Witals\Framework\Support\ServiceProvider;
use App\Foundation\Modules\ModuleContext;
use Modules\Seo\Services\SeoService;

/**
 * Wires the SEO module into the shared ModuleContext:
 *   - render.head  → injects canonical / robots / Open Graph / Twitter tags
 *   - edit.fields  → exposes SEO fields to dashboard edit screens
 */
class SeoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SeoService::class, function ($app) {
            return new SeoService(
                $app,
                $app->make(ModuleContext::class),
                $app->make(\Cycle\Database\DatabaseProviderInterface::class),
            );
        });
    }

    public function boot(): void
    {
        $context = $this->app->make(ModuleContext::class);
        $seo = $this->app->make(SeoService::class);

        $context->on('render.head', static fn ($payload) => $seo->renderHead($payload));
        $context->on('edit.fields', static fn ($payload) => $seo->editFields($payload));
    }
}