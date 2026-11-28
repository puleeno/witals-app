<?php

declare(strict_types=1);

namespace Modules\Home\Sections;

use App\Foundation\Application;

class ViewSection implements SectionInterface
{
    public function __construct(private readonly Application $app)
    {
    }

    public function getType(): string
    {
        return 'view';
    }

    public function render(array $data, array $context): string
    {
        $view = (string) ($data['view'] ?? '');
        if ($view === '') {
            return '';
        }
        $extra = isset($data['data']) && is_array($data['data']) ? $data['data'] : [];
        $factory = $this->app->make(\Witals\Framework\Contracts\View\Factory::class);

        return $factory->make($view, array_merge($context, $extra))->render();
    }
}