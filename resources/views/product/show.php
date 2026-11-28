<?php $items = $items ?? []; $slider = $items['slider_image'] ?? []; $other = $items['other_products'] ?? []; ?>

<div class="c"></div>
<div class="crumb"><div class="grid"><a href="/">Trang chủ</a> <i class="fa fa-angle-right"></i> <a href="/san-pham/">Sản phẩm</a></div><div class="c5"></div></div>
<div class="c10"></div>



<script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "Product",
        "name": <?= json_encode((string)($name ?? ''), JSON_UNESCAPED_UNICODE|JSON_HEX_TAG) ?>,
        "image": <?= json_encode((string)($site_address ?? '').($image ?? ''), JSON_UNESCAPED_UNICODE|JSON_HEX_TAG) ?>,
        "description": "",
        "brand": {
            "@type": "Brand",
            "name": "XanhVina"
        },
        "sku": <?= json_encode((string)($ma ?? ''), JSON_UNESCAPED_UNICODE) ?>,
        "offers": {
            "@type": "Offer",
            "url": <?= json_encode((string)($site_address ?? '').($cat_link ?? ''), JSON_UNESCAPED_UNICODE|JSON_HEX_TAG) ?>,
            "priceCurrency": "VND",
            "price": <?= json_encode((string)($price_number ?? '0'), JSON_UNESCAPED_UNICODE) ?>,
            "priceValidUntil": "2023-12-31",
            "availability": "https://schema.org/InStock",
            "itemCondition": "https://schema.org/NewCondition"
        },
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.8",
            "bestRating": "5",
            "worstRating": "1",
            "ratingCount": "50"
        }
    }
</script>

