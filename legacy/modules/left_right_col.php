<?php
//Develop Team: Binh Minh group
//binhminhgroup@gmail.com
defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

$tpl1;

function left_right_col() {

    global $DB, $dir_path, $lang_dir, $tpl1, $cache_image_path, $CONFIG, $page;

    $tpl1 = new TemplatePower($CONFIG['template_dir'] . "/left_right_col.htm");

    $tpl1->prepare();


    $tpl1->assignGlobal("lang", $lang);
    $tpl1->assignGlobal("lang_dir", $lang_dir);

    langsite($tpl1);
    $dbMenu = new dbMenu();

    $tpl1->assignGlobal("menuleft", menuleft());



    $dbright = $dbMenu->selectByLocation('inright');

    foreach ($dbright as $rs) {        if (!is_array($rs)) { continue; }

        if ($rs['id_category'] > 0) {

            if ($rs['data_type'] == 'logo') {

                $tpl1->newBlock("advRight");
                $tpl1->assign("catname", $rs['name']);
				
                advRightSlide($tpl1, $rs['id_category']);
            }

            if ($rs['data_type'] == 'video') {

                $tpl1->newBlock("video");
                $tpl1->assign("catname", $rs['name']);
                $tpl1->assign("link", $dir_path . '/' . $rs['url']);
                $tpl1->assign('catimage', '<img src="' . $cache_image_path . cropimage(32, 32, $dir_path . '/' . $rs['image']) . '"  >');
                videoLeft($rs['id_category']);
            }

            if ($rs['data_type'] == 'news') {
                $tpl1->newBlock("news");
                
                $tpl1->assign("catname", $rs['name']);
                $tpl1->assign("link", $dir_path . '/' . $rs['url']);
                $tpl1->assign("id_category", $rs['id_category']);
                $tpl1->assign('catimage', '<img src="' . $cache_image_path . cropimage(32, 32, $dir_path . '/' . $rs['imageadv']) . '"  >');
                newsRight($rs['id_category']);
            }

            if ($rs['data_type'] == 'tuvan') {
                $tpl1->newBlock("tuvan");
                $tpl1->assign("catname", $rs['name']);
                $tpl1->assign("link", $dir_path . '/' . $rs['url']);
                $tpl1->assign("id_category", $rs['id_category']);
                $tpl1->assign('catimage', '<img src="' . $cache_image_path . cropimage(32, 32, $dir_path . '/' . $rs['imageadv']) . '"  >');
                tuvanRight($rs['id_category']);
            }

            if ($rs['data_type'] == 'product') {
                $tpl1->newBlock("productRight");
                $tpl1->assign("catname", $rs['name']);
                $tpl1->assign("link", $dir_path . '/' . $rs['url']);
                $tpl1->assign("id_category", $rs['id_category']);
                $tpl1->assign('catimage', '<img src="' . $cache_image_path . cropimage(32, 32, $dir_path . '/' . $rs['image']) . '"  >');
                productRight($rs['id_category']);
            }
        }
    }


    support_online();

    return $tpl1->getOutputContent();
}

function advRight($tpl1, $idc) {

    global $DB, $cache_image_path, $dir_path;
    $idc = intval($idc);
    $lg = new dbLogo;
    $dblg = $lg->logoList($idc);

    foreach ($dblg as $rs) {        if (!is_array($rs)) { continue; }

        $tpl1->newBlock("advRight");
        if ($rs['image']) {
            $tpl1->assign("image", '<a href="' . $rs['link'] . '" target="' . $rs['target'] . '"><img src="' . $rs['image'] . '" width="100%" alt="' . $rs['name'] . '"></a>');
        } else {
            $tpl1->assign("image", $rs['comment']);
        }
    }
}

function advRightSlide($tpl1, $idc) {

    global $DB, $tpl1, $cache_image_path, $dir_path;
    $idc = intval($idc);
    $lg = new dbLogo;
    $dblg = $lg->logoList($idc);


    foreach ($dblg as $rs) {        if (!is_array($rs)) { continue; }

        $tpl1->newBlock("advRightSlide");
        if ($rs['image']) {
            $tpl1->assign("image", '<a href="' . $rs['link'] . '" target="' . $rs['target'] . '"> <img src="' . $rs['image'] . '" width="100%" alt="' . $rs['name'] . '"/></a>');
        } else {
            $tpl1->assign("image", $rs['comment']);
        }
    }
}

