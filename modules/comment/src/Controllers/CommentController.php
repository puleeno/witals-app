<?php

declare(strict_types=1);

namespace Modules\Comment\Controllers;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;
use Witals\Framework\Http\AbstractController;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

/**
 * Public controller — thay thế legacy comment submit.
 * Route: POST /comment/post
 */
class CommentController extends AbstractController
{
    public function __construct(
        private readonly Application $app,
        private readonly DatabaseProviderInterface $dbal,
    ) {
    }

    public function post(Request $request): Response
    {
        $input = $request->input() ?? [];
        if (!is_array($input)) {
            $input = [];
        }
        $json = $request->getJson();
        if (is_array($json)) {
            $input = array_merge($input, $json);
        }

        $comment = trim((string) ($input['inputcomment'] ?? $input['comment'] ?? ''));
        if ($comment === '') {
            return Response::json(['error' => 'Vui lòng nhập nội dung'], 422);
        }

        $table = $this->table();
        try {
            $this->dbal->database()->insert($table)->values([
                'comment'    => $comment,
                'createdate' => time(),
                'active'     => 0,
                'thu_tu'     => 0,
                'name'       => trim((string) ($input['inputname'] ?? $input['name'] ?? '')) ?: null,
                'email'      => trim((string) ($input['inputemail'] ?? $input['email'] ?? '')) ?: null,
                'phone'      => trim((string) ($input['inputphone'] ?? $input['phone'] ?? '')) ?: null,
                'parentid'   => (int) ($input['parentid'] ?? 0) ?: null,
                'table_name' => (string) ($input['table_name'] ?? 'product'),
                'id_item'    => (string) ($input['id_item_name'] ?? 'id_product'),
                'id_value'   => (string) ($input['id_value'] ?? $input['id_product'] ?? '0'),
                'user_like'  => 0,
                'star_rate'  => (int) ($input['star_rate'] ?? 3),
                'id_admin'   => 0,
            ])->run();
        } catch (\Throwable $e) {
            return Response::json(['error' => 'Không thể gửi bình luận'], 500);
        }

        return Response::json(['status' => 'ok', 'message' => 'Đã gửi bình luận']);
    }

    private function table(): string
    {
        $override = $this->app->config('database.tables.comments');

        return is_string($override) && $override !== '' ? $override : 'comments';
    }
}