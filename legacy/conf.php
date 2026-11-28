<?php

/**
 * Legacy CMS configuration (port of old-source/conf.php.example).
 *
 * Values are resolved from the process environment first (loaded from .env by
 * the Witals bootstrap), then by parsing .env directly so the legacy runner
 * also works when it is executed outside the framework.
 */

function legacy_env(string $key, string $default = ''): string
{
    $value = getenv($key);
    if ($value !== false && $value !== '') {
        return $value;
    }
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
        return (string) $_ENV[$key];
    }
    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
        return (string) $_SERVER[$key];
    }

    static $envFile = null;
    if ($envFile === null) {
        $envFile = [];
        $path = dirname(__DIR__) . '/.env';
        if (is_readable($path)) {
            foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }
                [$k, $v] = explode('=', $line, 2);
                $envFile[trim($k)] = trim(trim($v), "\"'");
            }
        }
    }

    return $envFile[$key] ?? $default;
}

$config['dbname'] = legacy_env('DB_DATABASE', 'xanhvina');
$config['username'] = legacy_env('DB_USERNAME', 'root');
$config['password'] = legacy_env('DB_PASSWORD', 'root');
$config['host'] = legacy_env('DB_HOST', '127.0.0.1');

if (!defined('CMS_DEBUG')) {
    define('CMS_DEBUG', legacy_env('APP_DEBUG', 'false') === 'true');
}

$site_address = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
$dir_path = '';
$cache_file_dir = dirname(__FILE__);

$imagedir_finder = "/uploaded/";
$dir_image_upload = "/uploaded/" . date('Y', time()) . "/" . date('m', time()) . "/";
$dir_image_browser = '/uploaded/';
$cache_image_path = $dir_path . '/temp/';
$minify = 0;
$clearcachetime = time() + 172800; // 2 ngày

// config url
$rewrite = 1;
$url_extension_category = '/';
$url_extension_article = '.html'; // .htm,.html
$url_max_number_category = 4; //1,2,3,4
