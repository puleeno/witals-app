<?php

declare(strict_types=1);

namespace Modules\Newsletter\Providers;

use Witals\Framework\Support\ServiceProvider;
use App\Http\Routing\Contracts\RouterInterface;
use Modules\Newsletter\Controllers\SubscribeController;

/**
 * Đăng ký route public thay thế legacy newsletter submit.
 */
class NewsletterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $router = $this->app->make(RouterInterface::class);
        $router->post('/newsletter/subscribe', [SubscribeController::class, 'subscribe']);
    }
}