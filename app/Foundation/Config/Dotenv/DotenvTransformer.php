<?php

declare(strict_types=1);

namespace App\Foundation\Config\Dotenv;

use App\Foundation\Config\Contracts\ConfigurationTransformerInterface;
use Dotenv\Parser\Parser;

class DotenvTransformer implements ConfigurationTransformerInterface
{
    public function transform(mixed $raw): array
    {
        if (!is_string($raw) || empty($raw)) {
            return [];
        }

        // Use phpdotenv parser
        $parser = new Parser();
        $entries = $parser->parse($raw);

        $config = [];
        foreach ($entries as $entry) {
            $config[$entry->getName()] = $entry->getValue()->get()->getChars();
        }

        return $config;
    }
}
