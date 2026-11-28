<?php

declare(strict_types=1);

namespace Modules\Contact\Providers;

use Witals\Framework\Support\ServiceProvider;
use App\Http\Routing\Contracts\RouterInterface;
use Witals\Framework\Events\Contracts\EventDispatcherInterface;
use Modules\Contact\Controllers\ContactController;
use Modules\Contact\Events\ContactSubmitted;
use Modules\Contact\Listeners\SendContactMail;

class ContactServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $router = $this->app->make(RouterInterface::class);
        $router->post('/contact/submit', [ContactController::class, 'submit']);
        $router->get('/lien-he', [ContactController::class, 'page']);
        $router->get('/lib/imagesercurity.php', [\Modules\Contact\Controllers\CaptchaController::class, 'image']);

        $events = $this->app->make(EventDispatcherInterface::class);
        $events->listen(ContactSubmitted::class, [new SendContactMail($this->app), 'handle']);
    }
}