<?php

declare(strict_types=1);

namespace App\Http;

use Witals\Framework\Application;
use Witals\Framework\Contracts\Http\Kernel as KernelContract;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;
use Psr\Log\LoggerInterface;

/**
 * HTTP Kernel
 * Handles HTTP request processing and middleware
 */
class Kernel implements KernelContract
{
    use KernelStateDemoTrait;

    protected Application $app;
    protected array $middleware = [];
    protected LoggerInterface $logger;

    public function __construct(Application $app, LoggerInterface $logger)
    {
        $this->app = $app;
        $this->logger = $logger;
    }

    /**
     * Handle an incoming HTTP request
     */
    public function handle(Request $request): Response
    {
        $this->logger->info("Incoming request: {method} {uri}", [
            'method' => $request->method(),
            'uri' => $request->uri(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        ]);

        try {
            // Simple routing example
            $path = $request->path();

            // Route: Home
            if ($path === '/' || $path === '') {
                return $this->handleHome($request);
            }

            // Route: Health check
            if ($path === '/health') {
                return $this->handleHealth($request);
            }

            // Route: Info
            if ($path === '/info') {
                return $this->handleInfo($request);
            }

            // Route: State Demo
            if ($path === '/state-demo') {
                return $this->handleStateDemo($request);
            }

            // 404 Not Found
            return Response::json([
                'error' => 'Not Found',
                'path' => $path,
            ], 404);

        } catch (\Throwable $e) {
            return Response::json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle home route
     */
    protected function handleHome(Request $request): Response
    {
        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Witals Framework</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                }
                .container {
                    text-align: center;
                    padding: 2rem;
                }
                h1 {
                    font-size: 3rem;
                    margin-bottom: 1rem;
                    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
                }
                .badge {
                    display: inline-block;
                    padding: 0.5rem 1rem;
                    background: rgba(255,255,255,0.2);
                    border-radius: 20px;
                    margin: 0.5rem;
                    backdrop-filter: blur(10px);
                }
                .info {
                    margin-top: 2rem;
                    padding: 1.5rem;
                    background: rgba(255,255,255,0.1);
                    border-radius: 10px;
                    backdrop-filter: blur(10px);
                }
                .links {
                    margin-top: 2rem;
                }
                .links a {
                    color: white;
                    text-decoration: none;
                    padding: 0.75rem 1.5rem;
                    background: rgba(255,255,255,0.2);
                    border-radius: 5px;
                    margin: 0.5rem;
                    display: inline-block;
                    transition: all 0.3s;
                }
                .links a:hover {
                    background: rgba(255,255,255,0.3);
                    transform: translateY(-2px);
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>🚀 Witals Framework</h1>
                <div class="badge">Environment: {$this->getEnvironmentName()}</div>
                <div class="info">
                    <p><strong>PHP Version:</strong> {$this->getPhpVersion()}</p>
                    <p><strong>Server:</strong> {$this->getServerInfo()}</p>
                    <p><strong>Memory Usage:</strong> {$this->getMemoryUsage()}</p>
                </div>
                <div class="links">
                    <a href="/health">Health Check</a>
                    <a href="/info">System Info</a>
                    <a href="/state-demo">State Demo</a>
                </div>
            </div>
        </body>
        </html>
        HTML;

        return Response::html($html);
    }

    /**
     * Handle health check route
     */
    protected function handleHealth(Request $request): Response
    {
        return Response::json([
            'status' => 'healthy',
            'environment' => $this->getEnvironmentName(),
            'timestamp' => time(),
            'uptime' => $this->getUptime(),
        ]);
    }

    /**
     * Handle info route
     */
    protected function handleInfo(Request $request): Response
    {
        return Response::json([
            'app' => [
                'name' => 'Witals Framework',
                'environment' => $this->getEnvironmentName(),
                'is_roadrunner' => $this->app->isRoadRunner(),
            ],
            'php' => [
                'version' => PHP_VERSION,
                'sapi' => PHP_SAPI,
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
            ],
            'server' => [
                'software' => $this->getServerInfo(),
                'protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'Unknown',
            ],
            'performance' => [
                'memory_usage' => $this->getMemoryUsage(),
                'peak_memory' => $this->getPeakMemory(),
                'uptime' => $this->getUptime(),
            ],
        ]);
    }

    protected function getEnvironmentName(): string
    {
        if ($this->app->isRoadRunner()) return 'RoadRunner';
        if ($this->app->isReactPhp()) return 'ReactPHP';
        if ($this->app->isSwoole()) return 'Swoole';
        if ($this->app->isOpenSwoole()) return 'OpenSwoole';
        
        return 'Traditional Web Server';
    }

    protected function getPhpVersion(): string
    {
        return PHP_VERSION;
    }

    protected function getServerInfo(): string
    {
        if ($this->app->isRoadRunner()) return 'RoadRunner';
        if ($this->app->isReactPhp()) return 'ReactPHP (Event Loop)';
        if ($this->app->isSwoole()) return 'Swoole Server';
        if ($this->app->isOpenSwoole()) return 'OpenSwoole Server';

        return $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown SAPI';
    }

    protected function getMemoryUsage(): string
    {
        return $this->formatBytes(memory_get_usage(true));
    }

    protected function getPeakMemory(): string
    {
        return $this->formatBytes(memory_get_peak_usage(true));
    }

    protected function getUptime(): string
    {
        if (!defined('WITALS_START')) {
            return 'N/A';
        }
        $uptime = microtime(true) - WITALS_START;
        return number_format($uptime, 3) . 's';
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
