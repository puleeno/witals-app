<?php

declare(strict_types=1);

namespace PrestoWorld\Hooks;

use Witals\Framework\Application;
use PrestoWorld\Contracts\Hooks\Registries\HookRegistryInterface;
use PrestoWorld\Contracts\Hooks\Dispatchers\ActionDispatcherInterface;
use PrestoWorld\Contracts\Hooks\HookStateType;
use PrestoWorld\Hooks\Registries\ArrayRegistry;
use PrestoWorld\Hooks\Dispatchers\SyncDispatcher;
use RuntimeException;
use Laravel\SerializableClosure\SerializableClosure;

/**
 * Hook Manager
 * Manages Actions and Filters with adaptive infrastructure.
 */
class HookManager
{
    protected const MAX_OBJECT_WEIGHT = 8192; // 8KB

    protected Application $app;
    protected HookRegistryInterface $registry;
    protected ActionDispatcherInterface $dispatcher;
    protected HookRunner $runner;
    
    /**
     * Tầng 2: Instance Pool (Chỉ tồn tại trong 1 Request Flow)
     * Giúp tái sử dụng Filter trong cùng một request để tối ưu CPU.
     */
    protected array $instancePool = [];

    public function __construct(
        Application $app, 
        HookRegistryInterface $registry,
        ActionDispatcherInterface $dispatcher
    ) {
        $this->app = $app;
        $this->registry = $registry;
        $this->dispatcher = $dispatcher;
        $this->runner = new HookRunner($app);
    }

    /**
     * Tầng 1: The Sanitizer (Weight Check)
     * Kiểm tra kích thước của Object trước khi lưu.
     */
    protected function sanitize(mixed $callback): string
    {
        $serialized = $this->serializeCallback($callback);
        
        // Weight Check: Kiểm tra kích thước chuỗi serialized
        if (strlen($serialized) > self::MAX_OBJECT_WEIGHT) {
            throw new RuntimeException(
                'Hook callback exceeds maximum allowed size of ' . self::MAX_OBJECT_WEIGHT . ' bytes'
            );
        }

        return $serialized;
    }

    /**
     * Serialize callback to string format.
     */
    protected function serializeCallback(mixed $callback): string
    {
        if (is_array($callback) && count($callback) === 2) {
            [$target, $method] = $callback;
            
            if (is_object($target)) {
                return 'Object:' . base64_encode(serialize($target)) . '@' . $method;
            }
            
            return $target . '@' . $method;
        }

        if ($callback instanceof \Closure) {
            $wrapper = new SerializableClosure($callback);
            return 'Closure:' . base64_encode(serialize($wrapper));
        }

        return (string)$callback;
    }

    /**
     * Add a filter (Pipeline Stage)
     */
    public function addFilter(string $hook, mixed $callback, int $priority = 10, HookStateType $stateType = HookStateType::VOLATILE): void
    {
        $this->registry->set('filter', $hook, $this->sanitize($callback), $priority, $stateType);
    }

    /**
     * Apply filters (Weighted Pipeline with Instance Pooling)
     */
    public function applyFilters(string $hook, mixed $value, ...$args): mixed
    {
        $hooksMetadata = $this->registry->get('filter', $hook);
        
        if (empty($hooksMetadata)) {
            return $value;
        }

        $callables = [];
        foreach ($hooksMetadata as $meta) {
            $callables[] = $this->resolveHook($meta);
        }

        $pipeline = new Pipeline();
        return $pipeline->send($value)
            ->with($args)
            ->through($callables)
            ->then(fn ($passable) => $passable);
    }

    /**
     * Resolve a hook prototype into a safe callable based on its state type.
     */
    protected function resolveHook(array $meta): callable
    {
        $prototype = $meta['callback'];
        $stateType = $meta['state_type'] ?? 'volatile';
        $stateKey  = md5($prototype); // Unique ID for this hook logic
        $poolKey   = md5($prototype . $stateType);

        return function (...$args) use ($prototype, $stateType, $stateKey, $poolKey) {
            // 1. Scoped/Shared Pool: Reuse instance within the same flow/request
            // Wait: For 'shared', we still want to reuse the instance in the SAME worker
            // to avoid repeated unserialization, but the runner handles the 'shared' data.
            if (($stateType === 'scoped' || $stateType === 'shared') && isset($this->instancePool[$poolKey])) {
                // Actually, pooling the CALLABLE might not be enough if we want to hydrate every time for shared.
                // But for Scoped, it's fine.
            }

            // Let's use the runner for every execution to ensure try-finally safety.
            // If it's scoped, HookManager can potentially cache the instance, 
            // but for simplicity and robustness, the runner is the source of truth.
            
            return $this->runner->run($prototype, $args, $stateKey, $stateType);
        };
    }

    /**
     * Resolve a prototype into a safe callable.
     */
    protected function resolveCallback(string $prototype): callable
    {
        return function (...$args) use ($prototype) {
            return $this->runner->run($prototype, $args);
        };
    }

    /**
     * Remove a filter
     */
    public function removeFilter(string $hook, mixed $callback, int $priority = 10): void
    {
        $this->registry->remove('filter', $hook, $this->sanitize($callback), $priority);
    }

    /**
     * Remove all filters
     */
    public function removeAllFilters(string $hook, int|bool $priority = false): void
    {
        if ($priority === false) {
            $priority = null;
        }
        $this->registry->clear('filter', $hook, $priority);
    }

    /**
     * Add an action (Observer)
     */
    public function addAction(string $hook, mixed $callback, int $priority = 10, HookStateType $stateType = HookStateType::VOLATILE): void
    {
        $this->registry->set('action', $hook, $this->sanitize($callback), $priority, $stateType);
    }

    /**
     * Do action (Ephemeral Execution)
     */
    public function doAction(string $hook, ...$args): void
    {
        $hooksMetadata = $this->registry->get('action', $hook);
        
        foreach ($hooksMetadata as $meta) {
            $this->dispatcher->dispatch($hook, $meta, $args);
        }
    }

    /**
     * Remove an action
     */
    public function removeAction(string $hook, mixed $callback, int $priority = 10): void
    {
        $this->registry->remove('action', $hook, $this->sanitize($callback), $priority);
    }

    /**
     * Remove all actions
     */
    public function removeAllActions(string $hook, int|bool $priority = false): void
    {
        if ($priority === false) {
            $priority = null;
        }
        $this->registry->clear('action', $hook, $priority);
    }

    /**
     * Tầng 3: The Garbage Collector (Flow-Scope GC)
     * Clear Instance Pool sau mỗi Request.
     */
    public function flushCache(): void
    {
        $this->instancePool = [];
    }

    /**
     * Thực thi trực tiếp dành cho Dispatchers (Sync or Task Worker).
     */
    public function executeDispatchedAction(array $meta, array $args): mixed
    {
        $prototype = $meta['callback'];
        $stateType = $meta['state_type'] ?? 'volatile';
        $stateKey  = md5($prototype);

        return $this->runner->run($prototype, $args, $stateKey, $stateType);
    }
}
