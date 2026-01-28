<?php

declare(strict_types=1);

namespace PrestoWorld\Hooks\Registries;

use PrestoWorld\Contracts\Hooks\Registries\HookRegistryInterface;
use PrestoWorld\Contracts\Hooks\HookStateType;

/**
 * Compiled File Registry
 * Fallback for restricted environments (Shared Hosting).
 * Generates a static PHP file for Opcache optimization.
 */
class FileCompiledRegistry implements HookRegistryInterface
{
    protected string $cachePath;
    protected array $hooks = [];

    public function __construct(string $cachePath)
    {
        $this->cachePath = $cachePath;
        if (file_exists($this->cachePath)) {
            $this->hooks = include $this->cachePath;
        }
    }

    public function set(string $type, string $hook, string $callback, int $priority, HookStateType $stateType = HookStateType::VOLATILE): void
    {
        $this->hooks[$type][$hook][] = [
            'callback'   => $callback,
            'priority'   => $priority,
            'state_type' => $stateType->value
        ];

        usort($this->hooks[$type][$hook], fn($a, $b) => $a['priority'] <=> $b['priority']);
        
        // Persist to file (Warm-up)
        file_put_contents(
            $this->cachePath, 
            '<?php return ' . var_export($this->hooks, true) . ';'
        );
    }

    public function get(string $type, string $hook): array
    {
        return $this->hooks[$type][$hook] ?? [];
    }
}
