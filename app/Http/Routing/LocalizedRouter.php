<?php

declare(strict_types=1);

namespace App\Http\Routing;

use App\Http\Routing\Contracts\RouterInterface;
use App\Http\Routing\Contracts\RouteRegistryInterface;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

/**
 * LocalizedRouter — Strategy Pattern implementation for multilingual routing.
 *
 * PERFORMANCE DESIGN:
 * ─────────────────────────────────────────────────────────────────
 * • Selected once at container boot via RouterFactory (singleton).
 * • Per-request work is minimal: one regex match on the path string,
 *   then delegate to parent::dispatch() with a locale-stripped path.
 * • NO Reflection — uses Route::matchesMethodAndPath(method, path)
 *   which accepts the stripped path directly, avoiding Request mutation.
 * ─────────────────────────────────────────────────────────────────
 *
 * URL structure:
 *   Default locale  → /path          (no prefix)
 *   Other locales   → /{locale}/path
 */
class LocalizedRouter extends Router implements RouterInterface
{
    protected array $supportedLocales;
    protected string $defaultLocale;

    public function __construct(
        \Witals\Framework\Application $app,
        \Psr\Log\LoggerInterface $logger,
        array $supportedLocales,
        string $defaultLocale
    ) {
        parent::__construct($app, $logger);
        $this->supportedLocales = $supportedLocales;
        $this->defaultLocale    = $defaultLocale;
    }

    /**
     * Dispatch with locale-prefix awareness.
     *
     * Strip the locale prefix once, resolve locale,
     * then match routes against the clean canonical path.
     */
    public function dispatch(Request $request): mixed
    {
        $rawPath = $request->path();
        $locale  = $this->defaultLocale;
        $path    = $rawPath;

        // Cheap single-regex extraction: /{locale}[/rest]
        if (preg_match('#^/([a-z]{2})(/.*)?$#', $rawPath, $m)
            && in_array($m[1], $this->supportedLocales, true)
        ) {
            $locale = $m[1];
            $path   = ($m[2] ?? '/');
        }

        $this->logger->debug("LocalizedRouter: rawPath={rawPath}, locale={locale}, cleanPath={cleanPath}", [
            'rawPath' => $rawPath,
            'locale' => $locale,
            'cleanPath' => $path ?? '/',
        ]);

        // Set locale ONCE per request — O(1)
        app()->translator()->setLocale($locale);
        app()->view()->share('current_locale', $locale);

        $request = $request->withAttribute('locale', $locale);

        // Route matching against stripped path via registry
        $method = $request->method();

        $matched = $this->registry->match($request, RouteRegistryInterface::PRIORITY_HOOK, $path);
        if ($matched !== null) {
            return $this->registry->runAction($matched['action'], $request, $matched['params']);
        }

        // Hook-registered routes
        if ($this->hookFallback !== null) {
            $result = ($this->hookFallback)($request, ['locale' => $locale]);
            if ($result !== null) {
                return $result;
            }
        }

        // WordPress fallback
        if ($this->wordPressFallback) {
            return ($this->wordPressFallback)($request);
        }

        return Response::json(['error' => 'Not Found'], 404);
    }
}
