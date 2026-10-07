<?php

declare(strict_types=1);

namespace App\Foundation\Admin;

/**
 * Column definition for a TableList.
 *
 * Analogous to the column array entries returned by
 * WP_List_Table::get_columns() — but typed and richer.
 */
final class Column
{
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        public readonly bool   $sortable   = false,
        public readonly bool   $primary    = false,    // The "title" column — gets row actions
        public readonly bool   $hidden     = false,
        public readonly string $cssClass   = '',
        public readonly ?string $width     = null,     // e.g. '120px', '10%'
    ) {}

    public static function make(string $key, string $label): self
    {
        return new self($key, $label);
    }

    public function sortable(bool $default = false): self
    {
        return new self(
            $this->key, $this->label, true,
            $this->primary, $this->hidden, $this->cssClass, $this->width
        );
    }

    public function primary(): self
    {
        return new self(
            $this->key, $this->label, $this->sortable,
            true, $this->hidden, $this->cssClass, $this->width
        );
    }

    public function width(string $width): self
    {
        return new self(
            $this->key, $this->label, $this->sortable,
            $this->primary, $this->hidden, $this->cssClass, $width
        );
    }
}
