<?php /** @var array<string,mixed> $cat */ ?>
<?php /** @var array<string,mixed>|null $info */ ?>
<?php /** @var array<int,array<string,mixed>> $others */ ?>

<div class="c"></div>

<div class="c10"></div>

<div class="grid">
    <div class="flex-container flex-space-between">

        <div class="cell-3-4 tab-cell-1-1">
            <div class="c20"></div>

            <h1 class="page-name"><?= htmlspecialchars((string)($cat['name'] ?? ''), ENT_QUOTES) ?></h1>
            <div class="c15"></div>
            <div class="content-detail"><?= $cat['content'] ?? '' ?></div>
            <div class="c20"></div>

            <?php if ($info): ?>
                <h2 class="page-name"><?= htmlspecialchars((string)$info['name'], ENT_QUOTES) ?></h2>
                <div class="c15"></div>
                <div class="content-detail"><?= $info['content'] ?? '' ?></div>
                <div class="c20"></div>
                <div class="line-gray"></div>
                <Div class="c20"></Div>
                <div class="addthis_toolbox addthis_default_style ">
                    <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
                    <a class="addthis_button_tweet"></a>
                    <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
                    <a class="addthis_counter addthis_pill_style"></a>
                </div>
                <div class="c10"></div>
            <?php endif; ?>

            <?php if ($others): ?>
                <div style="font-weight:bold; color:#FF0000">Các bài viết khác</div>
                <div class="c5"></div>
                <ul class="other-article">
                    <?php foreach ($others as $o): ?>
                        <li><a href="<?= htmlspecialchars((string)($o['link_detail'] ?? ''), ENT_QUOTES) ?>"><i class="fa fa-angle-double-right"></i><?= htmlspecialchars((string)$o['name'], ENT_QUOTES) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="cell-1-4 tab-cell-1-1">
            <?= $leftcol ?? '' ?>
        </div>

    </div>

    <div class="c10"></div>
</div>