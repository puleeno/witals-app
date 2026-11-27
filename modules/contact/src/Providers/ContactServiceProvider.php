<?php

declare(strict_types=1);

namespace Modules\Contact\Providers;

use Witals\Framework\Support\ServiceProvider;
use App\Http\Routing\Contracts\RouterInterface;
use Modules\Contact\Controllers\ContactController;

class ContactServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $router = $this->app->make(RouterInterface::class);
        $router->post('/contact/submit', [ContactController::class, 'submit']);
    }
}