function newsRight($idc) {

    global $DB, $tpl1, $cache_image_path, $dir_path, $SETTING, $tags_news;
    $idc = intval($idc);
    $news = new dbNews();

    $tpl1->assign("news.idcat", $idc);

    $dbnews = $news->newsList($idc, 4);

    $i = 0;
    foreach ($dbnews as $rs) {        if (!is_array($rs)) { continue; }

        if ($rs['id_news'] > 0) {
            $i++;
            $tpl1->newBlock("itemnews");
            $tpl1->assign("createdate", '<div class="createdateleft">' . date('d/m/Y', $rs['ngay_dang']) . '</div>');
            if ($rs['image']) {
                $tpl1->assign('image', '<img src="' . $cache_image_path . cropimage(103, 78, $dir_path . '/' . $rs['image']) . '" width="100%" alt="' . $rs['name'] . '" class="image-news-left">');
            }
            $tpl1->assign("newsname", $rs['name']);
            $tpl1->assign("intro", strip_tags($rs['intro']));
            $tpl1->assign('link_detail', $dir_path . "/" . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
        }
    }
}

function tuvanRight($idc) {

    global $DB, $tpl1, $cache_image_path, $dir_path, $SETTING, $tags_news;
    $idc = intval($idc);
    $tuvan = new dbTuVan();

    $dbnews = $tuvan->itemList($tags_news, 5);

    $i = 0;
    foreach ($dbnews as $rs) {        if (!is_array($rs)) { continue; }

        if ($rs['id_tuvan'] > 0) {
            $i++;
            $tpl1->newBlock("item_tuvan");
            $tpl1->assign("createdate", '<div class="createdateleft">' . date('d/m/Y', $rs['ngay_dang']) . '</div>');
            if ($rs['image']) {
                $tpl1->assign('image', '<img src="' . $cache_image_path . cropimage(103, 78, $dir_path . '/' . $rs['image']) . '" width="100%" alt="' . $rs['name'] . '" class="image-news-left">');
            }
            $tpl1->assign("name", $rs['name']);
            $tpl1->assign("intro", $rs['intro']);
            $tpl1->assign('link_detail', $dir_path . "/" . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
        }
    }
}

function newsSlidesRight($idc) {

    global $DB, $tpl1, $cache_image_path, $dir_path, $SETTING;
    $idc = intval($idc);
    $news = new dbNews();
    $dbnews = $news->newsList($idc, 10);
    $i = 0;
    foreach ($dbnews as $rs) {        if (!is_array($rs)) { continue; }

        if ($rs['id_news'] > 0) {
            $i++;
            $tpl1->newBlock("slides_news_item");
            if ($rs['image'])
                $tpl1->assign('image', '<img src="' . $cache_image_path . resizeimage1(320, 210, $dir_path . '/' . $rs['image']) . '" alt="' . $rs['name'] . '" width="100%">');


            $tpl1->assign("newsname", $rs['name']);
            $tpl1->assign('link_detail', $dir_path . "/" . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
        }
    }
}

function articleSlidesRight($idc) {
    global $DB, $tpl1, $cache_image_path, $dir_path, $SETTING;
    $idc = intval($idc);
    $article = new dbArticle();
    $db = $article->itemList($idc, 10);
    $i = 0;
    foreach ($db as $rs) {        if (!is_array($rs)) { continue; }

        if ($rs['id_article'] > 0) {
            $i++;
            $tpl1->newBlock("slides_article_item");
            if ($rs['image'])
                $tpl1->assign('image', '<img src="' . $cache_image_path . cropimage(160, 160, $dir_path . '/' . $rs['image']) . '" alt="' . $rs['name'] . '" width="100%">');


            $tpl1->assign("name", $rs['name']);
            $tpl1->assign("intro", $rs['intro']);
            $tpl1->assign("author_name", $rs['author_name']);
            $tpl1->assign("author_info", $rs['author_info']);

            $tpl1->assign('linkdetail', $dir_path . "/" . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
        }
    }
}

function photoLeft($idc) {
    global $DB, $tpl1, $dir_path, $cache_image_path;
    $sql = "SELECT * FROM category WHERE active=1 AND id_category = $idc ORDER BY thu_tu DESC, name ASC LIMIT 1";
    $db = $DB->query($sql);
    if ($rs = mysqli_fetch_array($db)) {
        $tpl1->newBlock("photo");
        $tpl1->assign("name", $rs['name']);
        $tpl1->assign("link", $dir_path . '/' . $rs['url']);
        if ($rs['image']) {
            $tpl1->assign("image", '<img src="' . $cache_image_path . cropimage(230, 152, $dir_path . '/' . $rs['image']) . '" alt="' . $rs['name'] . '">');
        }
    }
}

function videoLeft($idc) {
    global $DB, $tpl1, $dir_path, $cache_image_path;
    $idc = intval($idc);

    $sql = "SELECT * FROM video WHERE active=1 AND (id_category IN(" . Category::getParentId($idc) . ") OR  groupcat IN(" . $idc . ")) ORDER BY thu_tu DESC, name ASC LIMIT 1";
    $db = $DB->query($sql);
    $n = 0;
    while ($rs = mysqli_fetch_array($db)) {
        $parse = parse_url($rs['video']);
        $n++;
        $parse = parse_url($rs['video']);

        if ($n == 1) {

            if ($parse['host'] == 'youtube.com' || $parse['host'] == 'www.youtube.com') {
                $tpl1->newBlock("youtube");
                $tpl1->assign("video", $rs['video']);
                $tpl1->assign("videocode", getVideoCode($rs['video']));
                $tpl1->assign("image", 'http://img.youtube.com/vi/' . getVideoCode($rs['video']) . '/0.jpg');
            } else {
                $tpl1->newBlock("localvideo");
                $tpl1->assign("video", $dir_path . '/' . $rs['video']);
                $tpl1->assign("image", $cache_image_path . resizeimage(628, '400', $dir_path . '/' . $rs['image']));
            }
            $tpl1->assign("name", $rs['name']);
            $tpl1->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
        } else {
            $tpl1->newBlock("other_video");
            $tpl1->assign("name", $rs['name']);
            $tpl1->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
            $tpl1->assign("image", 'http://img.youtube.com/vi/' . getVideoCode($rs['video']) . '/0.jpg');
        }
    }
}

function productRight($idc) {
    global $DB, $tpl1, $dir_path, $cache_image_path, $langLabel;
    $idc = intval($idc);
	
	$sql = "SELECT * FROM product WHERE active=1 AND (id_category IN(" . Category::getParentId($idc) . ") OR groupcat LIKE '%:" . $idc . ":%') ORDER BY thu_tu DESC LIMIT 8";
	
    $db = $DB->query($sql);
	
	
    while ($rs = mysqli_fetch_array($db)) {
		
		
        $tpl1->newBlock("itemProduct");
		
		$tpl1->assign("name", $rs['name']);
		
		$tpl1->assign("intro", $rs['intro']);
		$tpl1->assign("ma", $rs['ma']);

		if ($rs['icon'])
			$tpl1->assign("icon", '<div class="' . $rs['icon'] . '">' . $rs['texticon'] . '</div>');


		if (intval($rs['pricekm']) > 0) {
			$tpl1->assign("price", number_format((float)($rs['pricekm'])) . ' đ');
			$tpl1->assign("pricekm", number_format((float)($rs['price'])) . ' đ');

			$discount = 0;
			$pricekm = intval($rs['pricekm']);
			$price = intval($rs['price']);
			$km = $price - $pricekm;
			$discount = ($price > 0 ? intval(floatval($km / $price) * 100) : 0);
			if ($discount > 0)
				$tpl1->assign("discount-tags", "<div class='discount-tags'>-$discount%</div>");
		} else {
			if (intval($rs['price']) > 0) {
				$tpl1->assign("price", number_format((float)($rs['price'])) . ' đ');
			} else {
				if (intval($rs['price']) == 0) {
					
				} else {
					$tpl1->assign("price", $rs['price']);
				}
			}
		}

		if ($rs['image']) 
			$tpl1->assign("image", '<img src="' . $cache_image_path . resizeimage1(400, 400, $dir_path . '/' . $rs['image']) . '" alt="' . $rs['name'] . '" width="100%" >');
		
		
    }
}

function support_online() {
    global $DB, $tpl1, $cache_image_path, $dir_path, $SETTING, $language;
    $db = $DB->query("SELECT * FROM yahoo WHERE active = 1 $language ORDER BY thu_tu");

    $tpl1->assign("emailsupport", $SETTING->emailsupport);
    $tpl1->assign("yahoosupport", $SETTING->yahoosupport);
    $tpl1->assign("skypesupport", $SETTING->skypesupport);

    while ($rs = mysqli_fetch_array($db)) {
        $tpl1->newBlock("support_item");

        $tpl1->assign("sky", $rs["sky"]);
        $tpl1->assign("nic", $rs["nic"]);
        $tpl1->assign("name", $rs["name"]);
        $tpl1->assign("phone", $rs["phone"]);
    }
}

?>