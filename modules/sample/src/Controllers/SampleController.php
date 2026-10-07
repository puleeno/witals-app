<?php

declare(strict_types=1);

namespace Modules\Sample\Controllers;

use Witals\Framework\Http\AbstractController;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

class SampleController extends AbstractController
{
    public function hello(Request $request): Response
    {
        return $this->json([
            'message' => 'Hello from Sample Module!',
        ]);
    }

    public function greet(Request $request, string $name): Response
    {
        return $this->json([
            'message' => "Hello, {$name}!",
        ]);
    }
}
