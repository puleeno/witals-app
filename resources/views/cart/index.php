<?php $cart = $cart ?? []; ?>
<div class="content" style="background:#FFF">
    <div class="c"></div>
    <div class="crumb"><div class="grid"><a href="/">Trang chủ</a> <i class="fa fa-angle-right"></i> <a href="<?= htmlspecialchars($cart_path ?? '/gio-hang', ENT_QUOTES) ?>">Giỏ hàng</a></div><div class="c5"></div></div>
    <div class="c10"></div>

    <div class="grid">
        <h1 class="title-page"><span>Giỏ hàng</span></h1>
        <div class="c20"></div>

        <?php if ($cart === []): ?>
            <p>Giỏ hàng của bạn đang trống.</p>
        <?php else: ?>
            <div class="flex-container">
                <?php foreach ($cart as $item): ?>
                    <div class="cell-1-4 tab-cell-1-2" style="margin-bottom: 20px;">
                        <div class="producthome-item">
                            <div class="img-hv">
                                <img src="<?= htmlspecialchars((string)($item['image'] ?? ''), ENT_QUOTES) ?>" alt="<?= htmlspecialchars((string)($item['name'] ?? ''), ENT_QUOTES) ?>" width="100%">
                            </div>
                            <div class="producthot-box">
                                <h3><span class="producthot-item__name text-line-2"><?= htmlspecialchars((string)($item['name'] ?? ''), ENT_QUOTES) ?></span></h3>
                                <div class="c10"></div>
                                <div class="producthot__price">
                                    <span class="producthot__price-current"><?= htmlspecialchars((string)($item['price'] ?? ''), ENT_QUOTES) ?></span>
                                </div>
                                <div class="c10"></div>
                                <div>Số lượng: <?= (int)($item['qty'] ?? 1) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="c30"></div>
    </div>
</div>