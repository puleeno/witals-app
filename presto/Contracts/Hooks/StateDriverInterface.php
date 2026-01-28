<?php

declare(strict_types=1);

namespace PrestoWorld\Contracts\Hooks;

/**
 * State Driver Interface
 * Abstracts how state is stored and locked across different environments.
 */
interface StateDriverInterface
{
    /**
     * Get state data by key.
     */
    public function get(string $key): array;

    /**
     * Store state data by key.
     */
    public function set(string $key, array $data): void;

    /**
     * Acquire an atomic lock for the given key.
     */
    public function lock(string $key): bool;

    /**
     * Release the lock for the given key.
     */
    public function unlock(string $key): void;
}
