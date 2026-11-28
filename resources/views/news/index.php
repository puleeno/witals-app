<?php $items = $items ?? []; $cat = $cat ?? null; $dir = (string)($dir_path ?? ''); ?>
<div class="crumb"><div class="grid"><a href="/">Trang chủ</a> <i class="fa fa-angle-right"></i> <a href="/tin-tuc">Tin tức</a></div><div class="c5"></div></div>
<div class="c"></div>
<div class="img-poster" style="position:relative"></div>

<div class="news-container">
    <div class="grid">
        <div class="flex-container">
            <div class="cell-3-4 tab-cell-1-1">
                <h2 class="catchild-title"><?= htmlspecialchars((string)($cat['name'] ?? 'Tin tức'), ENT_QUOTES) ?></h2>
                <div class="c3"></div>
                <div class="newscat-info">
                    <a href="" class="newshot-cat"></a>
                    <span></span>
                </div>
                <div class="newscat-intro text-line-4 m-text-line-3"><?= $cat['intro'] ?? '' ?></div>
                <div class="c5"></div>

                <div class="tocWrap">
                    <div class="longPageTOC TOCShowHide">Xem nhanh</div>
                    <div class="longPageNavigation"></div>
                </div>
                <div class="tocContent"></div>

                <div class="newslist-wrapper">
                    <div class="c20"></div>
                    <div class="newslist">
                        <?php foreach ($items as $i): ?>
                        <div class="flex-container">
                            <div class="cell-4-12 tab-cell-1-1">
                                <a href="/tin-tuc/<?= htmlspecialchars((string)($i['url'] ?? $i['id_news'] ?? ''), ENT_QUOTES) ?>" class="newsimg">
                                    <img src="<?= htmlspecialchars((string)($i['image'] ?? ''), ENT_QUOTES) ?>" alt="" style="width:100%">
                                </a>
                            </div>
                            <div class="cell-8-12 tab-cell-1-1">
                                <h3 class="newsname text-line-3 m-text-line-2">
                                    <a href="/tin-tuc/<?= htmlspecialchars((string)($i['url'] ?? $i['id_news'] ?? ''), ENT_QUOTES) ?>"><?= $i['name'] ?? '' ?></a>
                                </h3>
                                <div class="newscat-info">
                                    <a href="" class="newshot-cat"></a>
                                    <span><?= !empty($i['ngay_dang']) ? date('d/m/Y', (int)$i['ngay_dang']) : '' ?></span>
                                </div>
                                <div class="newscat-intro text-line-4 m-text-line-3"><?= $i['intro'] ?? '' ?></div>
                            </div>
                        </div>
                        <div class="c20"></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="cell-1-4-sp tab-cell-1-1"></div>
        </div>
        <div class="c30"></div>
    </div>
</div>