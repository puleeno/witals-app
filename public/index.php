<?php

/**
 * Entry point for traditional web servers (Apache, Nginx, PHP built-in server)
 * This file handles requests when running on traditional PHP-FPM/CGI
 *
 * Two lanes:
 *
 *  1. Public site pages  -> legacy/site.php (original endcms templates + modules)
 *  2. Everything else    -> normal Witals pipeline (dashboard, api, health, ...)
 *
 * The legacy lane is included from global scope on purpose: the original CMS
 * code is procedural and reads/writes PHP globals.
 */

declare(strict_types=1);

use Witals\Framework\Contracts\RuntimeType;
use Witals\Framework\Server\ServerFactory;

define('WITALS_START', microtime(true));

// Register the Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// When running under `php -S`, act as the router script: let the built-in
// server answer real static files itself and fall through to the app for
// everything else (page URLs such as /san-pham/cay-trau-ba-gia.html).
if (PHP_SAPI === 'cli-server') {
    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $staticPath = rawurldecode((string) parse_url($uri, PHP_URL_PATH));
    if ($staticPath !== '' && is_file(__DIR__ . $staticPath)) {
        return false;
    }
}

// Bootstrap the application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// ---------------------------------------------------------------------------
// Decide which lane handles this request
// ---------------------------------------------------------------------------

$requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
$path = parse_url($requestUri, PHP_URL_PATH);
$path = is_string($path) && $path !== '' ? $path : '/';

/** Prefixes that must always be served by the Witals pipeline. */
$witalsPrefixes = ['/dashboard', '/api', '/admin', '/health', '/info', '/login', '/logout', '/_witals'];

$isWitalsRequest = false;
foreach ($witalsPrefixes as $prefix) {
    if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
        $isWitalsRequest = true;
        break;
    }
}

// Dynamic lane decision: if a module registered a controller/route for this
// path (public routes that replace legacy), route it through Witals even when
// the prefix is not in the static list above.
if (!$isWitalsRequest) {
    try {
        // Boot so module routes are registered before we probe the registry.
        $app->boot();
        $registry = $app->make(\App\Http\Routing\Contracts\RouteRegistryInterface::class);
        $req = \Witals\Framework\Http\Request::createFromGlobals();
        if ($registry->match($req, null, $path) !== null) {
            $isWitalsRequest = true;
        }
    } catch (\Throwable) {
        // Ignore — fall back to legacy below.
    }
}

// Static assets are served by the web server itself (Apache/Nginx/PHP built-in
// server resolve them before this script runs). Everything else is a page.
// Legacy lane đã được migrate sang Witals; fallback route trả 404.
$GLOBALS['__witals_app'] = $app;

// Create and start the traditional server using the factory
$server = ServerFactory::create(RuntimeType::TRADITIONAL, $app);
$server->start();
