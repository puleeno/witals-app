<?php

declare(strict_types=1);

namespace PrestoWorld\Hooks\State;

use PrestoWorld\Contracts\Hooks\StateDriverInterface;

/**
 * Array State Driver
 * Fallback for environments without persistent shared memory.
 * State is only shared within the SAME process/request.
 */
class ArrayStateDriver implements StateDriverInterface
{
    protected array $storage = [];
    protected array $locks = [];

    public function get(string $key): array
    {
        return $this->storage[$key] ?? [];
    }

    public function set(string $key, array $data): void
    {
        $this->storage[$key] = $data;
    }

    public function lock(string $key): bool
    {
        $this->locks[$key] = true;
        return true;
    }

    public function unlock(string $key): void
    {
        unset($this->locks[$key]);
    }
}
