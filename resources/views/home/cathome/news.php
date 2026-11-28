<?php
$row = $row ?? [];
$idc = (int)($row['id_category'] ?? 0);
$items = [];
try {
    $items = $db->select()->from('news')->where('active', 1)->where('id_category', $idc)->orderBy('id_news', 'DESC')->limit(6)->fetchAll();
} catch (\Throwable) { $items = []; }
$link = '/' . trim((string)($row['url'] ?? ''), '/');
?>
<div class="servicehome-wrapper" style="background-color: #3b3b3b0a;">
    <div class="c50"></div>
    <div class="grid">
        <h2 class="homecat-title homecat-title__service"><a href="<?= htmlspecialchars($link, ENT_QUOTES) ?>"><?= htmlspecialchars((string)($row['name'] ?? ''), ENT_QUOTES) ?></a></h2>
        <div class="c30"></div>
    </div>
    <div class="swiper-container slider-homenews">
        <div class="swiper-wrapper">
            <?php foreach ($items as $i): ?>
            <div class="swiper-slide">
                <div class="service-item">
                    <a href="/tin-tuc/<?= htmlspecialchars((string)($i['url'] ?? $i['id_news'] ?? ''), ENT_QUOTES) ?>">
                        <img src="<?= htmlspecialchars((string)($i['image'] ?? ''), ENT_QUOTES) ?>" width="100%" alt="<?= htmlspecialchars((string)($i['name'] ?? ''), ENT_QUOTES) ?>">
                    </a>
                    <div class="service-item__content">
                        <h3 class="service-item__name"><a href="/tin-tuc/<?= htmlspecialchars((string)($i['url'] ?? $i['id_news'] ?? ''), ENT_QUOTES) ?>" class="text-line-2"><?= htmlspecialchars((string)($i['name'] ?? ''), ENT_QUOTES) ?></a></h3>
                        <div class="text-line-3"><?= htmlspecialchars((string)($i['intro'] ?? ''), ENT_QUOTES) ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="service-button-next"><i class="fas fa-chevron-right"></i></div>
        <div class="service-button-prev"><i class="fas fa-chevron-left"></i></div>
    </div>
    <div class="c30"></div>
    <div class="c50"></div>
</div>