<?php

declare(strict_types=1);

namespace App\Foundation\Debug;

use Witals\Framework\Contracts\ResettableInterface;

class DebugBar implements ResettableInterface
{
    private array $entries = [];

    public function add(string $key, mixed $value): void
    {
        $this->entries[$key] = $value;
    }

    public function getEntries(): array
    {
        return $this->entries;
    }

    public function render(): string
    {
        if ($this->entries === []) {
            return '';
        }

        $html = '<div id="presto-debug-bar" style="position:fixed;bottom:0;left:0;right:0;background:#1a1a2e;color:#eee;font:12px monospace;z-index:99999;max-height:40vh;overflow:auto;border-top:2px solid #e94560;">';
        $html .= '<div style="padding:8px 12px;background:#16213e;font-weight:bold;">Presto Debug Bar</div>';
        $html .= '<div style="padding:8px 12px;">';

        foreach ($this->entries as $key => $value) {
            $display = is_scalar($value) ? (string) $value : json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $html .= '<div style="margin:4px 0;"><strong>' . htmlspecialchars((string) $key) . ':</strong> '
                . '<span style="color:#0f0;">' . htmlspecialchars((string) $display) . '</span></div>';
        }

        $html .= '</div></div>';
        return $html;
    }

    public function reset(): void
    {
        $this->entries = [];
    }
}
