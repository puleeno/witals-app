<?php

declare(strict_types=1);

namespace Modules\Faq\Providers;

use Witals\Framework\Support\ServiceProvider;
use App\Http\Routing\Contracts\RouterInterface;
use Modules\Faq\Controllers\FaqController;

class FaqServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $router = $this->app->make(RouterInterface::class);
        $router->post('/faq/send', [FaqController::class, 'send']);
    }
}