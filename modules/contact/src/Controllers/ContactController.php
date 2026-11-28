<?php

declare(strict_types=1);

namespace Modules\Contact\Controllers;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;
use Witals\Framework\Http\AbstractController;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;
use Witals\Framework\Events\Contracts\EventDispatcherInterface;
use Modules\Contact\Events\ContactSubmitted;

/**
 * Public controller — thay thế legacy contact submit.
 * Route: POST /contact/submit
 */
class ContactController extends AbstractController
{
    public function __construct(
        private readonly Application $app,
        private readonly DatabaseProviderInterface $dbal,
    ) {
    }

    public function submit(Request $request): Response
    {
        $input = $request->input() ?? [];
        if (!is_array($input)) {
            $input = [];
        }
        $json = $request->getJson();
        if (is_array($json)) {
            $input = array_merge($input, $json);
        }

        $fullname = trim((string) ($input['yourname'] ?? $input['name'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $phone = trim((string) ($input['phone'] ?? ''));
        $address = trim((string) ($input['address'] ?? ''));
        $subject = trim((string) ($input['subject'] ?? ''));
        $other = trim((string) ($input['other_request'] ?? $input['message'] ?? ''));

        if ($fullname === '' || $email === '') {
            return Response::json(['error' => 'Vui lòng nhập họ tên và email'], 422);
        }

        $title = 'Thông tin liên hệ: ';
        $content = '<table width="100%" border="1" cellspacing="0" cellpadding="0">'
            . '<tr><td colspan="2"><strong>' . htmlspecialchars($title . $fullname, ENT_QUOTES) . '</strong></td></tr>'
            . '<tr><td>Họ tên:</td><td>' . htmlspecialchars($fullname, ENT_QUOTES) . '</td></tr>'
            . '<tr><td>E-mail:</td><td>' . htmlspecialchars($email, ENT_QUOTES) . '</td></tr>'
            . '<tr><td>Điện thoại:</td><td>' . htmlspecialchars($phone, ENT_QUOTES) . '</td></tr>'
            . '<tr><td>Địa chỉ:</td><td>' . htmlspecialchars($address, ENT_QUOTES) . '</td></tr>'
            . '<tr><td>Nội dung:</td><td>' . htmlspecialchars($other . ($subject !== '' ? ' (' . $subject . ')' : ''), ENT_QUOTES) . '</td></tr>'
            . '</table>';

        $table = $this->table();
        try {
            $this->dbal->database()->insert($table)->values([
                'name'       => $title . $fullname,
                'content'    => $content,
                'xem'        => 0,
                'email'      => $email,
                'createdate' => time(),
            ])->run();
        } catch (\Throwable $e) {
            return Response::json(['error' => 'Không thể lưu liên hệ'], 500);
        }

        try {
            $this->app->make(EventDispatcherInterface::class)->dispatch(new ContactSubmitted([
                'name'    => $fullname,
                'email'   => $email,
                'phone'   => $phone,
                'address' => $address,
                'message' => $other . ($subject !== '' ? ' (' . $subject . ')' : ''),
            ]));
        } catch (\Throwable) {
            // event dispatch is best-effort
        }

        return Response::json(['status' => 'ok', 'message' => 'Đã gửi liên hệ']);
    }

    private function table(): string
    {
        $override = $this->app->config('database.tables.contact');

        return is_string($override) && $override !== '' ? $override : 'contact';
    }
}