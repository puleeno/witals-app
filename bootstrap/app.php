<?php

/**
 * Application Bootstrap
 * This file initializes the application and is shared between
 * traditional web servers and RoadRunner
 */

declare(strict_types=1);

use Witals\Framework\Application;
use App\Http\Kernel;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Create application instance
$app = new Application(
    basePath: dirname(__DIR__)
);

// Bind important interfaces
$app->singleton(
    \Witals\Framework\Contracts\Http\Kernel::class,
    \App\Http\Kernel::class
);

// Register service providers
$app->registerConfiguredProviders();

// Detect environment and configure accordingly
$environment = defined('WITALS_ENVIRONMENT') ? WITALS_ENVIRONMENT : 'traditional';

if ($environment === 'roadrunner') {
    // RoadRunner-specific configuration
    $app->setRoadRunnerMode(true);

    // Disable session auto-start (RoadRunner handles this differently)
    ini_set('session.auto_start', '0');

    // Optimize for long-running process
    gc_enable();
} else {
    // Traditional web server configuration
    $app->setRoadRunnerMode(false);
}

return $app;
