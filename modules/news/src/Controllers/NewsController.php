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

        $factory = $this->app->make(\Witals\Framework\Contracts\View\Factory::class);
        $data = [
            'items'          => $items,
            'page_title'     => 'Tin tức',
            'site_name'      => (string) config('app.name', 'Xanhvina'),
            'site_url'       => (string) config('app.url', ''),
            'site_description' => '',
            'site_keywords'  => '',
            'langcode'       => 'vi',
            'dir_path'       => '',
            'lang_dir'       => '',
            'hotline'        => (string) config('app.hotline', ''),
            'quantity'       => 0,
            'logo'           => '',
            'menubar'        => '',
            'slideshow'      => '',
        ];

        $html = $factory->make('layouts/head', $data)->render()
            . $factory->make('news/index', $data)->render()
            . $factory->make('layouts/foot', $data)->render();

        return Response::html($html);
    }

    private function table(): string
    {
        $override = $this->app->config('database.tables.news');

        return is_string($override) && $override !== '' ? $override : 'news';
    }
}