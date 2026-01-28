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
        
        // Convert string booleans
        if (in_array(strtolower($value), ['true', '(true)'], true)) {
            return true;
        }
        
        if (in_array(strtolower($value), ['false', '(false)'], true)) {
            return false;
        }
        
        if (in_array(strtolower($value), ['null', '(null)'], true)) {
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

if (!function_exists('path_join')) {
    /**
     * Join paths safely across OS
     */
    function path_join(string ...$paths): string
    {
        return preg_replace('#/+#', '/', implode('/', array_filter($paths)));
    }
}
