<?php
$items = $items ?? [];
$menu = $menu ?? [];
$cat = $cat ?? null;
$dir = (string)($dir_path ?? '');
$catImage = $cat && !empty($cat['image']) ? $dir . '/' . ltrim((string)$cat['image'], '/') : '/uploaded/pic-cat/pic-default.jpg';
$catName = $cat['name'] ?? 'Sản phẩm';
$catUrl = $cat['url'] ?? '';
$productBase = '/san-pham';
$addcartBase = '/addcart';

$children = function (int $pid) use (&$children, $menu) {
    $out = [];
    foreach ($menu as $m) {
        if ((int)($m['parentid'] ?? 0) === $pid) {
            $out[] = $m;
        }
    }
    return $out;
};
$renderMenu = function (array $rows, int $level = 0) use (&$renderMenu, $children) {
    if ($rows === []) return '';
    $cls = $level === 0 ? 'menuleft' : 'submenu';
    $h = '<ul class="' . $cls . '">';
    foreach ($rows as $r) {
        $id = (int)($r['id_category'] ?? 0);
        $sub = $children($id);
        $h .= '<li class=""><a href="/' . htmlspecialchars((string)($r['url'] ?? ''), ENT_QUOTES) . '">'
            . htmlspecialchars((string)($r['name'] ?? ''), ENT_QUOTES);
        if ($sub) {
            $h .= ' <i class="fa fa-chevron-down show-sub-menu" id="mnu_' . $id . '"></i>';
        }
        $h .= '</a>';
        if ($sub) {
            $h .= $renderMenu($sub, $level + 1);
        }
        $h .= '</li>';
    }
    return $h . '</ul>';
};
$topMenu = $children(0);
?>

<div class="img-poster" style="position:relative">
    <a class="hide-on-tab" href=""><img src="<?php echo htmlspecialchars((string) ($catImage), ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8'); ?>" alt="" style="width:100%"/></a>
</div>
<div class="c"></div>
<div class="crumb"><div class="grid"><a href="/">Trang chủ</a> <i class="fa fa-angle-right"></i> <a href="<?php echo htmlspecialchars((string) ($productBase), ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8'); ?>/">Sản phẩm</a></div><div class="c5"></div></div>
<div class="c10"></div>

<div class="flashsale-wrapper">
    <div class="grid">
        <div class="text-center"><div style="padding:10px 0px"></div></div>
        <div class="flex-container">
            <div class="cell-1-4 hide-on-tab">
                <div class="title-left"><a href="<?php echo htmlspecialchars((string) ($productBase), ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8'); ?>/">Sản phẩm</a></div>
                <?php echo $renderMenu($topMenu); ?>
            </div>
            <div class="cell-3-4 tab-cell-1-1">
                <div class="title-left"><?php echo htmlspecialchars((string) ($catName), ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8'); ?></div>
                <div class="c10"></div>
                <div class="flex-container">
                    <?php foreach($items as $i): ?>
                    <?php
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
                                <a href="<?php echo htmlspecialchars((string) ($productBase), ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8'); ?>/<?php echo htmlspecialchars((string) ($i['url'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8'); ?>" class="img-shine" title="<?php echo htmlspecialchars((string) ($i['name'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8'); ?>">
                                    <img src="<?php echo htmlspecialchars((string) ($i['image'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8'); ?>" alt="<?php echo htmlspecialchars((string) ($i['name'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8'); ?>" style="width:100%"/>
                                </a>
                                <a class="addcart" href="<?php echo htmlspecialchars((string) ($addcartBase), ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8'); ?>/<?php echo htmlspecialchars((string) ($i['url'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8'); ?>" title="Thêm vào giỏ hàng"><i class="fas fa-cart-plus"></i></a>
                            </div>
                            <div class="producthot-box">
                                <h3>
                                    <a href="<?php echo htmlspecialchars((string) ($productBase), ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8'); ?>/<?php echo htmlspecialchars((string) ($i['url'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8'); ?>" class="producthot-item__name text-line-2" title="<?php echo htmlspecialchars((string) ($i['name'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8'); ?>">
                                        <?php echo htmlspecialchars((string) ($i['name'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8'); ?>
                                    </a>
                                </h3>
                                <div class="c10"></div>
                                <div class="producthot-item__intro text-line-2"><?php echo strip_tags((string)($i['intro'] ?? '')); ?></div>
                                <div class="producthot__price">
                                    <span class="producthot__price-old"><?php echo htmlspecialchars((string) ($pricekmOut), ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8'); ?></span>
                                    <span class="producthot__price-current"><?php echo htmlspecialchars((string) ($priceOut), ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8'); ?></span>
                                </div>
                            </div>
                            <div class="c10"></div>
                            <?php echo $sale; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="c20"></div>
                <div class="text-center"><a href="#" class="btn-seeall" id="loadMoreProduct">Xem thêm sản phẩm</a></div>
            </div>
        </div>
    </div>
</div>
