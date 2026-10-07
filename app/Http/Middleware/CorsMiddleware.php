<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

class CorsMiddleware
{
    protected array $allowedOrigins;

    protected array $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    protected array $allowedHeaders = [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'Accept',
        'Origin',
    ];

    protected int $maxAge = 86400;

    public function __construct()
    {
        $origins = env('CORS_ALLOWED_ORIGINS', '');
        if ($origins === '*') {
            $this->allowedOrigins = ['*'];
        } elseif ($origins !== '') {
            $this->allowedOrigins = array_map('trim', explode(',', $origins));
        } elseif (env('APP_ENV', 'production') !== 'production') {
            $this->allowedOrigins = ['*'];
        } else {
            $this->allowedOrigins = [];
        }
    }

    public function handle(Request $request, \Closure $next): Response
    {
        $origin = $request->header('Origin', '');

        if ($request->method() === 'OPTIONS') {
            return $this->createPreflightResponse($origin);
        }

        $response = $next($request);

        if ($origin === '' || $origin === null) {
            return $response->withHeader('Access-Control-Allow-Origin', '*');
        }

        if (in_array('*', $this->allowedOrigins, true) || in_array($origin, $this->allowedOrigins, true)) {
            $response = $response
                ->withHeader('Access-Control-Allow-Origin', $origin)
                ->withHeader('Access-Control-Allow-Credentials', 'true')
                ->withHeader('Access-Control-Expose-Headers', 'Content-Disposition');
        }

        return $response;
    }

    protected function createPreflightResponse(string $origin): Response
    {
        $response = new Response('', 204);

        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $origin ?: '*')
            ->withHeader('Access-Control-Allow-Methods', implode(', ', $this->allowedMethods))
            ->withHeader('Access-Control-Allow-Headers', implode(', ', $this->allowedHeaders))
            ->withHeader('Access-Control-Max-Age', (string) $this->maxAge);

        if ($origin !== '') {
            $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}
