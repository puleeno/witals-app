<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\ServiceProvider;
use Psr\Log\LoggerInterface;
use Witals\Framework\Log\LogManager;

/**
 * Log Service Provider
 */
class LogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->singleton(LoggerInterface::class, function ($app) {
            return new LogManager([
                'default' => 'standard',
                'channels' => [
                    'standard' => [
                        'driver' => 'standard',
                        'path' => $app->basePath('storage/logs/prestoworld.log'),
                        'level' => 'info', // Show from Info and above in production log
                        'buffered' => true,
                        'formatter' => 'json',
                    ],
                    'debug' => [
                        'driver' => 'debug',
                        'level' => 'debug', // Show everything in debug console
                    ],
                    'null' => [
                        'driver' => 'null',
                    ],
                ],
            ]);
        });
    }
}
