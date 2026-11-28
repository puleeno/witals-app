<?php

declare(strict_types=1);

namespace Modules\Home\Sections;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;

class NewsSection implements SectionInterface
{
    public function __construct(private readonly Application $app)
    {
    }

    public function getType(): string
    {
        return 'news';
    }

    public function render(array $data, array $context): string
    {
        $limit = (int) ($data['limit'] ?? 6);
        $title = (string) ($data['title'] ?? 'Tin tức');

        $items = [];
        try {
            $db = $this->app->make(DatabaseProviderInterface::class)->database();
            $items = $db->select()->from($this->table('news'))
                ->where('active', 1)
                ->orderBy('ngay_dang', 'DESC')
                ->limit($limit > 0 ? $limit : 6)
                ->fetchAll();
        } catch (\Throwable) {
            $items = [];
        }

        $factory = $this->app->make(\Witals\Framework\Contracts\View\Factory::class);

        return $factory->make('home/section-news', array_merge($context, [
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