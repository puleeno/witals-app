<?php

declare(strict_types=1);

namespace App\Foundation\Theme\Contracts;

use App\Foundation\Theme\Theme;

interface ThemeAdapterInterface
{
    /**
     * Boot the theme adapter
     */
    public function boot(Theme $theme): void;

    /**
     * Render the current view
     */
    public function render(string $view, array $data = []): string;

    /**
     * Get adapter type
     */
    public function getType(): string;
}
