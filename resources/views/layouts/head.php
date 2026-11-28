<!doctype html>
<html lang="vi">
    <head>
        <meta charset="utf-8"/>
        <title><?= htmlspecialchars($site_name ?? '', ENT_QUOTES) ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes"/>

        <meta name="theme-color" content="#0099ff"/>
        <meta name="language" content="<?= htmlspecialchars($langcode ?? '', ENT_QUOTES) ?>" />
        <meta name="description" content="<?= htmlspecialchars($site_description ?? '', ENT_QUOTES) ?>" />
        <meta name="keywords" content="<?= htmlspecialchars($site_keywords ?? '', ENT_QUOTES) ?>" />
		<?= $canonical ?? '' ?>
        <meta property="og:url" name="og:url" content="<?= htmlspecialchars($site_url ?? '', ENT_QUOTES) ?>" data-app/>
        <meta property="og:type" name="og:type" content="website" data-app/>
        <meta property="og:description" name="og:description" content="<?= htmlspecialchars($site_description ?? '', ENT_QUOTES) ?>" data-app/>
        <meta property="og:title" name="og:title" content="<?= htmlspecialchars($site_name ?? '', ENT_QUOTES) ?>" data-app/>
        <?= $site_image ?? '' ?>


        <link href="/favicon.png" rel="shortcut icon" />

        <link rel="stylesheet" type="text/css" href="<?= htmlspecialchars($dir_path ?? '', ENT_QUOTES) ?>/css/font.css?v=20261127b"/>
        <link rel="stylesheet" type="text/css" href="<?= htmlspecialchars($dir_path ?? '', ENT_QUOTES) ?>/css/style.css?v=20261127b"/>
        <link rel="stylesheet" type="text/css" href="<?= htmlspecialchars($dir_path ?? '', ENT_QUOTES) ?>/css/grid.css?v=20261127b"/>
        <link rel="stylesheet" type="text/css" href="<?= htmlspecialchars($dir_path ?? '', ENT_QUOTES) ?>/css/widget-call-left.css?v=20261127b"/>
        <link rel="stylesheet" type="text/css" href="<?= htmlspecialchars($dir_path ?? '', ENT_QUOTES) ?>/css/fontawesome-5.12.1/css/all.min.css?v=20261127b"/>
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"/>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/js/swiper.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/css/swiper.css"/>

        <script src="<?= htmlspecialchars($dir_path ?? '', ENT_QUOTES) ?>/js/script.js?v=20261127b"></script>

		<?= $toppage ?? '' ?>

		<?= $header_tag ?? '' ?>

    </head>
    <body>

	<?= $bodypage ?? '' ?>

        <div id="page">

            <div class="header-search topheader hide-on-pad hide-on-tab">
                <div class="grid">
                    <div class="flex-container">
                        <div class="cell-7-12 d-flex">

                            <div class="logo logo-pc">
                                <?= $logo ?? '' ?>
                            </div>


                            <div class="header__form">
                                <form id="searchform" method="get" action="/<?= htmlspecialchars($lang_dir ?? '', ENT_QUOTES) ?>search/" class="header__search">
                                    <div class="header__search-input-wrap">
                                        <input class="header__search-input keysearch" type="text" name="qr" value="<?= htmlspecialchars($qr ?? '', ENT_QUOTES) ?>" placeholder="Nhập để tìm kiếm sản phẩm" autocomplete="off">
                                            <ul class="searchresult" style="display: none;"></ul>
                                    </div>
                                    <a class="btn-search" onclick="$('#searchform').submit(); return false;" href="#">
                                        <i class="fa fa-search" aria-hidden="true"></i>
                                    </a>
                                </form>
								<script src="<?= htmlspecialchars($dir_path ?? '', ENT_QUOTES) ?>/js/autocomplete.js"></script>
                            </div>
                        </div>

                        <div class="cell-5-12 d-flex">
                            <a href="tel:<?= htmlspecialchars($hotline ?? '', ENT_QUOTES) ?>" class="header-hotline">
                                Hotline:
                                <?= htmlspecialchars($hotline ?? '', ENT_QUOTES) ?>
                            </a>

                            <a href="/gio-hang" class="headercart">
                                <div class="headercart__img">
                                    <i class="fas fa-shopping-cart"></i>
                                    <div class="headercart-notice countcart"><?= htmlspecialchars($quantity ?? '', ENT_QUOTES) ?></div>
                                </div>
                                Giỏ hàng
                            </a>
                        </div>

                    </div>
                </div>

            </div>

            <header class="header hide-on-pad hide-on-tab">

                <div class="grid">
                    <div class="menubar">
                        <?= $menubar ?? '' ?>
                        <div class="c"></div>
                    </div>

                </div>
                <div class="c"></div>
            </header>

            <div class="header-mobile hide-on-pc ">

					<div class="flex-container flex-space-between">
                        <div class="cell-1-3">
                            <a class="menu-mobile " href="#menu">
								<img src="<?= htmlspecialchars($dir_path ?? '', ENT_QUOTES) ?>/images/m-icon-menu.png" alt="Menu"/>
							</a>

                        </div>

						<div class="cell-1-3" style="text-align:center;">
                            <?= $logo ?? '' ?>
						</div>

						<div class="cell-1-3">


							<div class="cart-mobile" >
								<a href="/gio-hang">
									<img src="<?= htmlspecialchars($dir_path ?? '', ENT_QUOTES) ?>/images/m-icon-cart.png" alt="Giỏ hàng"/>
								</a>

								 <span class="countcart"><?= htmlspecialchars($quantity ?? '', ENT_QUOTES) ?></span>
							</div>

                        </div>
					</div>
                <div class="c"></div>
            </div>



            <script type="text/javascript">
                $(document).ready(function () {
                    $(".show-search-mobile").click(function () {
                        $(".search-mobile").stop().slideToggle("slow");
                    });
                });
            </script>

            <div class="content" style="background:#FFF">

            <?= $slideshow ?? '' ?>
