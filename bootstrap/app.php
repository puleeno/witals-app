<?php

/**
 * Application Bootstrap
 * This file initializes the application and is shared across all runtimes:
 * - Traditional (PHP-FPM/Apache/Nginx)
 * - RoadRunner
 * - ReactPHP
 * - Swoole
 * - OpenSwoole
 */

declare(strict_types=1);

use Witals\Framework\Application;
use Witals\Framework\Contracts\RuntimeType;
use App\Http\Kernel;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Auto-detect runtime or use explicitly set environment
$runtime = null;
if (defined('WITALS_RUNTIME')) {
    $runtime = RuntimeType::from(WITALS_RUNTIME);
}

// Create application instance with auto-detected or explicit runtime
$app = new Application(
    basePath: dirname(__DIR__),
    runtime: $runtime
);

// Bind important interfaces
$app->singleton(
    \Witals\Framework\Contracts\Http\Kernel::class,
    \App\Http\Kernel::class
);

// Register Enterprise Logger
$app->singleton(\Psr\Log\LoggerInterface::class, function ($app) {
    return new \Witals\Framework\Log\LogManager([
        'default'  => getenv('APP_DEBUG') === 'true' ? 'debug' : 'standard',
        'channels' => [
            'standard' => [
                'driver'    => 'standard',
                'path'      => $app->basePath('storage/logs/witals.log'),
                'buffered'  => true,
                'formatter' => 'json', // Use JSON for enterprise log analysis
            ],
            'debug' => [
                'driver' => 'debug',
            ],
        ],
    ]);
});

// Register service providers
$app->registerConfiguredProviders();

// Configure based on runtime type
if ($app->isLongRunning()) {
    // Long-running process optimizations
    // (RoadRunner, ReactPHP, Swoole, OpenSwoole)
    
    // Disable session auto-start (handled differently in long-running)
    ini_set('session.auto_start', '0');
    
    // Enable garbage collection
    gc_enable();
    
    // Set memory limit for long-running processes
    if (!ini_get('memory_limit') || ini_get('memory_limit') === '-1') {
        ini_set('memory_limit', '256M');
    }
}

return $app;
