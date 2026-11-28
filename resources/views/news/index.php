<?php $items = $items ?? []; ?>

				

				<div class="c20"></div>
				<div class="c20"></div>
<?php foreach(($items['newsItem'] ?? []) as $i): ?>

				<div class=" flex-space-between flex-container">
					
					<div class="cell-1-3 tab-cell-1-2" style="margin-bottom:30px;">
						<a href="<?= htmlspecialchars($i['link_detail'] ?? $link_detail ?? '', ENT_QUOTES) ?>"> <?= $i['image'] ?? $image ?? '' ?> </a>
						

					</div>
					
					<div class="cell-2-3 tab-cell-1-2">
						<h2 class="news-name"> <a href="<?= htmlspecialchars($i['link_detail'] ?? $link_detail ?? '', ENT_QUOTES) ?>"><?= $i['name'] ?? $name ?? '' ?></a> </h2>
						<div class="c10"></div>
						<div class="hide-on-mobile"><?= $i['intro'] ?? $intro ?? '' ?></div>
					</div>

				</div>
				<div class="c20"></div>
<?php endforeach; ?>


				<div class="c10"></div>
				<div class="c20" style="border-top:solid 1px #CCC;"></div>
				<div><?= $i['pages'] ?? $pages ?? '' ?></div>
				<div class="c20"></div>

			
