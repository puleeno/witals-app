<?php

declare(strict_types=1);

namespace Modules\Dashboard\Controllers;

use App\Foundation\Admin\AdminController;
use App\Foundation\Application;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

/**
 * Dashboard controller — renders the admin SPA shell.
 *
 * The heavy lifting (chrome, nav, design-system CSS) lives in AdminController so
 * every module admin screen shares the same layout. The body is intentionally
 * minimal: the SPA (SolidJS) mounts into #admin-app and hydrates from /api/*.
 */
class DashboardController extends AdminController
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function index(Request $request): Response
    {
        $content = $this->renderShell();

        return Response::html($this->adminPage('Bảng điều khiển', $content));
    }

    private function renderShell(): string
    {
        $cards = '';
        foreach ($this->statCards() as $s) {
            $cards .= sprintf(
                '<div class="admin-stat-card"><div class="stat-icon">%s</div>'
                . '<div class="stat-value">%s</div><div class="stat-label">%s</div></div>',
                htmlspecialchars($s['icon'], ENT_QUOTES),
                htmlspecialchars($s['value'], ENT_QUOTES),
                htmlspecialchars($s['label'], ENT_QUOTES),
            );
        }

        return <<<HTML
        <div class="admin-dashboard-grid">
            {$cards}
        </div>
        <div id="admin-app" data-api-base="/api"></div>
        HTML;
    }

    /**
     * Headless JSON endpoint consumed by the SolidJS SPA.
     */
    public function stats(Request $request): Response
    {
        return Response::json(['stats' => $this->statCards()]);
    }

    /**
     * @return array<int, array{label: string, value: string, icon: string}>
     */
    private function statCards(): array
    {
        return [
            ['label' => 'Đơn hàng',   'value' => '0',  'icon' => '🛒'],
            ['label' => 'Khách hàng', 'value' => '0',  'icon' => '👥'],
            ['label' => 'Doanh thu',  'value' => '₫0', 'icon' => '💰'],
            ['label' => 'Trang SEO',  'value' => '0',  'icon' => '🔍'],
        ];
    }
}