<div class="product-wrapper">
    <div class="grid">
        <div class="flex-container flex-space-between">
            <div class="cell-5-12 tab-cell-1-1">
                <div class="swiper-container gallery-top swiper-container-initialized swiper-container-horizontal swiper-container-autoheight">
                    <div class="swiper-wrapper" style="height: 517px; transform: translate3d(-1054px, 0px, 0px); transition-duration: 0ms;">
                        <?php if ($slider === []): ?>
                        <div class="swiper-slide" style="width: 517px; margin-right: 10px;"><img src="<?= htmlspecialchars((string)($image ?? ''), ENT_QUOTES) ?>" alt="<?= htmlspecialchars((string)($name ?? ''), ENT_QUOTES) ?>" title="<?= htmlspecialchars((string)($name ?? ''), ENT_QUOTES) ?>" style="width:100%"></div>
                        <?php else: foreach ($slider as $i): ?>
                        <div class="swiper-slide" style="width: 517px; margin-right: 10px;"><img src="<?= htmlspecialchars((string)($i['image'] ?? ''), ENT_QUOTES) ?>" alt="<?= htmlspecialchars((string)($name ?? ''), ENT_QUOTES) ?>" title="<?= htmlspecialchars((string)($name ?? ''), ENT_QUOTES) ?>" style="width:100%"></div>
                        <?php endforeach; endif; ?>
                    </div>
                    <div class="swiper-button-next swiper-button-black" tabindex="0" role="button" aria-label="Next slide" aria-disabled="false"></div>
                    <div class="swiper-button-prev swiper-button-black" tabindex="0" role="button" aria-label="Previous slide" aria-disabled="false"></div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>

                <div class="c10"></div>
                <div class="swiper-container gallery-thumbs-pro swiper-container-initialized swiper-container-horizontal swiper-container-free-mode swiper-container-thumbs">
                    <div class="swiper-wrapper" style="transform: translate3d(0px, 0px, 0px); transition-duration: 0ms;">
                        <?php if ($slider === []): ?>
                        <div class="swiper-slide swiper-slide-active" style="width: 91.4px; margin-right: 15px;"><img src="<?= htmlspecialchars((string)($thumbimage ?? $image ?? ''), ENT_QUOTES) ?>" alt="<?= htmlspecialchars((string)($name ?? ''), ENT_QUOTES) ?>" title="<?= htmlspecialchars((string)($name ?? ''), ENT_QUOTES) ?>" style="width:100%"></div>
                        <?php else: foreach ($slider as $i): ?>
                        <div class="swiper-slide" style="width: 91.4px; margin-right: 15px;"><img src="<?= htmlspecialchars((string)($i['thumb'] ?? $i['image'] ?? ''), ENT_QUOTES) ?>" alt="<?= htmlspecialchars((string)($name ?? ''), ENT_QUOTES) ?>" title="<?= htmlspecialchars((string)($name ?? ''), ENT_QUOTES) ?>" style="width:100%"></div>
                        <?php endforeach; endif; ?>
                    </div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>

                <div class="c10"></div>

                <script>
                    var swiper = new Swiper(".gallery-thumbs-pro", {
                        spaceBetween: 15,
                        slidesPerView: 5,
                        freeMode: true,
                        watchSlidesProgress: true,
                        autoplay: true,
                    });
                    var swiper2 = new Swiper(".gallery-top", {
                        spaceBetween: 10,
                        loops: true,
                        slidesPerView: 1,
                        autoplay: {
                            delay: 2500,
                            disableOnInteraction: false,
                        },
                        autoHeight: true,
                        navigation: {
                            nextEl: ".swiper-button-next",
                            prevEl: ".swiper-button-prev",
                        },
                        thumbs: {
                            swiper: swiper,
                        },
                    });
                </script>

                <div class="c10"></div>
            </div>
            <div class="cell-7-12 tab-cell-1-1">
                <div style="font-size: 24px">
                    <strong><?= htmlspecialchars((string)($name ?? ''), ENT_QUOTES) ?></strong>
                </div>
                <div class="c5"></div>
                <div class="tinhtrang" style="float: left; margin-right: 20px">
                    SKU: <b><?= htmlspecialchars((string)($ma ?? ''), ENT_QUOTES) ?></b>
                </div>

                <div class="tinhtrang" style="float: left">
                    Thương hiệu: <b style="color: #5b8651"><?= htmlspecialchars((string)($hangsx ?? 'Xanhvina'), ENT_QUOTES) ?></b>
                </div>
                <div class="c10"></div>

                <div class="tinhtrang">Tình trạng: <b>Còn hàng</b></div>
                <div class="c10"></div>
                <div class="price">
                    <div class="pricekm-detail" id="pricekm"><?= htmlspecialchars((string)($price ?? 'Liên hệ'), ENT_QUOTES) ?></div>
                    <small>(Giá bán chưa bao gồm phí vận chuyển)</small>
                </div>
                <div class="c20"></div>
                <b style="float: left">Chọn mua:</b>
                <div class="c5"></div>
                <div class="product-ver size">
                </div>
                <div class="c20"></div>
                <div class="number">
                    <span> <b>Số lượng :</b> </span>
                    <span class="minus">-</span>
                    <input type="text" id="qty" name="qty" value="1">
                    <span class="plus">+</span>
                </div>
                <div class="attr_item-title">Mã giảm giá</div>
                <div class="c5"></div>

                <input type="text" name="voucher" id="voucher" style="width: 250px; font-size: 16px; padding: 10px; border-radius: 25px" placeholder="Mã giảm giá">

                <div class="c10"></div>
                <span class="voucher_value" style="color: #0f75bc; font-family: 'Muli-B'; font-size: 18px"></span>
                <div class="c10"></div>

                <div class="flex-container" style="justify-content: flex-start; align-items: center">
                    <a class="buynow-btn" href="<?= htmlspecialchars((string)($linkcart ?? ''), ENT_QUOTES) ?>?qty=1&amp;voucher=">
                        <i class="fas fa-cart-plus"></i> Mua hàng ngay
                    </a>

                    <a class="addcart-btn" href="<?= htmlspecialchars((string)($linkcart ?? ''), ENT_QUOTES) ?>?qty=1&amp;voucher=">
                        <i class="fas fa-arrow-right"></i> Thêm vào giỏ hàng</a>
                </div>
                <p>
                    <img src="/uploaded/logo/logo.png" alt="" width="40px">
                    <b style="color: #5b8651">Xanhvina</b> đảm bảo : 3 ngày trả hàng/hoàn tiền
                </p>

                <div class="c20"></div>
                
                <div class="c10"></div>
            </div>

            <input type="hidden" id="link_to_cart" value="<?= htmlspecialchars((string)($linkcart ?? ''), ENT_QUOTES) ?>">

            <script>
                $("#voucher").change(function () {
                    $(".voucher_value").load(
                        "/api/voucher/check?idcat=<?= (int)($idcat ?? 0) ?>&v=" +
                            $(this).val()
                    );
                    linkcart();
                });
                $(".buynow-btn").click(function () {
                    linkcart();
                });
                $(document).ready(function () {
                    let basePrice = parseInt($("#pricekm").text().replace(/\D/g, "")) || 0;
                    $("#pricekm").data("base-price", basePrice);

                    $(".attr-item input[type=checkbox]").change(function () {
                        let currentPrice = $("#pricekm").data("base-price");
                        $(".attr-item input[type=checkbox]").each(function () {
                            if ($(this).is(":checked")) {
                                let accessoryPrice =
                                    parseInt($(this).val().replace(/\D/g, "")) || 0;
                                currentPrice += accessoryPrice;
                            }
                        });
                        $("#pricekm").html(currentPrice.toLocaleString("vi-VN") + " đ");
                        linkcart();
                    });
                });
                $(".product-ver-item").click(function () {
                    $(".product-ver-item").removeClass("choose-price");
                    $(this).addClass("choose-price");
                    $("#pricekm").html($(this).attr("data-price-format"));
                    linkcart();
                });

                $(".addcart-btn").click(function () {
                    linkcart();
                });

                function linkcart() {
                    var voucher = $("#voucher").val();
                    var link_to_cart =
                        $("#link_to_cart").val() +
                        "?qty=" +
                        $("#qty").val() +
                        "&voucher=" +
                        voucher;

                    $(".addcart-btn").attr("href", link_to_cart);
                    $(".buynow-btn").attr("href", link_to_cart);
                }

                $("#qty").change(function () {
                    linkcart();
                });

                $(function () {
                    $(".product-ver-item").first().addClass("choose-price");
                    linkcart();
                });

                $(".minus").click(function () {
                    var $input = $(this).parent().find("input");
                    var count = parseInt($input.val()) - 1;
                    count = count < 1 ? 1 : count;
                    $input.val(count);
                    $input.change();
                    return false;
                });
                $(".plus").click(function () {
                    var $input = $(this).parent().find("input");
                    $input.val(parseInt($input.val()) + 1);
                    $input.change();
                    return false;
                });
            </script>

            <div class="cell-1-1">
                <div class="c30"></div>
                <div class="line-ww"></div>
                <div class="c30"></div>

                <div class="flex-container">
                    <div class="cell-3-4 tab-cell-1-1">
                        <div class="text0h" style="text-align: justify">
                            <div class="product-content tocContent"><div class="content-pro"><?= $content ?? '' ?></div>

