<?php

declare(strict_types=1);

namespace Modules\Pages\Controllers;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;
use Modules\StaticPages\Repositories\StaticPagesRepository;
use Witals\Framework\Http\AbstractController;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

/**
 * PagesController — catch-all cho category pages (daily/article/service/info...).
 * Route fallback: GET /{alias}
 */
class PagesController extends AbstractController
{
    public function __construct(
        private readonly Application $app,
        private readonly DatabaseProviderInterface $dbal,
        private readonly StaticPagesRepository $staticPages,
    ) {
    }

    public function show(Request $request, string $alias): Response
    {
        $alias = trim($alias, '/');
        if ($alias === '') {
            return $this->notFound();
        }

        $cat = $this->findCategory($alias);
        if (!$cat) {
            return $this->notFound();
        }

        $type = (string) ($cat['data_type'] ?? '');
        $idc = (int) ($cat['id_category'] ?? 0);

        switch ($type) {
            case 'info':
                return $this->renderInfo($cat, $idc);
            case 'daily':
                return $this->renderDaily($cat);
            case 'article':
                return $this->renderArticle($cat, $idc);
            case 'service':
                return $this->renderService($cat, $idc);
            case 'product':
            case 'news':
                // Đã có route riêng; nếu tới đây thì alias không khớp route Witals.
                return $this->notFound();
            default:
                return $this->renderGeneric($cat);
        }
    }

    /** @return array<string, mixed>|null */
    private function findCategory(string $alias): ?array
    {
        $url = $alias . '/';
        try {
            $rows = $this->dbal->database()->select()->from('category')
                ->where('url', $url)->where('active', 1)->limit(1)->fetchAll();
            return $rows[0] ?? null;
        } catch (\Throwable) {
            return null;
        }
    }

    /** @param array<string, mixed> $cat */
    private function renderInfo(array $cat, int $idc): Response
    {
        $info = $this->staticPages->resolveInfo($idc);
        $others = $info ? $this->staticPages->otherArticles($idc, (int) $info['id_info']) : [];

        return Response::html($this->render('static-pages/show', [
            'cat'    => $cat,
            'info'   => $info,
            'others' => $others,
        ]));
    }

    /** @param array<string, mixed> $cat */
    private function renderDaily(array $cat): Response
    {
        return Response::html($this->render('pages/daily', [
            'cat'        => $cat,
            'page_title' => (string) ($cat['name'] ?? 'Điểm bán'),
        ]));
    }

    /** @param array<string, mixed> $cat */
    private function renderArticle(array $cat, int $idc): Response
    {
        $children = $this->children($idc);
        return Response::html($this->render('pages/article', [
            'cat'        => $cat,
            'children'   => $children,
            'page_title' => (string) ($cat['name'] ?? 'Bài viết'),
        ]));
    }

    /** @param array<string, mixed> $cat */
    private function renderService(array $cat, int $idc): Response
    {
        $children = $this->children($idc);
        return Response::html($this->render('pages/service', [
            'cat'        => $cat,
            'children'   => $children,
            'page_title' => (string) ($cat['name'] ?? 'Dịch vụ'),
        ]));
    }

    /** @param array<string, mixed> $cat */
    private function renderGeneric(array $cat): Response
    {
        return Response::html($this->render('pages/generic', [
            'cat'        => $cat,
            'page_title' => (string) ($cat['name'] ?? ''),
        ]));
    }

    /** @return array<int, array<string, mixed>> */
    private function children(int $idc): array
    {
        try {
            return $this->dbal->database()->select()->from('category')
                ->where('parentid', $idc)->where('active', 1)
                ->orderBy('thu_tu')->fetchAll();
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Fallback cho mọi path không khớp route nào (multi-segment) → 404.
     */
    public function fallback(Request $request): Response
    {
        return $this->notFound();
    }

    private function notFound(): Response
    {
        return Response::html($this->render('notfound/index', [
            'page_title' => '404',
        ]), 404);
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