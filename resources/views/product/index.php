<?php $items = $items ?? []; ?>

	<div class="c20"></div>
<div class="grid">
	
    <h1 class="title-page"><span><?= htmlspecialchars($i['cat_title'] ?? $cat_title ?? '', ENT_QUOTES) ?></span></h1>
    <div class="c20"></div>
	
	<script type="application/ld+json">
			{
			"@context": "http://schema.org",
			"@type": "NewsArticle",
			"mainEntityOfPage":{
			"@type":"WebPage",
			"@id":"<?= htmlspecialchars($i['site_address'] ?? $site_address ?? '', ENT_QUOTES) ?><?= htmlspecialchars($i['cat_link'] ?? $cat_link ?? '', ENT_QUOTES) ?>"
			},
			"headline": "<?= htmlspecialchars($i['cat_title'] ?? $cat_title ?? '', ENT_QUOTES) ?>",
			"image": [
				"<?= htmlspecialchars($i['site_address'] ?? $site_address ?? '', ENT_QUOTES) ?><?= htmlspecialchars($i['cat_image'] ?? $cat_image ?? '', ENT_QUOTES) ?>"
			],			
			"datePublished": "<?= htmlspecialchars($i['create_date'] ?? $create_date ?? '', ENT_QUOTES) ?>",
			"dateModified": "<?= htmlspecialchars($i['create_date'] ?? $create_date ?? '', ENT_QUOTES) ?>",
			"author": {
			"@type": "Person",
			"name": "Admin"
			},
			"description": "<?= htmlspecialchars($i['cat_description'] ?? $cat_description ?? '', ENT_QUOTES) ?>"
			}
		</script>	
	
	<div class=""> <?= htmlspecialchars($i['cat_intro'] ?? $cat_intro ?? '', ENT_QUOTES) ?></div>
    <div class="c10"></div>
    

	
	
	

    <div class="flex-container">
<?php foreach(($items['product_item'] ?? []) as $i): ?>

        <div class="cell-1-4 tab-cell-1-2" style="margin-bottom: 20px;">
            <div class="producthome-item">
                <div class="img-hv">
                    <a href="<?= htmlspecialchars($i['link_detail'] ?? $link_detail ?? '', ENT_QUOTES) ?>" class="img-shine" title="<?= htmlspecialchars($i['name'] ?? $name ?? '', ENT_QUOTES) ?>">
                        <?= htmlspecialchars($i['image'] ?? $image ?? '', ENT_QUOTES) ?>
                    </a>
                    <a class="addcart" href="<?= htmlspecialchars($i['linkcart'] ?? $linkcart ?? '', ENT_QUOTES) ?>" title="Thêm vào giỏ hàng"><i class="fas fa-cart-plus"></i></a>
                </div>
                <div class="producthot-box">
                    <h3>
                        <a href="<?= htmlspecialchars($i['link_detail'] ?? $link_detail ?? '', ENT_QUOTES) ?>" class="producthot-item__name text-line-2" title="<?= htmlspecialchars($i['name'] ?? $name ?? '', ENT_QUOTES) ?>">
                            <?= htmlspecialchars($i['name'] ?? $name ?? '', ENT_QUOTES) ?>
                        </a>
                    </h3>
                    <div class="c10"></div>
                    <div class="producthot__price">
                        <span class="producthot__price-old"><?= htmlspecialchars($i['pricekm'] ?? $pricekm ?? '', ENT_QUOTES) ?></span>
                        <span class="producthot__price-current"><?= htmlspecialchars($i['price'] ?? $price ?? '', ENT_QUOTES) ?></span>
                    </div>
                </div>
                <div class="c10"></div>
                <?= htmlspecialchars($i['sale_off'] ?? $sale_off ?? '', ENT_QUOTES) ?>
            </div>
        </div>
<?php endforeach; ?>

    </div>



<!--
    <div class="c10"></div>
    <div class="paging"><?= htmlspecialchars($i['pages'] ?? $pages ?? '', ENT_QUOTES) ?></div>
    <div class="c5"></div>
-->
	
	
	<div id="pagingProduct" class="pagingProduct">
		
	</div>
	<input type="hidden" id="totalProduct" value="<?= htmlspecialchars($i['totalProduct'] ?? $totalProduct ?? '', ENT_QUOTES) ?>" />
	<a href="#" style="<?= htmlspecialchars($i['display_none'] ?? $display_none ?? '', ENT_QUOTES) ?>" id="loadMoreProduct" rel="1"> Xem thêm <span id="spconlai"><?= htmlspecialchars($i['productRemain'] ?? $productRemain ?? '', ENT_QUOTES) ?></span> sản phẩm </a>
	
	<script>
	$("#loadMoreProduct").click(function(){
		
		var pagenum = parseInt($(this).attr("rel"));
		var spconlai = parseInt($("#spconlai").html());
		$.ajax({
			url: "/index4.php?page=ajaxprovider&code=loadMoreProduct&pagenum=" + pagenum + "&idcat=" + <?= htmlspecialchars($i['idcat'] ?? $idcat ?? '', ENT_QUOTES) ?>,
			success: function (data) {
				html = data;
				$("#pagingProduct").append(html);
				
				spconlai = spconlai - 20;
				if (spconlai <=0) $("#loadMoreProduct").hide();

				$("#spconlai").html(spconlai);
			}
		});		
		
		$(this).attr("rel",pagenum + 1);
		return false;
	});
	
	</script>
	
	<div class="cat-content"><?= htmlspecialchars($i['cat_content'] ?? $cat_content ?? '', ENT_QUOTES) ?> </div>    
    <div class="c30"></div>	
	
	
</div>	
    
