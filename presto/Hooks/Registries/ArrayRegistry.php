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

    public function remove(string $type, string $hook, string $callback, int $priority): void
    {
        if (!isset($this->registry[$type][$hook])) return;

        foreach ($this->registry[$type][$hook] as $key => $meta) {
            if ($meta['callback'] === $callback && $meta['priority'] === $priority) {
                unset($this->registry[$type][$hook][$key]);
            }
        }
    }

    public function clear(string $type, string $hook, ?int $priority = null): void
    {
        if (!isset($this->registry[$type][$hook])) return;

        if ($priority === null) {
            unset($this->registry[$type][$hook]);
            return;
        }

        foreach ($this->registry[$type][$hook] as $key => $meta) {
            if ($meta['priority'] === $priority) {
                unset($this->registry[$type][$hook][$key]);
            }
        }
    }
}
