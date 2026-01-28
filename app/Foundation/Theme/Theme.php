<?php

declare(strict_types=1);

namespace App\Foundation\Theme;

use App\Foundation\Application;
use App\Foundation\Theme\Contracts\ThemeInterface;

class Theme implements ThemeInterface
{
    protected Application $app;
    protected string $path;
    protected array $metadata;
    protected ThemeType $type;
    protected bool $booted = false;

    public function __construct(Application $app, string $path, array $metadata = [])
    {
        $this->app = $app;
        $this->path = $path;
        $this->metadata = !empty($metadata) ? $metadata : $this->loadMetadata();
        $this->type = $this->detectType();
    }

    protected function loadMetadata(): array
    {
        $metadata = [
            'name' => basename($this->path),
            'title' => ucwords(str_replace(['-', '_'], ' ', basename($this->path))),
            'version' => '1.0.0',
            'description' => '',
            'author' => 'Unknown'
        ];

        // 1. Check Native theme.json
        if (file_exists($this->path . '/theme.json')) {
            $json = json_decode(file_get_contents($this->path . '/theme.json'), true);
            if ($json) {
                return array_merge($metadata, $json);
            }
        }

        // 2. Check WordPress style.css headers
        if (file_exists($this->path . '/style.css')) {
            $content = file_get_contents($this->path . '/style.css');
            $headers = [
                'title'       => 'Theme Name',
                'version'     => 'Version',
                'description' => 'Description',
                'author'      => 'Author'
            ];

            foreach ($headers as $key => $header) {
                if (preg_match('/' . $header . ':\s*(.*)$/m', $content, $matches)) {
                    $metadata[$key] = trim($matches[1]);
                }
            }
        }

        return $metadata;
    }

    protected function detectType(): ThemeType
    {
        $themeJsonPath = $this->path . '/theme.json';
        
        // Native Priority: If engine is prestoworld or it has our specific theme.json format
        if (file_exists($themeJsonPath)) {
            $config = json_decode(file_get_contents($themeJsonPath), true);
            if (isset($config['engine']) && $config['engine'] === 'prestoworld') {
                return ThemeType::NATIVE;
            }
        }

        // FSE Detection: theme.json (standard WP) or templates directory
        if (is_dir($this->path . '/templates')) {
            return ThemeType::FSE;
        }

        // Classic WordPress themes don't have templates dir but have style.css + index.php
        return ThemeType::LEGACY;
    }

    public function getName(): string
    {
        return $this->metadata['name'] ?? basename($this->path);
    }

    public function getTitle(): string
    {
        return $this->metadata['title'] ?? $this->getName();
    }

    public function getVersion(): string
    {
        return $this->metadata['version'] ?? '1.0.0';
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getType(): string
    {
        return $this->type->value;
    }

    public function getTypeEnum(): ThemeType
    {
        return $this->type;
    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $helpersPath = $this->path . '/src/helpers.php';
        if (file_exists($helpersPath)) {
            require_once $helpersPath;
        }

        $this->booted = true;
    }

    public function isActive(): bool
    {
        // Avoid circular dependency by getting manager from app
        return $this->app->make(ThemeManager::class)->getActiveTheme() === $this;
    }
}
