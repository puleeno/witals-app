<?php

declare(strict_types=1);

namespace App\Foundation\Admin;

/**
 * Bulk action definition.
 *
 * Maps to the entries returned by WP_List_Table::get_bulk_actions().
 */
final class BulkAction
{
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        public readonly string $confirmMessage = '',
    ) {}

    public static function make(string $key, string $label): self
    {
        return new self($key, $label);
    }

    public function confirm(string $message): self
    {
        return new self($this->key, $this->label, $message);
    }
}
