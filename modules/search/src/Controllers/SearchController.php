<?php

declare(strict_types=1);

namespace Modules\Search\Controllers;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;
use Witals\Framework\Http\AbstractController;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

class SearchController extends AbstractController
{
    public function __construct(
        private readonly Application $app,
        private readonly DatabaseProviderInterface $dbal,
    ) {
    }

    public function index(Request $request): Response
    {
        $raw = (string) ($request->input('qr') ?? $request->input('keyword') ?? '');
        $kw = $this->removeSign(trim($raw));
        $items = [];
        $short = mb_strlen($kw) < 3;

        if (!$short) {
            $like = '%' . $kw . '%';
            $db = $this->dbal->database();
            $rows = $db->query(
                "SELECT * FROM search_content WHERE (name_unsign LIKE ? OR tags LIKE ?) AND active=1 ORDER BY id_search DESC",
                [$like, $like]
            )->fetchAll();
            $ids = [];
            foreach ($rows as $r) {
                $items[] = $r;
                $ids[] = (int) $r['id_search'];
            }
            if ($items) {
                $in = implode(',', $ids);
                $more = $db->query(
                    "SELECT * FROM search_content WHERE id_search NOT IN ($in) AND intro_unsign LIKE ? AND active=1 ORDER BY id_search DESC",
                    [$like]
                )->fetchAll();
                foreach ($more as $r) {
                    $items[] = $r;
                }
            }
        }

        return Response::html($this->render('search/index', [
            'keywords'   => $raw,
            'h_keywords' => $kw,
            'short'      => $short,
            'items'      => $items,
            'page_title' => 'Tìm kiếm',
        ]));
    }

    private function removeSign(string $str): string
    {
        return strtolower(str_replace(
            ['à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
             'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ','ì','í','ị','ỉ','ĩ',
             'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
             'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ','ỳ','ý','ỵ','ỷ','ỹ','đ'],
            ['a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
             'e','e','e','e','e','e','e','e','e','e','e','i','i','i','i','i',
             'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
             'u','u','u','u','u','u','u','u','u','u','u','y','y','y','y','y','d'],
            $str
        ));
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