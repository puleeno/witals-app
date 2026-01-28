<?php

declare(strict_types=1);

namespace App\Foundation\Config;

use App\Foundation\Config\Contracts\ConfigurationReaderInterface;
use App\Foundation\Config\Contracts\ConfigurationTransformerInterface;

class ConfigLoader
{
    private ConfigurationReaderInterface $reader;
    private ConfigurationTransformerInterface $transformer;

    public function __construct(
        ConfigurationReaderInterface $reader,
        ConfigurationTransformerInterface $transformer
    ) {
        $this->reader = $reader;
        $this->transformer = $transformer;
    }

    public function load(string $path): void
    {
        // 1. Read
        $raw = $this->reader->read($path);

        // 2. Transform
        $config = $this->transformer->transform($raw);

        // 3. Apply to Environment
        foreach ($config as $key => $value) {
            // Don't overwrite existing environment variables
            if (!isset($_SERVER[$key]) && !isset($_ENV[$key])) {
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }
}
