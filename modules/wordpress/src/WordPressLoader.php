<?php

declare(strict_types=1);

namespace PrestoWorld\WordPress;

use Witals\Framework\Application;

/**
 * WordPress Loader
 */
class WordPressLoader
{
    private Application $app;
    private string $wpPath;
    private bool $loaded = false;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->wpPath = $app->basePath('public');
    }

    public function load(): bool
    {
        if ($this->loaded) {
            return true;
        }

        if (!file_exists($this->wpPath . '/wp-load.php')) {
            // WordPress not installed or path incorrect
            return false;
        }

        $this->defineWordPressConstants();

        if (!$this->loadWordPressConfig()) {
            return false;
        }

        require_once $this->wpPath . '/wp-load.php';

        $this->loaded = true;
        return true;
    }

    private function defineWordPressConstants(): void
    {
        if (!defined('ABSPATH')) {
            define('ABSPATH', $this->wpPath . '/');
        }

        if (!defined('DISABLE_WP_CRON')) {
            define('DISABLE_WP_CRON', true);
        }

        if (!defined('WP_DEBUG')) {
            define('WP_DEBUG', env('APP_DEBUG', false));
        }
    }

    private function loadWordPressConfig(): bool
    {
        $configPath = $this->wpPath . '/wp-config.php';
        if (file_exists($configPath)) {
            require_once $configPath;
            return true;
        }
        return false;
    }

    public function isLoaded(): bool
    {
        return $this->loaded;
    }

    public function getWordPressPath(): string
    {
        return $this->wpPath;
    }
}
