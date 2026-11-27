<?php

declare(strict_types=1);

namespace App\Foundation\Admin;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;
use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

/**
 * ModuleCrudController — a generic, schema-driven CRUD backend for data modules.
 *
 * Any module that declares an `admin.crud` block in module.json is
 * automatically manageable from the dashboard with zero per-module code:
 *
 *   "admin": {
 *     "group": "Nội dung",
 *     "nav":  { "label": "Newsletter", "url": "/dashboard/newsletter", "icon": "✉️" },
 *     "crud": { "table": "newsletter", "title_field": "email",
 *               "list_fields": ["email", "created_at"] }
 *   }
 *
 * Routes (registered by ModuleCrudServiceProvider):
 *   GET  /dashboard/{module}              list
 *   GET  /dashboard/{module}/create       create form
 *   POST /dashboard/{module}              insert
 *   GET  /dashboard/{module}/{id}/edit    edit form
 *   POST /dashboard/{module}/{id}         update
 *   POST /dashboard/{module}/{id}/delete  delete
 *   GET  /api/{module}                    JSON list
 */
class ModuleCrudController extends AdminController
{
    public function __construct(
        Application $app,
        private readonly DatabaseProviderInterface $dbal,
    ) {
        parent::__construct($app);
    }

    // ---------------------------------------------------------------------
    // HTML: list
    // ---------------------------------------------------------------------
    public function index(Request $request, string $module): Response
    {
        $def = $this->resolve($module);
        if ($def === null) {
            return Response::html($this->adminPage('Không tìm thấy', '<p>Module không hợp lệ.</p>'), 404);
        }

        $rows = $this->rows($def['table']);
        $cols = $def['list_fields'];

        $head = '';
        foreach ($cols as $c) {
            $head .= '<th>' . htmlspecialchars($c, ENT_QUOTES) . '</th>';
        }
        $head .= '<th>Thao tác</th>';

        $body = '';
        foreach ($rows as $row) {
            $body .= '<tr>';
            foreach ($cols as $c) {
                $body .= '<td>' . htmlspecialchars((string) ($row[$c] ?? ''), ENT_QUOTES) . '</td>';
            }
            $id = (int) ($row[$def['pk']] ?? 0);
            $base = '/dashboard/' . $module;
            $body .= '<td class="admin-row-actions">'
                . '<a href="' . $base . '/' . $id . '/edit" class="admin-btn admin-btn-sm">Sửa</a> '
                . '<form method="post" action="' . $base . '/' . $id . '/delete" style="display:inline" onsubmit="return confirm(\'Xóa?\')">'
                . '<button class="admin-btn admin-btn-sm admin-btn-danger">Xóa</button></form>'
                . '</td>';
            $body .= '</tr>';
        }

        if ($body === '') {
            $body = '<tr><td colspan="' . (count($cols) + 1) . '" style="text-align:center;color:var(--text-dim)">Chưa có dữ liệu</td></tr>';
        }

        $content = '<div class="admin-table-wrap"><table class="admin-table"><thead><tr>'
            . $head . '</tr></thead><tbody>' . $body . '</tbody></table></div>';

        return Response::html($this->adminPage($def['title'], $content, [
            'new_url' => '/dashboard/' . $module . '/create',
        ]));
    }

    // ---------------------------------------------------------------------
    // HTML: create / edit form
    // ---------------------------------------------------------------------
    public function create(Request $request, string $module): Response
    {
        return $this->form($module, [], '/dashboard/' . $module, 'Thêm mới');
    }

    public function edit(Request $request, string $module, int $id): Response
    {
        $def = $this->resolve($module);
        if ($def === null) {
            return Response::html($this->adminPage('Không tìm thấy', '<p>Module không hợp lệ.</p>'), 404);
        }
        $row = $this->find($def['table'], $def['pk'], $id);

        return $this->form($module, $row, '/dashboard/' . $module . '/' . $id, 'Cập nhật');
    }

    private function form(string $module, array $row, string $action, string $label): Response
    {
        $def = $this->resolve($module);
        if ($def === null) {
            return Response::html($this->adminPage('Không tìm thấy', '<p>Module không hợp lệ.</p>'), 404);
        }

        $fields = '';
        $allowed = is_array($def['form_fields']) ? $def['form_fields'] : null;
        foreach ($def['columns'] as $col) {
            $name = $col['name'];
            if (in_array($name, [$def['pk'], 'created_at', 'updated_at'], true)) {
                continue;
            }
            if ($allowed !== null && !in_array($name, $allowed, true)) {
                continue;
            }
            $value = htmlspecialchars((string) ($row[$name] ?? ''), ENT_QUOTES);
            $type = $col['type'];
            $input = match ($type) {
                'text', 'textarea', 'longtext', 'mediumtext' =>
                    '<textarea name="' . $name . '" class="admin-input" rows="3">' . $value . '</textarea>',
                'boolean', 'bool' =>
                    '<select name="' . $name . '" class="admin-input"><option value="0"' . ($value === '1' ? '' : ' selected') . '>Không</option><option value="1"' . ($value === '1' ? ' selected' : '') . '>Có</option></select>',
                'integer', 'biginteger', 'tinyint', 'smallint', 'decimal', 'float' =>
                    '<input type="number" name="' . $name . '" value="' . $value . '" class="admin-input">',
                'datetime', 'date', 'timestamp' =>
                    '<input type="datetime-local" name="' . $name . '" value="' . $value . '" class="admin-input">',
                default =>
                    '<input type="text" name="' . $name . '" value="' . $value . '" class="admin-input">',
            };
            $fields .= '<div class="admin-field"><label>' . htmlspecialchars($name, ENT_QUOTES) . '</label>' . $input . '</div>';
        }

        $content = '<form method="post" action="' . $action . '" class="admin-form">'
            . $fields
            . '<div class="admin-form-actions"><button class="admin-btn admin-btn-primary">' . $label . '</button>'
            . '<a href="/dashboard/' . $module . '" class="admin-btn">Hủy</a></div>'
            . '</form>';

        return Response::html($this->adminPage($def['title'], $content, [
            'breadcrumbs' => ['Dashboard' => '/dashboard', $def['title'] => '/dashboard/' . $module],
        ]));
    }

