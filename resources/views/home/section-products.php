<?php $items = $items ?? []; ?>
<div class="home-section home-section-products">
    <div class="grid">
        <div class="title-page"><span><?= htmlspecialchars((string)($title ?? 'Sản phẩm'), ENT_QUOTES) ?></span></div>
        <div class="c20"></div>
        <div class="flex-container">
            <?php foreach ($items as $i): ?>
            <div class="cell-1-4 tab-cell-1-2" style="margin-bottom: 20px;">
                <div class="producthome-item">
                    <div class="img-hv">
                        <a href="/san-pham/<?= htmlspecialchars((string)($i['url'] ?? ''), ENT_QUOTES) ?>" class="img-shine" title="<?= htmlspecialchars((string)($i['name'] ?? ''), ENT_QUOTES) ?>">
                            <img src="<?= htmlspecialchars((string)($i['image'] ?? ''), ENT_QUOTES) ?>" alt="<?= htmlspecialchars((string)($i['name'] ?? ''), ENT_QUOTES) ?>" width="100%">
                        </a>
                        <div class="hvr-show">
                            <a href="/addcart/<?= htmlspecialchars((string)($i['url'] ?? ''), ENT_QUOTES) ?>" title="Thêm vào giỏ hàng"><i class="fas fa-cart-plus"></i></a>
                        </div>
                    </div>
                    <div class="producthot-box">
                        <h3>
                            <a href="/san-pham/<?= htmlspecialchars((string)($i['url'] ?? ''), ENT_QUOTES) ?>" class="producthot-item__name text-line-2">
                                <?= htmlspecialchars((string)($i['name'] ?? ''), ENT_QUOTES) ?>
                            </a>
                        </h3>
                        <div class="c10"></div>
                        <div class="producthot__price">
                            <span class="producthot__price-old"><?= htmlspecialchars((string)($i['pricekm'] ?? ''), ENT_QUOTES) ?></span>
                            <span class="producthot__price-current"><?= htmlspecialchars((string)($i['price'] ?? ''), ENT_QUOTES) ?></span>
                        </div>
                    </div>
                    <div class="c10"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="c30"></div>
    </div>
</div>