<?php

declare(strict_types=1);

namespace App\Foundation\Theme;

use App\Foundation\Application;
use App\Foundation\Theme\Contracts\ThemeAdapterInterface;
use App\Foundation\Theme\Adapters\NativeThemeAdapter;
use App\Foundation\Theme\Adapters\WordPressThemeAdapter;

class ThemeManager
{
    protected Application $app;
    protected array $themes = [];
    protected ?Theme $activeTheme = null;
    protected ?ThemeAdapterInterface $adapter = null;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function discover(): void
    {
        // 1. Native Themes
        $this->scanDirectory($this->app->basePath('themes'));

        // 2. WordPress Themes
        $this->scanDirectory($this->app->basePath('public/wp-content/themes'));
    }

    protected function scanDirectory(string $path): void
    {
        if (!is_dir($path)) return;

        foreach (scandir($path) as $dir) {
            if ($dir === '.' || $dir === '..') continue;

            $themePath = $path . '/' . $dir;
            if (is_dir($themePath)) {
                $theme = new Theme($this->app, $themePath);
                $this->themes[$theme->getName()] = $theme;
            }
        }
    }

    public function setActiveTheme(string $name): void
    {
        if (isset($this->themes[$name])) {
            $this->activeTheme = $this->themes[$name];
            $this->adapter = $this->resolveAdapter($this->activeTheme);
            $this->adapter->boot($this->activeTheme);
        }
    }

    protected function resolveAdapter(Theme $theme): ThemeAdapterInterface
    {
        return match($theme->getTypeEnum()) {
            ThemeType::NATIVE => new NativeThemeAdapter(),
            ThemeType::FSE    => new WordPressThemeAdapter($this->app, 'fse'),
            ThemeType::LEGACY => new WordPressThemeAdapter($this->app, 'legacy'),
        };
    }

    public function render(string $view, array $data = []): string
    {
        if (!$this->adapter) {
            return "Theme Engine not initialized.";
        }

        return $this->adapter->render($view, $data);
    }

    public function getActiveTheme(): ?Theme
    {
        return $this->activeTheme;
    }

    public function all(): array
    {
        return $this->themes;
    }
}
