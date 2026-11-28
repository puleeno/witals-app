<?php

declare(strict_types=1);

namespace Modules\Contact\Listeners;

use App\Foundation\Application;
use Modules\Contact\Events\ContactSubmitted;

class SendContactMail
{
    public function __construct(private readonly Application $app)
    {
    }

    public function handle(ContactSubmitted $event): void
    {
        $data = $event->data;
        $email = trim((string) ($data['email'] ?? ''));
        if ($email === '') {
            return;
        }

        $subject = 'Xanhvina — Liên hệ từ khách hàng: ' . (string) ($data['name'] ?? '');
        $body = '<h3>Thông tin liên hệ mới</h3>'
            . '<p><b>Họ tên:</b> ' . htmlspecialchars((string) ($data['name'] ?? '')) . '</p>'
            . '<p><b>Email:</b> ' . htmlspecialchars($email) . '</p>'
            . '<p><b>Điện thoại:</b> ' . htmlspecialchars((string) ($data['phone'] ?? '')) . '</p>'
            . '<p><b>Địa chỉ:</b> ' . htmlspecialchars((string) ($data['address'] ?? '')) . '</p>'
            . '<p><b>Nội dung:</b> ' . htmlspecialchars((string) ($data['message'] ?? '')) . '</p>';

        try {
            $sender = $this->app->make('email.sender');
            $sender->send((string) ($data['to'] ?? 'info.xanhvina@gmail.com'), $subject, $body);
        } catch (\Throwable) {
            // mail is best-effort; never break the request
        }
    }
}