    // ---------------------------------------------------------------------
    // HTML: write actions
    // ---------------------------------------------------------------------
    public function store(Request $request, string $module): Response
    {
        $def = $this->resolve($module);
        if ($def === null) {
            return Response::html('<p>Module không hợp lệ.</p>', 404);
        }
        $data = $this->filterData($def, (array) $request->post());
        if ($this->hasColumn($def, 'created_at')) {
            $data['created_at'] = new \DateTimeImmutable('now');
        }
        $this->insert($def['table'], $data);

        return Response::redirect('/dashboard/' . $module);
    }

    public function update(Request $request, string $module, int $id): Response
    {
        $def = $this->resolve($module);
        if ($def === null) {
            return Response::html('<p>Module không hợp lệ.</p>', 404);
        }
        $data = $this->filterData($def, (array) $request->post());
        if ($this->hasColumn($def, 'updated_at')) {
            $data['updated_at'] = new \DateTimeImmutable('now');
        }
        $this->updateRow($def['table'], $def['pk'], $id, $data);

        return Response::redirect('/dashboard/' . $module);
    }

    public function destroy(Request $request, string $module, int $id): Response
    {
        $def = $this->resolve($module);
        if ($def === null) {
            return Response::html('<p>Module không hợp lệ.</p>', 404);
        }
        $this->delete($def['table'], $def['pk'], $id);

        return Response::redirect('/dashboard/' . $module);
    }

    // ---------------------------------------------------------------------
    // JSON API
    // ---------------------------------------------------------------------
    public function apiList(Request $request, string $module): Response
    {
        $def = $this->resolve($module);
        if ($def === null) {
            return Response::json(['error' => 'module not found'], 404);
        }

        return Response::json(['data' => $this->rows($def['table'])]);
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    /**
     * Resolve module definition from module.json + schema.json.
     *
     * @return array{table:string,title:string,list_fields:array,columns:array}|null
     */
    private function resolve(string $module): ?array
    {
        $metaFile = $this->app->basePath('modules/' . $module . '/module.json');
        if (!is_file($metaFile)) {
            return null;
        }
        $meta = json_decode((string) file_get_contents($metaFile), true);
        $crud = is_array($meta) ? ($meta['admin']['crud'] ?? null) : null;
        if (!is_array($crud) || empty($crud['table'])) {
            return null;
        }

        $logical = (string) $crud['table'];
        $override = $this->app->config('database.tables.' . $logical);
        $table = is_string($override) && $override !== '' ? $override : $logical;

        $columns = [];
        $schemaFile = $this->app->basePath('modules/' . $module . '/schema.json');
        if (is_file($schemaFile)) {
            $schema = json_decode((string) file_get_contents($schemaFile), true);
            foreach ($schema['tables'] ?? [] as $t) {
                if (($t['name'] ?? '') === $logical) {
                    $columns = $t['columns'] ?? [];
                    break;
                }
            }
        }

        $listFields = $crud['list_fields'] ?? array_slice(array_column($columns, 'name'), 0, 4);

        return [
            'table'       => $table,
            'pk'          => (string) ($crud['pk'] ?? 'id'),
            'title'       => (string) ($crud['title'] ?? $meta['admin']['nav']['label'] ?? $module),
            'list_fields' => $listFields,
            'form_fields' => $crud['form_fields'] ?? null,
            'columns'     => $columns,
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function rows(string $table): array
    {
        try {
            return $this->dbal->database()->select()->from($table)->fetchAll();
        } catch (\Throwable) {
            return [];
        }
    }

    /** @return array<string, mixed>|null */
    private function find(string $table, string $pk, int $id): ?array
    {
        try {
            $rows = $this->dbal->database()->select()->from($table)->where($pk, $id)->limit(1)->fetchAll();

            return $rows[0] ?? null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function insert(string $table, array $data): void
    {
        try {
            $this->dbal->database()->insert($table)->values($data)->run();
        } catch (\Throwable) {
        }
    }

    private function updateRow(string $table, string $pk, int $id, array $data): void
    {
        try {
            $this->dbal->database()->update($table, $data, [$pk => $id])->run();
        } catch (\Throwable) {
        }
    }

    private function delete(string $table, string $pk, int $id): void
    {
        try {
            $this->dbal->database()->delete($table, [$pk => $id])->run();
        } catch (\Throwable) {
        }
    }

    /**
     * @param array<string, mixed> $def
     */
    private function hasColumn(array $def, string $name): bool
    {
        foreach ($def['columns'] as $col) {
            if (($col['name'] ?? '') === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Keep only writable columns present in the request.
     *
     * @param  array<string, mixed> $input
     * @return array<string, mixed>
     */
    private function filterData(array $def, array $input): array
    {
        $skip = [$def['pk'], 'created_at', 'updated_at'];
        $out = [];
        foreach ($def['columns'] as $col) {
            $name = $col['name'];
            if (in_array($name, $skip, true) || !array_key_exists($name, $input)) {
                continue;
            }
            $out[$name] = is_string($input[$name]) ? trim($input[$name]) : $input[$name];
        }

        return $out;
    }
}