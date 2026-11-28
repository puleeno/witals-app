<?php

declare(strict_types=1);

namespace Modules\Comment\Providers;

use Witals\Framework\Support\ServiceProvider;
use App\Http\Routing\Contracts\RouterInterface;
use Modules\Comment\Controllers\CommentController;

class CommentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $router = $this->app->make(RouterInterface::class);
        $router->post('/comment/post', [CommentController::class, 'post']);
    }
}