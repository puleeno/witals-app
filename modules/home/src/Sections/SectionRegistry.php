<?php

declare(strict_types=1);

namespace Modules\Home\Sections;

use Modules\Home\Sections\HtmlSection;
use Modules\Home\Sections\ViewSection;
use Modules\Home\Sections\ProductsSection;
use Modules\Home\Sections\NewsSection;
use Modules\Home\Sections\SlideshowSection;
use Modules\Home\Sections\CathomeSection;
use App\Foundation\Application;

/**
 * Registry of available section types. Add/remove/reorder sections purely via
 * config (module.json config.sections); unknown types are skipped.
 */
class SectionRegistry
{
    /** @var array<string, SectionInterface> */
    private array $sections = [];

    public function __construct(private readonly Application $app)
    {
        $this->add(new HtmlSection());
        $this->add(new ViewSection($app));
        $this->add(new ProductsSection($app));
        $this->add(new NewsSection($app));
        $this->add(new SlideshowSection($app));
        $this->add(new CathomeSection($app));
    }

    public function add(SectionInterface $section): void
    {
        $this->sections[$section->getType()] = $section;
    }

    public function has(string $type): bool
    {
        return isset($this->sections[$type]);
    }

    /** @return array<int, string> */
    public function types(): array
    {
        return array_keys($this->sections);
    }

    /**
     * @param array<int, array<string, mixed>> $configSections
     * @param array<string, mixed> $context
     */
    public function render(array $configSections, array $context): string
    {
        $out = '';
        foreach ($configSections as $entry) {
            if (!is_array($entry)) {
                continue;
            }
            $type = (string) ($entry['type'] ?? '');
            if ($type === '' || !isset($this->sections[$type])) {
                continue;
            }
            $data = isset($entry['data']) && is_array($entry['data']) ? $entry['data'] : [];
            $out .= $this->sections[$type]->render($data, $context);
        }

        return $out;
    }
}