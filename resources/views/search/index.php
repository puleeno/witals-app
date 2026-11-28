<?php /** @var array<int,array<string,mixed>> $items */ ?>
<div class="c20"></div>
<div class="grid">
    <div class="c20"></div>
    <?php if ($short): ?>
        <div class="news-title" style="font-size: 14px; text-align: center;" >Từ khóa quá ngắn</div>
    <?php else: ?>
        <div>
            <h1 class="news-title" style="font-size: 14px;" >Kết quả tìm kiếm "<?= htmlspecialchars($keywords, ENT_QUOTES) ?>"</h1>
        </div>
    <?php endif; ?>

    <?php if (!$short && !$items): ?>
        <div class="c20"></div>
        <div style="text-align: center; font-weight: bold">Không tìm thấy kết quả</div>
        <div class="c10"></div>
    <?php endif; ?>

    <div class="flex-container flex-space-between search-item-list">
        <?php foreach ($items as $rs): ?>
            <?php
                $link = (string)($rs['url'] ?? '');
                if ($link !== '' && !str_starts_with($link, 'http')) {
                    $link = '/' . $link;
                }
            ?>
            <div class="cell-1-1 " >
                <h2 class="news-name" style="margin: 0px;">
                    <a href="<?= htmlspecialchars($link, ENT_QUOTES) ?>"><?= htmlspecialchars((string)$rs['name'], ENT_QUOTES) ?></a>
                </h2>
                <div class="cat-path">Danh mục <?= htmlspecialchars((string)($rs['id_category'] ?? ''), ENT_QUOTES) ?></div>

                <div class="c10"></div>
                <div class="flex-container">
                    <div class="cell-1-12 tab-cell-1-4" >
                        <a href="<?= htmlspecialchars($link, ENT_QUOTES) ?>"><?= $rs['image'] ?? '' ?></a>
                    </div>

                    <div class="cell-11-12 tab-cell-3-4" >
                        <div style="text-align:justify;padding-top:5px;">
                            <?= htmlspecialchars(mb_substr(strip_tags((string)($rs['intro'] ?? '')), 0, 100), ENT_QUOTES) ?>
                        </div>
                    </div>
                </div>
                <div class="c30"></div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="paging"></div>

    <div class="c20"></div>
</div>

<style>
.search-result{ font-family:Arial; }
.highlight{ background-color: #ffff00; }
.result-title{ margin: 0px; padding: 0px }
.result-title a{ text-decoration: none; font-size: 16px; color: #222; }
.cat-path a{ color: #666; font-size: 12px; text-decoration: none; font-weight: bold; }
</style>

<script type="text/javascript" src="<?= htmlspecialchars($dir_path ?? '', ENT_QUOTES) ?>/js/hightlight.js"></script>
<script language="javascript">
    $(function () {
        $('.search-item-list').highlight('<?= htmlspecialchars($keywords ?? '', ENT_QUOTES) ?>');
        $('.search-item-list').highlight('<?= htmlspecialchars($h_keywords ?? '', ENT_QUOTES) ?>');
    });
</script>