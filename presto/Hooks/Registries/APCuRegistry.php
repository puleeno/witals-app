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
}
