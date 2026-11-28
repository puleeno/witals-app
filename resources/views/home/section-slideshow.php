<?php $slides = $slides ?? $items ?? []; ?>
<?php if ($slides !== []): ?>
<div class="home-section home-section-slideshow">
    <div class="swiper-container gallery-top">
        <div class="swiper-wrapper">
            <?php foreach ($slides as $s): ?>
            <div class="swiper-slide">
                <a href="<?= htmlspecialchars((string)($s['url'] ?? ''), ENT_QUOTES) ?>">
                    <img src="<?= htmlspecialchars((string)($s['image'] ?? ''), ENT_QUOTES) ?>" alt="<?= htmlspecialchars((string)($s['name'] ?? ''), ENT_QUOTES) ?>" width="100%">
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>