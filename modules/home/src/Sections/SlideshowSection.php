<?php

declare(strict_types=1);

namespace Modules\Home\Sections;

use App\Foundation\Application;

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
        $factory = $this->app?->make(\Witals\Framework\Contracts\View\Factory::class);
        if ($factory === null) {
            return '';
        }

        return $factory->make('home/section-slideshow', array_merge($context, $data))->render();
    }
}