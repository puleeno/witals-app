<?php

declare(strict_types=1);

use Witals\Framework\Http\Response;

/*
|--------------------------------------------------------------------------
| API routes (Witals lane) — /api/*
|--------------------------------------------------------------------------
| The file is required from RouteServiceProvider::loadRoutes() with
| $router in scope.
*/

$router->get('/api/health', static function () {
    return Response::json([
        'status' => 'ok',
        'time' => date('c'),
    ]);
});
