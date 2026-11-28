<?php

declare(strict_types=1);

namespace Modules\Home\Controllers;

use App\Foundation\Application;
use App\Foundation\Site\SiteContext;
use Modules\Home\Sections\SectionRegistry;
use Witals\Framework\Http\AbstractController;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly Application $app,
        private readonly SiteContext $site,
        private readonly SectionRegistry $sections,
    ) {
    }

    public function index(Request $request): Response
    {
        $context = $this->site->build();
        $config = $this->configSections();

        $body = $this->sections->render($config, $context);

        $factory = $this->app->make(\Witals\Framework\Contracts\View\Factory::class);
        $base = array_merge($context, ['page_title' => $context['site_name'] ?? 'Trang chủ']);

        $html = $factory->make('layouts/head', $base)->render()
            . $body
            . $factory->make('layouts/foot', $base)->render();

        return Response::html($html);
    }

    /** @return array<int, array<string, mixed>> */
    private function configSections(): array
    {
        $file = dirname(__DIR__, 2) . '/module.json';
        if (is_file($file)) {
            $meta = json_decode((string) file_get_contents($file), true);
            $sections = $meta['config']['sections'] ?? null;
            if (is_array($sections)) {
                return array_values(array_filter($sections, 'is_array'));
            }
        }

        return [];
    }
}