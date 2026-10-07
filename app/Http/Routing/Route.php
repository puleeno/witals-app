<?php

declare(strict_types=1);

namespace App\Http\Routing;

use Closure;
use Witals\Framework\Http\Request;

class Route
{
    protected string $method;
    protected string $path;
    protected $action;
    protected array $parameters = [];

    protected array $wheres = [];

    public function __construct(string $method, string $path, $action)
    {
        $this->method = strtoupper($method);
        $this->path = '/' . ltrim($path, '/');
        $this->action = $action;
    }

    public function where(string|array $name, ?string $expression = null): static
    {
        if (is_array($name)) {
            foreach ($name as $n => $exp) {
                $this->wheres[$n] = $exp;
            }
        } else {
            $this->wheres[$name] = $expression;
        }

        return $this;
    }

    public function matches(Request $request): bool
    {
        $requestMethod = $request->method();
        if ($this->method !== $requestMethod) {
            // Standard: HEAD matches GET
            if (!($this->method === 'GET' && $requestMethod === 'HEAD')) {
                return false;
            }
        }

        $pattern = $this->getRegexPattern();
        $path = $request->path();
        
        $match = preg_match($pattern, $path, $matches);
        // error_log("Route: Matching '{$path}' against '{$pattern}' -> " . ($match ? 'YES' : 'NO'));
        
        if ($match) {
            $this->parameters = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return true;
        }

        return false;
    }

    /**
     * Match against an explicit method + path string (no Request mutation needed).
     * Used by LocalizedRouter to match against a locale-stripped path.
     */
    public function matchesMethodAndPath(string $method, string $path): bool
    {
        $requestMethod = strtoupper($method);
        if ($this->method !== $requestMethod) {
            if (!($this->method === 'GET' && $requestMethod === 'HEAD')) {
                return false;
            }
        }

        $pattern = $this->getRegexPattern();
        $match   = preg_match($pattern, $path, $matches);

        if ($match) {
            $this->parameters = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return true;
        }

        return false;
    }

    public function getRegexPattern(): string
    {
        // Convert {param} to (?P<param>[^/]+) or (?P<param>expression)
        return '#^' . preg_replace_callback('/\{([a-zA-Z0-9_]+)\}/', function ($matches) {
            $name = $matches[1];
            $expression = $this->wheres[$name] ?? '[^/]+';
            return "(?P<{$name}>{$expression})";
        }, $this->path) . '$#';
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
