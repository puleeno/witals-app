<?php

declare(strict_types=1);

namespace App\Foundation\Module;

use App\Foundation\Application;
use Psr\Log\LoggerInterface;

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
     * Sort modules using a topological sort so that dependencies are always
     * loaded before the modules that depend on them.
     * Falls back to priority ordering when no dependency edge exists.
     *
     * @throws \RuntimeException on circular dependency
     */
    private function getSortedModules(): array
    {
        $modules  = $this->modules;  // name => ModuleInterface
        $sorted   = [];
        $visiting = [];              // grey nodes (currently on stack)
        $visited  = [];              // black nodes (fully processed)

        $visit = null;
        $visit = function (string $name) use (&$modules, &$sorted, &$visiting, &$visited, &$visit): void {
            if (isset($visited[$name])) {
                return;
            }

            if (isset($visiting[$name])) {
                throw new \RuntimeException(
                    "ModuleManager: Circular dependency detected involving module '{$name}'."
                );
            }

            $visiting[$name] = true;

            $module = $modules[$name] ?? null;
            if ($module !== null) {
                // Visit each dependency first
                foreach ($module->getDependencies() as $depName) {
                    if (!isset($modules[$depName])) {
                        $this->app->make(LoggerInterface::class)->warning(
                            "ModuleManager: Module '{module}' depends on '{dependency}' which is not installed.",
                            ['module' => $name, 'dependency' => $depName]
                        );
                        continue;
                    }
                    $visit($depName);
                }
            }

            unset($visiting[$name]);
            $visited[$name] = true;

            if ($module !== null) {
                $sorted[] = $module;
            }
        };

        // Sort modules by priority first so that same-priority modules with no
        // dependency edges are still ordered deterministically.
        $prioritised = array_values($modules);
        usort($prioritised, function (ModuleInterface $a, ModuleInterface $b) {
            if ($a->getPriority() !== $b->getPriority()) {
                return $a->getPriority() <=> $b->getPriority();
            }
            return strcmp($a->getName(), $b->getName());
        });

        foreach ($prioritised as $module) {
            $visit($module->getName());
        }

        return $sorted;
    }

    /**
     * Get all discovered modules (unordered, keyed by name).
     */
    public function all(): array
    {
        return $this->modules;
    }

    /**
     * Get all enabled modules in guaranteed dependency-safe load order.
     * Use this instead of all() whenever order matters (schema sync, boots, etc.).
     */
    public function allSorted(): array
    {
        return $this->getSortedModules();
    }

    /**
     * Check if module is loaded
     */
    public function isLoaded(string $name): bool
    {
        return isset($this->loaded[$name]);
    }
}
