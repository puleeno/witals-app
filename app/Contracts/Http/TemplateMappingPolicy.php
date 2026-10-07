<?php

declare(strict_types=1);

namespace App\Contracts\Http;

interface TemplateMappingPolicy
{
    public function match(string $path): ?string;
}
