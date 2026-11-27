<?php

//Develop Team: Binh Minh group
//binhminhgroup@gmail.com

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

$tpl = new TemplatePower($mobile_temp. $CONFIG['template_dir'] . "/home.htm");
$tpl->prepare();


$tpl->assignGlobal("dir_path", $dir_path);
langsite();

$objProduct = new dbProduct();
include_once("legacy/modules/db.provider/db.menu.php");

$dbHome = new dbMenu;
$home = new clsHome;


$tpl->printToScreen();

class clsHome {

    function __construct() {
        global $DB, $idc, $tpl, $dir_path, $cache_image_path, $SETTING, $dbHome;


        $db = $dbHome->selectByLocation('cathome');
        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }



            if ($rs['id_category'] > 0) {

                if ($rs['data_type'] == 'logo') {
                    $tpl->newBlock("advhome");
                    $this->advHome($rs['id_category']);
                }



                if ($rs['data_type'] == 'album') {

                    $tpl->newBlock("albumHome");
                    $tpl->assign("catname", $rs['title']);
                    $tpl->assign("catcontent", $rs['content']);
                    $tpl->assign("link", $dir_path . '/' . $rs['url']);
                    $tpl->assign("idcat", $rs['id_category']);
                    $this->albumHome($rs['id_category']);
                }

                if ($rs['data_type'] == 'video') {

                    $tpl->newBlock("video");
                    $tpl->assign("catname", $rs['name']);
                    $tpl->assign("catcontent", $rs['content']);
                    $tpl->assign("link", $dir_path . '/' . $rs['url']);
                    $this->videoHome($rs['id_category']);
                }

                if ($rs['data_type'] == 'product') {
                    $tpl->newBlock("product");
                    $tpl->assign("catname", $rs['name']);
                    $tpl->assign("link", $dir_path . '/' . $rs['url']);
                    $tpl->assign("catintro", $rs['intro']);
                    $tpl->assign("catimage", $rs['image'] ? $dir_path . '/' . ltrim($rs['image'], '/') : '');
                    $this->proHome($rs['id_category']);
                }

                if ($rs['data_type'] == 'news') {
                    $tpl->newBlock("news");
                    $tpl->assign("catname", $rs['name']);
                    $tpl->assign("link", $dir_path . '/' . $rs['url']);
                    $tpl->assign("catintro", $rs['intro']);
                    $this->newsHome($rs['id_category']);
                }

                if ($rs['data_type'] == 'info') {
                    $tpl->newBlock("info");

                    $tpl->assign("catname", $rs['name']);
                    $tpl->assign("link", $dir_path . '/' . $rs['url']);
                    $tpl->assign("catintro", $rs['intro']);

                    if ($rs['image'])
                        $tpl->assign("catimage", '<img src="' . $rs['image'] . '" width="100%"  alt="' . $rs['name'] . '"  style="display:block"  >');



                    $this->infoHome($rs['id_category']);
                }
                if ($rs['data_type'] == 'service') {
                    $tpl->newBlock("serviceHome");
                    $tpl->assign("catname", $rs['title']);
                    $tpl->assign("link", $dir_path . '/' . $rs['url']);
                    $tpl->assign("catintro", $rs['intro']);
                    $this->serviceHome($rs['id_category']);
                }

                if ($rs['data_type'] == 'article') {
                    $tpl->newBlock("article");
                    $tpl->assign("catname", $rs['title']);
                    $tpl->assign("link", $dir_path . '/' . $rs['url']);
                    $tpl->assign("catintro", $rs['intro']);
                    $this->articleHome($rs['id_category']);
                }
            }
        }

        include("legacy/modules/cathome.php");
        $tpl->assignGlobal("cathome", catHome());
        /*
        $tpl->assignGlobal("menuProduct", menuProduct());
        $this->support_online();
        */
        
        
        include_once("legacy/modules/partner_logo.php");
        $tpl->assignGlobal("s_partner",partner_logo());        
    }

    private function infoHome($idc) {
        global $DB, $tpl, $dir_path, $cache_image_path, $SETTING;
        include_once(__DIR__ . '/db.provider/db.info.php');
        $info = new dbInfo();
        $db = $info->itemList($idc, 1);
        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }


            if ($rs['id_info'] > 0) {

                $tpl->assign(array(
                    'name' => $rs['name'],
                    'intro' => $rs['intro'],
                    'content' => $rs['content'])
                );


                if ($rs['image']) {
                    $tpl->assign("image", '<img src="' . $cache_image_path . cropimage(385, 282, $dir_path . '/' . $rs['image']) . '" width="100%"  alt="' . $rs['name'] . '"  style="display:block" class="image-info-home" >');
                    $tpl->assign("image_url", $rs['image']);
                }

                $tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
            }
        }
    }

    function videoHome($idc) {
        global $DB, $tpl, $dir_path, $cache_image_path, $SETTING;
        $idc = intval($idc);
        $sql = "SELECT * FROM video WHERE active=1 AND (id_category IN(" . Category::getParentId($idc) . ") OR  groupcat IN(" . $idc . ")) ORDER BY thu_tu DESC, name ASC LIMIT 3";
        $db = $DB->query($sql);
        $n = 0;
        while ($rs = mysqli_fetch_array($db)) {
            $parse = parse_url($rs['video']);

            if ($parse['host'] == 'youtube.com' || $parse['host'] == 'www.youtube.com') {
                $tpl->newBlock("youtube");
                $tpl->assign("video", $rs['video']);
                $tpl->assign("videocode", getVideoCode($rs['video']));
                $tpl->assign("image", 'http://img.youtube.com/vi/' . getVideoCode($rs['video']) . '/0.jpg');
            } else {
                $tpl->newBlock("localvideo");
                $tpl->assign("video", $dir_path . '/' . $rs['video']);
                $tpl->assign("image", $cache_image_path . resizeimage(600, 400, $dir_path . '/' . $rs['image']));
            }
            $tpl->assign("name", $rs['name']);
            $tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);

            /*
              $n++;
              if ($n == 1) {

              if ($parse['host'] == 'youtube.com' || $parse['host'] == 'www.youtube.com') {
              $tpl->newBlock("youtube");
              $tpl->assign("video", $rs['video']);
              $tpl->assign("videocode", getVideoCode($rs['video']));
              $tpl->assign("image", 'http://img.youtube.com/vi/' . getVideoCode($rs['video']) . '/0.jpg');
              } else {
              $tpl->newBlock("localvideo");
              $tpl->assign("video", $dir_path . '/' . $rs['video']);
              $tpl->assign("image", $cache_image_path . resizeimage(628, '400', $dir_path . '/' . $rs['image']));
              }
              $tpl->assign("name", $rs['name']);
              $tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
              } else {
              $tpl->newBlock("other_video");
              $tpl->assign("name", $rs['name']);
              $tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
              $tpl->assign("image", 'http://img.youtube.com/vi/' . getVideoCode($rs['video']) . '/0.jpg');
              }
             */
        }
    }

    function proHome($idc) {

        global $DB, $tpl, $cache_image_path, $dir_path, $SETTING, $langLabel, $dbHome;

        /*
          $dbsub = $dbHome->listSubCat($idc);
          if (count($dbsub) > 0) $tpl->assign("dropdown", '<i class="fa fa-angle-down" aria-hidden="true"></i>');

          foreach ($dbsub as $rssub) {              if (!is_array($rssub)) { continue; }

          $tpl->newBlock("product_subcat");
          $tpl->assign("subname", $rssub['name']);
          $tpl->assign("sublink", $rssub['url']);
          }
         */


        $idc = intval($idc);
        $pro = new dbProduct();
        $db = $pro->itemList($idc, 99, $filter, $orderby);

        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }


            if ($rs['id_product'] > 0) {
                $i++;
                $tpl->newBlock("product_item");
                $tpl->assign("name", $rs['name']);
                $tpl->assign("ma", $rs['ma']);
                $tpl->assign("ttkhuyenmai", $rs['ttkhuyenmai']);
                if ($rs['image'])
                    $tpl->assign("image", '<img src="' . $cache_image_path . resizeimage1(400, 400, $dir_path . '/' . $rs['image']) . '" alt="' . $rs['name'] . '" width="100%" >');

                $tpl->assign("baohanh", $rs['baohanh']);
                $tpl->assign("xuatxu", $rs['xuatxu']);
                $tpl->assign("tinhtrang", $rs['tinhtrang']);

                if ($rs['icon'])
                    $tpl->assign("icon", '<div class="' . $rs['icon'] . '">' . $rs['texticon'] . '</div>');

                //$tpl->assign("attribute",$objProduct->getAttr(intval($rs['id_category']),$rs['attr']));
                $tpl->assign("link_cart", $dir_path . '/' . $lang_dir . 'addcart/' . $rs['url']);
                $tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
                if (intval($rs['pricekm']) > 0) {
                    $tpl->assign("price", number_format((float)($rs['pricekm'])) . 'đ');
                    $tpl->assign("pricekm", number_format((float)($rs['price'])) . 'đ');
                    $discount = 0;
                    $pricekm = intval($rs['pricekm']);
                    $price = intval($rs['price']);
                    $km = $price - $pricekm;
                    $discount = ($price > 0 ? intval(floatval($km / $price) * 100) : 0);
                    if ($discount > 0) {
                        $tpl->assign("discount-tags", "<div class='discount-tags'>-$discount%</div>");
                        $tpl->assign("sale_off", '<div class="home-product-item__sale-off"><span class="home-product-item__sale-off-percent">-' . $discount . '%</span><span class="home-product-item__sale-off-label">GIẢM</span></div>');
                    }
                } else {
                    if (intval($rs['price']) > 0) {
                        $tpl->assign("price", number_format((float)($rs['price'])) . 'đ');
                    } else {
                        if (intval($rs['price']) == 0) {
                            
                        } else {
                            $tpl->assign("price", $rs['price']);
                        }
                    }
                }
				
				$product_ver = json_decode($rs['product_ver']);

				foreach ($product_ver as $key => $value) {
					$tpl->newBlock("mausac");
					$tpl->assign("tenphienban", $value->tenphienban);
					$tpl->assign("motaphienban", $value->motaphienban);
				}					
				
				
            }
			
			
			
        }
    }

    function advHome($idc) {
        global $DB, $tpl, $cache_image_path, $dir_path;
        $idc = intval($idc);
        $lg = new dbLogo;

        $dblg = $lg->logoList($idc);
        foreach ($dblg as $rs) {            if (!is_array($rs)) { continue; }

            $tpl->newBlock("advItem");
            if ($rs['image']) {
                $tpl->assign("image", '<a href="' . $rs['link'] . '" target="' . $rs['target'] . '"><img src="' . $rs['image'] . '" width="100%" alt="' . $rs['name'] . '"></a>');
            } else {
                $tpl->assign("image", $rs['comment']);
            }
        }
    }

    function newsHot($idc) {
        global $DB, $tpl, $dir_path, $cache_image_path, $lang, $SETTING;
        $idc = intval($idc);

        $sql = "SELECT * FROM news WHERE active=1 AND (id_category IN(" . Category::getParentId($idc) . ") OR groupcat LIKE '%:" . $idc . ":%') ORDER BY thu_tu DESC, id_news DESC LIMIT 5"; //.;

        $db = $DB->query($sql);
        while ($rs = mysqli_fetch_array($db)) {

            $tpl->newBlock("newshotItem");
            if ($rs['image'])
                $tpl->assign("image", '<img src="' . $cache_image_path . cropimage(300, 200, $dir_path . '/' . $rs['image']) . '"  width="100%" alt="' . $rs['name'] . '">');

            $tpl->assign("name", $rs['name']);
            $tpl->assign("intro", strstrim(strip_tags($rs['intro'], 15)));
            $tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
        }
    }

    function newsHome($idc) {
        global $DB, $tpl, $dir_path, $cache_image_path, $lang, $SETTING;
        $idc = intval($idc);

        $sql = "SELECT * FROM news WHERE active=1 AND (id_category IN(" . Category::getParentId($idc) . ") OR groupcat LIKE '%:" . $idc . ":%') ORDER BY thu_tu DESC, id_news DESC LIMIT 6"; //.;

        $db = $DB->query($sql);

        while ($rs = mysqli_fetch_array($db)) {

            $tpl->newBlock("news_item");
            if ($rs['image'])
                $tpl->assign("image", '<img src="' . $cache_image_path . cropimage(387, 285, $dir_path . '/' . $rs['image']) . '"  width="100%" alt="' . $rs['name'] . '">');

            $tpl->assign("createdate", date('d/m/Y', $rs['ngay_dang']));
            $tpl->assign("name", $rs['name']);
            $tpl->assign("intro", strip_tags($rs['intro']));
            $tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
        }


        /*
          while ($rs = mysqli_fetch_array($db)) {
          $i++;

          if ($i == 1) {
          $tpl->newBlock("bignews");
          if ($rs['image'])
          $tpl->assign("image", '<img src="' . $cache_image_path . cropimage(300, 200, $dir_path . '/' . $rs['image']) . '"  width="100%" alt="' . $rs['name'] . '">');



          }elseif($i < 5){
          $tpl->newBlock("smallnews");
          if ($rs['image'])
          $tpl->assign("image", '<img src="' . $cache_image_path . cropimage(300, 200, $dir_path . '/' . $rs['image']) . '"  width="100%" alt="' . $rs['name'] . '">');

          }else{

          $tpl->newBlock("smallnews2");
          if ($rs['image'])
          $tpl->assign("image", '<img src="' . $cache_image_path . cropimage(300, 200, $dir_path . '/' . $rs['image']) . '"  width="100%" alt="' . $rs['name'] . '">');


          }
          $tpl->assign("createdate", date('d/m/Y', $rs['ngay_dang']));
          $tpl->assign("intro", strip_tags($rs['intro']));
          $tpl->assign("name", $rs['name']);
          $tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
          }
         */
    }

    function tuvanHome($idc) {
        global $DB, $tpl, $dir_path, $cache_image_path, $lang, $SETTING;
        $idc = intval($idc);
        $sql = "SELECT * FROM tuvan WHERE active=1 AND (id_category IN(" . Category::getParentId($idc) . ") OR groupcat LIKE '%:" . $idc . ":%') ORDER BY thu_tu DESC, id_tuvan DESC LIMIT 3"; //.;
        $db = $DB->query($sql);
        while ($rs = mysqli_fetch_array($db)) {

            $tpl->newBlock("tuvan_item");
            if ($rs['image'])
                $tpl->assign("image", '<img src="' . $cache_image_path . cropimage(114, 84, $dir_path . '/' . $rs['image']) . '"  width="100%" alt="' . $rs['name'] . '">');

            $tpl->assign("name", $rs['name']);
            $tpl->assign("intro", strstrim($rs['intro'], 15));
            $tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
        }
    }

    function serviceHome($idc) {
        global $DB, $tpl, $dir_path, $cache_image_path, $lang, $SETTING;
        $idc = intval($idc);

        $sql = "SELECT * FROM service WHERE active=1 AND (id_category IN(" . Category::getParentId($idc) . ") OR groupcat LIKE '%:" . $idc . ":%') ORDER BY thu_tu DESC, id_service DESC LIMIT 5"; //.;

        $db = $DB->query($sql);

        while ($rs = mysqli_fetch_array($db)) {

            $tpl->newBlock("serviceItem");
            if ($rs['image'])
                $tpl->assign("image", '<img src="' . $cache_image_path . cropimage(600, 400, $dir_path . '/' . $rs['image']) . '" width="100%" alt="' . $rs['name'] . '">');

            if ($rs['imageadv'])
                $tpl->assign("icon", '<img src="' . $rs['imageadv'] . '"  alt="' . $rs['name'] . '">');

            $tpl->assign("intro", strip_tags($rs['intro']));


            $tpl->assign("name", $rs['name']);
            $tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
        }
    }

    function articleHome($idc) {
        global $DB, $tpl, $dir_path, $cache_image_path, $lang, $SETTING;
        $idc = intval($idc);

        $sql = "SELECT * FROM article WHERE active=1 AND (id_category IN(" . Category::getParentId($idc) . ") OR groupcat LIKE '%:" . $idc . ":%') ORDER BY thu_tu DESC, id_article DESC LIMIT 5"; //.;

        $db = $DB->query($sql);

        while ($rs = mysqli_fetch_array($db)) {
            $tpl->newBlock("articleItem");
            $tpl->assign(array(
                'id' => $rs['id_article'],
                'name' => $rs['name'],
                'author_name' => $rs['author_name'],
                'author_info' => $rs['author_info'],
                'link_detail' => $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']
            ));
            $tpl->assign("intro", strip_tags($rs['intro']));
            if ($rs['image'])
                $tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(311, 311, $dir_path . '/' . $rs['image']) . '" alt="' . $rs['name'] . '" width="100%" />');
        }
    }

    function albumHome($idc) {
        global $DB, $tpl, $dir_path, $cache_image_path, $lang, $SETTING;
        $idc = intval($idc);

        $sql = "SELECT * FROM album WHERE active=1 AND id_category IN(" . Category::getParentId($idc) . ") ORDER BY thu_tu DESC LIMIT 5";

        $db = $DB->query($sql);

        while ($rs = mysqli_fetch_array($db)) {
            $tpl->newBlock("albumItem");
            if ($rs['image'])
                $tpl->assign("image", '<img src="' . $cache_image_path . cropimage(600, 400, $dir_path . '/' . $rs['image']) . '" width="100%" alt="' . $rs['name'] . '">');

            $tpl->assign("name", $rs['name']);
            $tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
        }
    }


    function support_online() {
        global $DB, $tpl, $cache_image_path, $dir_path, $SETTING, $language;
        $db = $DB->query("SELECT * FROM yahoo WHERE active = 1 $language ORDER BY thu_tu");

        $tpl->assign("emailsupport", $SETTING->emailsupport);
        $tpl->assign("yahoosupport", $SETTING->yahoosupport);
        $tpl->assign("skypesupport", $SETTING->skypesupport);

        while ($rs = mysqli_fetch_array($db)) {
            $tpl->newBlock("support_item");
            $tpl->assign("sky", $rs["sky"]);
            $tpl->assign("nic", $rs["nic"]);
            $tpl->assign("name", $rs["name"]);
            $tpl->assign("phone", $rs["phone"]);
        }
    }

    function sliderImageAlbum($id_item) {
        global $DB, $tpl, $dir_path, $cache_image_path;
        $id_value = $id_item;
        include_once (__DIR__ . '/manager/dataprovider/db_sys_image.php');
        $sysImage = new dbSysImage();
        $db = $sysImage->getList("", "album", "id_album", $id_value);

        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }

            if ($rs['id'] > 0) {
                $tpl->newBlock("album_image");
                $tpl->assign("name", $rs['name']);
                $tpl->assign("image", '<img  src="' . $rs['image'] . '" alt="' . $rs['name'] . '" title="' . $rs['name'] . '" width="100%" />');
            }
        }


        $tpl->assignGlobal("item_paged", $db['pages']);
    }

}

?>