<?php

declare(strict_types=1);

namespace Modules\Email\Services;

class MailSender
{
    public function send(string $to, string $subject, string $body): bool
    {
        // Mail logic here
        return true;
    }
}
