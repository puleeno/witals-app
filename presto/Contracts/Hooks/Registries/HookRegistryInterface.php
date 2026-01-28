<?php

declare(strict_types=1);

namespace PrestoWorld\Contracts\Hooks\Registries;

use PrestoWorld\Contracts\Hooks\HookStateType;

interface HookRegistryInterface
{
    /**
     * Set a hook record.
     */
    public function set(string $type, string $hook, string $callback, int $priority, HookStateType $stateType = HookStateType::VOLATILE): void;

    /**
     * Get all hooks for a specific name and type.
     * Should return an array of callback strings sorted by priority.
     */
    public function get(string $type, string $hook): array;
}
