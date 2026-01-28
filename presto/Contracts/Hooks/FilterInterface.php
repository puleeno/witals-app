<?php

declare(strict_types=1);

namespace PrestoWorld\Contracts\Hooks;

interface FilterInterface
{
    /**
     * Transform the given value.
     */
    public function handle(mixed $value, array $args = []): mixed;
}
