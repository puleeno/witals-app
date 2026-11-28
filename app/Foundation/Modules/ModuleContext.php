<?php

declare(strict_types=1);

namespace App\Foundation\Modules;

/**
 * ModuleContext — the shared bus that lets modules exchange data and embed
 * functionality into one another (headless-CMS style).
 *
 * Two primitives:
 *   - share()/get(): a key/value context bag (request-scoped data).
 *   - on()/emit()/render(): hooks. A module registers a callback for a hook
 *     name; another module (or the host) emits it. `render()` joins the
 *     string results, which is how SEO/schema.org inject into <head>.
 *
 * Registered as a singleton, so every module resolves the same instance.
 */
class ModuleContext
{
    /** @var array<string, callable[]> */
    protected array $hooks = [];

    /** @var array<string, mixed> */
    protected array $shared = [];

    public function share(string $key, mixed $value): void
    {
        $this->shared[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->shared[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->shared);
    }

    /**
     * Register a callback for a hook. Callbacks receive ($payload, ModuleContext).
     */
    public function on(string $hook, callable $callback): void
    {
        $this->hooks[$hook][] = $callback;
    }

    /**
     * Emit a hook and return every callback result (in registration order).
     */
    public function emit(string $hook, mixed $payload = []): array
    {
        $results = [];
        foreach ($this->hooks[$hook] ?? [] as $callback) {
            $results[] = $callback($payload, $this);
        }

        return $results;
    }

    /**
     * Emit a hook and concatenate its results into a single string.
     * Non-string results are cast; null/false are skipped.
     */
    public function render(string $hook, mixed $payload = []): string
    {
        $out = '';
        foreach ($this->emit($hook, $payload) as $result) {
            if (is_string($result) && $result !== '') {
                $out .= $result;
            }
        }

        return $out;
    }

    public function reset(): void
    {
        $this->hooks = [];
        $this->shared = [];
    }
}