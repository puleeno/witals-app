<?php

declare(strict_types=1);

namespace App\Foundation\Config;

class ChainConfigLoader
{
    private array $loaders = [];

    /**
     * Add a loader with associated path
     */
    public function addLoader(ConfigLoader $loader, string $path): self
    {
        $this->loaders[] = ['loader' => $loader, 'path' => $path];
        return $this;
    }

    /**
     * Load all configurations
     */
    public function load(): void
    {
        foreach ($this->loaders as $item) {
            $item['loader']->load($item['path']);
        }
    }
}
