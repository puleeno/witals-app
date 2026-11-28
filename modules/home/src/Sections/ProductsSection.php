<?php

declare(strict_types=1);

namespace Modules\Home\Sections;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;

class ProductsSection implements SectionInterface
{
    public function __construct(private readonly Application $app)
    {
    }

    public function getType(): string
    {
        return 'products';
    }

    public function render(array $data, array $context): string
    {
        $limit = (int) ($data['limit'] ?? 8);
        $title = (string) ($data['title'] ?? 'Sản phẩm');
        $cat = (int) ($data['category'] ?? 0);

        $items = [];
        try {
            $db = $this->app->make(DatabaseProviderInterface::class)->database();
            $q = $db->select()->from($this->table('product'))->where('active', 1);
            if ($cat > 0) {
                $q = $q->where('id_category', $cat);
            }
            $items = $q->orderBy('thu_tu', 'ASC')->limit($limit > 0 ? $limit : 8)->fetchAll();
        } catch (\Throwable) {
            $items = [];
        }

        $factory = $this->app->make(\Witals\Framework\Contracts\View\Factory::class);

        return $factory->make('home/section-products', array_merge($context, [
            'items' => $items,
            'title' => $title,
        ]))->render();
    }

    private function table(string $logical): string
    {
        $override = $this->app->config('database.tables.' . $logical);

        return is_string($override) && $override !== '' ? $override : $logical;
    }
}