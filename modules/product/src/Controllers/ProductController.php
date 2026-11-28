<?php

declare(strict_types=1);

namespace Modules\Product\Controllers;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;
use Witals\Framework\Http\AbstractController;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

class ProductController extends AbstractController
{
    public function __construct(
        private readonly Application $app,
        private readonly DatabaseProviderInterface $dbal,
    ) {
    }

    /**
     * GET /product — danh sách sản phẩm.
     */
    public function index(Request $request): Response
    {
        $items = [];
        try {
            $items = $this->dbal->database()
                ->select()->from($this->table())
                ->where('active', 1)
                ->orderBy('thu_tu', 'ASC')
                ->limit(30)
                ->fetchAll();
        } catch (\Throwable) {
            $items = [];
        }

        return Response::html($this->render('product/index', [
            'items'      => ['product_item' => $items],
            'page_title' => 'Sản phẩm',
        ]));
    }

    /**
     * GET /san-pham/{id|slug} — chi tiết (id hoặc slug trong cột url).
     */
    public function show(Request $request, string $id): Response
    {
        $item = null;
        try {
            $db = $this->dbal->database();
            $table = $this->table();
            if (ctype_digit($id)) {
                $rows = $db->select()->from($table)->where('id_product', (int) $id)->limit(1)->fetchAll();
            } else {
                $slug = rawurldecode($id);
                $rows = $db->select()->from($table)->where('url', $slug)->limit(1)->fetchAll();
            }
            $item = $rows[0] ?? null;
        } catch (\Throwable) {
            $item = null;
        }

        if ($item === null) {
            return Response::html($this->render('product/index', [
                'items' => ['product_item' => []], 'page_title' => 'Không tìm thấy sản phẩm',
            ]), 404);
        }

        $url = (string) ($item['url'] ?? '');
        $priceNum = (int) preg_replace('/\D/', '', (string) ($item['price'] ?? '0'));
        $other = [];
        try {
            $rows = $this->dbal->database()
                ->select()->from($this->table())
                ->where('active', 1)
                ->limit(8)
                ->fetchAll();
            foreach ($rows as $r) {
                if ((string) ($r['url'] ?? '') === $url) {
                    continue;
                }
                $other[] = [
                    'name'        => $r['name'] ?? '',
                    'image'       => $r['image'] ?? '',
                    'price'       => $r['price'] ?? '',
                    'pricekm'     => $r['pricekm'] ?? '',
                    'link_detail' => '/san-pham/' . (string) ($r['url'] ?? ''),
                    'link_cart'   => '/addcart/' . (string) ($r['url'] ?? ''),
                ];
            }
        } catch (\Throwable) {
            $other = [];
        }

        return Response::html($this->render('product/show', array_merge($item, [
            'item'         => $item,
            'name'         => $item['name'] ?? '',
            'image'        => $item['image'] ?? '',
            'ma'           => $item['ma'] ?? '',
            'hangsx'       => $item['hangsx'] ?? 'Xanhvina',
            'price'        => $item['price'] ?? 'Liên hệ',
            'pricekm'      => $item['pricekm'] ?? '',
            'price_number' => $priceNum,
            'content'      => $item['content'] ?? '',
            'linkcart'     => '/addcart/' . $url,
            'cat_link'     => '/san-pham/' . $url,
            'idcat'        => (int) ($item['id_category'] ?? 0),
            'items'        => [
                'slider_image'   => [],
                'other_products' => $other,
                'attr111'        => [],
                'phienban'       => [],
                'faq_item'       => [],
            ],
            'page_title'   => (string) ($item['name'] ?? ''),
        ])));
    }

    /**
     * @param array<string, mixed> $data
     */
    private function render(string $view, array $data): string
    {
        $factory = $this->app->make(\Witals\Framework\Contracts\View\Factory::class);
        $site = $this->app->make(\App\Foundation\Site\SiteContext::class)->build();
        $base = array_merge($site, $data);

        return $factory->make('layouts/head', $base)->render()
            . $factory->make($view, $base)->render()
            . $factory->make('layouts/foot', $base)->render();
    }

    private function table(): string
    {
        $override = $this->app->config('database.tables.product');

        return is_string($override) && $override !== '' ? $override : 'product';
    }
}