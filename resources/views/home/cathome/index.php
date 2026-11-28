<?php
$product = $product ?? [];
$news = $news ?? [];
$video = $video ?? [];
$other = $other ?? [];
$dir = (string)($dir_path ?? '');
$productBase = '/san-pham';
$addcartBase = '/addcart';
$newsBase = '/tin-tuc';
?>
<style>
    .flashsale-header { display: flex; align-items: center; gap: 10px; }
    .flashsale-header h2 { margin: 0; }
</style>

<?php foreach ($product as $grp): $cat = $grp['cat']; $items = $grp['items']; $catimage = $cat['image'] ? $dir . '/' . ltrim((string)$cat['image'], '/') : ''; $link = '/' . trim((string)($cat['url'] ?? ''), '/'); ?>
<!-- START BLOCK : product -->
<div class="flashsale-wrapper">
    <div class="c50"></div>
    <div class="grid">
        <div class="homecat-title">
            <div class="flashsale-header">
                <img class="flas-sales" src="<?= htmlspecialchars($catimage ?: 'data:image/gif;base64,R0lGODlhAQABAAAAACwAAAAAAQABAAA=', ENT_QUOTES) ?>" width="80" alt="<?= htmlspecialchars((string)($catname ?? ''), ENT_QUOTES) ?>">
                <h2><a href="<?= htmlspecialchars($link, ENT_QUOTES) ?>"><?= htmlspecialchars((string)($cat['name'] ?? ''), ENT_QUOTES) ?></a></h2>
                <div class="flashsale-timer"><div class="countdown"></div></div>
            </div>
        </div>
        <div class="c30"></div>
        <div class="flex-container">
            <?php foreach ($items as $i):
                $price = (float)($i['price'] ?? 0);
                $pricekm = (float)($i['pricekm'] ?? 0);
                $priceOut = $pricekm > 0 ? number_format($pricekm) . ' đ' : ($price > 0 ? number_format($price) . ' đ' : (string)($i['price'] ?? ''));
                $pricekmOut = $pricekm > 0 ? number_format($price) . ' đ' : '';
                $sale = '';
                if ($pricekm > 0 && $price > 0) {
                    $disc = (int)floor((($price - $pricekm) / $price) * 100);
                    if ($disc > 0) {
                        $sale = '<div class="home-product-item__sale-off"><span class="home-product-item__sale-off-percent">-' . $disc . '%</span><span class="home-product-item__sale-off-label">GIẢM</span></div>';
                    }
                }
            ?>
            <div class="cell-1-4 tab-cell-1-2">
                <div class="producthome-item">
                    <div class="img-hv">
                        <a href="<?= htmlspecialchars($productBase . '/' . (string)($i['url'] ?? ''), ENT_QUOTES) ?>" class="img-shine" title="<?= htmlspecialchars((string)($i['name'] ?? ''), ENT_QUOTES) ?>">
                            <img src="<?= htmlspecialchars((string)($i['image'] ?? ''), ENT_QUOTES) ?>" alt="<?= htmlspecialchars((string)($i['name'] ?? ''), ENT_QUOTES) ?>" style="width:100%">
                        </a>
                        <a class="addcart" href="<?= htmlspecialchars($addcartBase . '/' . (string)($i['url'] ?? ''), ENT_QUOTES) ?>" title="Thêm vào giỏ hàng"><i class="fas fa-cart-plus"></i></a>
                    </div>
                    <div class="producthot-box">
                        <h3>
                            <a href="<?= htmlspecialchars($productBase . '/' . (string)($i['url'] ?? ''), ENT_QUOTES) ?>" class="producthot-item__name text-line-2" title="<?= htmlspecialchars((string)($i['name'] ?? ''), ENT_QUOTES) ?>">
                                <?= htmlspecialchars((string)($i['name'] ?? ''), ENT_QUOTES) ?>
                            </a>
                        </h3>
                        <div class="c10"></div>
                        <div class="producthot-item__intro text-line-2"><?= strip_tags((string)($i['intro'] ?? '')) ?></div>
                        <div class="producthot__price">
                            <span class="producthot__price-old"><?= htmlspecialchars($pricekmOut, ENT_QUOTES) ?></span>
                            <span class="producthot__price-current"><?= htmlspecialchars($priceOut, ENT_QUOTES) ?></span>
                        </div>
                    </div>
                    <div class="c10"></div>
                    <?= $sale ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="c30"></div>
        <div class="text-center"><a href="<?= htmlspecialchars($link, ENT_QUOTES) ?>" class="btn-seeall">Xem Tất cả</a></div>
        <div class="c30"></div>
    </div>
    <div class="c20"></div>
</div>
<!-- END BLOCK : product -->
<?php endforeach; ?>

