<?php

declare(strict_types=1);

namespace Modules\Faq\Controllers;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;
use Witals\Framework\Http\AbstractController;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

/**
 * Public controller — thay thế legacy sendquestion submit.
 * Route: POST /faq/send
 */
class FaqController extends AbstractController
{
    public function __construct(
        private readonly Application $app,
        private readonly DatabaseProviderInterface $dbal,
    ) {
    }

    public function send(Request $request): Response
    {
        $input = $request->input() ?? [];
        if (!is_array($input)) {
            $input = [];
        }
        $json = $request->getJson();
        if (is_array($json)) {
            $input = array_merge($input, $json);
        }

        $name = trim((string) ($input['name'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $phone = trim((string) ($input['phonenumber'] ?? $input['phone'] ?? ''));
        $question = trim((string) ($input['question'] ?? ''));

        if ($name === '' || $question === '') {
            return Response::json(['error' => 'Vui lòng nhập câu hỏi'], 422);
        }

        $table = $this->table();
        try {
            $this->dbal->database()->insert($table)->values([
                'name'           => $question,
                'title'          => $question,
                'content'        => $question,
                'active'         => 0,
                'ngay_dang'      => time(),
                'lang'           => (string) ($input['lang'] ?? ''),
                'customer_name'  => $name,
                'customer_phone' => $phone,
                'customer_email' => $email,
            ])->run();
        } catch (\Throwable $e) {
            return Response::json(['error' => 'Không thể gửi câu hỏi'], 500);
        }

        return Response::json(['status' => 'ok', 'message' => 'Đã gửi câu hỏi']);
    }

    private function table(): string
    {
        $override = $this->app->config('database.tables.faq');

        return is_string($override) && $override !== '' ? $override : 'faq';
    }
}