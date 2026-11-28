<?php $items = $items ?? []; ?>
<div class="home-section home-section-news">
    <div class="grid">
        <div class="title-page"><span><?= htmlspecialchars((string)($title ?? 'Tin tức'), ENT_QUOTES) ?></span></div>
        <div class="c20"></div>
        <div class="flex-container">
            <?php foreach ($items as $i): ?>
            <div class="cell-1-4 tab-cell-1-2" style="margin-bottom: 20px;">
                <div class="producthome-item">
                    <div class="img-hv">
                        <a href="/tin-tuc/<?= htmlspecialchars((string)($i['url'] ?? $i['id_news'] ?? ''), ENT_QUOTES) ?>" class="img-shine">
                            <img src="<?= htmlspecialchars((string)($i['image'] ?? ''), ENT_QUOTES) ?>" alt="<?= htmlspecialchars((string)($i['name'] ?? ''), ENT_QUOTES) ?>" width="100%">
                        </a>
                    </div>
                    <div class="producthot-box">
                        <h3>
                            <a href="/tin-tuc/<?= htmlspecialchars((string)($i['url'] ?? $i['id_news'] ?? ''), ENT_QUOTES) ?>" class="producthot-item__name text-line-2">
                                <?= htmlspecialchars((string)($i['name'] ?? ''), ENT_QUOTES) ?>
                            </a>
                        </h3>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="c30"></div>
    </div>
</div>