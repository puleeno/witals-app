<?php

declare(strict_types=1);

if (!function_exists('env')) {
    /**
     * Get environment variable value
     */
    function env(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        
        if ($value === false) {
            return $default;
        }

        if (!is_string($value)) {
            return $value;
        }
        
        $lowercaseValue = strtolower($value);

        // Convert string booleans
        if (in_array($lowercaseValue, ['true', '(true)'], true)) {
            return true;
        }
        
        if (in_array($lowercaseValue, ['false', '(false)'], true)) {
            return false;
        }
        
        if (in_array($lowercaseValue, ['null', '(null)'], true)) {
            return null;
        }
        
        return $value;
    }
}

if (!function_exists('app')) {
    /**
     * Get the application instance
     */
    function app(?string $abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return \Witals\Framework\Container\Container::getInstance();
        }
        
        return \Witals\Framework\Container\Container::getInstance()->make($abstract, $parameters);
    }
}

if (!function_exists('config')) {
    /**
     * Get configuration value
     */
    function config(string $key, $default = null)
    {
        return app()->config($key, $default);
    }
}

if (!function_exists('base_path')) {
    /**
     * Get base path
     */
    function base_path(string $path = ''): string
    {
        return app()->basePath($path);
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get storage path
     */
    function storage_path(string $path = ''): string
    {
        return app()->storagePath($path);
    }
}

if (!function_exists('path_join')) {
    /**
     * Join paths safely across OS
     */
    function path_join(string ...$paths): string
    {
        return preg_replace('#/+#', '/', implode('/', array_filter($paths)));
    }
}
if (!function_exists('now')) {
    /**
     * Get a new Chronos instance for the current time
     */
    function now(): \Cake\Chronos\Chronos
    {
        return \Cake\Chronos\Chronos::now();
    }
}

if (!function_exists('trans')) {
    /**
     * Translate the given message.
     */
    function trans(string $key, array $replace = [], ?string $locale = null)
    {
        return app()->translator()->get($key, $replace, $locale);
    }
}

if (!function_exists('__')) {
    /**
     * Translate the given message (alias for trans).
     * Supports both:
     *   Witals: __($key, $replace, $locale)
     *   WordPress: __($text, $domain)
     */
    function __(string $key, array|string $replace = [], ?string $locale = null)
    {
        if (is_string($replace)) {
            // WordPress-style: __($text, $domain)
            // $replace is actually the text domain.
            // Fall back to $key (no translation in non-WP context).
            return $key;
        }
        return trans($key, $replace, $locale);
    }
}

if (!function_exists('locale_url')) {
    /**
     * Get URL with locale prefix.
     */
    function locale_url(string $path, ?string $locale = null): string
    {
        $locale = $locale ?: app()->translator()->getLocale();
        $defaultLocale = config('app.locale', 'en');
        
        $path = '/' . ltrim($path, '/');
        
        // Don't prefix if it's already prefixed
        if (preg_match('#^/([a-z]{2})(/.*)?$#', $path, $matches)) {
            $supportedLocales = config('app.locales', ['en', 'vi']);
            if (in_array($matches[1], $supportedLocales, true)) {
                return $path;
            }
        }

        if ($locale === $defaultLocale) {
            return $path;
        }
        
        return '/' . $locale . $path;
    }
}

if (!function_exists('route_url')) {
    /**
     * Get a localized URL for a route key.
     */
    function route_url(string $key, ?string $locale = null): string
    {
        $path = __('routes.' . $key, [], $locale);
        if ($path === 'routes.' . $key) {
            $path = $key; // Fallback to key if no translation
        }
        
        return locale_url($path, $locale);
    }
}

if (!function_exists('current_url_with_lang')) {
    /**
     * Get current URL with a different language code.
     */
    function current_url_with_lang(string $locale): string
    {
        $request = app(\Witals\Framework\Http\Request::class);
        $path = $request->path();
        
        // Strip out existing locale prefix from request path if present
        $supportedLocales = config('app.locales', ['en', 'vi']);
        if (preg_match('#^/([a-z]{2})(/.*)?$#', $path, $matches)) {
            if (in_array($matches[1], $supportedLocales, true)) {
                $path = ($matches[2] ?? '/');
            }
        }
        
        $url = locale_url($path, $locale);
        
        $query = $_GET;
        unset($query['lang']); // Cleanup old system
        
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }
        
        return $url;
    }
}

if (!function_exists('is_vietnam')) {
    /**
     * Check if the current locale is Vietnamese.
     */
    function is_vietnam(): bool
    {
        return app()->translator()->getLocale() === 'vi';
    }
}

if (!function_exists('current_locale')) {
    /**
     * Get the current application locale.
     */
    function current_locale(): string
    {
        return app()->translator()->getLocale();
    }
}

if (!function_exists('auth_user')) {
    /**
     * Get the currently authenticated user.
     */
    function auth_user(): ?array
    {
        $auth = app(\Witals\Framework\Contracts\Auth\AuthContextInterface::class);
        $token = $auth->getToken();
        
        return $token ? $token->getPayload() : null;
    }
}

if (!function_exists('is_authenticated')) {
    /**
     * Check if the current user is authenticated.
     */
    function is_authenticated(): bool
    {
        return auth_user() !== null;
    }
}

if (!function_exists('view')) {
    /**
     * Render a view template.
     */
    function view(string $view, array $data = []): string
    {
        return app(\Witals\Framework\Contracts\View\Factory::class)->make($view, $data)->render();
    }
}

if (!function_exists('dispatch')) {
    /**
     * Dispatch a job to the queue.
     */
    function dispatch(object $job): ?string
    {
        /** @var \Witals\Framework\Queue\QueueManager $queue */
        $queue = app(\Witals\Framework\Queue\QueueManager::class);

        return $queue->push($job);
    }
}

if (!function_exists('dispatch_later')) {
    /**
     * Dispatch a job to the queue after a delay.
     */
    function dispatch_later(\DateTimeInterface|\DateInterval|int $delay, object $job): ?string
    {
        /** @var \Witals\Framework\Queue\QueueManager $queue */
        $queue = app(\Witals\Framework\Queue\QueueManager::class);

        return $queue->later($delay, $job);
    }
}

if (!function_exists('redirect')) {
    /**
     * Create a redirect response.
     */
    function redirect(string $url, int $status = 302): \Witals\Framework\Http\Response
    {
        return \Witals\Framework\Http\Response::redirect($url, $status);
    }
}
