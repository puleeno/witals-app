<?php

/**
 * RoadRunner Worker Entry Point
 * This file handles requests when running on RoadRunner
 */

declare(strict_types=1);

use Spiral\RoadRunner\Worker;
use Spiral\RoadRunner\Http\PSR7Worker;
use Nyholm\Psr7\Factory\Psr17Factory;

define('WITALS_START', microtime(true));
define('WITALS_ENVIRONMENT', 'roadrunner');

// Register the Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap the application once (outside the worker loop)
$app = require_once __DIR__ . '/bootstrap/app.php';

// Boot the application ONCE for the worker lifetime
// This is the key difference from traditional mode
$app->boot();

// Create RoadRunner worker
$worker = Worker::create();
$factory = new Psr17Factory();
$psr7Worker = new PSR7Worker($worker, $factory, $factory, $factory);

// Worker loop - handles multiple requests
while ($req = $psr7Worker->waitRequest()) {
    try {
        // Convert PSR-7 request to application request
        $request = \Witals\Framework\Http\Request::createFromPsr7($req);

        // Handle the request
        $response = $app->handle($request);

        // Convert application response to PSR-7 response
        $psr7Response = $response->toPsr7($factory);

        // Send the response back to RoadRunner
        $psr7Worker->respond($psr7Response);

        // Clean up after request
        $app->afterRequest($request, $response);

    } catch (\Throwable $e) {
        // Log the error
        error_log(sprintf(
            "[RoadRunner Worker Error] %s in %s:%d\n%s",
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        ));

        // Send error response
        $errorResponse = $factory->createResponse(500)
            ->withHeader('Content-Type', 'application/json');
        $errorResponse->getBody()->write(json_encode([
            'error' => 'Internal Server Error',
            'message' => $e->getMessage(),
        ]));

        $psr7Worker->respond($errorResponse);
    }
}
