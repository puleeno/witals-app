<?php

declare(strict_types=1);

namespace App\Http\Mappings;

use App\Contracts\Http\TemplateMappingPolicy;

class ConfigMappingPolicy implements TemplateMappingPolicy
{
    /** @var list<array{type:string, pattern?:string, prefix?:string, template:string}> */
    private array $exactRules;

    /** @var list<array{type:string, pattern?:string, prefix?:string, template:string}> */
    private array $prefixRules;

    private string $defaultTemplate;

    public function __construct(array $mapping, string $defaultTemplate = 'index')
    {
        $this->defaultTemplate = $defaultTemplate;
        $parsed = $this->compileRules($mapping);
        $this->exactRules = $parsed['exact'];
        $this->prefixRules = $parsed['prefix'];
    }

    public function match(string $path): ?string
    {
        foreach ($this->exactRules as $rule) {
            if ($path === $rule['pattern']) {
                return $rule['template'];
            }
        }

        foreach ($this->prefixRules as $rule) {
            if ($rule['type'] === 'wildcard' && str_starts_with($path, $rule['prefix'])) {
                return $rule['template'];
            }
            if ($rule['type'] === 'prefix' && $this->isPrefixMatch($path, $rule['pattern'])) {
                return $rule['template'];
            }
        }

        return $this->defaultTemplate;
    }

    /**
     * @return array{exact: list<array>, prefix: list<array>}
     */
    private function compileRules(array $mapping): array
    {
        $exact = [];
        $prefix = [];
        foreach ($mapping as $pattern => $template) {
            if ($pattern === '/') {
                $exact[] = ['type' => 'exact', 'pattern' => '/', 'template' => $template];
            } elseif (str_ends_with($pattern, '/*')) {
                $wildPrefix = rtrim($pattern, '*');
                $prefix[] = ['type' => 'wildcard', 'prefix' => $wildPrefix, 'template' => $template];
                $exactPrefix = rtrim($wildPrefix, '/');
                if ($exactPrefix !== '') {
                    $exact[] = ['type' => 'exact', 'pattern' => $exactPrefix, 'template' => $template];
                }
            } else {
                $exact[] = ['type' => 'exact', 'pattern' => $pattern, 'template' => $template];
                $prefix[] = ['type' => 'prefix', 'pattern' => $pattern, 'template' => $template];
            }
        }

        usort($prefix, fn(array $a, array $b): int => (strlen($b['prefix'] ?? $b['pattern']) <=> strlen($a['prefix'] ?? $a['pattern'])));

        return ['exact' => $exact, 'prefix' => $prefix];
    }

    private function isPrefixMatch(string $path, string $prefix): bool
    {
        return $prefix !== '/' && str_starts_with($path, $prefix . '/');
    }
}
