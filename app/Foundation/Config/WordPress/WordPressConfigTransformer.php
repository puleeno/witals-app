<?php

declare(strict_types=1);

namespace App\Foundation\Config\WordPress;

use App\Foundation\Config\Contracts\ConfigurationTransformerInterface;

class WordPressConfigTransformer implements ConfigurationTransformerInterface
{
    private const KEY_MAPPING = [
        'DB_NAME' => 'DB_DATABASE',
        'DB_USER' => 'DB_USERNAME',
        'DB_PASSWORD' => 'DB_PASSWORD',
        'DB_HOST' => 'DB_HOST',
        'DB_CHARSET' => 'DB_CHARSET',
        'WP_DEBUG' => 'APP_DEBUG',
        'WP_HOME' => 'APP_URL',
        'WP_SITEURL' => 'WP_SITEURL',
        // Auth Keys & Salts
        'AUTH_KEY'          => 'WP_AUTH_KEY',
        'SECURE_AUTH_KEY'   => 'WP_SECURE_AUTH_KEY',
        'LOGGED_IN_KEY'     => 'WP_LOGGED_IN_KEY',
        'NONCE_KEY'         => 'WP_NONCE_KEY',
        'AUTH_SALT'         => 'WP_AUTH_SALT',
        'SECURE_AUTH_SALT'  => 'WP_SECURE_AUTH_SALT',
        'LOGGED_IN_SALT'    => 'WP_LOGGED_IN_SALT',
        'NONCE_SALT'        => 'WP_NONCE_SALT',
    ];

    public function transform(mixed $raw): array
    {
        if (!is_string($raw) || empty($raw)) {
            return [];
        }

        $config = [];
        
        // Regex to robustly match define('KEY', 'VALUE');
        // Handles:
        // - Single quotes with escaped quotes inside: 'str\'ing'
        // - Double quotes with escaped quotes inside: "str\"ing"
        // - Boolean and Numbers
        $pattern = '/define\s*\(\s*[\'"](?<key>[A-Z0-9_]+)[\'"]\s*,\s*(?<value>\'[^\']*(?:\\\.[^\']*)*\'|"[^"]*(?:\\\.[^"]*)*"|true|false|[0-9.]+)\s*\)\s*;/i';
        
        if (preg_match_all($pattern, $raw, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $key = $match['key'];
                $valueRaw = $match['value'];
                
                // Parse value (remove quotes)
                $value = $this->parseValue($valueRaw);

                // Map standard WP keys to PrestoWorld/Witals keys
                if (isset(self::KEY_MAPPING[$key])) {
                    $mappedKey = self::KEY_MAPPING[$key];
                    $config[$mappedKey] = $value;

                    // Special handling for DB_HOST to extract port
                    if ($key === 'DB_HOST') {
                        $this->parseDbHost($value, $config);
                    }
                }

                // Also keep the original WP key
                $config[$key] = $value;
            }
        }

        // Regex for table prefix variable: $table_prefix = 'wp_';
        if (preg_match('/\$table_prefix\s*=\s*[\'"](?<prefix>[a-zA-Z0-9_]+)[\'"]\s*;/', $raw, $matches)) {
            $config['WP_TABLE_PREFIX'] = $matches['prefix'];
        }

        return $config;
    }

    private function parseValue(string $value): mixed
    {
        // Boolean
        if (strtolower($value) === 'true') {
            return true;
        }
        if (strtolower($value) === 'false') {
            return false;
        }

        // Strings with quotes
        if ((str_starts_with($value, "'") && str_ends_with($value, "'")) ||
            (str_starts_with($value, '"') && str_ends_with($value, '"'))
        ) {
            return substr($value, 1, -1);
        }

        // Numbers
        if (is_numeric($value)) {
            return $value;
        }

        // Return raw if unsure (might be a constant or function call, e.g. dirname(__FILE__))
        // For safety/simplicity, we might skip complex expressions
        return $value;
    }

    private function parseDbHost(string $host, array &$config): void
    {
        if (str_contains($host, ':')) {
            $parts = explode(':', $host, 2);
            $config['DB_HOST'] = $parts[0];
            $config['DB_PORT'] = $parts[1];
        } else {
            $config['DB_PORT'] = 3306; // Default MySQL port
        }
    }
}
