<?php

/**
 * Entry point for traditional web servers (Apache, Nginx, PHP built-in server)
 * This file handles requests when running on traditional PHP-FPM/CGI
 */

declare(strict_types=1);

define('WITALS_START', microtime(true));
define('WITALS_ENVIRONMENT', 'traditional');

// Register the Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap the application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Boot the application (lifecycle: onBoot)
$app->boot();

// Handle the incoming request
$request = \Witals\Framework\Http\Request::createFromGlobals();
$response = $app->handle($request);

// Send the response
$response->send();

// Terminate the application
$app->terminate($request, $response);
