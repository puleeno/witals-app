<?php

declare(strict_types=1);

namespace App\Foundation\Admin;

use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

/**
 * Base Admin Controller
 *
 * Provides common helpers for all module admin controllers:
 *   - HTML page scaffold (header, nav, footer)
 *   - Form field renderers (input, select, textarea, checkbox…)
 *   - Flash / notice rendering
 *   - JSON response helper
 *   - Redirect helper
 *
 * Every module admin controller extends this class.
 */
abstract class AdminController
{
    protected mixed $app;
    protected \Witals\Framework\Support\AssetManager $assets;

    public function __construct(mixed $app)
    {
        $this->app = $app;
        $this->assets = $app->make(\Witals\Framework\Support\AssetManager::class);
    }

    // =========================================================================
    // Page scaffold
    // =========================================================================

    /**
     * Wrap content in the shared admin page chrome.
     *
     * @param string $title    Page/section title
     * @param string $content  Body HTML
     * @param array  $options  ['new_url' => '/dashboard/x/create', 'breadcrumbs' => [...]]
     */
    protected function adminPage(string $title, string $content, array $options = []): string
    {
        $newUrl      = $options['new_url'] ?? '';
        $newLabel    = $options['new_label'] ?? 'Add New';
        $breadcrumbs = $options['breadcrumbs'] ?? [];

        $addBtn  = $newUrl
            ? "<a href=\"{$newUrl}\" class=\"admin-btn admin-btn-primary\">{$newLabel}</a>"
            : '';

        $breadcrumbHtml = '';
        if (!empty($breadcrumbs)) {
            $parts = [];
            foreach ($breadcrumbs as $label => $url) {
                $parts[] = $url ? "<a href=\"{$url}\">{$label}</a>" : "<span>{$label}</span>";
            }
            $breadcrumbHtml = '<nav class="admin-breadcrumbs">' . implode(' › ', $parts) . '</nav>';
        }

        // Configure assets for Admin (using advanced AssetManager)
        $this->assets->setContext('admin');

        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>{$title} — Xanhvina Admin</title>
            {$this->assets->renderCss()}
            <style>{$this->adminCss()}</style>
        </head>
        <body class="admin-admin">
            <div class="admin-admin-layout">
                <aside class="admin-sidebar">
                    <div class="admin-sidebar-brand">
                        <svg width="24" height="24" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="32" height="32" rx="8" fill="#1677ff"/>
                            <path d="M12 10H20C21.1046 10 22 10.8954 22 12V20C22 21.1046 21.1046 22 20 22H12C10.8954 22 10 21.1046 10 20V12C10 10.8954 10.8954 10 12 10Z" stroke="white" stroke-width="2"/>
                        </svg>
                        Xanh<span>Vina.</span>
                    </div>
                    {$this->adminNav()}
                    <div class="sidebar-footer">
                        <div class="nav-user-profile">
                            <div class="avatar" style="background: linear-gradient(135deg, #6366f1, #a855f7);">AD</div>
                            <div class="info">
                                <span class="name">Admin Xanhvina</span>
                                <span class="role">Quản trị viên</span>
                            </div>
                            <button class="logout-btn">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                </aside>
                <main class="admin-main-wrapper">
                    <header class="admin-main-header">
                        <div class="header-breadcrumb">
                            {$breadcrumbHtml}
                        </div>
                        <div class="header-actions">
                            <div class="header-search">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <input type="text" placeholder="Tìm kiếm License key, tên khách hàng...">
                            </div>
                            <div class="header-notif">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span class="pulse"></span>
                            </div>
                            {$addBtn}
                        </div>
                    </header>
                    <div class="admin-content-area">
                        <h1 class="page-title">{$title}</h1>
                        {$content}
                        
                        <footer class="admin-admin-footer">
                            <div class="footer-left">
                                <strong>Xanhvina</strong> — Hệ thống quản trị
                            </div>
                            <div class="footer-right">
                                © 2026 Xanhvina
                            </div>
                        </footer>
                    </div>
                </main>
            </div>
            {$this->assets->renderJs()}
            <script>{$this->adminJs()}</script>
        </body>
        </html>
        HTML;
    }

