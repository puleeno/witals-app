<?php

declare(strict_types=1);

namespace App\Foundation\Admin;

use App\Foundation\Application;

/**
 * AdminNavRegistry — collects dashboard navigation items declared by modules.
 *
 * A module opts into the dashboard by adding an `admin` block to its
 * module.json:
 *
 *   "admin": {
 *     "group": "Nội dung",                       // optional group label
 *     "nav": { "label": "Tin tức", "url": "/dashboard/news", "icon": "📰" }
 *   }
 *
 * Items with the same `group` are merged under one collapsible group. Items
 * without a group are rendered as top-level links. Disabled modules
 * (respecting the `modules.enabled.<name>` config override) are skipped.
 *
 * The registry is intentionally decoupled from ModuleManager: it reads
 * module.json files directly so it works even before modules are booted.
 */
class AdminNavRegistry
{
    /** @var array<int, array{label:string,url:string,icon:string}> */
    private array $topLevel = [];

    /** @var array<string, array{icon:string, children: array<int, array{label:string,url:string,icon:string}>}> */
    private array $groups = [];

    public function __construct(private readonly Application $app)
    {
        $this->scan();
    }

    private function scan(): void
    {
        $modulesPath = $this->app->basePath('modules');
        if (!is_dir($modulesPath)) {
            return;
        }

        foreach (scandir($modulesPath) as $dir) {
            if ($dir === '.' || $dir === '..') {
                continue;
            }
            $metaFile = $modulesPath . '/' . $dir . '/module.json';
            if (!is_file($metaFile)) {
                continue;
            }

            $meta = json_decode((string) file_get_contents($metaFile), true);
            if (!is_array($meta)) {
                continue;
            }

            $name = (string) ($meta['name'] ?? $dir);
            $override = $this->app->config('modules.enabled.' . $name);
            $enabled = $override !== null ? (bool) $override : (bool) ($meta['enabled'] ?? false);
            if (!$enabled) {
                continue;
            }

            $admin = $meta['admin'] ?? null;
            if (!is_array($admin) || empty($admin['nav'])) {
                continue;
            }

            $nav = $admin['nav'];
            if (!is_array($nav) || empty($nav['label']) || empty($nav['url'])) {
                continue;
            }

            $item = [
                'label' => (string) $nav['label'],
                'url'   => (string) $nav['url'],
                'icon'  => (string) ($nav['icon'] ?? '📦'),
            ];

            $group = isset($admin['group']) ? (string) $admin['group'] : '';
            if ($group === '') {
                $this->topLevel[] = $item;
                continue;
            }

            if (!isset($this->groups[$group])) {
                $this->groups[$group] = [
                    'icon'     => (string) ($admin['group_icon'] ?? '📁'),
                    'children' => [],
                ];
            }
            $this->groups[$group]['children'][] = $item;
        }
    }

    /**
     * Merge module-declared nav into the built-in groups and return the final
     * ordered list consumed by AdminController::adminNav().
     *
     * @return array<int, array<string, mixed>>
     */
    public function items(): array
    {
        $base = [
            ['label' => 'Bảng điều khiển', 'url' => '/dashboard', 'icon' => '📊'],
            'Kinh doanh' => [
                'icon' => '🛍️',
                'children' => [
                    ['label' => 'Đơn hàng',      'url' => '/dashboard/orders',     'icon' => '🛒'],
                    ['label' => 'Khách hàng',    'url' => '/dashboard/customers',  'icon' => '👥'],
                    ['label' => 'Hóa đơn',       'url' => '/dashboard/invoices',   'icon' => '📄'],
                    ['label' => 'Cộng tác viên', 'url' => '/dashboard/affiliates', 'icon' => '🤝'],
                ],
            ],
            'Sản phẩm Số' => [
                'icon' => '📦',
                'children' => [
                    ['label' => 'Gói Thành Viên', 'url' => '/dashboard/memberships', 'icon' => '💎'],
                    ['label' => 'Software Licenses', 'url' => '/dashboard/licenses', 'icon' => '💻'],
                    ['label' => 'Themes Manager', 'url' => '/dashboard/catalog?type=theme', 'icon' => '🎨'],
                    ['label' => 'Plugins Repository', 'url' => '/dashboard/catalog?type=plugin', 'icon' => '🔌'],
                    ['label' => 'Quản lý Dịch vụ', 'url' => '/dashboard/web-services', 'icon' => '🛠️'],
                ],
            ],
            'Dịch vụ Hạ tầng' => [
                'icon' => '☁️',
                'children' => [
                    ['label' => 'Quản lý Hosting',  'url' => '/dashboard/hosting',   'icon' => '🖥️'],
                    ['label' => 'Quản lý Tên miền', 'url' => '/dashboard/domains',   'icon' => '🌐'],
                    ['label' => 'Chứng chỉ SSL',    'url' => '/dashboard/infrastructure/ssl', 'icon' => '🔒'],
                    ['label' => 'Email Hosting',    'url' => '/dashboard/infrastructure/email', 'icon' => '📧'],
                ],
            ],
            'Hệ thống' => [
                'icon' => '⚙️',
                'children' => [
                    ['label' => 'API Keys (Updater)', 'url' => '/dashboard/profile', 'icon' => '⚡'],
                    ['label' => 'Webhooks',      'url' => '/dashboard/webhooks',   'icon' => '🪝'],
                ],
            ],
        ];

        // Merge module groups into base (append new groups, append children to
        // existing same-named groups).
        foreach ($this->groups as $label => $group) {
            if (isset($base[$label]) && is_array($base[$label])) {
                $base[$label]['children'] = array_merge($base[$label]['children'], $group['children']);
            } else {
                $base[$label] = $group;
            }
        }

        // Top-level module links: insert right after the dashboard link.
        foreach ($this->topLevel as $item) {
            array_splice($base, 1, 0, [$item]);
        }

        return $base;
    }
}