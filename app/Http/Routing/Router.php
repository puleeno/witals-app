<?php

declare(strict_types=1);

namespace App\Http\Routing;

use Witals\Framework\Application;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;
use Psr\Log\LoggerInterface;
use App\Http\Routing\Contracts\RouterInterface;
use App\Http\Routing\Contracts\RouteRegistryInterface;

class Router implements RouterInterface
{
    protected RouteRegistryInterface $registry;
    protected \Psr\Log\LoggerInterface $logger;
    protected ?\Closure $wordPressFallback = null;
    protected ?\Closure $hookFallback = null;
    protected array $prefixStack = [];

    public function __construct(Application $app, \Psr\Log\LoggerInterface $logger)
    {
        $this->registry = $app->make(RouteRegistryInterface::class);
        $this->logger = $logger;
    }

    public function get(string $path, $action): void
    {
        $this->addRoute('GET', $path, $action);
    }

    public function post(string $path, $action): void
    {
        $this->addRoute('POST', $path, $action);
    }

    public function put(string $path, $action): void
    {
        $this->addRoute('PUT', $path, $action);
    }

    public function delete(string $path, $action): void
    {
        $this->addRoute('DELETE', $path, $action);
    }

    public function group(array $attributes, \Closure $callback): void
    {
        $prefix = $attributes['prefix'] ?? '';
        $this->prefixStack[] = $prefix;
        $callback($this);
        array_pop($this->prefixStack);
    }

    protected function addRoute(string $method, string $path, $action): void
    {
        $prefix = implode('', $this->prefixStack);
        $fullPath = $prefix . '/' . ltrim($path, '/');
        $this->registry->addRoute(
            method: $method,
            path: $fullPath,
            action: $action,
            priority: RouteRegistryInterface::PRIORITY_NATIVE,
        );
        $this->logger->debug("Router: Registered route {method} {path}", ['method' => $method, 'path' => $fullPath]);
    }

    public function setWordPressFallback(callable $fallback): void
    {
        $this->wordPressFallback = $fallback;
    }

    public function setHookFallback(callable $hook): void
    {
        $this->hookFallback = $hook;
    }

    public function loadRoutesFrom(string $path): void
    {
        if (file_exists($path)) {
            $router = $this;
            require $path;
            
            $this->logger->info("Router: Loaded external routes from '{path}'", [
                'path' => $path
            ]);
        }
    }

    public function dispatch(Request $request): mixed
    {
        $path = '/' . ltrim($request->path(), '/');
        $this->logger->debug("Router: dispatching {method} {path}", [
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
            'path' => $path,
        ]);

        // 1. Try module routes (PRIORITY_MODULE = 0) + native routes (PRIORITY_NATIVE = 1)
        $result = $this->registry->dispatch($request);
        if ($result !== null) {
            $this->logger->info("Router: Match found via registry for {method} {path}", [
                'method' => $request->method(),
                'path' => $path,
            ]);
            return $result;
        }

        // 2. Try hook-registered routes (PRIORITY_HOOK = 2)
        if ($this->hookFallback !== null) {
            $hookResult = ($this->hookFallback)($request);
            if ($hookResult !== null) {
                return $hookResult;
            }
        }

        // 3. Try WordPress rewrite rules (PRIORITY_FALLBACK)
        $this->logger->info("Router: No match for {method} {path}", [
            'method' => $request->method(),
            'path' => $path,
        ]);

        if ($this->wordPressFallback) {
            return ($this->wordPressFallback)($request);
        }

        return Response::json(['error' => 'Not Found'], 404);
    }

    public function getRoutes(): array
    {
        return $this->registry->getAll();
    }
}
