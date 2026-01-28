<?php

declare(strict_types=1);

namespace PrestoWorld\Contracts\Hooks;

/**
 * Stateful Hook Interface
 * Hooks implementing this can have their internal state persisted and shared.
 */
interface StatefulHookInterface
{
    /**
     * Set the internal state of the hook.
     */
    public function __hydrate(array $data): void;

    /**
     * Get the internal state of the hook for persistence.
     */
    public function __extract(): array;
}
