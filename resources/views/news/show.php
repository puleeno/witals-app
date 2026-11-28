<?php $items = $items ?? []; ?>

                <div class="c20"></div>
                <h1 class="title-news-detail" style="text-align:left"> <?= htmlspecialchars($i['name'] ?? $name ?? '', ENT_QUOTES) ?></h1>
                <div class="c20"></div>

                <div class="intro-detail" ><?= htmlspecialchars($i['intro'] ?? $intro ?? '', ENT_QUOTES) ?></div>
                <div class="c10"></div>
				
				
		<div id="tocDiv">                                                           
			<ul id="tocList">                                                       

			</ul>                                                                   
		</div> 				

		<div class="c10"></div>
		<div class="longPageNavigation"></div> 				
		<div class="c10"></div>
		
		<div class="content-detail tocContent">
			<?= htmlspecialchars($i['content'] ?? $content ?? '', ENT_QUOTES) ?>
		</div>

				
				
<script>                                                                        
$(".tocContent").LongPageNavigation({ longPageNavigationPosition:"longPageNavigation" });
</script>				

                <div class="c20"></div>
				
				<script type="application/ld+json">
					{
					  "@context": "https://schema.org",
					  "@type": "NewsArticle",
					  "mainEntityOfPage": {
						"@type": "WebPage",
						"@id": "<?= htmlspecialchars($i['site_address'] ?? $site_address ?? '', ENT_QUOTES) ?><?= htmlspecialchars($i['link_detail'] ?? $link_detail ?? '', ENT_QUOTES) ?>"
					  },
					  "headline": "<?= htmlspecialchars($i['name'] ?? $name ?? '', ENT_QUOTES) ?>",
					  "image": [
						"<?= htmlspecialchars($i['site_address'] ?? $site_address ?? '', ENT_QUOTES) ?><?= htmlspecialchars($i['image'] ?? $image ?? '', ENT_QUOTES) ?>"
					   ],
					  "datePublished": "<?= htmlspecialchars($i['createdate'] ?? $createdate ?? '', ENT_QUOTES) ?>",
					  "dateModified": "<?= htmlspecialchars($i['createdate'] ?? $createdate ?? '', ENT_QUOTES) ?>",
					  "author": {
						"@type": "Person",
						"name": "Admin"
					  },
					   "publisher": {
						"@type": "Organization",
						"name": "Kata",
						"logo": {
						  "@type": "ImageObject",
						  "url": "<?= htmlspecialchars($i['site_address'] ?? $site_address ?? '', ENT_QUOTES) ?>/uploaded/logo/logo.png"
						}
					  },
					  "description": "<?= htmlspecialchars($i['description'] ?? $description ?? '', ENT_QUOTES) ?>"
					}
				</script>
				
                <div class="c20"></div>