<div class="c20">&nbsp;</div>
</div>
                        </div>

                        <div class="sharethis-inline-share-buttons st-left  st-inline-share-buttons st-animated" id="st-1"><div class="st-btn st-first " data-network="facebook" style="display: inline-block;">
  <img alt="facebook sharing button" src="https://platform-cdn.sharethis.com/img/facebook.svg">
  
</div><div class="st-btn " data-network="twitter" style="display: inline-block;">
  <img alt="twitter sharing button" src="https://platform-cdn.sharethis.com/img/twitter.svg">
  
</div><div class="st-btn " data-network="linkedin" style="display: inline-block;">
  <img alt="linkedin sharing button" src="https://platform-cdn.sharethis.com/img/linkedin.svg">
  
</div><div class="st-btn " data-network="pinterest" style="display: inline-block;">
  <img alt="pinterest sharing button" src="https://platform-cdn.sharethis.com/img/pinterest.svg">
  
</div><div class="st-btn " data-network="whatsapp" style="display: inline-block;">
  <img alt="whatsapp sharing button" src="https://platform-cdn.sharethis.com/img/whatsapp.svg">
  
</div><div class="st-btn st-last " data-network="sharethis" style="display: inline-block;">
  <img alt="sharethis sharing button" src="https://platform-cdn.sharethis.com/img/sharethis.svg">
  
</div></div>
                    </div>
                    <div class="cell-1-4 tab-cell-1-1">







<div class="c10"></div>






<div class="c10"></div>
</div>

                    <div class="c10"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="c50"></div>

    <div class="flashsale-wrapper" style="background-color: #00000018">
        <div class="c50"></div>

        <div class="grid">
            <div class="title-page">Sản phẩm tương tự</div>
            <div class="c20"></div>
            <div class="flex-container">
                <div class="c30"></div>
                <div class="flex-container">

                    <?php foreach ($other as $i): ?>
                    <div class="cell-1-4 tab-cell-1-2">
                        <div class="producthome-item">
                            <div class="img-hv">
                                <a href="<?= htmlspecialchars((string)($i['link_detail'] ?? ''), ENT_QUOTES) ?>" class="img-shine"> <img src="<?= htmlspecialchars((string)($i['image'] ?? ''), ENT_QUOTES) ?>" alt="<?= htmlspecialchars((string)($i['name'] ?? ''), ENT_QUOTES) ?>" style="width:100%"> </a>
                                <div class="hvr-show">
                                    <a href="<?= htmlspecialchars((string)($i['link_cart'] ?? ''), ENT_QUOTES) ?>" title="Thêm vào giỏ hàng"><i class="fas fa-cart-plus"></i></a>
                                </div>
                            </div>

                            <div class="producthot-box">
                                <h3>
                                    <a href="<?= htmlspecialchars((string)($i['link_detail'] ?? ''), ENT_QUOTES) ?>" class="producthot-item__name text-line-2">
                                        <?= htmlspecialchars((string)($i['name'] ?? ''), ENT_QUOTES) ?>
                                    </a>
                                </h3>
                                <div class="c10"></div>

                                <div class="producthot__price">
                                    <span class="producthot__price-old"><?= htmlspecialchars((string)($i['pricekm'] ?? ''), ENT_QUOTES) ?> <span style="margin-right: 5px"></span></span>
                                    <span class="producthot__price-current"><?= htmlspecialchars((string)($i['price'] ?? ''), ENT_QUOTES) ?></span>
                                </div>
                            </div>

                            <div class="c10"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="cell-1-4 tab-cell-1-2"></div>
                    <div class="cell-1-4 tab-cell-1-2"></div>
                    <div class="cell-1-4 tab-cell-1-2"></div>
                </div>
            </div>
        </div>
        <div class="c20"></div>
        <div class="c30"></div>
    </div>


</div>
