<?php

declare(strict_types=1);

namespace App\Foundation\Admin;

use Witals\Framework\Http\Request;

/**
 * TableList — Abstract Base
 *
 * Equivalent of WordPress's WP_List_Table.
 * Every admin data-entry screen in every module extends this class.
 *
 * Design goals:
 *  - Framework-native: PSR-7 Request, no WP globals
 *  - Renders HTML (admin UI) and JSON (API / AJAX) from the same class
 *  - WP-bridge compatible: same concepts (columns, sortable, bulk actions,
 *    views, search, pagination, row actions) so a future WP bridge can
 *    map WP_List_Table callbacks onto this abstraction
 *
 * Quick-start in a module controller:
 *
 *   class CustomerTable extends TableList
 *   {
 *       protected string $primaryKey  = 'id';
 *       protected int    $perPage     = 20;
 *
 *       protected function columns(): array
 *       {
 *           return [
 *               Column::make('cb', '')->width('40px'),     // checkbox
 *               Column::make('first_name', 'Name')->sortable()->primary(),
 *               Column::make('email', 'Email')->sortable(),
 *               Column::make('status', 'Status'),
 *               Column::make('created_at', 'Registered')->sortable(),
 *           ];
 *       }
 *
 *       protected function queryItems(int $page, int $perPage, string $orderBy, string $order, string $search): array
 *       {
 *           // return ['items' => [...], 'total' => N]
 *       }
 *
 *       protected function cellValue(string $column, array $row): string
 *       {
 *           return match ($column) {
 *               'status' => "<span class='badge badge-{$row['status']}'>{$row['status']}</span>",
 *               default  => esc($row[$column] ?? ''),
 *           };
 *       }
 *   }
 */
abstract class TableList
{
    // =========================================================================
    // Configuration — override in subclasses
    // =========================================================================

    /** Database / array primary key field name */
    protected string $primaryKey = 'id';

    /** Default rows per page */
    protected int $perPage = 20;

    /** Default sort column */
    protected string $defaultOrderBy = 'id';

    /** Default sort direction */
    protected string $defaultOrder = 'DESC';

    /** Singular noun for one item (used in messages) */
    protected string $singularName = 'item';

    /** Plural noun (used in messages) */
    protected string $pluralName = 'items';

    /** Admin base URL for this table (used in row action URL generation) */
    protected string $baseUrl = '';

    // =========================================================================
    // State — populated from Request in prepare()
    // =========================================================================

    protected int    $currentPage  = 1;
    protected int    $totalItems   = 0;
    protected string $orderBy      = '';
    protected string $order        = 'ASC';
    protected string $search       = '';
    protected string $currentView  = '';
    protected array  $items        = [];

    private bool $prepared = false;

    /** @var Column[] */
    private array $resolvedColumns = [];

    // =========================================================================
    // Abstract interface (implement in each module's table class)
    // =========================================================================

    /**
     * Define table columns.
     * @return Column[]
     */
    abstract protected function columns(): array;

    /**
     * Query the data source for the current page/sort/search state.
     *
     * @return array{ items: array<array<string,mixed>>, total: int }
     */
    abstract protected function queryItems(
        int    $page,
        int    $perPage,
        string $orderBy,
        string $order,
        string $search,
        string $view,
    ): array;

    /**
     * Render the value for a single cell.
     * Return safe HTML string.
     */
    abstract protected function cellValue(string $column, array $row): string;

    // =========================================================================
    // Optional overrides
    // =========================================================================

    /**
     * Bulk actions available for this table.
     * @return BulkAction[]
     */
    protected function bulkActions(): array
    {
        return [];
    }

    /**
     * View filters (status tabs like "All | Active | Trash").
     * @return ViewFilter[]
     */
    protected function viewFilters(): array
    {
        return [];
    }

    /**
     * Row actions for the primary column (Edit | Delete | View…).
     * @return RowAction[]
     */
    protected function rowActions(): array
    {
        return [];
    }

    /**
     * Whether show a "Search Items" box.
     */
    protected function isSearchable(): bool
    {
        return true;
    }

    // =========================================================================
    // Lifecycle
    // =========================================================================

