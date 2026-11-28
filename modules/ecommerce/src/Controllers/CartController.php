<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Controllers;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;
use Witals\Framework\Contracts\Session\SessionInterface;
use Witals\Framework\Http\AbstractController;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

class CartController extends AbstractController
{
    private const SESSION_KEY = 'ecommerce_cart';

    public function __construct(
        private readonly Application $app,
        private readonly DatabaseProviderInterface $dbal,
        private readonly SessionInterface $session,
    ) {
    }

    public function index(Request $request): Response
    {
        $this->session->start();
        $cart = $this->session->get(self::SESSION_KEY, []);
        if (!is_array($cart)) {
            $cart = [];
        }

        return Response::html($this->render('cart/index', [
            'cart'       => $cart,
            'page_title' => 'Giỏ hàng',
        ]));
    }

    public function add(Request $request, string $id): Response
    {
        $this->session->start();
        $cart = $this->session->get(self::SESSION_KEY, []);
        if (!is_array($cart)) {
            $cart = [];
        }

        $product = null;
        try {
            $db = $this->dbal->database();
            if (ctype_digit($id)) {
                $rows = $db->select()->from($this->table('product'))->where('id_product', (int) $id)->limit(1)->fetchAll();
            } else {
                $rows = $db->select()->from($this->table('product'))->where('url', rawurldecode($id))->limit(1)->fetchAll();
            }
            $product = $rows[0] ?? null;
        } catch (\Throwable) {
            $product = null;
        }

        if ($product !== null) {
            $key = (string) ($product['id_product'] ?? $id);
            $qty = max(1, (int) ($request->input('qty') ?? 1));
            $items = is_array($cart) ? $cart : [];
            if (isset($items[$key]) && is_array($items[$key])) {
                $items[$key]['qty'] = (int) ($items[$key]['qty'] ?? 1) + $qty;
            } else {
                $items[$key] = [
                    'id_product' => $product['id_product'] ?? 0,
                    'name'       => $product['name'] ?? '',
                    'price'      => $product['price'] ?? '',
                    'image'      => $product['image'] ?? '',
                    'qty'        => $qty,
                ];
            }
            $this->session->set(self::SESSION_KEY, $items);
        }

        return Response::redirect($this->path('cart', '/gio-hang'));
    }

    private function table(string $logical): string
    {
        $override = $this->app->config('database.tables.' . $logical);

        return is_string($override) && $override !== '' ? $override : $logical;
    }

    private function path(string $key, string $default): string
    {
        $file = dirname(__DIR__, 2) . '/module.json';
        if (is_file($file)) {
            $meta = json_decode((string) file_get_contents($file), true);
            $p = $meta['config']['paths'][$key] ?? null;
            if (is_string($p) && $p !== '') {
                return '/' . trim($p, '/');
            }
        }

        return $default;
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