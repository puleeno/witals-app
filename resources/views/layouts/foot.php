<!-- footer -->

<footer class="footer">

    <div class="footer-main">
        <div class="grid">


            <div class="flex-container">

                <div class="cell-1-3 tab-cell-1-1">

                    <div class="footer-block">
                        <div class="footer-title footer-title-mobile menuboxfooter" rel="ct">
                            <?= htmlspecialchars($footername ?? '', ENT_QUOTES) ?>
                        </div>

                        <ul>
                            <?= $textfooter ?? '' ?>
                        </ul>



                    </div>


                    <div class="c10"></div>

                </div>

                <div class="cell-2-3 tab-cell-1-1">

                    <div class="flex-container">

                        <div class="cell-2-12 tab-cell-1-1">

                            <div class="footer-block">

                                <?= htmlspecialchars($boxmenufooter1 ?? '', ENT_QUOTES) ?>

                                <div class="c10"></div>

                            </div>

                        </div>

                        <div class="cell-3-12 tab-cell-1-1">

                            <div class="footer-block">

                                <?= htmlspecialchars($boxmenufooter2 ?? '', ENT_QUOTES) ?>

                                <div class="c10"></div>

                            </div>

                        </div>

                        <div class="cell-3-12 tab-cell-1-1">

                            <div class="footer-block">

                                <?= htmlspecialchars($boxmenufooter3 ?? '', ENT_QUOTES) ?>

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
                                        <a href="<?= htmlspecialchars($facebook ?? '', ENT_QUOTES) ?>" class="footer-social__item" target="_blank">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    </li>
                                    <li class="youtube  hvr-float">
                                        <a href="<?= htmlspecialchars($youtube ?? '', ENT_QUOTES) ?>" class="footer-social__item" target="_blank">
                                            <i class="fab fa-youtube"></i>
                                        </a>
                                    </li>

                                    <li class="instagram  hvr-float">
                                        <a href="<?= htmlspecialchars($twitter ?? '', ENT_QUOTES) ?>" class="footer-social__item" target="_blank">
                                            <i class="fab fa-twitter"></i>
                                        </a>
                                    </li>

                                    <li class="instagram  hvr-float">
                                        <a href="<?= htmlspecialchars($instagram ?? '', ENT_QUOTES) ?>" class="footer-social__item" target="_blank">
                                            <i class="fab fa-instagram"></i>
                                        </a>
                                    </li>

                                </ul>
                            </div>

                            <div class="c10"></div>

                            <div class="c10"></div>

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
                <div class="txt-copy"><?= htmlspecialchars($copyright ?? '', ENT_QUOTES) ?></div>
            </div>
        </div>
    </div>
</div>


