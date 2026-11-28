<?php

declare(strict_types=1);

namespace Modules\Home\Sections;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;

class SlideshowSection implements SectionInterface
{
    public function __construct(private readonly ?Application $app = null)
    {
    }

    public function getType(): string
    {
        return 'slideshow';
    }

    public function render(array $data, array $context): string
    {
        if ($this->app === null) {
            return '';
        }
        $db = $this->app->make(DatabaseProviderInterface::class)->database();

        $cat = null;
        try {
            $rows = $db->select()->from('category')
                ->where('active', 1)
                ->where('vitri', 'LIKE', '%:slideshow:%')
                ->orderBy('thu_tu', 'ASC')
                ->orderBy('name', 'ASC')
                ->limit(1)
                ->fetchAll();
            $cat = $rows[0] ?? null;
        } catch (\Throwable) {
            $cat = null;
        }

        $slides = [];
        if ($cat !== null) {
            try {
                $slides = $db->select()->from('logo')
                    ->where('active', 1)
                    ->where('id_category', (int) ($cat['id_category'] ?? 0))
                    ->orderBy('thu_tu', 'ASC')
                    ->orderBy('name', 'ASC')
                    ->fetchAll();
            } catch (\Throwable) {
                $slides = [];
            }
        }

        if ($slides === []) {
            return '';
        }

        $factory = $this->app->make(\Witals\Framework\Contracts\View\Factory::class);

        return $factory->make('home/slideshow', array_merge($context, ['slides' => $slides]))->render();
    }
}