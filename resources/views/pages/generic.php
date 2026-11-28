<?php /** @var array<string,mixed> $cat */ ?>
<div class="c5"></div>
<div class="crumb"><div class="grid"><a href="/<?= htmlspecialchars($lang_dir ?? '', ENT_QUOTES) ?>">Trang chủ</a> <i class="fa fa-angle-right"></i> <?= htmlspecialchars((string)($cat['name'] ?? ''), ENT_QUOTES) ?></div></div>
<div class="c20"></div>

<div class="content-detail">
    <div class="c20"></div>
    <h1 class="title-page"><?= htmlspecialchars((string)($cat['name'] ?? ''), ENT_QUOTES) ?></h1>
    <div class="c10"></div>
    <div class="cat-content"><?= $cat['content'] ?? '' ?></div>
    <div class="c20"></div>
</div>