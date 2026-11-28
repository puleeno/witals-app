@php $items = $items ?? []; @endphp

                <div class="c20"></div>
                <h1 class="title-news-detail" style="text-align:left"> {{ $i['name'] ?? $name ?? '' }}</h1>
                <div class="c20"></div>

                <div class="intro-detail" >{{ $i['intro'] ?? $intro ?? '' }}</div>
                <div class="c10"></div>


		<div id="tocDiv">
			<ul id="tocList">

			</ul>
		</div>

		<div class="c10"></div>
		<div class="longPageNavigation"></div>
		<div class="c10"></div>

		<div class="content-detail tocContent">
			{{ $i['content'] ?? $content ?? '' }}
		</div>



<script>
$(".tocContent").LongPageNavigation({ longPageNavigationPosition:"longPageNavigation" });
</script>

                <div class="c20"></div>

				<script type="application/ld+json">
					{
					  "@@context": "https://schema.org",
					  "@@type": "NewsArticle",
					  "mainEntityOfPage": {
						"@@type": "WebPage",
						"@@id": "{{ $i['site_address'] ?? $site_address ?? '' }}{{ $i['link_detail'] ?? $link_detail ?? '' }}"
					  },
					  "headline": "{{ $i['name'] ?? $name ?? '' }}",
					  "image": [
						"{{ $i['site_address'] ?? $site_address ?? '' }}{{ $i['image'] ?? $image ?? '' }}"
					   ],
					  "datePublished": "{{ $i['createdate'] ?? $createdate ?? '' }}",
					  "dateModified": "{{ $i['createdate'] ?? $createdate ?? '' }}",
					  "author": {
						"@@type": "Person",
						"name": "Admin"
					  },
					   "publisher": {
						"@@type": "Organization",
						"name": "Kata",
						"logo": {
						  "@@type": "ImageObject",
						  "url": "{{ $i['site_address'] ?? $site_address ?? '' }}/uploaded/logo/logo.png"
						}
					  },
					  "description": "{{ $i['description'] ?? $description ?? '' }}"
					}
				</script>

                <div class="c20"></div>
@if(!empty($items['itemForm']))



	<div class="form-dangky" style="background: #EEE;" >
		<div class="c10"></div>
		<form action="{{ $i['dir_path'] ?? $dir_path ?? '' }}/contact/submit" method="post" id="news_regform" data-alert="{{ $i['_ban_can_nhap_thong_tin'] ?? $_ban_can_nhap_thong_tin ?? '' }}" data-captcha ="{{ $i['_captcha_invalid'] ?? $_captcha_invalid ?? '' }}" data-success="{{ $i['_thong_tin_lien_he'] ?? $_thong_tin_lien_he ?? '' }}" data-redirect="/contact-success/" >

			<input type="hidden"  name="code" value="save" />

			<div class="form-title"> {{ $i['_form_title'] ?? $_form_title ?? '' }} </div>
			<div class="c10"></div>

			<div class="c20"></div>

			<input name="yourname" type="text" class="txt-contact notNull" placeholder="Họ tên (*)" id="yourname"  />
			<div class="c10"></div>

			<input name="phone" type="text" class="txt-contact notNull"  placeholder="Điện thoại (*)"  />
			<div class="c10"></div>
			<input name="email" type="text" class="txt-contact notNull"  placeholder="Email"  />
			<div class="c10"></div>
			<input name="address" type="text" class="txt-contact "  placeholder="{{ $i['_form_address'] ?? $_form_address ?? '' }}" />
			<div class="c10"></div>

			<input name="other_request" type="text" class="txt-contact "  placeholder="{{ $i['_form_request'] ?? $_form_request ?? '' }}" />

			<div class="c10"></div>


			<div class=" flex-space-between flex-container">

				<div class="cell-1-2  tab-cell-1-1">
					<div class="c10"></div>
					<div style="position:relative;">
						<input class="txt-contact notNull" placeholder="Enter Captcha" name="sercurity"/>
						<img src="{{ $i['dir_path'] ?? $dir_path ?? '' }}/lib/imagesercurity.php?recaptcha=n" name="imgCaptcha" align="absmiddle" class="imgCaptcha newsletter-capt-img" style="height: 41px; position: absolute; top: 0px; right: -18px; " />

					</div>

					<!--
					<div class="g-recaptcha" data-sitekey="6LdBXrsUAAAAALjogTzmn0KfGrRiKrkqJ5bWUXg_"></div>
					-->

				</div>
				<div class="cell-1-2  tab-cell-1-1">
					<input name="input" type="button" onClick="$('#news_regform').submit();" style="padding: 25px;" class="btn-send" value="{{ $i['_send'] ?? $_send ?? '' }}" />

				</div>

			</div>

			<div class="c20"></div>

			<div class="msgbox" style="font-size: 20px; text-align:center;"> </div>
			<div class="c5"></div>
		</form>


	</div>
@endif


				<div class="c20"></div>



                <!-- AddThis Button BEGIN -->
                <div class="addthis_toolbox addthis_default_style "> <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a> <a class="addthis_button_tweet"></a> <a class="addthis_button_google_plusone" g:plusone:size="medium"></a> <a class="addthis_counter addthis_pill_style"></a> </div>
                <!-- AddThis Button END -->
                <div class="c30"></div>




                <h3 class="title-other-news" ><a>{{ $i['_othernews'] ?? $_othernews ?? '' }}</a></h3>
                <div class="c20"></div>

				<div class=" flex-space-between flex-container">
@foreach(($items['lstOther'] ?? []) as $i)

					<div class="cell-1-2  tab-cell-1-2 mobile-cell-1-1" style="margin-bottom:30px;">

						<div class=" flex-space-between flex-container">
							<div class="cell-1-3" >
								<a href="{{ $i['link_detail'] ?? $link_detail ?? '' }}"> {{ $i['image'] ?? $image ?? '' }} </a>
							</div>
							<div class="cell-2-3" >
								<div class="news-name"> <a href="{{ $i['link_detail'] ?? $link_detail ?? '' }}">{{ $i['name'] ?? $name ?? '' }}</a> </div>
								<div class="c5"></div>
								<div class="text-line-3" >{{ $i['intro'] ?? $intro ?? '' }}</div>
							</div>

						</div>
					</div>
@endforeach


				</div>
