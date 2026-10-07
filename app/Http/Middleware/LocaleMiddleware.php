<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

/**
 * LocaleMiddleware — Early Locale Detection.
 * 
 * Works on Traditional Servers and RoadRunner.
 * Detects locale from URL prefix, Cookie, or Header early in the lifecycle.
 * This ensures the Translator has the correct locale even before routing starts.
 */
class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $supportedLocales = config('app.locales', ['en', 'vi']);
        $defaultLocale = config('app.locale', 'en');
        $locale = $defaultLocale;
        
        $path = $request->path();

        // 1. Detect from URL Prefix O(1)
        if (preg_match('#^/([a-z]{2})(/.*)?$#', $path, $matches)) {
            $candidate = $matches[1];
            if (in_array($candidate, $supportedLocales, true)) {
                $locale = $candidate;
            }
        } else {
            // 2. Fallback to Cookie
            if ($cookieLocale = $request->cookie('app_locale')) {
                if (in_array($cookieLocale, $supportedLocales, true)) {
                    $locale = $cookieLocale;
                }
            } else {
                // 3. Fallback to Accept-Language
                if ($header = $request->header('Accept-Language')) {
                    $lang = substr($header, 0, 2);
                    if (in_array($lang, $supportedLocales, true)) {
                        $locale = $lang;
                    }
                }
            }
        }

        // Set locale early
        app()->translator()->setLocale($locale);
        app()->view()->share('current_locale', $locale);
        
        return $next($request);
    }
}
