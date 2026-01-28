<?php

declare(strict_types=1);

namespace App\Foundation\Theme\Contracts;

interface ThemeInterface
{
    /**
     * Get theme unique identifier
     */
    public function getName(): string;

    /**
     * Get theme display name
     */
    public function getTitle(): string;

    /**
     * Get theme version
     */
    public function getVersion(): string;

    /**
     * Get theme path
     */
    public function getPath(): string;

    /**
     * Get theme type (native, classic, block)
     */
    public function getType(): string;

    /**
     * Boot the theme
     */
    public function boot(): void;

    /**
     * Check if theme is active
     */
    public function isActive(): bool;
}
