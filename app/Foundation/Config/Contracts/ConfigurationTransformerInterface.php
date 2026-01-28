<?php

declare(strict_types=1);

namespace App\Foundation\Config\Contracts;

interface ConfigurationTransformerInterface
{
    /**
     * Transform raw data into usable configuration
     * 
     * @param mixed $raw Raw data from reader
     * @return array Transformed configuration
     */
    public function transform(mixed $raw): array;
}
