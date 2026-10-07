<?php

declare(strict_types=1);

namespace App\Foundation\Admin;

/**
 * Row action definition.
 *
 * Analogous to the $actions array passed to
 * WP_List_Table::row_actions($actions, $always_visible).
 *
 * Example:
 *   RowAction::make('edit',   'Edit',   '/dashboard/customers/{id}/edit')
 *   RowAction::make('delete', 'Delete', '/api/customers/{id}')->method('DELETE')->confirm('Delete this customer?')
 */
final class RowAction
{
    public function __construct(
        public readonly string  $key,
        public readonly string  $label,
        public readonly string  $urlTemplate,   // {id} is replaced by the row's primary key value
        public readonly string  $method         = 'GET',
        public readonly string  $confirmMessage = '',
        public readonly string  $cssClass       = '',
        public readonly bool    $alwaysVisible  = false,
    ) {}

    public static function make(string $key, string $label, string $urlTemplate): self
    {
        return new self($key, $label, $urlTemplate);
    }

    public function method(string $method): self
    {
        return new self(
            $this->key, $this->label, $this->urlTemplate,
            strtoupper($method), $this->confirmMessage,
            $this->cssClass, $this->alwaysVisible
        );
    }

    public function confirm(string $message): self
    {
        return new self(
            $this->key, $this->label, $this->urlTemplate,
            $this->method, $message, $this->cssClass, $this->alwaysVisible
        );
    }

    public function css(string $class): self
    {
        return new self(
            $this->key, $this->label, $this->urlTemplate,
            $this->method, $this->confirmMessage, $class, $this->alwaysVisible
        );
    }

    /**
     * Resolve URL template by replacing {id} and any {field} with actual row values.
     */
    public function resolveUrl(array|object $row, string $primaryKey = 'id'): string
    {
        $data = is_array($row) ? $row : (array) $row;
        $url  = $this->urlTemplate;

        // Replace {id} with the primary key value
        $url = str_replace('{id}', (string)($data[$primaryKey] ?? ''), $url);

        // Replace any other {field} placeholders
        foreach ($data as $field => $value) {
            $url = str_replace('{' . $field . '}', (string)$value, $url);
        }

        return $url;
    }
}
