<?php

declare(strict_types=1);

namespace PrestoWorld\WordPress\Providers;

use App\Support\ServiceProvider;
use PrestoWorld\WordPress\WordPressLoader;
use PrestoWorld\WordPress\WordPressBridge;

class WordPressServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Load WordPress Helpers
        $helpers = __DIR__ . '/../helpers.php';
        if (file_exists($helpers)) {
            require_once $helpers;
        }

        // Register WordPress Loader
        $this->singleton(WordPressLoader::class, function ($app) {
            return new WordPressLoader($app);
        });
        
        // Register WordPress Bridge
        $this->singleton(WordPressBridge::class, function ($app) {
            return new WordPressBridge(
                $app,
                $app->make(WordPressLoader::class)
            );
        });
    }

    public function boot(): void
    {
        // We will move WordPress loading logic here or in middleware
    }
}
