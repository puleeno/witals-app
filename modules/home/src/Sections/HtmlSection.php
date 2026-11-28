<?php

declare(strict_types=1);

namespace Modules\Home\Sections;

class HtmlSection implements SectionInterface
{
    public function getType(): string
    {
        return 'html';
    }

    public function render(array $data, array $context): string
    {
        return (string) ($data['content'] ?? '');
    }
}