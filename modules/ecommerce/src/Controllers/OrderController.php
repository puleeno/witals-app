<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Controllers;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;
use Witals\Framework\Contracts\Session\SessionInterface;
use Witals\Framework\Http\AbstractController;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

class OrderController extends AbstractController
{
    private const SESSION_KEY = 'ecommerce_cart';

    public function __construct(
        private readonly Application $app,
        private readonly DatabaseProviderInterface $dbal,
        private readonly SessionInterface $session,
    ) {
    }

    /**
     * POST /cart/checkout — lưu đơn hàng từ cart session, redirect /contact-success.
     */
    public function checkout(Request $request): Response
    {
        $this->session->start();
        $cart = $this->session->get(self::SESSION_KEY, []);
        if (!is_array($cart) || !$cart) {
            return Response::redirect('/gio-hang');
        }

        $input = $request->input() ?? [];
        if (!is_array($input)) {
            $input = [];
        }

        $name = trim((string) ($input['yourname'] ?? ''));
        $phone = trim((string) ($input['phone'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $address = trim((string) ($input['address'] ?? ''));
        $note = trim((string) ($input['other_request'] ?? ''));
        $payment = (string) ($input['httt'] ?? '1');

        $total = 0;
        $lines = [];
        foreach ($cart as $item) {
            if (!is_array($item)) {
                continue;
            }
            $price = (float) preg_replace('/\D/', '', (string) ($item['price'] ?? '0'));
            $qty = max(1, (int) ($item['qty'] ?? 1));
            $total += $price * $qty;
            $lines[] = ($item['name'] ?? '') . ' x' . $qty;
        }

        try {
            $this->dbal->database()->execute(
                'INSERT INTO orders (session, createdate, username, name, content, phone, email, address, addinfo, xem, code, status, total, payment_type, pay_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, 0, ?, ?, 0)',
                [
                    session_id(),
                    time(),
                    $name,
                    $name,
                    implode(', ', $lines) . ($note !== '' ? ' | ' . $note : ''),
                    $phone,
                    $email,
                    $address,
                    $note,
                    'save',
                    $total,
                    $payment,
                ]
            );
        } catch (\Throwable) {
            // best-effort
        }

        $this->session->set(self::SESSION_KEY, []);

        return Response::redirect('/contact-success');
    }

    public function success(Request $request): Response
    {
        return Response::html($this->render('orders/success', [
            'page_title' => 'Đặt hàng thành công',
        ]));
    }

    /** @param array<string, mixed> $data */
    private function render(string $view, array $data): string
    {
        $factory = $this->app->make(\Witals\Framework\Contracts\View\Factory::class);
        $site = $this->app->make(\App\Foundation\Site\SiteContext::class)->build();
        $base = array_merge($site, $data);

        return $factory->make('layouts/head', $base)->render()
            . $factory->make($view, $base)->render()
            . $factory->make('layouts/foot', $base)->render();
    }
}