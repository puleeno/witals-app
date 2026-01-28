<?php

declare(strict_types=1);

namespace PrestoWorld\Hooks;

use Witals\Framework\Application;
use Throwable;
use Laravel\SerializableClosure\SerializableClosure;

/**
 * Hook Runner
 * Executes hooks with memory safety and clean-up.
 */
class HookRunner
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Execute a serialized hook with memory safety and state management.
     */
    public function run(string $callback, array $args, ?string $stateKey = null, string $stateType = 'volatile'): mixed
    {
        $instance = null;
        $closure = null;
        $bridge = $this->app->has(\PrestoWorld\Hooks\State\StateBridge::class) 
            ? $this->app->make(\PrestoWorld\Hooks\State\StateBridge::class) 
            : null;

        if ($stateKey && $stateType === 'shared' && $bridge) {
            $bridge->lock($stateKey);
        }

        try {
            if (str_starts_with($callback, 'Closure:')) {
                $unserialized = unserialize(base64_decode(substr($callback, 8)));
                
                if ($unserialized instanceof SerializableClosure) {
                    $closure = $unserialized->getClosure();
                } else {
                    $closure = $unserialized;
                }

                // Use call_user_func_array for direct argument passing, bypassing Container auto-wiring mismatch
                return call_user_func_array($closure, $args);
            }

            if (str_starts_with($callback, 'Object:')) {
                [$target, $method] = explode('@', substr($callback, 7));
                $instance = unserialize(base64_decode($target));
                
                // Hydrate state if shared (overwrite the serialized state with current shared state)
                if ($stateKey && $stateType === 'shared' && $bridge) {
                    $bridge->hydrate($stateKey, $instance);
                }

                $result = $this->app->call([$instance, $method], $args);

                // Persist state if shared (save the modified state back)
                if ($stateKey && $stateType === 'shared' && $bridge) {
                    $bridge->persist($stateKey, $instance);
                }

                return $result;
            }

            if (str_contains($callback, '@')) {
                [$class, $method] = explode('@', $callback);
                $instance = $this->app->make($class);
                
                // Class-based hooks can also be stateful
                if ($stateKey && $stateType === 'shared' && $bridge) {
                    $bridge->hydrate($stateKey, $instance);
                }

                $result = $this->app->call([$instance, $method], $args);

                if ($stateKey && $stateType === 'shared' && $bridge) {
                    $bridge->persist($stateKey, $instance);
                }

                return $result;
            }

            // Fallback for standard callables (function names, etc.)
            if (is_callable($callback)) {
                return call_user_func_array($callback, $args);
            }

            return $this->app->call($callback, $args);
            
        } catch (Throwable $e) {
            // Log error or rethrow
            throw $e;
        } finally {
            if ($stateKey && $stateType === 'shared' && $bridge) {
                $bridge->unlock($stateKey);
            }
            // Only unset for volatile/scoped. For shared, state is managed externally.
            if ($stateType !== 'shared') {
                unset($instance);
                unset($closure);
            }
        }
    }
}
