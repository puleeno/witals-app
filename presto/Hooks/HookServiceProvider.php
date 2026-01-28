<?php

declare(strict_types=1);

namespace PrestoWorld\Hooks;

use Witals\Framework\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(HookManager::class, function ($app) {
            return new HookManager($app);
        });

        $this->app->alias(HookManager::class, 'hooks');
    }
}