<?php foreach ($news as $grp): $cat = $grp['cat']; $items = $grp['items']; $link = '/' . trim((string)($cat['url'] ?? ''), '/'); ?>
<!-- START BLOCK : news -->
<div class="servicehome-wrapper" style="background-color: #3b3b3b0a;">
    <div class="c50"></div>
    <div class="grid">
        <h2 class="homecat-title homecat-title__service"><a href="<?= htmlspecialchars($link, ENT_QUOTES) ?>"><?= htmlspecialchars((string)($cat['name'] ?? ''), ENT_QUOTES) ?></a></h2>
        <div class="c30"></div>
    </div>
    <div class="swiper-container slider-homenews">
        <div class="swiper-wrapper">
            <?php foreach ($items as $i): ?>
            <div class="swiper-slide">
                <div class="service-item">
                    <a href="<?= htmlspecialchars($newsBase . '/' . (string)($i['url'] ?? $i['id_news'] ?? ''), ENT_QUOTES) ?>">
                        <img src="<?= htmlspecialchars((string)($i['image'] ?? ''), ENT_QUOTES) ?>" style="width:100%" alt="<?= htmlspecialchars((string)($i['name'] ?? ''), ENT_QUOTES) ?>">
                    </a>
                    <div class="service-item__content">
                        <h3 class="service-item__name">
                            <a href="<?= htmlspecialchars($newsBase . '/' . (string)($i['url'] ?? $i['id_news'] ?? ''), ENT_QUOTES) ?>" class="text-line-2"><?= $i['name'] ?? '' ?></a>
                        </h3>
                        <div class="text-line-3"><?= $i['intro'] ?? '' ?></div>
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
<!-- END BLOCK : news -->
<?php endforeach; ?>

<?php foreach ($video as $grp): $cat = $grp['cat']; $items = $grp['items']; $link = '/' . trim((string)($cat['url'] ?? ''), '/'); ?>
<!-- START BLOCK : video -->
<div class="grid">
    <div class="c20"></div>
    <div class="title-cat-home"><a href="<?= htmlspecialchars($link, ENT_QUOTES) ?>"><?= htmlspecialchars((string)($cat['name'] ?? ''), ENT_QUOTES) ?></a></div>
    <a class="view-more-home" href="<?= htmlspecialchars($link, ENT_QUOTES) ?>">Xem thêm <i class="fa fa-angle-right"></i></a>
    <div class="c"></div>
    <div class="title-cat-line"></div>
    <div class="c20"></div>
    <div class="flex-container flex-space-between video-home">
        <?php foreach ($items as $i): $vc = ''; if (preg_match('%(?:youtu\.be/|v=|embed/)([A-Za-z0-9_-]+)%', (string)($i['video'] ?? ''), $m)) $vc = $m[1]; ?>
        <div class="cell-1-3 tab-cell-1-2 video-home-item">
            <div style="position:relative;padding-bottom:56.25%;padding-top:30px;height:0;overflow:hidden;"><iframe allowfullscreen frameborder="0" height="360" src="//www.youtube.com/embed/<?= htmlspecialchars($vc, ENT_QUOTES) ?>?modestbranding=1&amp;fs=1&amp;rel=0" style="position: absolute;top: 0;left: 0;width: 100%;height: 100%;" width="300"></iframe></div>
            <div class="c5"></div>
            <h3 class="video-home-name"><a href="<?= htmlspecialchars($newsBase . '/' . (string)($i['url'] ?? ''), ENT_QUOTES) ?>"><?= htmlspecialchars((string)($i['name'] ?? ''), ENT_QUOTES) ?></a></h3>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="c20"></div>
</div>
<!-- END BLOCK : video -->
<?php endforeach; ?>

<?php foreach ($other as $o): ?><?= is_string($o) ? $o : '' ?><?php endforeach; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const countdownElements = document.querySelectorAll('.countdown');
        let endTime = localStorage.getItem('flashsale_endTime');
        const now = Date.now();
        if (!endTime) { endTime = now + 4 * 60 * 60 * 1000; localStorage.setItem('flashsale_endTime', endTime); }
        else { endTime = parseInt(endTime); }
        countdownElements.forEach((el, index) => {
            if (index !== 0) { el.style.display = 'none'; return; }
            function updateCountdown() {
                const distance = endTime - Date.now();
                if (distance < 0) { endTime = Date.now() + 4 * 60 * 60 * 1000; localStorage.setItem('flashsale_endTime', endTime); }
                else {
                    const h = String(Math.floor(distance / 3600000)).padStart(2, '0');
                    const m = String(Math.floor((distance % 3600000) / 60000)).padStart(2, '0');
                    const s = String(Math.floor((distance % 60000) / 1000)).padStart(2, '0');
                    el.innerHTML = h + ':' + m + ':' + s;
                }
            }
            updateCountdown();
            setInterval(updateCountdown, 1000);
        });
    });
</script>
<script>
    $(function () {
        if (typeof Swiper === 'undefined') return;
        document.querySelectorAll('.slider-homenews').forEach(function (el) {
            new Swiper(el, {
                loop: true, slidesPerView: 4, spaceBetween: 20,
                autoplay: { delay: 2000 },
                pagination: { clickable: true },
                navigation: { nextEl: '.service-button-next', prevEl: '.service-button-prev' },
                breakpoints: { 320: { slidesPerView: 1, spaceBetween: 20 }, 768: { slidesPerView: 2.5, spaceBetween: 30 }, 1024: { slidesPerView: 3, spaceBetween: 20 } }
            });
        });
    });
</script>