    /**
     * Populate state from the HTTP request and execute the data query.
     * Call this before render() or toJson().
     */
    public function prepare(Request $request): static
    {
        $params = $request->query();

        $this->currentPage = max(1, (int)($params['paged']    ?? 1));
        $this->search      = trim($params['s']                 ?? '');
        $this->orderBy     = $params['orderby']                ?? $this->defaultOrderBy;
        $this->order       = strtoupper($params['order']       ?? $this->defaultOrder) === 'ASC' ? 'ASC' : 'DESC';
        $this->currentView = $params['status']                 ?? '';
        $this->perPage     = max(1, (int)($params['per_page']  ?? $this->perPage));

        // Validate orderBy is a declared sortable column
        $sortable = array_filter($this->getColumns(), fn(Column $c) => $c->sortable);
        $sortKeys = array_map(fn(Column $c) => $c->key, $sortable);
        if (!in_array($this->orderBy, $sortKeys, true)) {
            $this->orderBy = $this->defaultOrderBy;
        }

        $result          = $this->queryItems(
            $this->currentPage, $this->perPage,
            $this->orderBy, $this->order,
            $this->search, $this->currentView,
        );
        $this->items      = $result['items'] ?? [];
        $this->totalItems = $result['total'] ?? 0;
        $this->prepared   = true;

        return $this;
    }

    // =========================================================================
    // Rendering — HTML
    // =========================================================================

    /**
     * Render the full admin table as HTML.
     * Can be embedded into any theme template.
     */
    public function render(): string
    {
        $this->assertPrepared();

        $html  = '';
        $html .= $this->renderTopBar();
        $html .= $this->renderTable();
        $html .= $this->renderPagination('bottom');

        return $html;
    }

