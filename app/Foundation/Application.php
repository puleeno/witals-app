<?php

declare(strict_types=1);

namespace App\Foundation;

use Psr\Log\LoggerInterface;
use Witals\Framework\Application as BaseApplication;
use App\Foundation\Config\ConfigRepository;
use App\Foundation\Module\ModuleManager;

/**
 * Application
 * 
 * Extends Witals Framework Application with Service Provider support
 */
class Application extends BaseApplication
{
    /**
     * Module Manager
     */
    protected ?ModuleManager $moduleManager = null;

    /**
     * Registered service providers
     */
    protected array $serviceProviders = [];

    /**
     * Loaded service providers
     */
    protected array $loadedProviders = [];

    /**
     * Booted service providers
     */
    protected array $bootedProviders = [];
    protected array $config = [];

    protected ?ConfigRepository $configRepository = null;

    /**
     * Register a service provider
     */
    public function register(object|string $provider): mixed
    {
        // If string, instantiate
        if (is_string($provider)) {
            try {
                $provider = new $provider($this);
            } catch (\Throwable $e) {
                $this->logError(
                    '[App] Failed to instantiate provider {provider}: {message}',
                    ['provider' => is_string($provider) ? $provider : get_class($provider), 'message' => $e->getMessage()]
                );
                return null;
            }
        }

        $providerClass = get_class($provider);

        // Skip if already registered
        if (isset($this->serviceProviders[$providerClass])) {
            return $this->serviceProviders[$providerClass];
        }

        // Register the provider
        if (method_exists($provider, 'register')) {
            try {
                $provider->register();
            } catch (\Throwable $e) {
                $this->logError(
                    '[App] Failed to register provider {provider}: {message}',
                    ['provider' => $providerClass, 'message' => $e->getMessage()]
                );
                return null;
            }
        }
        
        $this->serviceProviders[$providerClass] = $provider;
        $this->loadedProviders[$providerClass] = $provider;

        if ($this->booted && method_exists($provider, 'boot')) {
            try {
                $provider->boot();
            } catch (\Throwable $e) {
                $this->logError(
                    '[App] Failed to boot provider {provider}: {message}',
                    ['provider' => $providerClass, 'message' => $e->getMessage()]
                );
            }
        }

        return $provider;
    }

    /**
     * Register multiple providers
     */
    public function registerProviders(array $providers): void
    {
        foreach ($providers as $provider) {
            $this->register($provider);
        }
    }

    /**
     * Boot all registered providers
     */
    protected function bootProviders(): void
    {
        foreach ($this->serviceProviders as $class => $provider) {
            if (!isset($this->bootedProviders[$class])) {
                try {
                    $provider->boot();
                } catch (\Throwable $e) {
                    $this->logError(
                        '[App] Failed to boot provider {provider}: {message}',
                        ['provider' => $class, 'message' => $e->getMessage()]
                    );
                }
                $this->bootedProviders[$class] = true;
            }
        }
    }

    /**
     * Data to be bound before boot
     */
    public function registerCoreContainerAliases(): void
    {
        parent::registerCoreContainerAliases();

        // Initialize Module Manager
        $this->moduleManager = new ModuleManager($this);
        $this->instance(ModuleManager::class, $this->moduleManager);
    }

    public function registerConfiguredProviders(): void
    {
        parent::registerConfiguredProviders();

        $this->singleton(
            \Witals\Framework\Contracts\Http\Kernel::class,
            \App\Http\Kernel::class
        );

        $this->registerProviders([
            \Witals\Framework\Auth\AuthServiceProvider::class,
            \App\Providers\LogServiceProvider::class,
            \App\Providers\DatabaseServiceProvider::class,
            \App\Providers\ViewServiceProvider::class,
            \App\Providers\RouteServiceProvider::class,
            \App\Providers\ConsoleServiceProvider::class,
            \App\Providers\AppServiceProvider::class,
        ]);
    }

    /**
     * Boot the application (override parent)
     */
    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        // Run all registered bootstrappers (including those added by packages)
        $this->bootstrap();