<div id="popupTuVanTham" class="modal-box">
    <a href="#" class="js-modal-close close btn-close"> [×] </a>
    <div class="modal-body">

        <div class="dangky">

            <div style="padding:20px; border-radius:10px; min-height:320px; ">


                <form action="<?= htmlspecialchars($dir_path ?? '', ENT_QUOTES) ?>/index4.php?page=contact&lang=<?= htmlspecialchars($lang ?? '', ENT_QUOTES) ?>" method="post" id="regform2" data-alert="<?= htmlspecialchars($_ban_can_nhap_thong_tin ?? '', ENT_QUOTES) ?>" data-captcha ="<?= htmlspecialchars($_captcha_invalid ?? '', ENT_QUOTES) ?>" data-success="<?= htmlspecialchars($_thong_tin_lien_he ?? '', ENT_QUOTES) ?>" data-redirect="/contact-success/" >

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
						<img src="<?= htmlspecialchars($dir_path ?? '', ENT_QUOTES) ?>/lib/imagesercurity.php?recaptcha=n" name="imgCaptcha" align="absmiddle" class="imgCaptcha newsletter-capt-img" style="height: 35px; position: absolute; top: 0px; right: 0px; " />

					</div>


					<div class="c20"></div>
                    <input name="input" type="button" onClick="$('#regform2').submit()" class="btn-send" value="<?= htmlspecialchars($_send ?? '', ENT_QUOTES) ?>" />
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


                <form action="<?= htmlspecialchars($dir_path ?? '', ENT_QUOTES) ?>/index4.php?page=contact&lang=<?= htmlspecialchars($lang ?? '', ENT_QUOTES) ?>" method="post" id="regform"
                    data-alert="<?= htmlspecialchars($_ban_can_nhap_thong_tin ?? '', ENT_QUOTES) ?>" data-captcha="<?= htmlspecialchars($_captcha_invalid ?? '', ENT_QUOTES) ?>"
                    data-success="<?= htmlspecialchars($_thong_tin_lien_he ?? '', ENT_QUOTES) ?>" data-redirect="/contact-success/">

                    <input type="hidden" name="code" value="save" />

                    <div class="form-title"> Tư vấn </div>
                    <div class="c10"></div>
                    <div class="form-label" style="text-align:center;"><i> Chúng tôi sẽ liên hệ với bạn trong thời gian
                            sớm nhất </i> </div>
                    <div class="c10"></div>
                    <div class="c20"></div>

                    <input name="yourname" type="text" class="txt-contact notNull" placeholder="Họ tên (*)"
                        id="yourname" />
                    <div class="c10"></div>

                    <input name="phone" type="text" class="txt-contact notNull" placeholder="Điện thoại (*)" />
                    <div class="c10"></div>
                    <input name="address" type="text" class="txt-contact " placeholder="Địa chỉ" />
                    <div class="c10"></div>

                    <select name="other_request" type="text" class="txt-contact">
                        <option value=""> Chọn hình thức tư vấn </option>
                        <option value="Tư vấn mua hàng"> Tư vấn mua hàng </option>
                        <option value="Tìm đại lý, cửa hàng gần nhất">Tìm đại lý, cửa hàng gần nhất </option>

                    </select>
                    <div class="c10"></div>

                    <div style="position:relative;">
                        <input class="newsletter newsletter_b notNull" placeholder="Enter Captcha" name="sercurity" />
                        <img src="<?= htmlspecialchars($dir_path ?? '', ENT_QUOTES) ?>/lib/imagesercurity.php?recaptcha=n" name="imgCaptcha" align="absmiddle"
                            class="imgCaptcha newsletter-capt-img"
                            style="height: 35px; position: absolute; top: 0px; right: 0px; " />

                    </div>


                    <div class="c20"></div>
                    <input name="input" type="button" onClick="$('#regform').submit()" class="btn-send" value="<?= htmlspecialchars($_send ?? '', ENT_QUOTES) ?>" />
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
</div>

<div id="button-btt">
    <a href="#"><i class="fa fa-chevron-up"></i> </a>
</div>

<script>
    $(function () {
        $('.countcart').html('<?= htmlspecialchars($quantity ?? '', ENT_QUOTES) ?>');
    });
</script>


<link rel="stylesheet" type="text/css" href="<?= htmlspecialchars($dir_path ?? '', ENT_QUOTES) ?>/css/fontawesome-5.12.1/css/all.min.css" />

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

<?= htmlspecialchars($menuMobile ?? '', ENT_QUOTES) ?>

<link rel="stylesheet" href="/css/widget-call-left.css" />
<div class="widget_wbt_call_now_widget">
    <div class="float-icon-hotline">
        <ul>
            <li class="hotline_float_icon"><a id="messengerButton" href="tel:<?= htmlspecialchars($hotline ?? '', ENT_QUOTES) ?>"><i
                        class="fa fa-hotline animated infinite tada"></i><span>Hotline: <?= htmlspecialchars($hotline ?? '', ENT_QUOTES) ?></span></a></li>
            <li class="hotline_float_icon"><a id="messengerButton" href="//zalo.me/0898596933" target="_blank"><i
                        class="fa fa-zalo animated infinite tada"></i><span>Nhắn tin qua Zalo</span></a></li>
            <li class="hotline_float_icon"><a id="messengerButton" href="//m.me/565240863840888" target="_blank"><i
                        class="fa fa-messenger animated infinite tada"></i><span>Nhắn tin qua Facebook</span></a></li>


        </ul>
    </div>
</div>



<?= htmlspecialchars($bottompage ?? '', ENT_QUOTES) ?>

</body>


<!----
☺☺☺ Design by Minh Nguyen. Email nguyensyminh@gmail.com - Hotline : 0968 67 55 68 ☺☺☺
---->

</html>
