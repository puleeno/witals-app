<?php

declare(strict_types=1);

namespace Modules\SchemaOrg\Providers;

use Witals\Framework\Support\ServiceProvider;
use App\Foundation\Modules\ModuleContext;
use Modules\SchemaOrg\Services\SchemaOrgService;
use Modules\Seo\Services\SeoService;

/**
 * Registers the schema.org JSON-LD renderer on the shared `render.head` hook.
 */
class SchemaOrgServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SchemaOrgService::class, function ($app) {
            $seo = null;
            try {
                $seo = $app->make(SeoService::class);
            } catch (\Throwable) {
                $seo = null;
            }

            return new SchemaOrgService($app, $app->make(ModuleContext::class), $seo);
        });
    }

    public function boot(): void
    {
        $context = $this->app->make(ModuleContext::class);
        $schema = $this->app->make(SchemaOrgService::class);

        $context->on('render.head', static fn ($payload) => $schema->renderHead($payload));
    }
}