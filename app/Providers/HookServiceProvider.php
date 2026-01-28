<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\ServiceProvider;
use PrestoWorld\Hooks\HookManager;

class HookServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\Witals\Framework\Support\EnvironmentDetector::class, function ($app) {
            return new \Witals\Framework\Support\EnvironmentDetector($app);
        });

        // 1. Determine Registry Implementation
        $this->app->singleton(\PrestoWorld\Contracts\Hooks\Registries\HookRegistryInterface::class, function ($app) {
            $detector = $app->make(\Witals\Framework\Support\EnvironmentDetector::class);

            if ($detector->isModern()) {
                return new \PrestoWorld\Hooks\Registries\SwooleTableRegistry();
            }

            if ($detector->hasAPCu()) {
                return new \PrestoWorld\Hooks\Registries\APCuRegistry();
            }

            // Fallback for Shared Hosting (Restricted)
            return new \PrestoWorld\Hooks\Registries\FileCompiledRegistry(
                path_join($app->basePath(), 'storage/framework/hooks.php')
            );
        });

        // 2. Determine Dispatcher Implementation
        $this->app->singleton(\PrestoWorld\Contracts\Hooks\Dispatchers\ActionDispatcherInterface::class, function ($app) {
            $detector = $app->make(\Witals\Framework\Support\EnvironmentDetector::class);

            if ($detector->isModern() && $app->has('swoole.server')) {
                return new \PrestoWorld\Hooks\Dispatchers\SwooleTaskDispatcher($app, $app->make('swoole.server'));
            }

            return new \PrestoWorld\Hooks\Dispatchers\SyncDispatcher($app);
        });

        // 3. Determine State Driver Implementation
        $this->app->singleton(\PrestoWorld\Contracts\Hooks\StateDriverInterface::class, function ($app) {
            $detector = $app->make(\Witals\Framework\Support\EnvironmentDetector::class);

            if ($detector->isModern()) {
                return new \PrestoWorld\Hooks\State\SwooleStateDriver();
            }

            if ($detector->hasAPCu()) {
                return new \PrestoWorld\Hooks\State\APCuStateDriver();
            }

            return new \PrestoWorld\Hooks\State\ArrayStateDriver();
        });

        // 4. Register State Bridge
        $this->app->singleton(\PrestoWorld\Hooks\State\StateBridge::class, function ($app) {
            return new \PrestoWorld\Hooks\State\StateBridge(
                $app->make(\PrestoWorld\Contracts\Hooks\StateDriverInterface::class)
            );
        });

        // 5. Register Hook Manager
        $this->app->singleton(HookManager::class, function ($app) {
            return new HookManager(
                $app,
                $app->make(\PrestoWorld\Contracts\Hooks\Registries\HookRegistryInterface::class),
                $app->make(\PrestoWorld\Contracts\Hooks\Dispatchers\ActionDispatcherInterface::class)
            );
        });

        $this->app->alias(HookManager::class, 'hooks');
    }

    public function boot(): void
    {
        // Add cleanup hook to Application lifecycle
        $this->app->terminating(function () {
            if ($this->app->has(HookManager::class)) {
                $this->app->make(HookManager::class)->flushCache();
            }
        });
    }
}