    /**
     * Build the nav group list, merging module-declared items (via each
     * module.json `admin` block) with the built-in defaults.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function navGroups(): array
    {
        $registry = new \App\Foundation\Admin\AdminNavRegistry($this->app);

        return $registry->items();
    }

    protected function adminNav(): string
    {
        $groups = $this->navGroups();

        $current = $_SERVER['REQUEST_URI'] ?? '';
        $html = '<div class="admin-nav-groups">';
        
        // Check if any submenu child is active
        $anySubmenuActive = false;
        foreach ($groups as $data) {
            if (isset($data['children'])) {
                foreach ($data['children'] as $child) {
                    if ($current === $child['url'] || str_starts_with($current, $child['url'] . '?')) {
                        $anySubmenuActive = true;
                        break 2;
                    }
                }
            }
        }

        foreach ($groups as $groupLabel => $data) {
            // Case 1: Simple Link (Indexed or has 'url')
            if (isset($data['url'])) {
                $active = ($current === $data['url'] || str_starts_with($current, $data['url'] . '?')) ? ' active' : '';
                $html .= "<a href=\"{$data['url']}\" class=\"admin-nav-item{$active}\">";
                $html .= "  <span class='nav-icon'>{$data['icon']}</span> <span class='nav-label'>{$data['label']}</span>";
                $html .= "</a>";
                continue;
            }

            // Case 2: Named Group with Children
            if (!isset($data['children']) || !is_array($data['children'])) {
                continue;
            }

            $hasActiveChild = false;
            foreach ($data['children'] as $child) {
                if ($current === $child['url'] || str_starts_with($current, $child['url'] . '?')) {
                    $hasActiveChild = true;
                    break;
                }
            }

            // Default to open 'Kinh doanh' if no other submenu is active
            $shouldOpen = $hasActiveChild || (!$anySubmenuActive && $groupLabel === 'Kinh doanh');
            $openClass = $shouldOpen ? ' is-open' : '';
            $activeParentClass = $hasActiveChild ? ' active-parent' : '';
            
            $html .= "<div class=\"admin-nav-group-wrapper{$openClass}\">";
            $html .= "  <div class=\"admin-nav-item has-children{$activeParentClass}\" data-toggle=\"submenu\">";
            $html .= "      <span class='nav-icon'>{$data['icon']}</span>";
            $html .= "      <span class='nav-label'>{$groupLabel}</span>";
            $html .= "      <span class='nav-chevron'><svg width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'><path d='M6 9l6 6 6-6'/></svg></span>";
            $html .= "  </div>";
            $html .= "  <div class=\"admin-submenu\">";
            
            foreach ($data['children'] as $child) {
                $childActive = ($current === $child['url'] || str_starts_with($current, $child['url'] . '?')) ? ' active' : '';
                $html .= "      <a href=\"{$child['url']}\" class=\"admin-submenu-item{$childActive}\">";
                $html .= "          <span class='sub-icon'>{$child['icon']}</span> <span class='sub-label'>{$child['label']}</span>";
                $html .= "      </a>";
            }
            
            $html .= "  </div>";
            $html .= "</div>";
        }
        
        $html .= '</div>';
        return $html;
    }

    // =========================================================================
    // Notices
    // =========================================================================

    protected function notice(string $message, string $type = 'info'): string
    {
        $icons = ['success' => '✅', 'error' => '❌', 'warning' => '⚠️', 'info' => 'ℹ️'];
        $icon  = $icons[$type] ?? 'ℹ️';
        return "<div class=\"admin-notice admin-notice-{$type}\">{$icon} {$message}</div>";
    }

    // =========================================================================
    // Form field helpers
    // =========================================================================

    protected function formOpen(string $action, string $method = 'POST', string $id = ''): string
    {
        $idAttr = $id ? " id=\"{$id}\"" : '';
        // PUT/PATCH/DELETE need _method override for HTML forms
        $realMethod = strtoupper($method);
        $formMethod = in_array($realMethod, ['GET', 'POST'], true) ? $realMethod : 'POST';
        $methodField = !in_array($realMethod, ['GET', 'POST'], true)
            ? "<input type=\"hidden\" name=\"_method\" value=\"{$realMethod}\">"
            : '';
        return "<form action=\"{$action}\" method=\"{$formMethod}\"{$idAttr} class=\"admin-form\">{$methodField}";
    }

    protected function formClose(): string
    {
        return '</form>';
    }

    protected function fieldGroup(string $label, string $input, string $hint = ''): string
    {
        $hintHtml = $hint ? "<p class=\"admin-field-hint\">{$hint}</p>" : '';
        return <<<HTML
        <div class="admin-field-group">
            <label class="admin-field-label">{$label}</label>
            <div class="admin-field-control">{$input}{$hintHtml}</div>
        </div>
        HTML;
    }

    protected function input(
        string $name,
        string $type = 'text',
        mixed  $value = '',
        string $placeholder = '',
        bool   $required = false
    ): string {
        $req   = $required ? ' required' : '';
        $ph    = $placeholder ? " placeholder=\"{$placeholder}\"" : '';
        $val   = htmlspecialchars((string)$value, ENT_QUOTES);
        return "<input type=\"{$type}\" name=\"{$name}\" id=\"field-{$name}\" value=\"{$val}\" class=\"admin-input\" autocomplete=\"off\"{$ph}{$req}>";
    }

    protected function textarea(string $name, mixed $value = '', string $placeholder = '', int $rows = 4): string
    {
        $ph  = $placeholder ? " placeholder=\"{$placeholder}\"" : '';
        $val = htmlspecialchars((string)$value, ENT_QUOTES);
        return "<textarea name=\"{$name}\" id=\"field-{$name}\" rows=\"{$rows}\" class=\"admin-input admin-textarea\"{$ph}>{$val}</textarea>";
    }

    protected function select(string $name, array $options, mixed $selected = '', bool $searchable = false, string $placeholder = 'Search...'): string
    {
        if ($searchable) {
            $formattedOptions = [];
            foreach ($options as $val => $lbl) {
                $formattedOptions[] = ['value' => (string)$val, 'label' => (string)$lbl];
            }
            return $this->searchableSelect($name, $formattedOptions, $selected, $placeholder);
        }

        $html = "<select name=\"{$name}\" id=\"field-{$name}\" class=\"admin-select\">";
        foreach ($options as $val => $label) {
            $sel   = (string)$val === (string)$selected ? ' selected' : '';
            $html .= "<option value=\"{$val}\"{$sel}>{$label}</option>";
        }
        $html .= '</select>';
        return $html;
    }

    protected function searchableSelect(string $name, array $options, mixed $value = '', string $placeholder = 'Search...'): string
    {
        $jsonOptions = json_encode($options);
        return <<<HTML
        <div data-solid-component="ComboBox" data-config='{"name":"{$name}", "options":{$jsonOptions}, "value":"{$value}", "placeholder":"{$placeholder}"}'></div>
HTML;
    }

    protected function searchableFieldGroup(string $label, string $name, array $options, mixed $value = '', string $placeholder = 'Search...'): string
    {
        return <<<HTML
        <div class="admin-field-group">
            <label class="admin-field-label">{$label}</label>
            <div class="admin-field-control">{$this->searchableSelect($name, $options, $value, $placeholder)}</div>
        </div>
HTML;
    }

    protected function checkbox(string $name, bool $checked = false, string $label = ''): string
    {
        $chk  = $checked ? ' checked' : '';
        $html = "<label class=\"admin-checkbox-label\">"
              . "<input type=\"checkbox\" name=\"{$name}\" id=\"field-{$name}\" value=\"1\"{$chk} class=\"admin-checkbox\">"
              . " {$label}</label>";
        return $html;
    }

    protected function submitBar(string $label = 'Save', string $cancelUrl = ''): string
    {
        $cancel = $cancelUrl
            ? "<a href=\"{$cancelUrl}\" class=\"admin-btn admin-btn-ghost\">Cancel</a>"
            : '';
        return "<div class=\"admin-submit-bar\">{$cancel}<button type=\"submit\" class=\"admin-btn admin-btn-primary\">{$label}</button></div>";
    }

    /**
     * Render a full admin-card-wrapped form section.
     */
    protected function formCard(string $title, string $fieldsHtml): string
    {
        return <<<HTML
        <div class="admin-card">
            <div class="admin-card-header"><h2 class="admin-card-title">{$title}</h2></div>
            <div class="admin-card-body">{$fieldsHtml}</div>
        </div>
        HTML;
    }

