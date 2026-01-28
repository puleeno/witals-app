<?php

declare(strict_types=1);

namespace App\Foundation\Theme;

use App\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->singleton(ThemeManager::class, function ($app) {
            $manager = new ThemeManager($app);
            $manager->discover();
            
            // Set default theme from config or env
            $defaultTheme = env('THEME_ACTIVE', 'default');
            $manager->setActiveTheme($defaultTheme);
            
            return $manager;
        });
    }

    public function boot(): void
    {
        // Manager is already initialized in registration for discovery
    }
}
