<?php

declare(strict_types=1);

namespace PrestoWorld\Hooks;

use PrestoWorld\Contracts\Hooks\FilterInterface;
use Closure;

/**
 * Filter Pipeline Pattern
 * Synchronously transforms data through a chain of filters.
 */
class Pipeline
{
    protected mixed $passable;
    protected array $args = [];
    protected array $pipes = [];

    /**
     * Set the object being sent through the pipeline.
     */
    public function send(mixed $passable): self
    {
        $this->passable = $passable;
        return $this;
    }

    /**
     * Set additional arguments for filters.
     */
    public function with(array $args): self
    {
        $this->args = $args;
        return $this;
    }

    /**
     * Set the array of pipes.
     */
    public function through(array $pipes): self
    {
        $this->pipes = $pipes;
        return $this;
    }

    /**
     * Run the pipeline with a final destination callback.
     */
    public function then(Closure $destination): mixed
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            $this->carry(),
            $destination
        );

        return $pipeline($this->passable);
    }

    /**
     * Get a Closure that represents a slice of the application pipeline.
     */
    protected function carry(): Closure
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                if (is_callable($pipe)) {
                    $result = $pipe($passable, ...$this->args);
                } elseif ($pipe instanceof FilterInterface) {
                    $result = $pipe->handle($passable, $this->args);
                } else {
                    $result = $pipe; // Fallback or handle specific types
                }

                return $stack($result);
            };
        };
    }
}
