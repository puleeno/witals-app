<?php

declare(strict_types=1);

namespace PrestoWorld\Hooks\Registries;

use PrestoWorld\Contracts\Hooks\Registries\HookRegistryInterface;
use PrestoWorld\Contracts\Hooks\HookStateType;

class ArrayRegistry implements HookRegistryInterface
{
    protected array $registry = [];

    public function set(string $type, string $hook, string $callback, int $priority, HookStateType $stateType = HookStateType::VOLATILE): void
    {
        $this->registry[$type][$hook][] = [
            'callback' => $callback,
            'priority' => $priority,
            'state_type' => $stateType->value
        ];

        // Sort by priority
        usort($this->registry[$type][$hook], fn($a, $b) => $a['priority'] <=> $b['priority']);
    }

    public function get(string $type, string $hook): array
    {
        return $this->registry[$type][$hook] ?? [];
    }
}
