<?php

declare(strict_types=1);

namespace Modules\Newsletter\Controllers;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;
use Witals\Framework\Http\AbstractController;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

/**
 * Public controller — thay thế legacy newsletter submit.
 *
 * Đăng ký route (NewsletterServiceProvider):
 *   POST /newsletter/subscribe
 */
class SubscribeController extends AbstractController
{
    public function __construct(
        private readonly Application $app,
        private readonly DatabaseProviderInterface $dbal,
    ) {
    }

    public function subscribe(Request $request): Response
    {
        $input = $request->input() ?? [];
        if (!is_array($input)) {
            $input = [];
        }
        $json = $request->getJson();
        if (is_array($json)) {
            $input = array_merge($input, $json);
        }
        $email = trim((string) ($input['email'] ?? ''));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Response::json(['error' => 'Email không hợp lệ'], 422);
        }

        $table = $this->table();
        try {
            $db = $this->dbal->database();
            $exists = $db->select()->from($table)->where('email', $email)->limit(1)->fetchAll();
            if (!empty($exists)) {
                return Response::json(['status' => 'exists', 'message' => 'Email đã đăng ký']);
            }
            $db->insert($table)->values([
                'email'      => $email,
                'name'       => trim((string) ($input['name'] ?? '')) ?: null,
                'created_at' => new \DateTimeImmutable('now'),
            ])->run();
        } catch (\Throwable $e) {
            return Response::json(['error' => 'Không thể lưu'], 500);
        }

        return Response::json(['status' => 'ok', 'message' => 'Đăng ký thành công']);
    }

    private function table(): string
    {
        $override = $this->app->config('database.tables.newsletter_subscribers');

        return is_string($override) && $override !== '' ? $override : 'newsletter_subscribers';
    }
}