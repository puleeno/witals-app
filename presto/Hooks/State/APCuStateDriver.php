<?php

declare(strict_types=1);

namespace PrestoWorld\Hooks\State;

use PrestoWorld\Contracts\Hooks\StateDriverInterface;

/**
 * APCu State Driver
 * Uses APCu for shared memory on traditional servers.
 */
class APCuStateDriver implements StateDriverInterface
{
    protected string $prefix = 'witals:state:';

    public function get(string $key): array
    {
        return apcu_fetch($this->prefix . $key) ?: [];
    }

    public function set(string $key, array $data): void
    {
        apcu_store($this->prefix . $key, $data);
    }

    public function lock(string $key): bool
    {
        // Primitive locking for APCu using apcu_add (atomic)
        $lockKey = $this->prefix . 'lock:' . $key;
        $start = microtime(true);
        
        while (!apcu_add($lockKey, true, 10)) { // 10 seconds TTL for safety
            if (microtime(true) - $start > 2.0) return false; // Timeout 2s
            usleep(1000);
        }
        
        return true;
    }

    public function unlock(string $key): void
    {
        apcu_delete($this->prefix . 'lock:' . $key);
    }
}
