<?php

declare(strict_types=1);

namespace Modules\NotFound\Controllers;

use App\Foundation\Application;
use Witals\Framework\Http\AbstractController;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

class NotFoundController extends AbstractController
{
    public function __construct(private readonly Application $app)
    {
    }

    public function index(Request $request): Response
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