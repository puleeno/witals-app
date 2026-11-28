<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Providers;

use Witals\Framework\Support\ServiceProvider;
use App\Http\Routing\Contracts\RouterInterface;
use Modules\Ecommerce\Controllers\ProductController;
use Modules\Ecommerce\Controllers\CartController;

class EcommerceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $paths = $this->paths();
        $router = $this->app->make(RouterInterface::class);

        $products = $this->normalizeBase($paths['products'] ?? '/san-pham');
        $cart = $this->normalizeBase($paths['cart'] ?? '/gio-hang');
        $addcart = $this->normalizeBase($paths['addcart'] ?? '/addcart');

        $router->get($products, [ProductController::class, 'index']);
        $router->get($products . '/{id}', [ProductController::class, 'show']);

        $router->get($cart, [CartController::class, 'index']);
        $router->get($addcart, [CartController::class, 'index']);
        $router->get($addcart . '/{id}', [CartController::class, 'add']);
        $router->get('/api/cart/count', [CartController::class, 'count']);
        $router->get('/cart/remove/{id}', [CartController::class, 'remove']);
        $router->get('/cart/update', [CartController::class, 'update']);
        $router->post('/cart/update', [CartController::class, 'update']);
        $router->post('/cart/checkout', [\Modules\Ecommerce\Controllers\OrderController::class, 'checkout']);
        $router->get('/contact-success', [\Modules\Ecommerce\Controllers\OrderController::class, 'success']);
        $router->get('/api/voucher/check', [CartController::class, 'voucher']);
    }

    /** @return array<string, string> */
    private function paths(): array
    {
        $file = dirname(__DIR__, 2) . '/module.json';
        if (is_file($file)) {
            $meta = json_decode((string) file_get_contents($file), true);
            if (is_array($meta) && isset($meta['config']['paths']) && is_array($meta['config']['paths'])) {
                return $meta['config']['paths'];
            }
        }

        return [];
    }

    private function normalizeBase(string $path): string
    {
        $path = '/' . trim($path, '/');

        return $path === '/' ? '' : rtrim($path, '/');
    }
}