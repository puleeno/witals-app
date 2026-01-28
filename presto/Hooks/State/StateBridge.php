<?php

declare(strict_types=1);

namespace PrestoWorld\Hooks\State;

use PrestoWorld\Contracts\Hooks\StateDriverInterface;
use PrestoWorld\Contracts\Hooks\StatefulHookInterface;

/**
 * State Bridge
 * Orchestrates hydration and persistence of hook states.
 */
class StateBridge
{
    protected StateDriverInterface $driver;

    public function __construct(StateDriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Hydrate an object with its shared state.
     */
    public function hydrate(string $key, object $instance): void
    {
        if ($instance instanceof StatefulHookInterface) {
            $data = $this->driver->get($key);
            $instance->__hydrate($data);
        }
    }

    /**
     * Persist an object's state.
     */
    public function persist(string $key, object $instance): void
    {
        if ($instance instanceof StatefulHookInterface) {
            $data = $instance->__extract();
            $this->driver->set($key, $data);
        }
    }

    /**
     * Acquire lock for shared state.
     */
    public function lock(string $key): void
    {
        $this->driver->lock($key);
    }

    /**
     * Release lock for shared state.
     */
    public function unlock(string $key): void
    {
        $this->driver->unlock($key);
    }
}
