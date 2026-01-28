<?php

declare(strict_types=1);

namespace App\Foundation\Config\Contracts;

interface ConfigurationReaderInterface
{
    /**
     * Read configuration from source
     * 
     * @param string $path Source path (optional)
     * @return mixed Raw configuration data
     */
    public function read(string $path = ''): mixed;
}
