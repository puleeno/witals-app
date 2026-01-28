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
        if (!isset($this->hooks[$type][$hook])) {
            $this->hooks[$type][$hook] = [];
        }

        // Check for duplicates to prevent file bloat
        foreach ($this->hooks[$type][$hook] as $index => $registered) {
            if ($registered['callback'] === $callback && $registered['priority'] === $priority) {
                // Already registered, update state type if needed and return
                if ($registered['state_type'] !== $stateType->value) {
                    $this->hooks[$type][$hook][$index]['state_type'] = $stateType->value;
                    $this->save();
                }
                return;
            }
        }

        $this->hooks[$type][$hook][] = [
            'callback'   => $callback,
            'priority'   => $priority,
            'state_type' => $stateType->value
        ];

        usort($this->hooks[$type][$hook], fn($a, $b) => $a['priority'] <=> $b['priority']);
        
        $this->save();
    }

    protected function save(): void
    {
        // Persist to file (Warm-up)
        // Use atomic write to prevent corruption
        $content = '<?php return ' . var_export($this->hooks, true) . ';';
        $tmp = $this->cachePath . '.tmp';
        file_put_contents($tmp, $content);
        rename($tmp, $this->cachePath);
        
        // Invalidate opcache for this file
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($this->cachePath, true);
        }
    }

    public function get(string $type, string $hook): array
    {
        return $this->hooks[$type][$hook] ?? [];
    }

    public function remove(string $type, string $hook, string $callback, int $priority): void
    {
        if (!isset($this->hooks[$type][$hook])) return;

        $modified = false;
        foreach ($this->hooks[$type][$hook] as $key => $meta) {
            if ($meta['callback'] === $callback && $meta['priority'] === $priority) {
                unset($this->hooks[$type][$hook][$key]);
                $modified = true;
            }
        }

        if ($modified) {
            // Re-index not strictly needed but good for clean export
            $this->hooks[$type][$hook] = array_values($this->hooks[$type][$hook]);
            $this->save();
        }
    }

    public function clear(string $type, string $hook, ?int $priority = null): void
    {
        if (!isset($this->hooks[$type][$hook])) return;

        if ($priority === null) {
            unset($this->hooks[$type][$hook]);
            $this->save();
            return;
        }

        $modified = false;
        foreach ($this->hooks[$type][$hook] as $key => $meta) {
            if ($meta['priority'] === $priority) {
                unset($this->hooks[$type][$hook][$key]);
                $modified = true;
            }
        }

        if ($modified) {
            $this->hooks[$type][$hook] = array_values($this->hooks[$type][$hook]);
            $this->save();
        }
    }
}
