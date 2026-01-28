<?php

declare(strict_types=1);

namespace App\Foundation\Module;

/**
 * Module Interface
 */
interface ModuleInterface
{
    /**
     * Get module unique name
     */
    public function getName(): string;

    /**
     * Get module version
     */
    public function getVersion(): string;

    /**
     * Get module description
     */
    public function getDescription(): string;

    /**
     * Get module type (core, optional, wordpress)
     */
    public function getType(): string;

    /**
     * Get module priority (lower runs first)
     */
    public function getPriority(): int;

    /**
     * Get module requirements
     * 
     * @return array{php: string, modules: string[]}
     */
    public function getRequirements(): array;

    /**
     * Get service providers provided by this module
     * 
     * @return string[]
     */
    public function getProviders(): array;

    /**
     * Check if module is enabled
     */
    public function isEnabled(): bool;

    /**
     * Boot the module
     */
    public function boot(): void;
}
