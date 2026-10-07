<?php

declare(strict_types=1);

namespace App\Foundation\Config\WordPress;

use App\Foundation\Config\Contracts\ConfigurationReaderInterface;
use RuntimeException;

class WordPressConfigReader implements ConfigurationReaderInterface
{
    public function read(string $path = ''): string
    {
        if (!file_exists($path)) {
            // It's optional, so we return empty string instead of throwing error if not found?
            // Or let the loader handle file existence.
            // For now, return empty if not found.
            return '';
        }

        $content = file_get_contents($path);
        
        if ($content === false) {
            throw new RuntimeException("Unable to read WordPress config file: {$path}");
        }

        return $content;
    }
}
