<?php

declare(strict_types=1);

namespace PrestoWorld\Hooks\Registries;

use PrestoWorld\Contracts\Hooks\Registries\HookRegistryInterface;
use PrestoWorld\Contracts\Hooks\HookStateType;

class APCuRegistry implements HookRegistryInterface
{
    protected string $prefix = 'witals:hooks:';

    public function set(string $type, string $hook, string $callback, int $priority, HookStateType $stateType = HookStateType::VOLATILE): void
    {
        $key = $this->prefix . $type . ':' . $hook;
        $current = apcu_fetch($key) ?: [];
        
        $current[] = [
            'callback'   => $callback,
            'priority'   => $priority,
            'state_type' => $stateType->value
        ];

        // Sort by priority
        usort($current, fn($a, $b) => $a['priority'] <=> $b['priority']);
        
        apcu_store($key, $current);
    }

    public function get(string $type, string $hook): array
    {
        $key = $this->prefix . $type . ':' . $hook;
        return apcu_fetch($key) ?: [];
    }

    public function remove(string $type, string $hook, string $callback, int $priority): void
    {
        $key = $this->prefix . $type . ':' . $hook;
        $hooks = apcu_fetch($key);

        if (!$hooks || !is_array($hooks)) return;

        $modified = false;
        foreach ($hooks as $k => $meta) {
            if ($meta['callback'] === $callback && $meta['priority'] === $priority) {
                unset($hooks[$k]);
                $modified = true;
            }
        }

        if ($modified) {
            apcu_store($key, array_values($hooks));
        }
    }

    public function clear(string $type, string $hook, ?int $priority = null): void
    {
        $key = $this->prefix . $type . ':' . $hook;
        
        if ($priority === null) {
            apcu_delete($key);
            return;
        }

        $hooks = apcu_fetch($key);
        if (!$hooks || !is_array($hooks)) return;

        $modified = false;
        foreach ($hooks as $k => $meta) {
            if ($meta['priority'] === $priority) {
                unset($hooks[$k]);
                $modified = true;
            }
        }

        if ($modified) {
            apcu_store($key, array_values($hooks));
        }
    }
}
