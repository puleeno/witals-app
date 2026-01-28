<?php

declare(strict_types=1);

namespace App\Support;

use App\Contracts\ServiceProviderInterface;
use Witals\Framework\Application;

/**
 * Abstract Service Provider
 * 
 * Base class for all service providers
 */
abstract class ServiceProvider implements ServiceProviderInterface
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register services
     */
    public function register(): void
    {
        // Override in child classes
    }

    /**
     * Boot services
     */
    public function boot(): void
    {
        // Override in child classes
    }

    /**
     * Dependencies
     */
    public function dependencies(): array
    {
        return [];
    }

    /**
     * Should load
     */
    public function shouldLoad(): bool
    {
        return true;
    }

    /**
     * Helper: Bind singleton
     */
    protected function singleton(string $abstract, $concrete = null): void
    {
        $this->app->singleton($abstract, $concrete);
    }

    /**
     * Helper: Bind instance
     */
    protected function instance(string $abstract, $instance): void
    {
        $this->app->instance($abstract, $instance);
    }

    /**
     * Helper: Bind
     */
    protected function bind(string $abstract, $concrete = null): void
    {
        $this->app->bind($abstract, $concrete);
    }

    /**
     * Helper: Get config value
     */
    protected function config(string $key, $default = null)
    {
        return $this->app->config($key, $default);
    }
}
