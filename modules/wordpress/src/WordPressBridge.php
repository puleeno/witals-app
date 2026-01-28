<?php

declare(strict_types=1);

namespace PrestoWorld\WordPress;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Nyholm\Psr7\Response;
use Witals\Framework\Application;

/**
 * WordPress Bridge
 * 
 * Handles orchestrating WordPress within the PrestoWorld lifecycle.
 */
class WordPressBridge implements MiddlewareInterface
{
    private Application $app;
    private WordPressLoader $loader;
    private ?string $forcedTheme = null;

    public function __construct(Application $app, WordPressLoader $loader)
    {
        $this->app = $app;
        $this->loader = $loader;
    }

    /**
     * Force WordPress to use a specific theme
     */
    public function forceTheme(string $themeSlug): void
    {
        $this->forcedTheme = $themeSlug;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->loader->load()) {
            return new Response(500, [], 'WordPress could not be loaded. Please ensure WordPress is installed in public/ directory.');
        }

        // Prepare the environment for this specific request
        $this->setupWordPressEnvironment($request);

        if ($this->forcedTheme) {
            $this->applyThemeHooks();
        }

        ob_start();
        try {
            $this->runWordPressLifecycle();
            $content = ob_get_clean();
            
            return new Response(200, ['Content-Type' => 'text/html; charset=UTF-8'], $content);
        } catch (\Throwable $e) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            return new Response(500, [], "WordPress Execution Error: " . $e->getMessage());
        }
    }

    private function setupWordPressEnvironment(ServerRequestInterface $request): void
    {
        $uri = $request->getUri();
        
        // Populate superglobals for WordPress
        $_SERVER['REQUEST_URI'] = $uri->getPath() . ($uri->getQuery() ? '?' . $uri->getQuery() : '');
        $_SERVER['PATH_INFO'] = $uri->getPath();
        $_SERVER['REQUEST_METHOD'] = $request->getMethod();
        $_SERVER['QUERY_STRING'] = $uri->getQuery();
        $_SERVER['HTTP_HOST'] = $uri->getHost();
        
        $_GET = $request->getQueryParams();
        $_POST = $request->getParsedBody() ?? [];
        $_COOKIE = $request->getCookieParams();
        $_REQUEST = array_merge($_GET, $_POST);

        // Reset $wp, $wp_query in long-running environment
        global $wp, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header;
        $wp_did_header = false; // Force re-run template loader
    }

    private function applyThemeHooks(): void
    {
        $theme = $this->forcedTheme;
        
        // Force WP to use our selected theme
        add_filter('template', fn() => $theme);
        add_filter('stylesheet', fn() => $theme);
    }

    private function runWordPressLifecycle(): void
    {
        global $wp, $wp_query, $wp_the_query, $wp_did_header;

        // Ensure theme engine is using WordPress templates
        if (!defined('WP_USE_THEMES')) {
            define('WP_USE_THEMES', true);
        }

        // Initialize the main WordPress process
        if (function_exists('wp')) {
            wp();
        }

        // Include the template loader which chooses the right file from the theme
        if (file_exists(ABSPATH . 'wp-includes/template-loader.php')) {
            include ABSPATH . 'wp-includes/template-loader.php';
        }
    }
}
