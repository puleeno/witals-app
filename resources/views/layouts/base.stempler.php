<!doctype html>
<html lang="vi">
    <head>
        <meta charset="utf-8"/>
        <title>{{ $site_name ?? '' }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes"/>

        <meta name="theme-color" content="#0099ff"/>
        <meta name="language" content="{{ $langcode ?? '' }}" />
        <meta name="description" content="{{ $site_description ?? '' }}" />
        <meta name="keywords" content="{{ $site_keywords ?? '' }}" />
		{!! $canonical ?? '' !!}
        <meta property="og:url" name="og:url" content="{{ $site_url ?? '' }}" data-app/>
        <meta property="og:type" name="og:type" content="website" data-app/>
        <meta property="og:description" name="og:description" content="{{ $site_description ?? '' }}" data-app/>
        <meta property="og:title" name="og:title" content="{{ $site_name ?? '' }}" data-app/>
        {!! $site_image ?? '' !!}


        <link href="/favicon.png" rel="shortcut icon" />

        <link rel="stylesheet" type="text/css" href="{{ $dir_path ?? '' }}/css/font.css?v=20261127b"/>
        <link rel="stylesheet" type="text/css" href="{{ $dir_path ?? '' }}/css/style.css?v=20261127b"/>
        <link rel="stylesheet" type="text/css" href="{{ $dir_path ?? '' }}/css/grid.css?v=20261127b"/>
        <link rel="stylesheet" type="text/css" href="{{ $dir_path ?? '' }}/css/widget-call-left.css?v=20261127b"/>
        <link rel="stylesheet" type="text/css" href="{{ $dir_path ?? '' }}/css/fontawesome-5.12.1/css/all.min.css?v=20261127b"/>
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"/>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/js/swiper.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/css/swiper.css"/>

        <script src="{{ $dir_path ?? '' }}/js/script.js?v=20261127b"></script>

		{!! $toppage ?? '' !!}

		{!! $header_tag ?? '' !!}

    </head>
    <body>

	{!! $bodypage ?? '' !!}

        <div id="page">

            <div class="header-search topheader hide-on-pad hide-on-tab">
                <div class="grid">
                    <div class="flex-container">
                        <div class="cell-7-12 d-flex">

                            <div class="logo logo-pc">
                                {!! $logo ?? '' !!}
                            </div>


                            <div class="header__form">
                                <form id="searchform" method="get" action="/{{ $lang_dir ?? '' }}search/" class="header__search">
                                    <div class="header__search-input-wrap">
                                        <input class="header__search-input keysearch" type="text" name="qr" value="{{ $qr ?? '' }}" placeholder="Nhập để tìm kiếm sản phẩm" autocomplete="off">
                                            <ul class="searchresult" style="display: none;"></ul>
                                    </div>
                                    <a class="btn-search" onclick="$('#searchform').submit(); return false;" href="#">
                                        <i class="fa fa-search" aria-hidden="true"></i>
                                    </a>
                                </form>
								<script src="{{ $dir_path ?? '' }}/js/autocomplete.js"></script>
                            </div>
                        </div>

                        <div class="cell-5-12 d-flex">
                            <a href="tel:{{ $hotline ?? '' }}" class="header-hotline">
                                Hotline:
                                {{ $hotline ?? '' }}
                            </a>

                            <a href="/gio-hang" class="headercart">
                                <div class="headercart__img">
                                    <i class="fas fa-shopping-cart"></i>
                                    <div class="headercart-notice countcart">{{ $quantity ?? '' }}</div>
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
                        {!! $menubar ?? '' !!}
                        <div class="c"></div>
                    </div>

                </div>
                <div class="c"></div>
            </header>

            <div class="header-mobile hide-on-pc ">

					<div class="flex-container flex-space-between">
                        <div class="cell-1-3">
                            <a class="menu-mobile " href="#menu">
								<img src="{{ $dir_path ?? '' }}/images/m-icon-menu.png" alt="Menu"/>
							</a>

                        </div>

						<div class="cell-1-3" style="text-align:center;">
                            {!! $logo ?? '' !!}
						</div>

						<div class="cell-1-3">


							<div class="cart-mobile" >
								<a href="/gio-hang">
									<img src="{{ $dir_path ?? '' }}/images/m-icon-cart.png" alt="Giỏ hàng"/>
								</a>

								 <span class="countcart">{{ $quantity ?? '' }}</span>
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

            {!! $slideshow ?? '' !!}
{!! $content ?? '' !!}
<!-- footer -->
</div><!-- /.content -->

<footer class="footer">

    <div class="footer-main">
        <div class="grid">


            <div class="flex-container">

                <div class="cell-1-3 tab-cell-1-1">

                    <div class="footer-block">
                        <div class="footer-title footer-title-mobile menuboxfooter" rel="ct">
                            CÔNG TY TNHH THƯƠNG MẠI VÀ SẢN XUẤT XANHVINA
                        </div>

                        <ul>
	<li><span style="font-size:14px;"><strong>Đăng k&yacute; kinh doanh số</strong>:<strong> </strong>0110487876<span style="color:#e74c3c;"><strong>&nbsp;</strong></span><strong>- </strong>Sở KH&amp;ĐT TP&nbsp;H&agrave; Nội</span></li>
	<li><strong>Văn Ph&ograve;ng HN</strong>: Số 45, Thanh Liệt, Thanh Tr&igrave;, H&agrave; Nội</li>
	<li><strong>S&agrave;i G&ograve;n:&nbsp;</strong>618, Trần Đại Nghĩa, Phường T&acirc;n Tạo A, Quận B&igrave;nh T&acirc;n, TP Hồ Ch&iacute; Minh</li>
	<li><span style="font-size:14px;"><strong>Trụ sở ch&iacute;nh</strong>:&nbsp;</span>Nh&agrave; số 4, Th&ocirc;n Thượng, X&atilde; B&igrave;nh Minh, TP H&agrave; Nội, Việt Nam</li>
	<li><span style="font-size: 14px;"><b>Xưởng sản xuất: </b>Nh&agrave; số 2 Ng&otilde; 4 Cẩm Kh&ecirc;, Ph&uacute; Thọ</span></li>
	<li><span style="font-size:14px;"><strong>Hotline:&nbsp;</strong>0898.596.933 &nbsp;- 0971.048.490</span></li>
	<li><span style="font-size:14px;"><strong>Email:</strong> info.xanhvina@@gmail.com</span></li>
</ul>



                    </div>


                    <div class="c10"></div>

                </div>

                <div class="cell-2-3 tab-cell-1-1">

                    <div class="flex-container">

                        <div class="cell-2-12 tab-cell-1-1">

                            <div class="footer-block">

                                


















<div class="footer-title hide-on-tab ">  Sản phẩm  </div>
<div class="footer-title footer-title-mobile hide-on-pc hide-on-pad " rel="15">  Sản phẩm </div>

<div class="c5 hide-on-tab"></div>

<ul class="menuboxfooter" id="boxcol15">
    <li><a href="/cay-gia/"> <i class="fa fa-angle-right"></i> Cây giả</a></li>
    <li><a href="/co-la-reu-gia/"> <i class="fa fa-angle-right"></i> Cỏ, lá, rêu giả</a></li>
    <li><a href="/hoa-gia/"> <i class="fa fa-angle-right"></i> Hoa Giả</a></li>
    <li><a href="/chau-cay-canh/"> <i class="fa fa-angle-right"></i> Chậu cây cảnh</a></li>
</ul>
<div class="c10"></div>






                                <div class="c10"></div>

                            </div>

                        </div>

                        <div class="cell-3-12 tab-cell-1-1">

                            <div class="footer-block">

                                


















<div class="footer-title hide-on-tab ">  Thi công tiểu cảnh  </div>
<div class="footer-title footer-title-mobile hide-on-pc hide-on-pad " rel="80">  Thi công tiểu cảnh </div>

<div class="c5 hide-on-tab"></div>

<ul class="menuboxfooter" id="boxcol80">
    <li><a href="/tuong-cay-gia/"> <i class="fa fa-angle-right"></i> Tường cây giả</a></li>
    <li><a href="/tieu-canh-cay-gia/"> <i class="fa fa-angle-right"></i> Tiểu cảnh cây giả</a></li>
    <li><a href="/buc-cay-gia/"> <i class="fa fa-angle-right"></i> Bồn Cây Giả, Bục Cây Giả</a></li>
    <li><a href="/tuong-hoa-gia/"> <i class="fa fa-angle-right"></i> Tường Hoa Giả</a></li>
</ul>
<div class="c10"></div>






                                <div class="c10"></div>

                            </div>

                        </div>

                        <div class="cell-3-12 tab-cell-1-1">

                            <div class="footer-block">

                                


















<div class="footer-title hide-on-tab ">  Điều khoản chính sách  </div>
<div class="footer-title footer-title-mobile hide-on-pc hide-on-pad " rel="81">  Điều khoản chính sách </div>

<div class="c5 hide-on-tab"></div>

<ul class="menuboxfooter" id="boxcol81">
    <li><a href="/chinh-sach-thanh-toan/"> <i class="fa fa-angle-right"></i> Chính sách thanh toán</a></li>
    <li><a href="/chinh-sach-doi-tra/"> <i class="fa fa-angle-right"></i> Chính sách đổi trả</a></li>
    <li><a href="/chinh-sach-giao-hang/"> <i class="fa fa-angle-right"></i> Chính sách giao hàng</a></li>
    <li><a href="/chinh-sach-bao-mat-thong-tin-khach-hang/"> <i class="fa fa-angle-right"></i> Chính sách bảo mật</a></li>
    <li><a href="/chinh-sach-kiem-hang/"> <i class="fa fa-angle-right"></i> Chính sách kiểm hàng</a></li>
    <li><a href="/chinh-sach-bao-hanh/"> <i class="fa fa-angle-right"></i> Chính sách bảo hành</a></li>
    <li><a href="/dieu-khoan-su-dung/"> <i class="fa fa-angle-right"></i> Điều khoản sử dụng</a></li>
</ul>
<div class="c10"></div>






                                <div class="c10"></div>



                            </div>

                        </div>

                        <div class="cell-4-12 tab-cell-1-1">

                            <div class="footer-block">
                                <div class="footer-title footer-title-mobile menuboxfooter" rel="ct">
                                    Theo dõi chúng tôi
                                </div>
                                <ul class="footer-social">

                                    <li class="facebook  hvr-float">
                                        <a href="https://www.facebook.com/congtyxanhvina" class="footer-social__item" target="_blank">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    </li>
                                    <li class="youtube  hvr-float">
                                        <a href="https://www.youtube.com/@@xanhvina" class="footer-social__item" target="_blank">
                                            <i class="fab fa-youtube"></i>
                                        </a>
                                    </li>

                                    <li class="instagram  hvr-float">
                                        <a href="" class="footer-social__item" target="_blank">
                                            <i class="fab fa-twitter"></i>
                                        </a>
                                    </li>

                                    <li class="instagram  hvr-float">
                                        <a href="" class="footer-social__item" target="_blank">
                                            <i class="fab fa-instagram"></i>
                                        </a>
                                    </li>

                                </ul>
                            </div>

                            <div class="c10"></div>
                            <a href='http://online.gov.vn/Home/WebDetails/110267' target="_blank">
                                <img src='/images/tbbct200.png' width="150" />
                            </a>
                            <a href="//www.dmca.com/Protection/Status.aspx?ID=c7be7d6d-8177-4cc6-bf6c-eb43d005e06e"
                                title="DMCA.com Protection Status" class="dmca-badge">
                                <img src="https://images.dmca.com/Badges/dmca_protected_sml_120n.png?ID=c7be7d6d-8177-4cc6-bf6c-eb43d005e06e"
                                    alt="DMCA.com Protection Status" /></a>
                            <script src="https://images.dmca.com/Badges/DMCABadgeHelper.min.js"> </script>
                            <div class="c10"></div>
							
							<!--
                            <iframe
                                src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fcongtyxanhvina%2F&tabs=timeline&width=300&height=135&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId=119722755203098"
                                width="300" height="135" style="border:none;overflow:hidden" scrolling="no"
                                frameborder="0" allowfullscreen="true"
                                allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
								
							-->	

                        </div>


                    </div>



                </div>

            </div>




            <div class="c20"></div>


        </div>
    </div>


</footer>

<div class="footer-copyright">
    <div class="grid">
        <div class="flex-container">
            <div class="cell-1-4">
                <div class="txt-copy">{{ $copyright ?? '' }}</div>
            </div>
        </div>
    </div>
</div>


<div id="popupTuVanTham" class="modal-box">
    <a href="#" class="js-modal-close close btn-close"> [×] </a>
    <div class="modal-body">

        <div class="dangky">

            <div style="padding:20px; border-radius:10px; min-height:320px; ">


                <form action="{{ $dir_path ?? '' }}/contact/submit" method="post" id="regform2" data-alert="{{ $_ban_can_nhap_thong_tin ?? '' }}" data-captcha ="{{ $_captcha_invalid ?? '' }}" data-success="{{ $_thong_tin_lien_he ?? '' }}" data-redirect="/contact-success/" >

                    <input type="hidden"  name="code" value="save" />

					<div class="form-title"> Tư vấn mua sản phẩm </div>
                    <div class="c10"></div>

                    <div class="c20"></div>

                    <input name="yourname" type="text" class="txt-contact notNull" placeholder="Họ tên bạn(*)" id="yourname"  />
					<div class="c10"></div>

                    <input name="phone" type="text" class="txt-contact notNull"  placeholder="Điện thoại (*)"  />
					<div class="c10"></div>
					<input name="address" type="text" class="txt-contact "  placeholder="Địa chỉ" />
					<div class="c10"></div>
					<input name="other_request" type="text" class="txt-contact"  placeholder="Nội dung cần tư vấn" />
					<div class="c10"></div>


                    <div style="position:relative;">
						<input class="newsletter newsletter_b notNull" placeholder="Enter Captcha" name="sercurity"/>
						<img src="{{ $dir_path ?? '' }}/lib/imagesercurity.php?recaptcha=n" alt="Captcha" class="imgCaptcha newsletter-capt-img" style="height: 35px; position: absolute; top: 0px; right: 0px; " />

					</div>


					<div class="c20"></div>
                    <input name="input" type="button" onClick="$('#regform2').submit()" class="btn-send" value="{{ ($_send ?? '') ?: 'Gửi' }}" />
					<div class="c5"></div>
					<div class="msgbox" style="font-size: 20px; text-align:center;"> </div>
                </form>


            </div>

        </div>


    </div>
</div>

<div id="popupDangKy" class="modal-box">
    <a href="#" class="js-modal-close close btn-close"> [×] </a>
    <div class="modal-body">

        <div class="dangky">

            <div style="padding:20px; border-radius:10px; min-height:320px; ">


                <form action="{{ $dir_path ?? '' }}/contact/submit" method="post" id="regform"
                    data-alert="{{ $_ban_can_nhap_thong_tin ?? '' }}" data-captcha="{{ $_captcha_invalid ?? '' }}"
                    data-success="{{ $_thong_tin_lien_he ?? '' }}" data-redirect="/contact-success/">

                    <input type="hidden" name="code" value="save" />

                    <div class="form-title"> Tư vấn </div>
                    <div class="c10"></div>
                    <div class="form-label" style="text-align:center;"><i> Chúng tôi sẽ liên hệ với bạn trong thời gian
                            sớm nhất </i> </div>
                    <div class="c10"></div>
                    <div class="c20"></div>

                    <input name="yourname" type="text" class="txt-contact notNull" placeholder="Họ tên (*)"
                        id="yourname2" />
                    <div class="c10"></div>

                    <input name="phone" type="text" class="txt-contact notNull" placeholder="Điện thoại (*)" />
                    <div class="c10"></div>
                    <input name="address" type="text" class="txt-contact " placeholder="Địa chỉ" />
                    <div class="c10"></div>

                    <select name="other_request" class="txt-contact">
                        <option value=""> Chọn hình thức tư vấn </option>
                        <option value="Tư vấn mua hàng"> Tư vấn mua hàng </option>
                        <option value="Tìm đại lý, cửa hàng gần nhất">Tìm đại lý, cửa hàng gần nhất </option>

                    </select>
                    <div class="c10"></div>

                    <div style="position:relative;">
                        <input class="newsletter newsletter_b notNull" placeholder="Enter Captcha" name="sercurity" />
                        <img src="{{ $dir_path ?? '' }}/lib/imagesercurity.php?recaptcha=n" alt="Captcha"
                            class="imgCaptcha newsletter-capt-img"
                            style="height: 35px; position: absolute; top: 0px; right: 0px; " />

                    </div>


                    <div class="c20"></div>
                    <input name="input" type="button" onClick="$('#regform').submit()" class="btn-send" value="{{ ($_send ?? '') ?: 'Gửi' }}" />
                    <div class="c5"></div>
                    <div class="msgbox" style="font-size: 20px; text-align:center;"> </div>
                </form>


            </div>

        </div>


    </div>
</div>

<script>
    $(function () {

        var appendthis = ("<div class='modal-overlay js-modal-close'></div>");

        $('a[data-modal-id]').click(function (e) {
            e.preventDefault();
            $("body").append(appendthis);
            $(".modal-overlay").fadeTo(500, 0.7);
            var modalBox = $(this).attr('data-modal-id');
            $('#' + modalBox).fadeIn($(this).data());
        });

        $(".js-modal-close, .modal-overlay").click(function () {
            $(".modal-box, .modal-overlay").fadeOut(500, function () {
                $(".modal-overlay").remove();
            });
            return false;
        });

        $(window).resize(function () {
            $(".modal-box").css({
                top: ($(window).height() - $(".modal-box").outerHeight()) / 2,
                left: ($(window).width() - $(".modal-box").outerWidth()) / 2
            });
        });
        $(window).resize();
    });
</script>

</div>

<div id="button-btt">
    <a href="#"><i class="fa fa-chevron-up"></i> </a>
</div>

<script>
    $(function () {
        $('.countcart').html('{{ (string)($quantity ?? '') }}');
        // Đồng bộ badge từ nguồn cart Thật (ecommerce_cart), tránh JS cũ ghi đè.
        $.getJSON('/api/cart/count', function (r) {
            if (r && typeof r.count !== 'undefined') {
                $('.countcart').html(r.count);
            }
        });
    });
</script>


<link rel="stylesheet" type="text/css" href="{{ $dir_path ?? '' }}/css/fontawesome-5.12.1/css/all.min.css" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hover.css/2.3.1/css/hover.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jQuery.mmenu/4.7.5/css/jquery.mmenu.all.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.mmenu/4.7.5/js/jquery.mmenu.min.all.min.js"
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>

{{ $menuMobile ?? '' }}

<link rel="stylesheet" href="/css/widget-call-left.css" />
<div class="widget_wbt_call_now_widget">
    <div class="float-icon-hotline">
        <ul>
            <li class="hotline_float_icon"><a id="messengerButton1" href="tel:{{ $hotline ?? '' }}"><i
                        class="fa fa-hotline animated infinite tada"></i><span>Hotline: {{ $hotline ?? '' }}</span></a></li>
            <li class="hotline_float_icon"><a id="messengerButton2" href="//zalo.me/0898596933" target="_blank"><i
                        class="fa fa-zalo animated infinite tada"></i><span>Nhắn tin qua Zalo</span></a></li>
            <li class="hotline_float_icon"><a id="messengerButton" href="//m.me/565240863840888" target="_blank"><i
                        class="fa fa-messenger animated infinite tada"></i><span>Nhắn tin qua Facebook</span></a></li>


        </ul>
    </div>
</div>



{!! $bottompage ?? '' !!}

</body>


<!----
☺☺☺ Design by Minh Nguyen. Email nguyensyminh@@gmail.com - Hotline : 0968 67 55 68 ☺☺☺
---->

</html>