    protected function renderTopBar(): string
    {
        $html  = '<div class="admin-table-topbar">';
        $html .= $this->renderViewFilters();
        $html .= '<div class="admin-table-topbar-actions">';
        $html .= $this->isSearchable() ? $this->renderSearchBox() : '';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    protected function renderViewFilters(): string
    {
        $filters = $this->viewFilters();
        if (empty($filters)) {
            return '';
        }

        $html = '<ul class="admin-subsubsub">';
        foreach ($filters as $i => $filter) {
            $active  = $filter->current ? ' class="current"' : '';
            $url     = $this->addQueryArg([$filter->queryVar => $filter->queryValue]);
            $count   = $filter->count > 0 ? " <span class='count'>{$filter->count}</span>" : '';
            $html   .= "<li><a href=\"{$url}\"{$active}>{$filter->label}{$count}</a></li>";
        }
        $html .= '</ul>';
        return $html;
    }

    protected function renderSearchBox(): string
    {
        $value = htmlspecialchars($this->search, ENT_QUOTES);
        $label = "Tìm kiếm {$this->pluralName}...";
        return <<<HTML
        <div class="admin-search-box">
            <div class="search-input-wrap">
                <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <input type="search" id="admin-search-input" name="s" value="{$value}" placeholder="{$label}" />
            </div>
            <button type="submit" class="admin-btn admin-btn-primary">Tìm kiếm</button>
        </div>
        HTML;
    }

    protected function renderTable(): string
    {
        $html  = '<div class="admin-table-wrap">';
        $html .= $this->renderBulkActionsBar('top');
        $html .= '<div class="admin-list-table" role="grid">';
        $html .= $this->renderTableHead();
        $html .= $this->renderTableBody();
        $html .= $this->renderTableFoot();
        $html .= '</div>';
        $html .= $this->renderBulkActionsBar('bottom');
        $html .= '</div>';
        return $html;
    }

    protected function renderTableHead(): string
    {
        $html = '<div class="table-head" role="rowgroup"><div class="table-tr" role="row">';
        foreach ($this->getColumns() as $col) {
            if ($col->key === 'cb') {
                $html .= '<div class="table-th manage-column column-cb check-column" role="columnheader">'
                       . '<input type="checkbox" id="cb-select-all-1" />'
                       . '</div>';
                continue;
            }

            $width = $col->width ? " style=\"width:{$col->width}\"" : '';
            $css   = 'manage-column column-' . $col->key . ($col->primary ? ' column-primary' : '') . ($col->cssClass ? " {$col->cssClass}" : '');

            if ($col->sortable) {
                $dir     = ($this->orderBy === $col->key && $this->order === 'ASC') ? 'DESC' : 'ASC';
                $url     = $this->addQueryArg(['orderby' => $col->key, 'order' => strtolower($dir)]);
                $arrow   = $this->orderBy === $col->key ? ($this->order === 'ASC' ? ' ↑' : ' ↓') : '';
                $sortCss = $this->orderBy === $col->key ? ' sorted ' . strtolower($this->order) : ' sortable';
                $html   .= "<div class=\"table-th {$css}{$sortCss}\" role=\"columnheader\"{$width}>"
                         . "<a href=\"{$url}\"><span>{$col->label}</span><span class='sorting-indicator'>{$arrow}</span></a>"
                         . '</div>';
            } else {
                $html .= "<div class=\"table-th {$css}\" role=\"columnheader\"{$width}>{$col->label}</div>";
            }
        }
        $html .= '</div></div>';
        return $html;
    }

    protected function renderTableBody(): string
    {
        $html = '<div class="table-body" id="the-list" role="rowgroup">';

        if (empty($this->items)) {
            $html .= "<div class=\"table-tr no-items\" role=\"row\">"
                   . "<div class=\"table-td colspan-all\">No {$this->pluralName} found.</div>"
                   . '</div>';
            return $html . '</div>';
        }

        foreach ($this->items as $row) {
            $html .= $this->renderRow($row);
        }

        $html .= '</div>';
        return $html;
    }

    protected function renderTableFoot(): string
    {
        return str_replace('table-head', 'table-foot', $this->renderTableHead());
    }

    protected function renderRow(array $row): string
    {
        $pk   = $row[$this->primaryKey] ?? '';
        $html = "<div class=\"table-tr\" id=\"{$this->singularName}-{$pk}\" data-id=\"{$pk}\" role=\"row\">";

        foreach ($this->getColumns() as $col) {
            if ($col->key === 'cb') {
                $html .= "<div class=\"table-td check-column\" role=\"gridcell\">"
                       . "<input type=\"checkbox\" name=\"item[]\" value=\"{$pk}\" />"
                       . '</div>';
                continue;
            }

            $css   = 'column-' . $col->key . ($col->primary ? ' column-primary has-row-actions' : '');
            $value = $this->cellValue($col->key, $row);

            if ($col->primary) {
                $value .= $this->renderRowActions($row);
            }

            $html .= "<div class=\"table-td {$css}\" data-colname=\"{$col->label}\" role=\"gridcell\">{$value}</div>";
        }

        $html .= '</div>';
        return $html;
    }

    protected function renderRowActions(array $row): string
    {
        $actions = $this->rowActions();
        if (empty($actions)) {
            return '';
        }

        $html = '<div class="row-actions">';
        $last = count($actions) - 1;
        foreach ($actions as $i => $action) {
            $url    = $action->resolveUrl($row, $this->primaryKey);
            $sep    = $i < $last ? ' | ' : '';
            $css    = 'action-' . $action->key . ($action->cssClass ? " {$action->cssClass}" : '');
            $confirm = $action->confirmMessage
                     ? " onclick=\"return confirm('" . addslashes($action->confirmMessage) . "');\""
                     : '';
            if (in_array($action->method, ['DELETE', 'POST', 'PUT', 'PATCH'], true)) {
                $html .= "<span class=\"{$css}\"><a href=\"#\" data-url=\"{$url}\" data-method=\"{$action->method}\"{$confirm}>{$action->label}</a>{$sep}</span>";
            } else {
                $html .= "<span class=\"{$css}\"><a href=\"{$url}\"{$confirm}>{$action->label}</a>{$sep}</span>";
            }
        }
        $html .= '</div>';
        return $html;
    }

    protected function renderBulkActionsBar(string $position): string
    {
        $actions = $this->bulkActions();
        $html    = "<div class=\"tablenav {$position}\">";

        if (!empty($actions)) {
            $html .= '<div class="alignleft actions bulkactions">';
            $html .= "<select name=\"action_{$position}\" id=\"bulk-action-selector-{$position}\">";
            $html .= '<option value="-1">Bulk actions</option>';
            foreach ($actions as $action) {
                $confirm = $action->confirmMessage ? " data-confirm=\"{$action->confirmMessage}\"" : '';
                $html   .= "<option value=\"{$action->key}\"{$confirm}>{$action->label}</option>";
            }
            $html .= '</select>';
            $html .= "<button type=\"button\" class=\"admin-btn admin-btn-secondary admin-bulk-apply\" data-position=\"{$position}\">Apply</button>";
            $html .= '</div>';
        }

        $html .= $this->renderPagination($position);
        $html .= '</div>';
        return $html;
    }

    protected function renderPagination(string $position): string
    {
        $totalPages = (int)ceil($this->totalItems / max(1, $this->perPage));
        if ($totalPages <= 1) {
            return "<div class=\"tablenav-pages one-page\"><span class=\"displaying-num\">{$this->totalItems} {$this->pluralName}</span></div>";
        }

        $prev = max(1, $this->currentPage - 1);
        $next = min($totalPages, $this->currentPage + 1);

        $prevUrl  = $this->addQueryArg(['paged' => $prev]);
        $nextUrl  = $this->addQueryArg(['paged' => $next]);
        $firstUrl = $this->addQueryArg(['paged' => 1]);
        $lastUrl  = $this->addQueryArg(['paged' => $totalPages]);

        $start = ($this->currentPage - 1) * $this->perPage + 1;
        $end   = min($this->currentPage * $this->perPage, $this->totalItems);

        return <<<HTML
        <div class="tablenav-pages">
            <span class="displaying-num">{$this->totalItems} {$this->pluralName}</span>
            <span class="pagination-links">
                <a class="first-page button" href="{$firstUrl}" title="First page">«</a>
                <a class="prev-page button" href="{$prevUrl}" title="Previous page">‹</a>
                <span class="paging-input">
                    <span class="current-page">{$this->currentPage}</span>
                    of <span class="total-pages">{$totalPages}</span>
                </span>
                <a class="next-page button" href="{$nextUrl}" title="Next page">›</a>
                <a class="last-page button" href="{$lastUrl}" title="Last page">»</a>
            </span>
        </div>
        HTML;
    }

    // =========================================================================
    // JSON representation (for API / AJAX responses)
    // =========================================================================

    /**
     * Return a structured array suitable for JSON encoding.
     * Used by API controllers or AJAX handlers.
     */
    public function toJson(): array
    {
        $this->assertPrepared();

        $totalPages = (int)ceil($this->totalItems / max(1, $this->perPage));

        return [
            'data'       => $this->items,
            'pagination' => [
                'current_page' => $this->currentPage,
                'per_page'     => $this->perPage,
                'total'        => $this->totalItems,
                'total_pages'  => $totalPages,
                'has_prev'     => $this->currentPage > 1,
                'has_next'     => $this->currentPage < $totalPages,
            ],
            'meta' => [
                'order_by'  => $this->orderBy,
                'order'     => $this->order,
                'search'    => $this->search,
                'view'      => $this->currentView,
            ],
            'columns' => array_map(fn(Column $c) => [
                'key'      => $c->key,
                'label'    => $c->label,
                'sortable' => $c->sortable,
                'primary'  => $c->primary,
            ], array_filter($this->getColumns(), fn(Column $c) => $c->key !== 'cb')),
        ];
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /** @return Column[] */
    protected function getColumns(): array
    {
        if (empty($this->resolvedColumns)) {
            $this->resolvedColumns = $this->columns();
        }
        return $this->resolvedColumns;
    }

    /**
     * Build a URL with the given query params merged into the current base URL.
     */
    protected function addQueryArg(array $params): string
    {
        $base = $this->baseUrl ?: ($_SERVER['REQUEST_URI'] ?? '');
        // Strip existing paged/orderby/order/s params from base
        $base = preg_replace('/[?&](' . implode('|', array_keys($params)) . ')=[^&]*/', '', $base);
        $sep  = str_contains($base, '?') ? '&' : '?';
        return $base . $sep . http_build_query($params);
    }

    /**
     * Escape a value for safe HTML output.
     */
    protected function esc(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function assertPrepared(): void
    {
        if (!$this->prepared) {
            throw new \LogicException(
                static::class . '::prepare($request) must be called before render() or toJson().'
            );
        }
    }
}
