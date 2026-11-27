<?php

declare(strict_types=1);

use Witals\Framework\Http\Response;

/*
|--------------------------------------------------------------------------
| Web routes (Witals lane)
|--------------------------------------------------------------------------
| Site pages (/, /san-pham/..., /tin-tuc/...) are handled by the legacy
| runner in legacy/site.php before Witals dispatches, so only framework
| level pages live here. The file is required from
| RouteServiceProvider::loadRoutes() with $router in scope.
*/

$router->get('/health', static function () {
    return Response::json([
        'status' => 'ok',
        'time' => date('c'),
        'php' => PHP_VERSION,
    ]);
});

$router->get('/info', static function () {
    return Response::json([
        'app' => (string) config('app.name', 'xanhvina'),
        'env' => (string) config('app.env', 'production'),
        'debug' => (bool) config('app.debug', false),
    ]);
});