<?php if(!empty($items['itemForm'])): ?>

	
	

	<div class="form-dangky" style="background: #EEE;" >
		<div class="c10"></div>
		<form action="<?= htmlspecialchars($i['dir_path'] ?? $dir_path ?? '', ENT_QUOTES) ?>/contact/submit" method="post" id="news_regform" data-alert="<?= htmlspecialchars($i['_ban_can_nhap_thong_tin'] ?? $_ban_can_nhap_thong_tin ?? '', ENT_QUOTES) ?>" data-captcha ="<?= htmlspecialchars($i['_captcha_invalid'] ?? $_captcha_invalid ?? '', ENT_QUOTES) ?>" data-success="<?= htmlspecialchars($i['_thong_tin_lien_he'] ?? $_thong_tin_lien_he ?? '', ENT_QUOTES) ?>" data-redirect="/contact-success/" >
		
			<input type="hidden"  name="code" value="save" />                

			<div class="form-title"> <?= htmlspecialchars($i['_form_title'] ?? $_form_title ?? '', ENT_QUOTES) ?> </div>
			<div class="c10"></div>
			
			<div class="c20"></div>

			<input name="yourname" type="text" class="txt-contact notNull" placeholder="Họ tên (*)" id="yourname"  />
			<div class="c10"></div>

			<input name="phone" type="text" class="txt-contact notNull"  placeholder="Điện thoại (*)"  />
			<div class="c10"></div>
			<input name="email" type="text" class="txt-contact notNull"  placeholder="Email"  />
			<div class="c10"></div>
			<input name="address" type="text" class="txt-contact "  placeholder="<?= htmlspecialchars($i['_form_address'] ?? $_form_address ?? '', ENT_QUOTES) ?>" />
			<div class="c10"></div>
			
			<input name="other_request" type="text" class="txt-contact "  placeholder="<?= htmlspecialchars($i['_form_request'] ?? $_form_request ?? '', ENT_QUOTES) ?>" />
			
			<div class="c10"></div>

			
			<div class=" flex-space-between flex-container">
					
				<div class="cell-1-2  tab-cell-1-1">
					<div class="c10"></div>
					<div style="position:relative;">
						<input class="txt-contact notNull" placeholder="Enter Captcha" name="sercurity"/>
						<img src="<?= htmlspecialchars($i['dir_path'] ?? $dir_path ?? '', ENT_QUOTES) ?>/lib/imagesercurity.php?recaptcha=n" name="imgCaptcha" align="absmiddle" class="imgCaptcha newsletter-capt-img" style="height: 41px; position: absolute; top: 0px; right: -18px; " />
						
					</div>
					
					<!--
					<div class="g-recaptcha" data-sitekey="6LdBXrsUAAAAALjogTzmn0KfGrRiKrkqJ5bWUXg_"></div>
					-->
					
				</div>
				<div class="cell-1-2  tab-cell-1-1">
					<input name="input" type="button" onClick="$('#news_regform').submit();" style="padding: 25px;" class="btn-send" value="<?= htmlspecialchars($i['_send'] ?? $_send ?? '', ENT_QUOTES) ?>" />
				
				</div>
				
			</div>
			
			<div class="c20"></div>
			
			<div class="msgbox" style="font-size: 20px; text-align:center;"> </div>		
			<div class="c5"></div>
		</form>
			

	</div>
<?php endif; ?>

				
				<div class="c20"></div>
				
				
				
                <!-- AddThis Button BEGIN -->
                <div class="addthis_toolbox addthis_default_style "> <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a> <a class="addthis_button_tweet"></a> <a class="addthis_button_google_plusone" g:plusone:size="medium"></a> <a class="addthis_counter addthis_pill_style"></a> </div>
                <!-- AddThis Button END -->
                <div class="c30"></div>




                <h3 class="title-other-news" ><a><?= htmlspecialchars($i['_othernews'] ?? $_othernews ?? '', ENT_QUOTES) ?></a></h3>
                <div class="c20"></div>

				<div class=" flex-space-between flex-container">
<?php foreach(($items['lstOther'] ?? []) as $i): ?>

					<div class="cell-1-2  tab-cell-1-2 mobile-cell-1-1" style="margin-bottom:30px;">
					
						<div class=" flex-space-between flex-container">
							<div class="cell-1-3" >
								<a href="<?= htmlspecialchars($i['link_detail'] ?? $link_detail ?? '', ENT_QUOTES) ?>"> <?= htmlspecialchars($i['image'] ?? $image ?? '', ENT_QUOTES) ?> </a>
							</div>
							<div class="cell-2-3" >
								<div class="news-name"> <a href="<?= htmlspecialchars($i['link_detail'] ?? $link_detail ?? '', ENT_QUOTES) ?>"><?= htmlspecialchars($i['name'] ?? $name ?? '', ENT_QUOTES) ?></a> </div>
								<div class="c5"></div>
								<div class="text-line-3" ><?= htmlspecialchars($i['intro'] ?? $intro ?? '', ENT_QUOTES) ?></div>
							</div>
					
						</div>
					</div>
<?php endforeach; ?>


				</div>

				 
