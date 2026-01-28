<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Service Provider Contract
 * 
 * All service providers must implement this interface
 */
interface ServiceProviderInterface
{
    /**
     * Register services into the container
     * This runs BEFORE boot()
     */
    public function register(): void;

    /**
     * Boot services after all providers are registered
     * This runs AFTER all register() calls
     */
    public function boot(): void;

    /**
     * Get provider dependencies
     * 
     * @return array<class-string<ServiceProviderInterface>>
     */
    public function dependencies(): array;

    /**
     * Check if this provider should be loaded
     */
    public function shouldLoad(): bool;
}
