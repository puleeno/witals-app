<?php

declare(strict_types=1);

namespace App\Foundation\Module;

use App\Foundation\Application;

/**
 * Base Module Class
 */
abstract class Module implements ModuleInterface
{
    protected Application $app;
    protected array $metadata;
    protected string $path;
    protected bool $booted = false;

    public function __construct(Application $app, string $path, array $metadata = [])
    {
        $this->app = $app;
        $this->path = $path;
        $this->metadata = $metadata;
    }

    public function getName(): string
    {
        return $this->metadata['name'] ?? 'unknown';
    }

    public function getVersion(): string
    {
        return $this->metadata['version'] ?? '1.0.0';
    }

    public function getDescription(): string
    {
        return $this->metadata['description'] ?? '';
    }

    public function getType(): string
    {
        return $this->metadata['type'] ?? 'optional';
    }

    public function getPriority(): int
    {
        return $this->metadata['priority'] ?? 50;
    }

    public function getRequirements(): array
    {
        return $this->metadata['requires'] ?? [
            'php' => '>=8.1',
            'modules' => [],
        ];
    }

    public function getProviders(): array
    {
        return $this->metadata['providers'] ?? [];
    }

    public function isEnabled(): bool
    {
        return $this->metadata['enabled'] ?? false;
    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        // Register module providers
        foreach ($this->getProviders() as $provider) {
            $this->app->register($provider);
        }

        $this->booted = true;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
