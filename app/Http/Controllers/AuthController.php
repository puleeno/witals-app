<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Witals\Framework\Application;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;
use Cake\Chronos\Chronos;
use Psr\Log\LoggerInterface;
use Witals\Framework\Contracts\Auth\AuthContextInterface;
use Witals\Framework\Contracts\Auth\TokenStorageInterface;
use Witals\Framework\Contracts\Auth\HttpTransportInterface;
use Witals\Framework\Contracts\Auth\RateLimiterInterface;
use Witals\Framework\Contracts\Session\SessionInterface;

class AuthController
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Show login form
     */
    public function showLogin(Request $request): Response
    {
        // If already authenticated and is admin, redirect to dashboard
        $auth = $this->app->make(AuthContextInterface::class);
        if ($auth->getToken() !== null) {
            $redirect = $this->safeRedirect($request->query('redirect', '/dashboard'));
            return Response::html('', 302, ['Location' => $redirect]);
        }

        $redirect = $this->safeRedirect($request->query('redirect', '/dashboard'));
        $error = $request->query('error', '');
        $success = $request->query('success', '');
        
        $errorHtml = $error ? '<div class="alert-error">' . htmlspecialchars($error, ENT_QUOTES) . '</div>' : '';
        $successHtml = $success ? '<div class="alert-success">' . htmlspecialchars($success, ENT_QUOTES) . '</div>' : '';

        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Đăng nhập — DigitalCore Admin</title>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
                    background: #06080c;
                    color: #f1f5f9;
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    -webkit-font-smoothing: antialiased;
                    background-image:
                        radial-gradient(circle at 20% 20%, rgba(99, 102, 241, 0.12) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(168, 85, 247, 0.12) 0%, transparent 50%);
                }
                .login-container {
                    width: 100%;
                    max-width: 440px;
                    padding: 24px;
                }
                .login-card {
                    background: rgba(18, 22, 31, 0.85);
                    border-radius: 32px;
                    border: 1px solid rgba(255, 255, 255, 0.08);
                    padding: 48px 40px;
                    backdrop-filter: blur(25px);
                    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
                }
                .brand {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    font-size: 22px;
                    font-weight: 800;
                    margin-bottom: 40px;
                    letter-spacing: -0.04em;
                }
                .brand span { color: #6366f1; }
                .brand svg { flex-shrink: 0; }
                h1 {
                    font-size: 28px;
                    font-weight: 800;
                    margin-bottom: 8px;
                    letter-spacing: -0.03em;
                }
                .subtitle {
                    color: #64748b;
                    font-size: 14px;
                    margin-bottom: 36px;
                    line-height: 1.5;
                }
                .form-group {
                    margin-bottom: 24px;
                }
                label {
                    display: block;
                    font-size: 13px;
                    font-weight: 700;
                    color: #94a3b8;
                    margin-bottom: 10px;
                    text-transform: uppercase;
                    letter-spacing: 0.08em;
                }
                input[type="email"],
                input[type="password"],
                input[type="text"] {
                    width: 100%;
                    background: rgba(0, 0, 0, 0.3);
                    border: 1px solid rgba(255, 255, 255, 0.08);
                    border-radius: 16px;
                    padding: 16px 20px;
                    color: #f1f5f9;
                    font-size: 15px;
                    font-weight: 600;
                    outline: none;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    font-family: inherit;
                }
                input:focus {
                    border-color: #6366f1;
                    box-shadow: 0 0 0 5px rgba(99, 102, 241, 0.25);
                    background: rgba(0, 0, 0, 0.45);
                }
                input::placeholder {
                    color: #475569;
                }
                .form-row {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    margin-bottom: 32px;
                }
                .remember {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 14px;
                    color: #94a3b8;
                    cursor: pointer;
                    font-weight: 600;
                }
                .remember input[type="checkbox"] {
                    width: 18px;
                    height: 18px;
                    border-radius: 6px;
                    background: rgba(255, 255, 255, 0.05);
                    border: 1px solid rgba(255, 255, 255, 0.12);
                    appearance: none;
                    cursor: pointer;
                    position: relative;
                    transition: 0.3s;
                }
                .remember input[type="checkbox"]:checked {
                    background: #6366f1;
                    border-color: #6366f1;
                }
                .remember input[type="checkbox"]:checked::after {
                    content: "✓";
                    position: absolute;
                    color: #fff;
                    font-size: 11px;
                    font-weight: 900;
                    left: 50%;
                    top: 50%;
                    transform: translate(-50%, -50%);
                }
                .forgot {
                    color: #6366f1;
                    text-decoration: none;
                    font-size: 14px;
                    font-weight: 700;
                    transition: 0.25s;
                }
                .forgot:hover { color: #818cf8; }
                .btn-login {
                    width: 100%;
                    padding: 16px;
                    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
                    color: #fff;
                    border: none;
                    border-radius: 16px;
                    font-size: 16px;
                    font-weight: 800;
                    cursor: pointer;
                    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                    font-family: inherit;
                    letter-spacing: -0.01em;
                    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.35);
                }
                .btn-login:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 15px 35px rgba(99, 102, 241, 0.45);
                    filter: brightness(1.1);
                }
                .btn-login:active {
                    transform: translateY(0);
                }
                .alert-error {
                    background: rgba(239, 68, 68, 0.12);
                    border: 1px solid rgba(239, 68, 68, 0.25);
                    color: #fca5a5;
                    padding: 14px 20px;
                    border-radius: 14px;
                    font-size: 14px;
                    font-weight: 600;
                    margin-bottom: 24px;
                }
                .alert-success {
                    background: rgba(16, 185, 129, 0.12);
                    border: 1px solid rgba(16, 185, 129, 0.25);
                    color: #6ee7b7;
                    padding: 14px 20px;
                    border-radius: 14px;
                    font-size: 14px;
                    font-weight: 600;
                    margin-bottom: 24px;
                }
                .footer-text {
                    text-align: center;
                    margin-top: 32px;
                    font-size: 14px;
                    color: #64748b;
                }
                .footer-text a {
                    color: #6366f1;
                    text-decoration: none;
                    font-weight: 700;
                }
                .footer-text a:hover { color: #818cf8; }
            </style>
        </head>
        <body>
            <div class="login-container">
                <div class="login-card">
                    <div class="brand">
                        <svg width="28" height="28" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="32" height="32" rx="8" fill="#6366f1"/>
                            <path d="M12 10H20C21.1046 10 22 10.8954 22 12V20C22 21.1046 21.1046 22 20 22H12C10.8954 22 10 21.1046 10 20V12C10 10.8954 10.8954 10 12 10Z" stroke="white" stroke-width="2"/>
                        </svg>
                        Digital<span>Core.</span>
                    </div>

                    <h1>Đăng nhập</h1>
                    <p class="subtitle">Truy cập bảng điều khiển quản trị của bạn</p>

                    {$successHtml}
                    {$errorHtml}

                    <form method="POST" action="/login">
                        <input type="hidden" name="redirect" value="{$redirect}">
                        <input type="hidden" name="_token" value="{$this->csrfToken()}">

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="admin@example.com" required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="password">Mật khẩu</label>
                            <input type="password" id="password" name="password" placeholder="••••••••" required>
                        </div>

                        <div class="form-row">
                            <label class="remember">
                                <input type="checkbox" name="remember" value="1">
                                Ghi nhớ đăng nhập
                            </label>
                            <a href="/forgot-password" class="forgot">Quên mật khẩu?</a>
                        </div>

                        <button type="submit" class="btn-login">Đăng nhập</button>
                    </form>
                </div>
                <p class="footer-text">
                    Chưa có tài khoản? <a href="/register">Đăng ký ngay</a>
                </p>
                <p class="footer-text">
                    <a href="/">← Về trang chủ</a>
                </p>
            </div>
        </body>
        </html>
        HTML;

        return Response::html($html);
    }

    /**
     * Handle login form submission
     */
    public function handleLogin(Request $request): Response
    {
        $email = $request->post('email', '');
        $password = $request->post('password', '');
        $redirect = $this->safeRedirect($request->post('redirect', '/dashboard'));
        $remember = (bool) $request->post('remember', false);

        $rateLimiter = $this->app->make(RateLimiterInterface::class);
        $rateLimitKey = 'login:' . strtolower($email);

        if (!$rateLimiter->attempt($rateLimitKey)) {
            $availableIn = $rateLimiter->availableIn($rateLimitKey);
            return Response::html('', 302, [
                'Location' => '/login?error=' . urlencode("Quá nhiều lần thử. Vui lòng thử lại sau {$availableIn} giây.") . '&redirect=' . urlencode($redirect)
            ]);
        }

        if (empty($email) || empty($password)) {
            return Response::html('', 302, [
                'Location' => '/login?error=' . urlencode('Vui lòng nhập email và mật khẩu') . '&redirect=' . urlencode($redirect)
            ]);
        }

        $token = $request->post('_token', '');
        $session = $this->app->make(SessionInterface::class);
        $expected = $session->get('_csrf_token', '');

        $this->app->make(LoggerInterface::class)->debug('Auth: CSRF check', [
            'received' => $token,
            'expected' => $expected,
            'session_id' => session_id(),
        ]);

        if ($token === '' || $expected === '' || !hash_equals($expected, $token)) {
            return Response::html('', 302, [
                'Location' => '/login?error=' . urlencode('Invalid or expired session token. Please try again.') . '&redirect=' . urlencode($redirect)
            ]);
        }

        // Authenticate user
        $user = $this->authenticateUser($email, $password);

        if ($user === null) {
            return Response::html('', 302, [
                'Location' => '/login?error=' . urlencode('Email hoặc mật khẩu không đúng') . '&redirect=' . urlencode($redirect)
            ]);
        }

        // Create auth token
        $tokenStorage = $this->app->make(TokenStorageInterface::class);
        $httpTransport = $this->app->make(HttpTransportInterface::class);
        $authContext = $this->app->make(AuthContextInterface::class);

        $expiresAt = $remember
            ? Chronos::now()->addDays(30)
            : Chronos::now()->addHours(8);

        $token = $tokenStorage->create([
            'user_id' => $user['id'],
            'email'   => $user['email'],
            'role'    => $user['role'] ?? 'admin',
            'name'    => $user['name'] ?? $user['email'],
        ], $expiresAt);

        $authContext->start($token);

        // Create redirect response and set cookie
        $response = Response::html('', 302, ['Location' => $redirect]);
        return $httpTransport->commitToken($request, $response, $token, $expiresAt);
    }

    /**
     * Handle logout
     */
    public function handleLogout(Request $request): Response
    {
        $authContext = $this->app->make(AuthContextInterface::class);
        $token = $authContext->getToken();

        if ($token !== null) {
            $tokenStorage = $this->app->make(TokenStorageInterface::class);
            $httpTransport = $this->app->make(HttpTransportInterface::class);

            $tokenStorage->delete($token);
            $authContext->close();

            $response = Response::html('', 302, ['Location' => '/login']);
            return $httpTransport->removeToken($request, $response, $token);
        }

        return Response::html('', 302, ['Location' => '/login']);
    }

    /**
     * Authenticate user against database
     */
    protected function authenticateUser(string $email, string $password): ?array
    {
        try {
            $dbal = $this->app->make(\Cycle\Database\DatabaseProviderInterface::class);
            $db = $dbal->database();
            
            // 1. Try native users table
            $user = $db->table('users')
                ->where('email', $email)
                ->run()
                ->fetch();

            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }

            // 2. Try WordPress users table (Bridge)
            $wpUser = $db->table('wp_users')
                ->where('user_email', $email)
                ->orWhere('user_login', $email)
                ->run()
                ->fetch();

            if ($wpUser) {
                $hash = $wpUser['user_pass'];
                $isValid = false;

                // Handle standard bcrypt/argon2
                if (str_starts_with($hash, '$P$') || str_starts_with($hash, '$H$')) {
                    $isValid = $this->verifyPhpassHash($password, $hash);
                } elseif (strlen($hash) === 32) {
                    // Legacy MD5 — not secure. Prompt password reset instead.
                    // Instead of silently verifying with MD5, redirect to password reset.
                    throw new \RuntimeException('Mật khẩu của bạn sử dụng thuật toán cũ không còn an toàn. Vui lòng đặt lại mật khẩu.');
                } else {
                    $isValid = password_verify($password, $hash);
                }

                if ($isValid) {
                    // Fetch role from wp_usermeta (Bridge Role Detection)
                    $prefix = env('WP_TABLE_PREFIX', 'wp_');
                    $metaKey = "{$prefix}capabilities";
                    
                    $capabilitiesMeta = $db->table('wp_usermeta')
                        ->where('user_id', $wpUser['ID'])
                        ->where('meta_key', $metaKey)
                        ->run()
                        ->fetch();

                    $role = 'user'; // Default
                    if ($capabilitiesMeta && !empty($capabilitiesMeta['meta_value'])) {
                        // WordPress stores roles as a serialized array: a:1:{s:13:"administrator";b:1;}
                        $data = unserialize($capabilitiesMeta['meta_value'], ['allowed_classes' => false]);
                        if (is_array($data)) {
                            $roles = array_keys($data);
                            // Check if they have administrator role
                            if (in_array('administrator', $roles)) {
                                $role = 'administrator';
                            } elseif (in_array('editor', $roles)) {
                                $role = 'editor';
                            } else {
                                $role = reset($roles) ?: 'user';
                            }
                        }
                    }

                    return [
                        'id'    => $wpUser['ID'],
                        'email' => $wpUser['user_email'],
                        'name'  => $wpUser['display_name'],
                        'role'  => $role,
                    ];
                }
            }
        } catch (\Throwable $e) {
            $this->app->make(LoggerInterface::class)->error('Auth error: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
        }

        return null;
    }

    private function safeRedirect(string $url): string
    {
        if ($url === '' || str_starts_with($url, '/')) {
            if (str_starts_with($url, '//')) {
                return '/dashboard';
            }
            return $url;
        }

        return '/dashboard';
    }

    private function verifyPhpassHash(string $password, string $hash): bool
    {
        if (!str_starts_with($hash, '$P$') && !str_starts_with($hash, '$H$')) {
            return false;
        }

        $itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        $countLog2 = strpos($itoa64, $hash[3]);
        if ($countLog2 < 7 || $countLog2 > 30) {
            return false;
        }

        $count = 1 << $countLog2;
        $salt = substr($hash, 4, 8);

        if (strlen($salt) !== 8) {
            return false;
        }

        $digest = md5($salt . $password, true);
        for ($i = 0; $i < $count; $i++) {
            $digest = md5($digest . $password, true);
        }

        $expected = substr($hash, 0, 12) . $this->phpassEncode64($digest, 16, $itoa64);

        return hash_equals($hash, $expected);
    }

    private function phpassEncode64(string $input, int $length, string $itoa64): string
    {
        $output = '';
        $i = 0;
        do {
            $value = ord($input[$i++]);
            $output .= $itoa64[$value & 0x3f];
            if ($i < $length) {
                $value |= ord($input[$i]) << 8;
            }
            $output .= $itoa64[($value >> 6) & 0x3f];
            if ($i++ >= $length) {
                break;
            }
            if ($i < $length) {
                $value |= ord($input[$i]) << 16;
            }
            $output .= $itoa64[($value >> 12) & 0x3f];
            if ($i++ >= $length) {
                break;
            }
            $output .= $itoa64[($value >> 18) & 0x3f];
        } while ($i < $length);

        return $output;
    }

    private function csrfToken(): string
    {
        $session = $this->app->make(SessionInterface::class);
        $token = $session->get('_csrf_token', '');

        if ($token === '') {
            $token = bin2hex(random_bytes(32));
            $session->set('_csrf_token', $token);
        }

        return $token;
    }

    private function validateCsrf(string $token): bool
    {
        $session = $this->app->make(SessionInterface::class);
        $expected = $session->get('_csrf_token', '');

        if ($expected === '' || $token === '') {
            return false;
        }

        return hash_equals($expected, $token);
    }
}
