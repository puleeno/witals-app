<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Log\LoggerInterface;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

class LogRequestMiddleware
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function handle(Request $request, \Closure $next): Response
    {
        $this->logger->info('Incoming request: {method} {uri}', [
            'method' => $request->method(),
            'uri' => $request->uri(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        ]);

        return $next($request);
    }
}
