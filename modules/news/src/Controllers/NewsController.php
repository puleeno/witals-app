<?php

declare(strict_types=1);

namespace Modules\News\Controllers;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;
use Witals\Framework\Http\AbstractController;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

class NewsController extends AbstractController
{
    public function __construct(
        private readonly Application $app,
        private readonly DatabaseProviderInterface $dbal,
    ) {
    }

    /**
     * GET /news — danh sách tin tức, render bằng Stempler.
     */
    public function index(Request $request): Response
    {
        $items = [];
        try {
            $items = $this->dbal->database()
                ->select()->from($this->table())
                ->where('active', 1)
                ->orderBy('ngay_dang', 'DESC')
                ->limit(20)
                ->fetchAll();
        } catch (\Throwable) {
            $items = [];
        }

        return Response::html($this->render('news/index', [
            'items'      => $items,
            'cat'        => null,
            'page_title' => 'Tin tức',
        ]));
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
        $override = $this->app->config('database.tables.news');

        return is_string($override) && $override !== '' ? $override : 'news';
    }

    /**
     * GET /tin-tuc/{id|slug} — chi tiết (id hoặc slug trong cột url).
     */
    public function show(Request $request, string $id): Response
    {
        $item = null;
        try {
            $db = $this->dbal->database();
            $table = $this->table();
            if (ctype_digit($id)) {
                $rows = $db->select()->from($table)->where('id_news', (int) $id)->limit(1)->fetchAll();
            } else {
                $slug = rawurldecode($id);
                $rows = $db->select()->from($table)->where('url', $slug)->limit(1)->fetchAll();
            }
            $item = $rows[0] ?? null;
        } catch (\Throwable) {
            $item = null;
        }

        if ($item === null) {
            return Response::html($this->render('news/index', [
                'items' => [], 'cat' => null, 'page_title' => 'Không tìm thấy tin tức',
            ]), 404);
        }

        return Response::html($this->render('news/show', array_merge($item, [
            'item'       => $item,
            'items'      => ['lstOther' => []],
            'page_title' => (string) ($item['name'] ?? ''),
        ])));
    }
}