<?php

declare(strict_types=1);

namespace App\Foundation;

use Witals\Framework\Application as BaseApplication;
use App\Contracts\ServiceProviderInterface;
use App\Foundation\Module\ModuleManager;

/**
 * PrestoWorld Application
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

    /**
     * Register a service provider
     */
    public function register(string|ServiceProviderInterface $provider): void
    {
        // If string, instantiate
        if (is_string($provider)) {
            $provider = new $provider($this);
        }

        $providerClass = get_class($provider);

        // Skip if already registered
        if (isset($this->serviceProviders[$providerClass])) {
            return;
        }

        // Check if should load
        if (!$provider->shouldLoad()) {
            return;
        }

        // Register dependencies first
        foreach ($provider->dependencies() as $dependency) {
            $this->register($dependency);
        }

        // Register the provider
        $provider->register();
        
        $this->serviceProviders[$providerClass] = $provider;
        $this->loadedProviders[$providerClass] = true;
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
                $provider->boot();
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

    /**
     * Boot the application (override parent)
     */
    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        // Discover and load modules (they register their own providers)
        if ($this->moduleManager) {
            $this->moduleManager->discover();
            $this->moduleManager->loadEnabled();
        }

        // Boot providers
        $this->bootProviders();

        // Then boot parent (lifecycle)
        parent::boot();
    }

    /**
     * Get config value with dot notation
     */
    public function config(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $file = array_shift($keys);
        
        $configPath = $this->basePath("config/{$file}.php");
        
        if (!file_exists($configPath)) {
            return $default;
        }

        $config = require $configPath;
        
        foreach ($keys as $segment) {
            if (!is_array($config) || !array_key_exists($segment, $config)) {
                return $default;
            }
            $config = $config[$segment];
        }
        
        return $config;
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
     * Customize the error log destination for PrestoWorld.
     */
    public function getErrorLogPath(): string
    {
        return $this->basePath('storage/logs/prestoworld.log');
    }
}
