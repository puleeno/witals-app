<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\ServiceProvider;
use App\Foundation\Modules\ModuleContext;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Shared context/hook bus used by all modules to exchange data and
        // embed functionality into one another.
        $this->singleton(ModuleContext::class, static fn () => new ModuleContext());
    }
}
