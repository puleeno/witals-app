<?php

declare(strict_types=1);

namespace App\Foundation\Theme\Adapters;

use App\Foundation\Theme\Contracts\ThemeAdapterInterface;
use App\Foundation\Theme\Theme;

class NativeThemeAdapter implements ThemeAdapterInterface
{
    protected Theme $theme;

    public function boot(Theme $theme): void
    {
        $this->theme = $theme;
        
        $helpersPath = $theme->getPath() . '/src/helpers.php';
        if (file_exists($helpersPath)) {
            require_once $helpersPath;
        }

        // Register theme views path to the view system
        $viewPath = $theme->getPath() . '/src/views';
        if (is_dir($viewPath)) {
            \Witals\Framework\Application::getInstance()->view()->addLocation($viewPath);
            // Also register as a namespace for easy access e.g. theme::home
            \Witals\Framework\Application::getInstance()->view()->addNamespace('theme', $viewPath);
        }
    }

    public function render(string $view, array $data = []): string
    {
        $viewFactory = \Witals\Framework\Application::getInstance()->view();

        if (!$viewFactory->exists($view)) {
            // Try with theme namespace
            if ($viewFactory->exists("theme::{$view}")) {
                $view = "theme::{$view}";
            } else {
                return "Native View [{$view}] not found in theme or resources.";
            }
        }

        return $viewFactory->make($view, $data)->render();
    }

    public function getType(): string
    {
        return 'native';
    }
}
