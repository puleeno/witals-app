<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

/**
 * Static pages (data_type=info) — render qua module static-pages.
 * Markup giữ y hệt legacy info.htm.
 */

$app = $GLOBALS['__witals_app'] ?? null;
$repo = $app ? $app->make(\Modules\StaticPages\Repositories\StaticPagesRepository::class) : null;

$idc = intval($_GET['idc'] ?? 0);
$id = intval($_GET['id'] ?? 0);

$cat = $repo?->category($idc);
$info = $id > 0 ? $repo?->info($id) : $repo?->resolveInfo($idc);
if (!$info && $id > 0) {
    $info = $repo?->resolveInfo($idc);
}
$others = ($info && $repo) ? $repo->otherArticles($idc, (int) $info['id_info']) : [];

echo slidechild();
echo '<div class="c"></div>';
echo '<div class="crumb"><div class="grid"><a href="/' . $lang_dir . '"><i class="fa fa-home"></i></a> <i class="fa fa-angle-right"></i> ' . Get_Main_Cat_Name_path($idc) . '</div><div class="c5"></div></div>';
echo '<div class="c10"></div>';

echo '<div class="grid">';
echo '<div class="flex-container flex-space-between">';
echo '<div class="cell-3-4 tab-cell-1-1">';
echo '<div class="c20"></div>';

if ($repo && $cat) {
    echo $repo->renderBlock($cat, $info, $others, $dir_path);
}

echo '</div>';
echo '<div class="cell-1-4 tab-cell-1-1">' . left_right_col() . '</div>';
echo '</div>';
echo '<div class="c10"></div>';
echo '</div>';