<?php

declare(strict_types=1);

namespace Modules\AutoIndex\Controllers;

use Witals\Framework\Http\AbstractController;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;
use Modules\AutoIndex\Services\AutoIndexService;

class AutoIndexController extends AbstractController
{
    public function __construct(private readonly AutoIndexService $service)
    {
    }

    /**
     * POST /api/auto-index/submit
     * Body: { "url": "...", "entity_type": "...", "entity_id": 123 }
     */
    public function submit(Request $request): Response
    {
        $body = $request->input() ?? [];
        if (!is_array($body)) {
            $body = [];
        }
        $json = $request->getJson();
        if (is_array($json)) {
            $body = array_merge($body, $json);
        }
        $url = (string) ($body['url'] ?? '');

        if ($url === '') {
            return Response::json(['error' => 'url is required'], 422);
        }

        $result = $this->service->submit(
            $url,
            (string) ($body['entity_type'] ?? ''),
            (int) ($body['entity_id'] ?? 0),
        );

        return Response::json($result);
    }

    /**
     * GET /api/auto-index/status
     */
    public function status(Request $request): Response
    {
        return Response::json(['enabled' => $this->service->isEnabled()]);
    }
}