<?php

declare(strict_types=1);

namespace Modules\StaticPages\Repositories;

use Cycle\Database\DatabaseProviderInterface;

/**
 * StaticPagesRepository — đọc category (data_type=info) + bảng info,
 * render block nội dung trang tĩnh (markup y hệt legacy info.htm).
 */
final class StaticPagesRepository
{
    public function __construct(
        private readonly DatabaseProviderInterface $dbal,
    ) {
    }

    private function db()
    {
        return $this->dbal->database();
    }

    /** @return array<string, mixed>|null */
    public function category(int $idc): ?array
    {
        if ($idc <= 0) {
            return null;
        }
        $rows = $this->db()->select()->from('category')
            ->where('id_category', $idc)->limit(1)->fetchAll();
        return $rows[0] ?? null;
    }

    /** @return array<string, mixed>|null */
    public function info(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }
        $rows = $this->db()->select()->from('info')
            ->where('id_info', $id)->where('active', 1)->limit(1)->fetchAll();
        return $rows[0] ?? null;
    }

    /**
     * Nội dung info hiển thị cho category $idc: bản ghi đầu tiên active,
     * nếu không có thì lấy category con đầu tiên.
     * @return array<string, mixed>|null
     */
    public function resolveInfo(int $idc): ?array
    {
        $rows = $this->db()->select()->from('info')
            ->where('id_category', $idc)->where('active', 1)
            ->orderBy('thu_tu')->limit(1)->fetchAll();
        if ($rows) {
            return $rows[0];
        }

        $children = $this->db()->select()->from('category')
            ->where('parentid', $idc)->where('active', 1)
            ->orderBy('thu_tu')->limit(1)->fetchAll();
        if ($children) {
            return $this->resolveInfo((int) $children[0]['id_category']);
        }

        return null;
    }

    /**
     * Các bài viết khác cùng nhóm.
     * @return array<int, array<string, mixed>>
     */
    public function otherArticles(int $idc, int $excludeId): array
    {
        $parentIds = $this->parentIds($idc);
        $rows = $this->db()->select()->from('info')
            ->where('active', 1)
            ->where('id_info', '!=', $excludeId);

        $or = [];
        if ($parentIds !== '') {
            $or[] = 'id_category IN (' . $parentIds . ')';
        }
        $or[] = "groupcat LIKE '%:" . $idc . ":%'";

        // where('col','IN',...) không có; dùng raw condition.
        $cond = '(' . implode(' OR ', $or) . ')';
        $rows = $this->db()->query(
            'SELECT * FROM info WHERE active=1 AND id_info<>' . $excludeId . ' AND ' . $cond . ' ORDER BY thu_tu ASC, name ASC'
        )->fetchAll();

        return $rows;
    }

    /** @return string */
    public function parentIds(int $idc): string
    {
        $ids = [$idc];
        $seen = [];
        while ($idc > 0 && !isset($seen[$idc])) {
            $seen[$idc] = true;
            $rows = $this->db()->select()->from('category')
                ->where('id_category', $idc)->limit(1)->fetchAll();
            if (!$rows) {
                break;
            }
            $idc = (int) ($rows[0]['parentid'] ?? 0);
            if ($idc > 0) {
                $ids[] = $idc;
            }
        }
        return implode(',', $ids);
    }

    /**
     * Render block nội dung trang tĩnh (catname + catcontent + info + otherarticle).
     * @param array<string, mixed>|null $cat
     * @param array<string, mixed>|null $info
     * @param array<int, array<string, mixed>> $others
     * @param string $dirPath
     * @return string
     */
    public function renderBlock(?array $cat, ?array $info, array $others, string $dirPath): string
    {
        $catname = (string) ($cat['name'] ?? '');
        $catcontent = (string) ($cat['content'] ?? '');

        $html = '<h1 class="page-name">' . htmlspecialchars($catname, ENT_QUOTES) . '</h1>';
        $html .= '<div class="c15"></div>';
        $html .= '<div class="content-detail">' . $catcontent . '</div>';
        $html .= '<div class="c20"></div>';

        if ($info) {
            $html .= '<h2 class="page-name">' . htmlspecialchars((string) $info['name'], ENT_QUOTES) . '</h2>';
            $html .= '<div class="c15"></div>';
            $html .= '<div class="content-detail">' . (string) ($info['content'] ?? '') . '</div>';
            $html .= '<div class="c20"></div>';
            $html .= '<div class="line-gray"></div>';
            $html .= '<Div class="c20"></Div>';
            $html .= '<div class="addthis_toolbox addthis_default_style ">'
                . '<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>'
                . '<a class="addthis_button_tweet"></a>'
                . '<a class="addthis_button_google_plusone" g:plusone:size="medium"></a>'
                . '<a class="addthis_counter addthis_pill_style"></a>'
                . '</div>';
            $html .= '<div class="c10"></div>';
        }

        if ($others) {
            $html .= '<div style="font-weight:bold; color:#FF0000">Các bài viết khác</div>';
            $html .= '<div class="c5"></div>';
            $html .= '<ul class="other-article">';
            foreach ($others as $o) {
                $link = $dirPath . '/' . $this->categoryUrl((int) ($o['id_category'] ?? 0)) . (string) ($o['url'] ?? '');
                $html .= '<li><a href="' . htmlspecialchars($link, ENT_QUOTES) . '">'
                    . '<i class="fa fa-angle-double-right"></i>'
                    . htmlspecialchars((string) $o['name'], ENT_QUOTES) . '</a></li>';
            }
            $html .= '</ul>';
        }

        return $html;
    }

    private function categoryUrl(int $idc): string
    {
        $rows = $this->db()->select()->from('category')
            ->where('id_category', $idc)->limit(1)->fetchAll();
        return (string) ($rows[0]['url'] ?? '');
    }
}