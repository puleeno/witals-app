<?php
$row = $row ?? [];
$idc = (int)($row['id_category'] ?? 0);
$items = [];
try {
    $orig = $idc;
    $ids = [$orig];
    $guard = 0;
    while ($guard++ < 10) {
        $pr = $db->select()->from('category')->where('id_category', $idc)->limit(1)->fetchAll();
        $pid = (int)($pr[0]['parentid'] ?? 0);
        if ($pid <= 0 || in_array($pid, $ids, true)) break;
        $ids[] = $pid; $idc = $pid;
    }
    $all = $db->select()->from('product')->where('active', 1)->orderBy('thu_tu', 'ASC')->limit(500)->fetchAll();
    foreach ($all as $p) {
        $pc = (int)($p['id_category'] ?? 0);
        $gc = (string)($p['groupcat'] ?? '');
        if (in_array($pc, $ids, true) || ($orig > 0 && strpos($gc, ':' . $orig . ':') !== false)) {
            $items[] = $p;
        }
    }
} catch (\Throwable) { $items = []; }
$link = '/' . trim((string)($row['url'] ?? ''), '/');
$catimage = $row['image'] ? ((string)($dir_path ?? '')) . '/' . ltrim((string)$row['image'], '/') : '';
?>
<div class="flashsale-wrapper">
    <div class="c50"></div>
    <div class="grid">
        <div class="homecat-title">
            <div class="flashsale-header">
                <img class="flas-sales" src="<?= htmlspecialchars($catimage, ENT_QUOTES) ?>" width="80" alt="">
                <h2><a href="<?= htmlspecialchars($link, ENT_QUOTES) ?>"><?= htmlspecialchars((string)($row['name'] ?? ''), ENT_QUOTES) ?></a></h2>
                <div class="flashsale-timer"><div class="countdown"></div></div>
            </div>
        </div>
        <div class="c30"></div>
        <div class="flex-container">
            <?php foreach ($items as $i): ?>
            <div class="cell-1-4 tab-cell-1-2">
                <div class="producthome-item">
                    <div class="img-hv">
                        <a href="/san-pham/<?= htmlspecialchars((string)($i['url'] ?? ''), ENT_QUOTES) ?>" class="img-shine" title="<?= htmlspecialchars((string)($i['name'] ?? ''), ENT_QUOTES) ?>">
                            <img src="<?= htmlspecialchars((string)($i['image'] ?? ''), ENT_QUOTES) ?>" alt="<?= htmlspecialchars((string)($i['name'] ?? ''), ENT_QUOTES) ?>" width="100%">
                        </a>
                        <a class="addcart" href="/addcart/<?= htmlspecialchars((string)($i['url'] ?? ''), ENT_QUOTES) ?>" title="Thêm vào giỏ hàng"><i class="fas fa-cart-plus"></i></a>
                    </div>
                    <div class="producthot-box">
                        <h3>
                            <a href="/san-pham/<?= htmlspecialchars((string)($i['url'] ?? ''), ENT_QUOTES) ?>" class="producthot-item__name text-line-2" title="<?= htmlspecialchars((string)($i['name'] ?? ''), ENT_QUOTES) ?>">
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
        <div class="text-center"><a href="<?= htmlspecialchars($link, ENT_QUOTES) ?>" class="btn-seeall">Xem Tất cả</a></div>
        <div class="c30"></div>
    </div>
    <div class="c20"></div>
</div>