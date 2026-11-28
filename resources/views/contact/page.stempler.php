<div class="c"></div>
<div class="crumb"><div class="grid"><a href="/{{ $lang_dir ?? '' }}"><i class="fa fa-home"></i></a> <i class="fa fa-angle-right"></i> {{ $cat['name'] ?? 'Liên hệ' }}</div><div class="c5"></div></div>
<div class="c20"></div>
<div class="grid">

        <div >

            <div class="c10"></div>
            <h1 class="contact-title">
                {{ $cat['name'] ?? 'Liên hệ' }}
            </h1>
            <div class="c10"></div>

            <div class="flex-container flex-space-between">
                <div class="cell-1-2 mobile-cell-1-1 tab-cell-1-1 margin-bottom-20">
                	<div class="frm-contact">

					{!! $cat['content'] ?? '' !!}
					<div class="c10"></div>


                    <form action="{{ $dir_path ?? '' }}/contact/submit" method="post" id="contactform" data-alert="{{ $_ban_can_nhap_thong_tin ?? '' }}" data-captcha ="{{ $_captcha_invalid ?? '' }}" data-success="{{ $_thong_tin_lien_he ?? '' }}" data-redirect="/contact-success/" >

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
										<img src="{{ $dir_path ?? '' }}/lib/imagesercurity.php?recaptcha=n" alt="Captcha" class="imgCaptcha newsletter-capt-img" style="height: 35px; position: absolute; top: 0px; right: 0px; " />

									</div>


                                </div>

                                <div class="cell-1-2 mobile-cell-1-1 margin-bottom-20">
									<div class="c15"></div>
                                    <input name="input" type="button" onClick="$('#contactform').submit()" class="btn-contact" value="{{ ($_send ?? '') ?: 'Gửi' }}" /></div>
                            </div>
                            <div class="c10"></div>
                            <div class="msgbox" style="font-size: 16px;"> </div>
                        </div>
                    </form>
                    <div class="c10"></div>
                </div>
                </div>
                <div class="cell-1-2 mobile-cell-1-1 tab-cell-1-1">
                	{!! $cat['content'] ?? '' !!}
                </div>
            </div>
            <div class="c30"></div>
        </div>

</div>
<div class="c10"></div>
