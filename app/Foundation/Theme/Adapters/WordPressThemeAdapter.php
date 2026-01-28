<?php

declare(strict_types=1);

namespace App\Foundation\Theme\Adapters;

use App\Foundation\Theme\Contracts\ThemeAdapterInterface;
use App\Foundation\Theme\Theme;
use App\Foundation\Application;

class WordPressThemeAdapter implements ThemeAdapterInterface
{
    protected Theme $theme;
    protected Application $app;
    protected string $type; // 'legacy' or 'fse'

    public function __construct(Application $app, string $type = 'legacy')
    {
        $this->app = $app;
        $this->type = $type;
    }

    public function boot(Theme $theme): void
    {
        $this->theme = $theme;
        // In a real scenario, we would tell WP to use this theme
    }

    public function render(string $view, array $data = []): string
    {
        try {
            /** @var \PrestoWorld\WordPress\WordPressBridge $bridge */
            $bridge = $this->app->make(\PrestoWorld\WordPress\WordPressBridge::class);
            
            // Force the specific WordPress theme
            $bridge->forceTheme($this->theme->getName());
            
            // Create a basic PSR-7 request for the bridge
            $uri = new \Nyholm\Psr7\Uri('http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? '/'));
            $psr7Request = new \Nyholm\Psr7\ServerRequest(
                $_SERVER['REQUEST_METHOD'] ?? 'GET',
                $uri,
                [], // headers
                null, // body
                '1.1',
                $_SERVER
            );
            
            $response = $bridge->process($psr7Request, new class implements \Psr\Http\Server\RequestHandlerInterface {
                public function handle(\Psr\Http\Message\ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface {
                    return new \Nyholm\Psr7\Response(404, [], 'Not Found via WordPress');
                }
            });

            return (string) $response->getBody();
        } catch (\Throwable $e) {
            return "WordPress Render Error ({$this->type}) [{$this->theme->getName()}]: " . $e->getMessage();
        }
    }

    public function getType(): string
    {
        return $this->type;
    }
}