    // =========================================================================
    // Response helpers
    // =========================================================================

    protected function htmlResponse(string $html, int $status = 200): Response
    {
        return Response::html($html, $status);
    }

    protected function jsonResponse(mixed $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    protected function redirect(string $url, int $status = 302): Response
    {
        return new Response($status, ['Location' => $url], '');
    }

    // =========================================================================
    // DB helpers
    // =========================================================================

    protected function db(): \Cycle\Database\DatabaseInterface
    {
        return $this->app->make(\Cycle\Database\DatabaseInterface::class);
    }

    // =========================================================================
    // Inline CSS + JS (shared admin DesignSystem)
    // =========================================================================

    protected function adminCss(): string
    {
        return <<<CSS
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        :root {
            --ant-primary: #1677ff;
            --ant-primary-hover: #4096ff;
            --ant-primary-active: #0958d9;
            --ant-bg: #f5f5f5;
            --ant-bg-container: #ffffff;
            --ant-bg-elevated: #ffffff;
            --ant-border: #d9d9d9;
            --ant-border-secondary: #f0f0f0;
            --ant-text: rgba(0,0,0,0.88);
            --ant-text-secondary: rgba(0,0,0,0.65);
            --ant-text-tertiary: rgba(0,0,0,0.45);
            --ant-success: #52c41a;
            --ant-warning: #faad14;
            --ant-error: #ff4d4f;
            --ant-radius: 8px;
            --ant-radius-lg: 12px;
            --sidebar-width: 248px;
            --shadow-card: 0 1px 2px rgba(0,0,0,0.03), 0 1px 6px -1px rgba(0,0,0,0.02), 0 2px 4px rgba(0,0,0,0.02);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body.admin-admin { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: var(--ant-bg); color: var(--ant-text); font-size: 14px; line-height: 1.5715; -webkit-font-smoothing: antialiased; }
        .admin-admin a { color: var(--ant-primary); text-decoration: none; transition: color .2s; }
        .admin-admin a:hover { color: var(--ant-primary-hover); }

        .admin-admin-layout { display: flex; min-height: 100vh; background: var(--ant-bg); }

        /* Sidebar */
        .admin-sidebar { width: var(--sidebar-width); background: var(--ant-bg-container); border-right: 1px solid var(--ant-border-secondary); display: flex; flex-direction: column; position: fixed; height: 100vh; z-index: 1000; }
        .admin-sidebar-brand { padding: 20px 24px; font-size: 20px; font-weight: 700; display: flex; align-items: center; gap: 10px; color: var(--ant-text); border-bottom: 1px solid var(--ant-border-secondary); }
        .admin-sidebar-brand span { color: var(--ant-primary); }
        .admin-nav-groups { padding: 12px; flex: 1; overflow-y: auto; }
        .admin-nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: var(--ant-radius); color: var(--ant-text-secondary); font-weight: 500; cursor: pointer; margin-bottom: 2px; transition: all .2s; }
        .admin-nav-item:hover { background: rgba(0,0,0,0.04); color: var(--ant-text); }
        .admin-nav-item.active { background: #e6f4ff; color: var(--ant-primary); font-weight: 600; }
        .active-parent { color: var(--ant-text) !important; }
        .nav-icon { font-size: 16px; width: 20px; text-align: center; }
        .nav-chevron { margin-left: auto; opacity: .5; transition: transform .2s; }
        .is-open .nav-chevron { transform: rotate(180deg); }
        .admin-submenu { max-height: 0; overflow: hidden; transition: max-height .25s ease; }
        .is-open .admin-submenu { max-height: 800px; margin: 2px 0 6px; }
        .admin-submenu-item { display: flex; align-items: center; gap: 10px; padding: 9px 14px 9px 38px; border-radius: var(--ant-radius); color: var(--ant-text-secondary); font-size: 13.5px; transition: all .2s; }
        .admin-submenu-item:hover { background: rgba(0,0,0,0.04); color: var(--ant-text); }
        .admin-submenu-item.active { color: var(--ant-primary); background: #e6f4ff; font-weight: 600; }
        .sub-icon { width: 18px; text-align: center; opacity: .8; }
        .sidebar-footer { padding: 16px; border-top: 1px solid var(--ant-border-secondary); }
        .nav-user-profile { display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: var(--ant-radius); background: var(--ant-bg); }
        .nav-user-profile .avatar { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; background: var(--ant-primary); flex-shrink: 0; }
        .nav-user-profile .info { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .nav-user-profile .name { font-size: 13.5px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .nav-user-profile .role { font-size: 12px; color: var(--ant-text-tertiary); }
        .logout-btn { background: none; border: none; color: var(--ant-text-tertiary); cursor: pointer; padding: 6px; border-radius: 6px; }
        .logout-btn:hover { background: rgba(255,77,79,0.1); color: var(--ant-error); }

        /* Main */
        .admin-main-wrapper { flex: 1; margin-left: var(--sidebar-width); display: flex; flex-direction: column; min-width: 0; }
        .admin-main-header { padding: 14px 32px; border-bottom: 1px solid var(--ant-border-secondary); background: var(--ant-bg-container); position: sticky; top: 0; z-index: 900; display: flex; align-items: center; justify-content: space-between; gap: 24px; }
        .header-breadcrumb { flex: 1; }
        .admin-breadcrumbs { font-size: 14px; color: var(--ant-text-tertiary); }
        .admin-breadcrumbs a { color: var(--ant-text-secondary); }
        .header-actions { display: flex; align-items: center; gap: 16px; }
        .header-search { position: relative; display: flex; align-items: center; }
        .header-search svg { position: absolute; left: 12px; color: var(--ant-text-tertiary); }
        .header-search input { width: 260px; background: var(--ant-bg); border: 1px solid var(--ant-border); border-radius: var(--ant-radius); padding: 8px 12px 8px 38px; color: var(--ant-text); outline: none; transition: all .2s; }
        .header-search input:focus { border-color: var(--ant-primary); box-shadow: 0 0 0 2px rgba(22,119,255,0.1); background: #fff; }
        .header-notif { position: relative; color: var(--ant-text-secondary); cursor: pointer; }
        .header-notif .pulse { position: absolute; top: -2px; right: -2px; width: 8px; height: 8px; background: var(--ant-error); border-radius: 50%; border: 2px solid #fff; }
        .admin-content-area { padding: 24px 32px; }
        .page-title { font-size: 20px; font-weight: 600; margin-bottom: 20px; color: var(--ant-text); }
        .section-title { font-size: 16px; font-weight: 600; margin: 24px 0 16px; }

        /* Buttons */
        .admin-btn { display: inline-flex; align-items: center; gap: 8px; padding: 6px 16px; border-radius: var(--ant-radius); font-size: 14px; font-weight: 500; cursor: pointer; transition: all .2s; border: 1px solid var(--ant-border); background: #fff; color: var(--ant-text); box-shadow: 0 2px 0 rgba(0,0,0,0.02); }
        .admin-btn:hover { border-color: var(--ant-primary-hover); color: var(--ant-primary-hover); }
        .admin-btn-primary { background: var(--ant-primary); border-color: var(--ant-primary); color: #fff; box-shadow: 0 2px 0 rgba(5,145,255,0.1); }
        .admin-btn-primary:hover { background: var(--ant-primary-hover); border-color: var(--ant-primary-hover); color: #fff; }
        .admin-btn-danger { background: #fff; border-color: var(--ant-error); color: var(--ant-error); }
        .admin-btn-danger:hover { background: var(--ant-error); color: #fff; }
        .admin-btn-ghost { background: transparent; border-color: transparent; box-shadow: none; }
        .admin-btn-sm { padding: 2px 8px; font-size: 13px; border-radius: 6px; }

        /* Cards */
        .admin-card { background: var(--ant-bg-container); border-radius: var(--ant-radius-lg); box-shadow: var(--shadow-card); margin-bottom: 20px; overflow: hidden; }
        .admin-card-header { padding: 16px 24px; border-bottom: 1px solid var(--ant-border-secondary); }
        .admin-card-title { font-size: 16px; font-weight: 600; margin: 0; }
        .admin-card-body { padding: 24px; }

        /* Dashboard stat cards */
        .admin-dashboard-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; margin-bottom: 20px; }
        .admin-stat-card { background: var(--ant-bg-container); border-radius: var(--ant-radius-lg); box-shadow: var(--shadow-card); padding: 20px 24px; display: flex; align-items: center; gap: 16px; }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; background: #e6f4ff; display: flex; align-items: center; justify-content: center; font-size: 22px; }
        .stat-value { font-size: 24px; font-weight: 700; line-height: 1.2; }
        .stat-label { font-size: 13px; color: var(--ant-text-secondary); }

        /* Tables */
        .admin-table-wrap { background: var(--ant-bg-container); border-radius: var(--ant-radius-lg); box-shadow: var(--shadow-card); overflow: auto; }
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th { text-align: left; padding: 12px 16px; background: var(--ant-bg); font-weight: 600; font-size: 13px; color: var(--ant-text-secondary); border-bottom: 1px solid var(--ant-border-secondary); white-space: nowrap; }
        .admin-table td { padding: 12px 16px; border-bottom: 1px solid var(--ant-border-secondary); }
        .admin-table tbody tr:hover { background: var(--ant-bg); }
        .admin-row-actions { display: flex; gap: 8px; }
        .admin-thumb { width: 48px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid var(--ant-border-secondary); background: var(--ant-bg); display: block; }

        /* Forms */
        .admin-form { display: flex; flex-direction: column; gap: 16px; }
        .admin-field { display: flex; flex-direction: column; gap: 6px; }
        .admin-field-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
        .admin-field-label { font-weight: 500; }
        .admin-input, .admin-select, .admin-textarea { width: 100%; padding: 8px 12px; border: 1px solid var(--ant-border); border-radius: var(--ant-radius); color: var(--ant-text); background: #fff; outline: none; transition: all .2s; font-size: 14px; font-family: inherit; }
        .admin-input:focus, .admin-select:focus, .admin-textarea:focus { border-color: var(--ant-primary); box-shadow: 0 0 0 2px rgba(22,119,255,0.1); }
        .admin-textarea { min-height: 96px; resize: vertical; }
        .admin-field-hint { font-size: 12px; color: var(--ant-text-tertiary); }
        .admin-checkbox { width: 16px; height: 16px; accent-color: var(--ant-primary); }
        .admin-checkbox-label { display: flex; align-items: center; gap: 8px; }
        .admin-form-actions, .admin-submit-bar { display: flex; justify-content: flex-end; gap: 12px; margin-top: 8px; }

        /* Notices */
        .admin-notice { padding: 10px 16px; border-radius: var(--ant-radius); margin-bottom: 16px; font-weight: 500; border: 1px solid; }
        .admin-notice-success { background: #f6ffed; border-color: #b7eb8f; color: #389e0d; }
        .admin-notice-error { background: #fff2f0; border-color: #ffccc7; color: #cf1322; }
        .admin-notice-warning { background: #fffbe6; border-color: #ffe58f; color: #d48806; }
        .admin-notice-info { background: #e6f4ff; border-color: #91caff; color: #0958d9; }

        .admin-admin-footer { margin-top: 40px; padding: 20px 0; border-top: 1px solid var(--ant-border-secondary); display: flex; justify-content: space-between; font-size: 12px; color: var(--ant-text-tertiary); }
        CSS;
    }    protected function adminJs(): string
    {
        return <<<JS
        // Bulk action handler
        document.querySelectorAll('.admin-bulk-apply').forEach(btn => {
            btn.addEventListener('click', () => {
                const position = btn.dataset.position;
                const select   = document.querySelector('#bulk-action-selector-' + position);
                const action   = select ? select.value : '-1';
                if (action === '-1') { alert('Please select a bulk action.'); return; }
                const checked  = [...document.querySelectorAll('input[name="item[]"]:checked')].map(c => c.value);
                if (!checked.length) { alert('Please select at least one item.'); return; }
                const confirm  = select.options[select.selectedIndex].dataset.confirm;
                if (confirm && !window.confirm(confirm)) return;
                fetch('/api/' + window.location.pathname.split('/')[2], {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-Bulk-Action': action},
                    body: JSON.stringify({ids: checked, action})
                }).then(r => r.json()).then(d => { if (d.success) location.reload(); else alert(d.message || 'Error'); });
            });
        });

        // Select-all checkbox
        const selectAll = document.getElementById('cb-select-all-1');
        if (selectAll) {
            selectAll.addEventListener('change', () => {
                document.querySelectorAll('input[name="item[]"]').forEach(c => c.checked = selectAll.checked);
            });
        }

        // Multi-level menu toggle
        document.querySelectorAll('[data-toggle="submenu"]').forEach(parent => {
            parent.addEventListener('click', () => {
                const wrapper = parent.closest('.admin-nav-group-wrapper');
                
                // Close others (Accordion style)
                document.querySelectorAll('.admin-nav-group-wrapper').forEach(other => {
                    if (other !== wrapper) other.classList.remove('is-open');
                });
                
                wrapper.classList.toggle('is-open');
            });
        });

        // Non-GET row actions
        JS;
    }
}
