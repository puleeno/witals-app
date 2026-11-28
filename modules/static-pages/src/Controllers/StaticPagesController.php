<?php

declare(strict_types=1);

namespace Modules\StaticPages\Controllers;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;
use Modules\StaticPages\Repositories\StaticPagesRepository;
use Witals\Framework\Http\AbstractController;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

class StaticPagesController extends AbstractController
{
    public function __construct(
        private readonly Application $app,
        private readonly DatabaseProviderInterface $dbal,
        private readonly StaticPagesRepository $repo,
    ) {
    }

    /**
     * GET /static-pages/{alias} — render trang tĩnh theo alias category.
     */
    public function show(Request $request, string $alias): Response
    {
        $cat = $this->findCategoryByUrl($alias);
        if (!$cat) {
            return Response::html('Not found', 404);
        }

        $idc = (int) $cat['id_category'];
        $info = $this->repo->resolveInfo($idc);
        $others = $info ? $this->repo->otherArticles($idc, (int) $info['id_info']) : [];

        return Response::html($this->render('static-pages/show', [
            'cat'    => $cat,
            'info'   => $info,
            'others' => $others,
        ]));
    }

    /** @return array<string, mixed>|null */
    private function findCategoryByUrl(string $alias): ?array
    {
        $url = trim($alias, '/') . '/';
        $rows = $this->dbal->database()->select()->from('category')
            ->where('url', $url)->where('data_type', 'info')->limit(1)->fetchAll();
        return $rows[0] ?? null;
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