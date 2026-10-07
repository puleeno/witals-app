<?php

declare(strict_types=1);

namespace App\Http\Routing\Contracts;

use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

interface RouteRegistryInterface
{
    public const PRIORITY_MODULE = 0;
    public const PRIORITY_NATIVE = 1;
    public const PRIORITY_HOOK   = 2;
    public const PRIORITY_FALLBACK = 3;

    public function addRoute(string $method, string $path, mixed $action, int $priority = self::PRIORITY_NATIVE, array $options = []): void;
    public function addRoutes(array $routes, int $priority = self::PRIORITY_NATIVE): void;
    public function match(Request $request, ?int $maxPriority = null, ?string $path = null): ?array;
    public function dispatch(Request $request): ?Response;
    public function getAll(): array;
    public function clear(): void;
}
