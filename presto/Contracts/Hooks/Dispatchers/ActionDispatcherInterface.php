<?php

declare(strict_types=1);

namespace PrestoWorld\Contracts\Hooks\Dispatchers;

interface ActionDispatcherInterface
{
    /**
     * Dispatch an action with the given arguments.
     * The hook param contains metadata like 'callback', 'state_type', etc.
     */
    public function dispatch(string $hook, array $hookData, array $args): void;
}
