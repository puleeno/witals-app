<?php /** @var array<string,mixed> $cat */ ?>
<?php /** @var array<int,array<string,mixed>> $children */ ?>
<div class="c5"></div>
<div class="crumb"><div class="grid"><a href="/<?= htmlspecialchars($lang_dir ?? '', ENT_QUOTES) ?>">Trang chủ</a> <i class="fa fa-angle-right"></i> <?= htmlspecialchars((string)($cat['name'] ?? ''), ENT_QUOTES) ?></div></div>
<div class="c20"></div>

<div class="content-detail">
    <div class="c20"></div>
    <div class="grid">
        <div class="flex-container flex-space-between">
            <div class="cell-3-4 tab-cell-1-1">
                <h1 class="title-page"><?= htmlspecialchars((string)($cat['name'] ?? ''), ENT_QUOTES) ?></h1>
                <div class="c10"></div>
                <div class="cat-content"><?= $cat['content'] ?? '' ?></div>
                <div class="c10"></div>

                <?php if ($children): ?>
                    <div class="flex-container flex-space-between">
                        <?php foreach ($children as $c): ?>
                            <div class="cell-1-3" style="margin-bottom: 30px;">
                                <a href="/<?= htmlspecialchars(trim((string)($c['url'] ?? ''), '/'), ENT_QUOTES) ?>"><?= $c['image'] ?? '' ?></a>
                                <div class="c5"></div>
                                <h2 class="service-name"><a href="/<?= htmlspecialchars(trim((string)($c['url'] ?? ''), '/'), ENT_QUOTES) ?>"><?= htmlspecialchars((string)($c['name'] ?? ''), ENT_QUOTES) ?></a></h2>
                                <div class="c10"></div>
                                <?= $c['intro'] ?? '' ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="c30"></div>
</div>