<?php

declare(strict_types=1);

namespace App\Foundation\Config;

use RuntimeException;

class ConfigRepository
{
    private array $paths;

    private array $loaded = [];

    private ?string $cachePath = null;

    private ?array $cache = null;

    public function __construct(string|array $paths = ['config'])
    {
        $this->paths = is_array($paths) ? $paths : [$paths];
    }

    public function setCachePath(?string $path): void
    {
        $this->cachePath = $path;
        $this->cache = null;
        $this->loaded = [];
    }

    public function getCachePath(): ?string
    {
        return $this->cachePath;
    }

    public function hasCache(): bool
    {
        return $this->cachePath !== null && file_exists($this->cachePath);
    }

    public function getPaths(): array
    {
        return $this->paths;
    }

    public function setPaths(string|array $paths): void
    {
        $this->paths = is_array($paths) ? $paths : [$paths];
        $this->loaded = [];
    }

    public function addPath(string $path): void
    {
        $this->paths[] = $path;
        $this->loaded = [];
    }

    public function has(string $file): bool
    {
        if (isset($this->loaded[$file])) {
            return true;
        }

        if ($this->loadFromCache($file) !== null) {
            return true;
        }

        foreach ($this->paths as $path) {
            if ($this->fileExists($path, $file)) {
                return true;
            }
        }

        return false;
    }

    public function load(string $file, mixed $default = null): mixed
    {
        if (isset($this->loaded[$file])) {
            return $this->loaded[$file];
        }

        $cached = $this->loadFromCache($file);
        if ($cached !== null) {
            $this->loaded[$file] = $cached;
            return $cached;
        }

        foreach ($this->paths as $path) {
            $config = $this->loadFile($path, $file);
            if ($config !== null) {
                $this->loaded[$file] = $config;
                return $config;
            }
        }

        return $default;
    }

    public function loadAll(): array
    {
        $all = [];

        foreach ($this->paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $files = glob($path . '/*.php') ?: [];
            foreach ($files as $file) {
                $key = basename($file, '.php');
                $config = require $file;
                if (is_array($config)) {
                    $all[$key] = $config;
                    $this->loaded[$key] = $config;
                }
            }

            $jsonFiles = glob($path . '/*.json') ?: [];
            foreach ($jsonFiles as $file) {
                $key = basename($file, '.json');
                $content = file_get_contents($file);
                if ($content !== false) {
                    $config = json_decode($content, true);
                    if (is_array($config)) {
                        $all[$key] = $config;
                        $this->loaded[$key] = $config;
                    }
                }
            }
        }

        return $all;
    }

    public function set(string $file, array $config): void
    {
        $this->loaded[$file] = $config;
    }

    public function clearCache(): void
    {
        $this->loaded = [];
        $this->cache = null;
    }

    private function loadFromCache(string $file): mixed
    {
        if ($this->cache === null && $this->cachePath !== null && file_exists($this->cachePath)) {
            $data = require $this->cachePath;
            $this->cache = is_array($data) ? $data : null;
        }

        return $this->cache[$file] ?? null;
    }

    private function fileExists(string $path, string $file): bool
    {
        return file_exists($path . '/' . $file . '.php')
            || file_exists($path . '/' . $file . '.json');
    }

    private function loadFile(string $path, string $file): ?array
    {
        $phpPath = $path . '/' . $file . '.php';
        if (file_exists($phpPath)) {
            $result = require $phpPath;
            return is_array($result) ? $result : null;
        }

        $jsonPath = $path . '/' . $file . '.json';
        if (file_exists($jsonPath)) {
            $content = file_get_contents($jsonPath);
            if ($content === false) {
                throw new RuntimeException("Unable to read config file: {$jsonPath}");
            }
            $decoded = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException("Invalid JSON in config file {$jsonPath}: " . json_last_error_msg());
            }
            return $decoded;
        }

        return null;
    }
}