        // Discover and load modules (they register their own providers)
        if ($this->moduleManager) {
            $this->moduleManager->discover();
            $this->moduleManager->loadEnabled();

            // Register module routes into unified RouteRegistry
            if ($this->has(\App\Http\Routing\Contracts\RouteRegistryInterface::class)) {
                $registry = $this->make(\App\Http\Routing\Contracts\RouteRegistryInterface::class);

                // Delegate to Witals ModuleManager (scans framework/presto/modules/)
                if ($this->has(\Witals\Framework\Module\ModuleManager::class)) {
                    $witalsManager = $this->make(\Witals\Framework\Module\ModuleManager::class);
                    $witalsManager->registerModuleRoutes($registry);
                }

                // Admin auth only applies to admin routes — lazy-load
                $registry->addMiddlewareFor(
                    \App\Http\Middleware\AdminAuthMiddleware::class,
                    only: ['/dashboard', '/api/admin'],
                );
                $registry->addMiddleware(\App\Http\Middleware\CorsMiddleware::class);
                $registry->addMiddleware(\App\Http\Middleware\LocaleMiddleware::class);
            }
        }

        // Boot providers
        $this->bootProviders();

        // Then boot parent (lifecycle)
        parent::boot();
    }

    /**
     * Set config path(s) for the application.
     *
     * Each path can be absolute or relative to basePath.
     * Supports PHP and JSON config files.
     */
    public function setConfigPaths(string|array $paths): void
    {
        $paths = is_array($paths) ? $paths : [$paths];
        $this->resolveConfigRepository();
        $this->configRepository->setPaths(
            array_map(fn (string $p) => $this->resolveConfigPath($p), $paths)
        );
        $this->config = [];
    }

    /**
     * Add an additional config path.
     */
    public function addConfigPath(string $path): void
    {
        $this->resolveConfigRepository();
        $this->configRepository->addPath($this->resolveConfigPath($path));
        $this->config = [];
    }

    /**
     * Get the ConfigRepository instance.
     */
    public function getConfigRepository(): ConfigRepository
    {
        $this->resolveConfigRepository();
        return $this->configRepository;
    }

    /**
     * Get config value with dot notation.
     *
     * Looks up config files in registered paths (default: config/).
     * Supports PHP files returning arrays and JSON files.
     */
    public function config(string $key, $default = null)
    {
        $this->resolveConfigRepository();

        $keys = explode('.', $key);
        $file = array_shift($keys);

        if (isset($this->config[$file])) {
            $config = $this->config[$file];
        } else {
            $config = $this->configRepository->load($file);
            if ($config === null) {
                return $default;
            }
            $this->config[$file] = $config;
        }

        foreach ($keys as $segment) {
            if (!is_array($config) || !array_key_exists($segment, $config)) {
                return $default;
            }
            $config = $config[$segment];
        }

        return $config;
    }

    private function resolveConfigRepository(): void
    {
        if ($this->configRepository === null) {
            $this->configRepository = new ConfigRepository(
                $this->resolveConfigPath('config')
            );

            $cachePath = $this->basePath('bootstrap/cache/config.php');
            if (file_exists($cachePath)) {
                $this->configRepository->setCachePath($cachePath);
            }
        }
    }

    private function resolveConfigPath(string $path): string
    {
        return str_starts_with($path, '/') || str_starts_with($path, '.')
            ? $path
            : $this->basePath($path);
    }

    /**
     * Get all registered providers
     */
    public function getProviders(): array
    {
        return $this->serviceProviders;
    }

    /**
     * Check if provider is registered
     */
    public function hasProvider(string $provider): bool
    {
        return isset($this->serviceProviders[$provider]);
    }

    /**
     * Customize the error log destination.
     */
    public function getErrorLogPath(): string
    {
        return $this->basePath('storage/logs/app.log');
    }

    /**
     * "Extend" an abstract type in the container.
     */
    public function extend(string $abstract, \Closure $closure): void
    {
        parent::extend($abstract, $closure);
    }

    /**
     * Log an error via LoggerInterface if available, fall back to error_log.
     */
    private function logError(string $message, array $context = []): void
    {
        if ($this->has(LoggerInterface::class)) {
            try {
                $this->make(LoggerInterface::class)->error($message, $context);
                return;
            } catch (\Throwable) {
                // Fall through to error_log
            }
        }
        error_log('[' . ($context['provider'] ?? 'App') . '] ' . $message);
    }
}
