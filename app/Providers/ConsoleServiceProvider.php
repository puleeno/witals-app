<?php

declare(strict_types=1);

namespace App\Providers;

use Witals\Framework\Support\ServiceProvider;
use Witals\Framework\Console\Kernel;
use App\Console\Commands\WordPressZeroMigrateCommand;

class ConsoleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->extend(Kernel::class, function (Kernel $kernel) {
            $kernel->register(WordPressZeroMigrateCommand::class);
            return $kernel;
        });
    }
}
