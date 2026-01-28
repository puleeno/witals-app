<?php

/**
 * PrestoWorld Application Bootstrap
 * 
 * This file initializes the PrestoWorld application and is shared across all runtimes:
 * - Traditional (PHP-FPM/Apache/Nginx)
 * - RoadRunner
 * - ReactPHP
 * - Swoole
 * - OpenSwoole
 */

declare(strict_types=1);

use App\Foundation\Application;
use Witals\Framework\Contracts\RuntimeType;

use App\Foundation\Config\ChainConfigLoader;
use App\Foundation\Config\ConfigLoader;
use App\Foundation\Config\Dotenv\DotenvReader;
use App\Foundation\Config\Dotenv\DotenvTransformer;
use App\Foundation\Config\WordPress\WordPressConfigReader;
use App\Foundation\Config\WordPress\WordPressConfigTransformer;

// 1. Initialize Chain Config Loader
$loader = new ChainConfigLoader();

// 2. Add Native .env loader (High Priority)
$loader->addLoader(
    new ConfigLoader(new DotenvReader(), new DotenvTransformer()),
    __DIR__ . '/../.env'
);

// 3. Add WordPress config loader (Fallback Strategy / Zero Migrate)
$loader->addLoader(
    new ConfigLoader(new WordPressConfigReader(), new WordPressConfigTransformer()),
    __DIR__ . '/../public/wp-config.php'
);

// 4. Execute Load
$loader->load();

// Auto-detect runtime or use explicitly set environment
$runtime = null;
if (defined('WITALS_RUNTIME')) {
    $runtime = RuntimeType::from(WITALS_RUNTIME);
}

// Create PrestoWorld application instance
$app = new Application(
    basePath: dirname(__DIR__),
    runtime: $runtime
);

// Bind HTTP Kernel
$app->singleton(
    \Witals\Framework\Contracts\Http\Kernel::class,
    \App\Http\Kernel::class
);

// Load service providers from config
$providers = $app->config('app.providers', []);
$app->registerProviders($providers);

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
