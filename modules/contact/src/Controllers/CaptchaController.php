<?php

declare(strict_types=1);

namespace Modules\Contact\Controllers;

use Witals\Framework\Contracts\Session\SessionInterface;
use Witals\Framework\Http\AbstractController;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

class CaptchaController extends AbstractController
{
    public function __construct(private readonly SessionInterface $session)
    {
    }

    public function image(Request $request): Response
    {
        $this->session->start();
        $code = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        for ($i = 0; $i < 4; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        $this->session->set('captcha_code', $code);

        if (!function_exists('imagecreatetruecolor')) {
            return Response::text('', 200, ['Content-Type' => 'text/plain']);
        }

        $img = imagecreatetruecolor(120, 35);
        $bg = imagecolorallocate($img, 245, 245, 245);
        $fg = imagecolorallocate($img, 0, 102, 204);
        imagefilledrectangle($img, 0, 0, 120, 35, $bg);
        imagestring($img, 5, 15, 8, $code, $fg);

        ob_start();
        imagepng($img);
        $data = ob_get_clean();
        imagedestroy($img);

        return Response::text($data, 200, ['Content-Type' => 'image/png']);
    }
}