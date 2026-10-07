<?php

declare(strict_types=1);

namespace App\Http;

use Witals\Framework\Application;
use Witals\Framework\Contracts\Http\Kernel as KernelContract;
use Witals\Framework\Contracts\ResettableInterface;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;
use Psr\Log\LoggerInterface;
use App\Http\Middleware\DebugBarMiddleware;
use App\Http\Middleware\LogRequestMiddleware;

/**
 * HTTP Kernel
 * Orchestrates middleware pipeline and request lifecycle.
 *
 * Per-request lifecycle concerns (DebugBar reset, stateful singleton reset)
 * are handled via ResettableInterface — services register themselves
 * and are reset automatically.
 */
class Kernel implements KernelContract
{
    protected Application $app;
    protected LoggerInterface $logger;

    public function __construct(Application $app, LoggerInterface $logger)
    {
        $this->app = $app;
        $this->logger = $logger;
    }

    protected array $middleware = [
        \App\Http\Middleware\CorsMiddleware::class,
        \App\Http\Middleware\LogRequestMiddleware::class,
        \App\Http\Middleware\LocaleMiddleware::class,
        \Witals\Framework\Auth\Middleware\AuthMiddleware::class,
        \App\Http\Middleware\AdminAuthMiddleware::class,
        \App\Http\Middleware\DebugBarMiddleware::class,
    ];

    public function handle(Request $request): Response
    {
        // Per-request lifecycle: reset all stateful singletons
        foreach ($this->app->getInstances() as $instance) {
            if ($instance instanceof ResettableInterface) {
                $instance->reset();
            }
        }

        // Bind the current request instance to the container
        $this->app->instance(Request::class, $request);

        $pipeline = $this->middleware;
        $pipeline[] = fn($request) => $this->dispatchToRouter($request);

        return $this->sendRequestThroughPipeline($request, $pipeline);
    }

    protected function sendRequestThroughPipeline(Request $request, array $pipeline): Response
    {
        $middleware = array_shift($pipeline);

        if ($middleware === null) {
            throw new \RuntimeException('Middleware pipeline exhausted without response');
        }

        $next = fn($nextRequest) => $this->sendRequestThroughPipeline($nextRequest, $pipeline);

        if ($middleware instanceof \Closure) {
            return $middleware($request, $next);
        }

        if (is_string($middleware)) {
            $instance = $this->app->make($middleware);
            if (method_exists($instance, 'handle')) {
                return $instance->handle($request, $next);
            }
        }

        throw new \RuntimeException('Invalid middleware: ' . json_encode($middleware));
    }

    protected function dispatchToRouter(Request $request): Response
    {
        try {
            $router = $this->app->make(\App\Http\Routing\Router::class);
            $result = $router->dispatch($request);

            if ($result instanceof Response) {
                return $result;
            }

            return Response::html((string) $result);
        } catch (\Throwable $e) {
            $this->logger->error('Request error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            return Response::json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
}
