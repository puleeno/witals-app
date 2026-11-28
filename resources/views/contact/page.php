<?php /** @var array<string,mixed>|null $cat */ ?>
<div class="c"></div>
<div class="crumb"><div class="grid"><a href="/<?= htmlspecialchars($lang_dir ?? '', ENT_QUOTES) ?>"><i class="fa fa-home"></i></a> <i class="fa fa-angle-right"></i> <?= htmlspecialchars((string)($cat['name'] ?? 'Liên hệ'), ENT_QUOTES) ?></div><div class="c5"></div></div>
<div class="c20"></div>
<div class="grid">

        <div >

            <div class="c10"></div>
            <h1 class="contact-title">
                <?= htmlspecialchars((string)($cat['name'] ?? 'Liên hệ'), ENT_QUOTES) ?>
            </h1>
            <div class="c10"></div>

            <div class="flex-container flex-space-between">
                <div class="cell-1-2 mobile-cell-1-1 tab-cell-1-1 margin-bottom-20">
                	<div class="frm-contact">

					<?= $cat['content'] ?? '' ?>
					<div class="c10"></div>


                    <form action="<?= htmlspecialchars($dir_path ?? '', ENT_QUOTES) ?>/contact/submit" method="post" id="contactform" data-alert="<?= htmlspecialchars($_ban_can_nhap_thong_tin ?? '', ENT_QUOTES) ?>" data-captcha ="<?= htmlspecialchars($_captcha_invalid ?? '', ENT_QUOTES) ?>" data-success="<?= htmlspecialchars($_thong_tin_lien_he ?? '', ENT_QUOTES) ?>" data-redirect="/contact-success/" >

                        <input type="hidden"  name="code" value="save" />
                        <div class="" >
                            <strong>GỬI CÂU HỎI CHO CHÚNG TÔI</strong>
                            <div class="c10"></div>
                            <div class="flex-container flex-space-between">
                                <div class="cell-1-2 tab-cell-1-1">
                                    <div class="line-title">Họ tên<span>*</span></div>
                                    <div class="line-frm">
                                        <input name="yourname" type="text" class="txt-contact notNull" id="yourname"  />
                                    </div>
                                </div>
                                <div class="cell-1-2 tab-cell-1-1">
                                    <div class="line-title">Điện thoại<span>*</span></div>
                                    <div class="line-frm">
                                        <input name="phone" type="text" class="txt-contact notNull"  />
                                    </div>
                                </div>
                                <div class="cell-1-2 tab-cell-1-1">
                                    <div class="line-title">Địa chỉ</div>
                                    <div class="line-frm">
                                        <input name="address" type="text" class="txt-contact notNull"  />
                                    </div>
                                </div>

                                <div class="cell-1-2 tab-cell-1-1 margin-bottom-20">
                                    <div class="line-title">Email</div>
                                    <div class="line-frm">
                                        <input name="email" type="text" class="txt-contact notNull"  />
                                    </div>
                                </div>
                                 <div class="cell-1-1 tab-cell-1-1">
                                    <div class="line-title">Nội dung<span>*</span></div>
                                    <div class="line-frm">
                                        <input name="other_request" type="text" class="txt-contact notNull" />
                                    </div>
                                </div>
                                <div class="cell-1-2 tab-cell-1-1">
									<div class="c15"></div>
                                    <div style="position:relative;">
										<input class="newsletter newsletter_b notNull" placeholder="Enter Captcha" name="sercurity"/>
										<img src="<?= htmlspecialchars($dir_path ?? '', ENT_QUOTES) ?>/lib/imagesercurity.php?recaptcha=n" alt="Captcha" class="imgCaptcha newsletter-capt-img" style="height: 35px; position: absolute; top: 0px; right: 0px; " />

									</div>


                                </div>

                                <div class="cell-1-2 mobile-cell-1-1 margin-bottom-20">
									<div class="c15"></div>
                                    <input name="input" type="button" onClick="$('#contactform').submit()" class="btn-contact" value="<?= htmlspecialchars(($_send ?? '') ?: 'Gửi', ENT_QUOTES) ?>" /></div>
                            </div>
                            <div class="c10"></div>
                            <div class="msgbox" style="font-size: 16px;"> </div>
                        </div>
                    </form>
                    <div class="c10"></div>
                </div>
                </div>
                <div class="cell-1-2 mobile-cell-1-1 tab-cell-1-1">
                	<?= $cat['content'] ?? '' ?>
                </div>
            </div>
            <div class="c30"></div>
        </div>

</div>
<div class="c10"></div>