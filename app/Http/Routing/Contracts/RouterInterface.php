<?php

declare(strict_types=1);

namespace App\Http\Routing\Contracts;

use Witals\Framework\Http\Request;

interface RouterInterface
{
    public function get(string $path, mixed $action): void;
    public function post(string $path, mixed $action): void;
    public function put(string $path, mixed $action): void;
    public function delete(string $path, mixed $action): void;
    public function dispatch(Request $request): mixed;
    public function setWordPressFallback(callable $fallback): void;
    public function setHookFallback(callable $hook): void;
    public function loadRoutesFrom(string $path): void;
    public function getRoutes(): array;
}
