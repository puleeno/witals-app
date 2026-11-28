<?php

declare(strict_types=1);

namespace Modules\Contact\Events;

class ContactSubmitted
{
    /** @param array<string, mixed> $data */
    public function __construct(public readonly array $data)
    {
    }
}