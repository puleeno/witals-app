<?php

declare(strict_types=1);

namespace App\Foundation\Admin;

/**
 * View filter definition.
 *
 * Analogous to WP_List_Table::get_views() — tabs like "All | Active | Trash".
 */
final class ViewFilter
{
    public function __construct(
        public readonly string  $key,
        public readonly string  $label,
        public readonly int     $count     = 0,
        public readonly bool    $current   = false,
        public readonly string  $queryVar  = 'status',
        public readonly ?string $queryValue = null,
    ) {}

    public static function make(string $key, string $label): self
    {
        return new self($key, $label, queryValue: $key);
    }

    public function count(int $count): self
    {
        return new self(
            $this->key, $this->label, $count,
            $this->current, $this->queryVar, $this->queryValue
        );
    }

    public function current(bool $current = true): self
    {
        return new self(
            $this->key, $this->label, $this->count,
            $current, $this->queryVar, $this->queryValue
        );
    }

    public function queryVar(string $queryVar): self
    {
        return new self(
            $this->key, $this->label, $this->count,
            $this->current, $queryVar, $this->queryValue
        );
    }

    public function queryValue(?string $value): self
    {
        return new self(
            $this->key, $this->label, $this->count,
            $this->current, $this->queryVar, $value
        );
    }
}
