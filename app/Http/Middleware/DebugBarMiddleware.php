<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Witals\Framework\Application;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

class DebugBarMiddleware
{
    public function __construct(
        private Application $app,
    ) {}

    public function handle(Request $request, \Closure $next): Response
    {
        $response = $next($request);

        if (!env('APP_DEBUG_BAR', false)) {
            return $response;
        }

        if (!$this->app->has(\App\Foundation\Debug\DebugBar::class)) {
            return $response;
        }

        if ($response->getStatusCode() >= 300 && $response->getStatusCode() < 400) {
            return $response;
        }

        $content = $response->getContent();
        if (!is_string($content) || !str_contains($response->getHeader('Content-Type', ''), 'text/html')) {
            return $response;
        }

        $debugBar = $this->app->make(\App\Foundation\Debug\DebugBar::class);
        $debugBarHtml = $debugBar->render();

        if (str_contains($content, '</body>')) {
            $content = str_replace('</body>', $debugBarHtml . '</body>', $content);
        } else {
            $content .= $debugBarHtml;
        }

        return new Response($content, $response->getStatusCode(), $response->getHeaders());
    }
}
