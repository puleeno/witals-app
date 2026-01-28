<?php

declare(strict_types=1);

namespace App\Foundation\Config\Dotenv;

use App\Foundation\Config\Contracts\ConfigurationReaderInterface;
use RuntimeException;

class DotenvReader implements ConfigurationReaderInterface
{
    public function read(string $path = ''): string
    {
        if (!file_exists($path)) {
            return '';
        }

        $content = file_get_contents($path);
        
        if ($content === false) {
            throw new RuntimeException("Unable to read configuration file: {$path}");
        }

        return $content;
    }
}
