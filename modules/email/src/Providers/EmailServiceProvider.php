<?php

declare(strict_types=1);

namespace Modules\Email\Providers;

use Witals\Framework\Support\ServiceProvider;
use Psr\Log\LoggerInterface;
use Modules\Email\Services\MailSender;

class EmailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MailSender::class, function () {
            return new MailSender();
        });

        $this->app->alias(MailSender::class, 'email.sender');
    }

    public function boot(): void
    {
        add_action('module.email.registered', function () {
            $this->app->make(LoggerInterface::class)->info('[Email] Module ready — other modules can now use email.sender');
        });
    }
}
