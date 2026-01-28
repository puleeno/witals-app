<?php

declare(strict_types=1);

namespace App\Foundation\Module;

use App\Foundation\Application;

/**
 * Module Manager
 * 
 * Handles discovery, loading, and management of modules
 */
class ModuleManager
{
    private Application $app;
    private array $modules = [];
    private array $loaded = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Discover modules from modules directory
     */
    public function discover(): void
    {
        $modulesPath = $this->app->basePath('modules');
        
        if (!is_dir($modulesPath)) {
            return;
        }

        foreach (scandir($modulesPath) as $dir) {
            if ($dir === '.' || $dir === '..') {
                continue;
            }

            $modulePath = $modulesPath . '/' . $dir;
            $metadataPath = $modulePath . '/module.json';

            if (file_exists($metadataPath)) {
                $metadata = json_decode(file_get_contents($metadataPath), true);
                
                // Allow config override for enabled state
                $configKey = "modules.enabled.{$metadata['name']}";
                if ($this->app->config($configKey) !== null) {
                    $metadata['enabled'] = $this->app->config($configKey);
                }

                $this->modules[$metadata['name']] = new class($this->app, $modulePath, $metadata) extends Module {};
            }
        }
    }

    /**
     * Load enabled modules
     */
    public function loadEnabled(): void
    {
        $sorted = $this->getSortedModules();

        foreach ($sorted as $module) {
            if ($module->isEnabled()) {
                $this->load($module);
            }
        }
    }

    /**
     * Load specific module
     */
    public function load(ModuleInterface $module): void
    {
        $name = $module->getName();

        if (isset($this->loaded[$name])) {
            return;
        }

        // Register PSR-4 autoloading for module
        // Note: In a real app, composer should handle this, or we register manually
        // basic manual registration:
        $metadata = json_decode(file_get_contents($module->getPath() . '/module.json'), true);
        if (isset($metadata['autoload']['psr-4'])) {
            foreach ($metadata['autoload']['psr-4'] as $ns => $path) {
                $libPath = $module->getPath() . '/' . $path;
                spl_autoload_register(function ($class) use ($ns, $libPath) {
                    if (str_starts_with($class, $ns)) {
                        $relative = substr($class, strlen($ns));
                        $file = $libPath . str_replace('\\', '/', $relative) . '.php';
                        if (file_exists($file)) {
                            include $file;
                        }
                    }
                });
            }
        }

        $module->boot();
        $this->loaded[$name] = true;
    }

    /**
     * Sort modules by priority
     */
    private function getSortedModules(): array
    {
        $modules = $this->modules;

        usort($modules, function (ModuleInterface $a, ModuleInterface $b) {
            // Sort by priority (lower first)
            if ($a->getPriority() !== $b->getPriority()) {
                return $a->getPriority() <=> $b->getPriority();
            }
            // Same priority? alphabetical
            return strcmp($a->getName(), $b->getName());
        });

        return $modules;
    }

    /**
     * Get all discovered modules
     */
    public function all(): array
    {
        return $this->modules;
    }

    /**
     * Check if module is loaded
     */
    public function isLoaded(string $name): bool
    {
        return isset($this->loaded[$name]);
    }
}
