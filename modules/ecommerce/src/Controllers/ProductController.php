<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Controllers;

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

    public function index(Request $request): Response
    {
        $cat = (string) ($request->input('cat') ?? '');
        $items = [];
        try {
            $db = $this->dbal->database();
            $q = $db->select()->from($this->table('product'))->where('active', 1);
            if ($cat !== '' && ctype_digit($cat)) {
                $q = $q->where('id_category', (int) $cat);
            }
            $items = $q->orderBy('thu_tu', 'ASC')->limit(30)->fetchAll();
        } catch (\Throwable) {
            $items = [];
        }

        $menu = [];
        try {
            $menu = $this->dbal->database()->select()->from($this->table('category'))
                ->where('active', 1)
                ->where('data_type', 'product')
                ->orderBy('thu_tu', 'ASC')
                ->orderBy('name', 'ASC')
                ->fetchAll();
        } catch (\Throwable) {
            $menu = [];
        }

        return Response::html($this->render('product/index', [
            'items'      => $items,
            'menu'       => $menu,
            'cat'        => null,
            'page_title' => 'Sản phẩm',
        ]));
    }

    public function show(Request $request, string $id): Response
    {
        $item = null;
        $category = null;
        try {
            $db = $this->dbal->database();
            if (ctype_digit($id)) {
                $rows = $db->select()->from($this->table('product'))->where('id_product', (int) $id)->limit(1)->fetchAll();
                $item = $rows[0] ?? null;
            } else {
                $slug = rawurldecode($id);
                $rows = $db->select()->from($this->table('product'))->where('url', $slug)->limit(1)->fetchAll();
                $item = $rows[0] ?? null;
                if ($item === null) {
                    $crows = $db->select()->from($this->table('category'))->where('url', $slug)->limit(1)->fetchAll();
                    $category = $crows[0] ?? null;
                }
            }
        } catch (\Throwable) {
            $item = null;
        }

        if ($category !== null) {
            $items = [];
            $menu = [];
            try {
                $db = $this->dbal->database();
                $items = $db->select()->from($this->table('product'))
                    ->where('active', 1)
                    ->where('id_category', (int) ($category['id_category'] ?? 0))
                    ->orderBy('thu_tu', 'ASC')
                    ->limit(30)
                    ->fetchAll();
                $menu = $db->select()->from($this->table('category'))
                    ->where('active', 1)
                    ->where('data_type', 'product')
                    ->orderBy('thu_tu', 'ASC')
                    ->orderBy('name', 'ASC')
                    ->fetchAll();
            } catch (\Throwable) {
                $items = [];
            }

            return Response::html($this->render('product/index', [
                'items'      => $items,
                'menu'       => $menu,
                'cat'        => $category,
                'cat_title'  => (string) ($category['name'] ?? ''),
                'page_title' => (string) ($category['name'] ?? 'Danh mục'),
            ]));
        }

        if ($item === null) {
            return Response::html($this->render('product/index', [
                'items' => ['product_item' => []], 'page_title' => 'Không tìm thấy sản phẩm',
            ]), 404);
        }

        $url = (string) ($item['url'] ?? '');
        $priceNum = (int) preg_replace('/\D/', '', (string) ($item['price'] ?? '0'));
        $priceRaw = (float) ($item['price'] ?? 0);
        $pricekmRaw = (float) ($item['pricekm'] ?? 0);
        $priceFmt = $priceRaw > 0 ? number_format($priceRaw) . ' đ' : (string) ($item['price'] ?? 'Liên hệ');
        $pricekmFmt = $pricekmRaw > 0 ? number_format($pricekmRaw) . ' đ' : '';
        $other = [];
        try {
            $rows = $this->dbal->database()
                ->select()->from($this->table('product'))
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
                    'price'       => (float)($r['price'] ?? 0) > 0 ? number_format((float)$r['price']) . ' đ' : (string)($r['price'] ?? ''),
                    'pricekm'     => (float)($r['pricekm'] ?? 0) > 0 ? number_format((float)$r['pricekm']) . ' đ' : '',
                    'link_detail' => $this->productBase() . '/' . (string) ($r['url'] ?? ''),
                    'link_cart'   => $this->addcartBase() . '/' . (string) ($r['url'] ?? ''),
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
            'price'        => $priceFmt,
            'pricekm'      => $pricekmFmt,
            'price_number' => $priceNum,
            'content'      => $item['content'] ?? '',
            'linkcart'     => $this->addcartBase() . '/' . $url,
            'cat_link'     => $this->productBase() . '/' . $url,
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

    private function productBase(): string
    {
        return $this->path('products', '/san-pham');
    }

    private function addcartBase(): string
    {
        return $this->path('addcart', '/addcart');
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

    private function table(string $logical): string
    {
        $override = $this->app->config('database.tables.' . $logical);

        return is_string($override) && $override !== '' ? $override : $logical;
    }

    /** @param array<string, mixed> $data */
    private function render(string $view, array $data): string
    {
        $factory = $this->app->make(\Witals\Framework\Contracts\View\Factory::class);
        $site = $this->app->make(\App\Foundation\Site\SiteContext::class)->build();
        $base = array_merge($site, $data);

        return $factory->make('layouts/base', array_merge($base, ['content' => $factory->make($view, $base)->render()]))->render();
    }
}