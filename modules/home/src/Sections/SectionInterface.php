<?php

declare(strict_types=1);

namespace Modules\Home\Sections;

/**
 * A single dynamic block on the home page. Implementations turn a config
 * entry (type + data) into rendered HTML, so the home page can compose any
 * content/component without touching the controller.
 */
interface SectionInterface
{
    /** @param array<string, mixed> $data */
    public function getType(): string;

    /** @param array<string, mixed> $data @param array<string, mixed> $context */
    public function render(array $data, array $context): string;
}