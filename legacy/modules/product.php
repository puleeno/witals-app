<?php

//Develop Team: Binh Minh group
//binhminhgroup@gmail.com

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

$template_name = $cateinfo['template_name'];
if ($template_name)
    $tpl = new TemplatePower($CONFIG['template_dir'] . "/$template_name.htm");
else
    $tpl = new TemplatePower($CONFIG['template_dir'] . "/product.htm");

$tpl->prepare();
$tpl->assignGlobal("dir_path", $dir_path);

$tpl->assignGlobal("lang", $lang);
$tpl->assignGlobal("lang_dir", $lang_dir);

$tpl->assignGlobal("site_address", $site_address);

langsite();

$id = intval($_GET['id']);

if ($_GET['idcgr']) {
    $idc = intval($_GET['idcgr']);
} else {
    $idc = intval($_GET['idc']);
}


$tpl->assignGlobal("menubar", menubar());
include_once("legacy/modules/left_right_col.php");
$leftcol = left_right_col();
$tpl->assignGlobal("leftcol", $leftcol);

if ($id == 0)
    $tpl->assignGlobal("slideshow", slidechild());

$tpl->assignGlobal("menutab", menutab($idc));
$rs_cat = Category::categoryInfo($idc);

$tpl->assignGlobal("pathpage", '<div class="crumb"><div class="grid"><a href="/' . $lang_dir . '"><i class="fa fa-home"></i></a> <i class="fa fa-angle-right"></i> ' . Get_Main_Cat_Name_path($idc) . '</div><div class="c5"></div></div>');


//$root_idc = Category::get_root_category_id($idc);
//echo $root_idc;

$dbProduct = dbMenu::listSubCat($root_idc);
$arr = explode(",", $activeid);

$tpl->assignGlobal("hotline", $SETTING->hotline);
$tpl->assignGlobal("companyemail", $SETTING->companyemail);

$tpl->assignGlobal("idcat", $idc);


$objProduct = new dbProduct();
$product = new clsProduct();
$tags_news = $product->_construct();
$tpl->printToScreen();

class clsProduct {

    private $numberpage = 8;
    private $numberitempage = 15;

    public function _construct() {
        global $DB, $tpl, $dir_path, $idc, $id, $cache_image_path, $rs_cat;


        $tpl->assignGlobal("cat_title", $rs_cat['name']);
        $tpl->assignGlobal("cat_intro", '<div style="padding:10px 0px">' . $rs_cat['intro'] . '</div>');
        $tpl->assignGlobal("cat_content", '<div style="padding:10px 0px">' . $rs_cat['content'] . '</div>');
		
		if ($rs_cat['image'] != "")
			$tpl->assignGlobal("cat_image", $rs_cat['image']);
		else
			$tpl->assignGlobal("cat_image", $rs_cat['imageadv']);
		
		$tpl->assignGlobal("cat_link", $rs_cat['url']);
		$tpl->assignGlobal("cat_description", $rs_cat['description']);
		$tpl->assignGlobal("create_date", date('d/m/Y', $rs_cat['last_modify']));
		
		
        
        if ($id > 0) {
            $tpl->newBlock("proDetail");
            $this->proDetail($id);
        } else {
            $tpl->newBlock("productList");
            $this->productList();
        }
    }

    function productCatList($idc) {
        global $DB, $tpl, $objProduct, $dir_path, $cache_image_path, $SETTING, $langLabel;


        $db1 = dbMenu::listSubCat($idc);
        foreach ($db1 as $rs1) {            if (!is_array($rs1)) { continue; }

            $tpl->newBlock("catItem");
            $tpl->assign("catname", $rs1['name']);
            $tpl->assign("catContentItem", strstrim(strip_tags($rs1['content']), 30));
            $tpl->assign("linkcat", $dir_path . '/' . $rs1['url']);
            if ($rs1['image']) {
                $image = $cache_image_path . resizeimage1(285, 232, $dir_path . '/' . $rs1['image'], true);
                $tpl->assign("catimage", '<img src="' . $image . '" alt="' . $rs1['name'] . '" width="100%">');
            }
        }
    }

    public function productList() {
        global $DB, $idc, $tpl, $objProduct, $dir_path, $cache_image_path, $SETTING, $langLabel, $language, $root_idc;


		$thamlotsan = 0;
		$sql = "SELECT * FROM category WHERE active=1 $language AND parentid= $root_idc AND template_name = 'thamlotsan' ORDER BY thu_tu ASC, name ASC";
		$db1= $DB->query($sql);
		if($rs1 = mysqli_fetch_array($db1)){	
			$thamlotsan =  intval($rs1['id_category']);
			 
		}
		
        $db1 = dbMenu::listSubCat($thamlotsan);
        foreach ($db1 as $rs1) {            if (!is_array($rs1)) { continue; }

            $tpl->newBlock("catItem");
            $tpl->assign("catname", $rs1['name']);
            $tpl->assign("linkcat", $dir_path . '/' . $rs1['url']);

        }
		
		
        $db = $objProduct->itemList($idc, 20);
		
        $i = 0;
        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }

            if ($rs['id_product'] > 0) {
                $i++;
                $tpl->newBlock("product_item");

                $tpl->assign("name", $rs['name']);
                $tpl->assign("intro", $rs['intro']);
                $tpl->assign("ma", $rs['ma']);

                if ($rs['icon'])
                    $tpl->assign("icon", '<div class="' . $rs['icon'] . '">' . $rs['texticon'] . '</div>');


                if (intval($rs['pricekm']) > 0) {
                    $tpl->assign("price", number_format((float)($rs['pricekm'])) . ' đ');
                    $tpl->assign("pricekm", number_format((float)($rs['price'])) . ' đ');

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
                        $tpl->assign("price", number_format((float)($rs['price'])) . ' đ');
                    } else {
                        if (intval($rs['price']) == 0) {
                            
                        } else {
                            $tpl->assign("price", $rs['price']);
                        }
                    }
                }

                if ($rs['image']) 
                    $tpl->assign("image", '<img src="' . $cache_image_path . resizeimage1(400, 400, $dir_path . '/' . $rs['image']) . '" alt="' . $rs['name'] . '" width="100%" >');
				
                
                $tpl->assign("ttkhuyenmai", str_replace("&nbsp;", " ", strstrim(strip_tags($rs['intro']), 20)));
                $tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
                $tpl->assign("linkcart", $dir_path . '/' . $lang_dir . 'addcart/' . $rs['url']);
				
				$product_ver = json_decode($rs['product_ver']);

				foreach ($product_ver as $key => $value) {
					$tpl->newBlock("mausac");
					$tpl->assign("tenphienban", $value->tenphienban);
					$tpl->assign("motaphienban", $value->motaphienban);
				}				
				
				
            }
        }
		
		$sql = "SELECT count(*) as totalProduct FROM product WHERE active=1 AND (id_category IN (" . Category::getParentId($idc) . ") OR groupcat LIKE '%:" . $idc . ":%')";
		
		$db=$DB->query($sql);		
		if ($rs = mysqli_fetch_array($db)){
				$tpl->assignGlobal("totalProduct", $rs["totalProduct"]  );		
				
				$productRemain = intval($rs["totalProduct"]) - 20;
				if ($productRemain > 0 )
					$tpl->assignGlobal("productRemain", $productRemain );
				else
					$tpl->assignGlobal("display_none", "display:none; " );
					
			
		}
		

        //$tpl->assignGlobal("pages", $db['pages']);
    }

    function proDetail($id) {

        global $DB, $idc, $tpl, $objProduct, $dir_path, $cache_image_path, $static, $SETTING, $langLabel, $site_address, $lang_dir;


        $rs = $objProduct->itemDetail($id);

        $tpl->assignGlobal("hotline", $SETTING->hotline);
        if (intval($rs['id_product']) > 0) {
            $tpl->assignGlobal("name", $rs['name']);
            $tpl->assignGlobal("ma", $rs['ma']);
			
			
			$tpl->assignGlobal("hangsx", $this->getManufacture($rs['id_manufacture']));
			

            if (intval($rs['pricekm']) > 0) {
                $tpl->assign("price", number_format((float)($rs['pricekm'])) . ' đ');
                $tpl->assign("pricekm", number_format((float)($rs['price'])) . ' đ');
				
				$tpl->assign("price_number", $rs['pricekm'] );
				
				
            } else {
                if (intval($rs['price']) > 0) {
                    $tpl->assign("price", number_format((float)($rs['price'])) . ' đ');
					$tpl->assign("price_number", $rs['price'] );
                } else {
                    if (intval($rs['price']) == 0) {
                        
                    } else {
                        $tpl->assign("price", $rs['price']);
                    }
                }
            }
			
			$giaphukien = intval($rs['giaphukien']);
			

            $tpl->assign("linkcart", $dir_path . '/' . $lang_dir . 'addcart/' . $rs['url']);
            $tpl->assign("file", $rs['fileurl']);

          
            if ($rs['description'])
                $tpl->assign("description", $rs['description']);
            else
                $tpl->assign("description", strstrim($rs['intro'], 100));
            $tpl->assign("ttkhuyenmai", $rs['ttkhuyenmai']);
            $tpl->assign("intro", $rs['intro']);
            $tpl->assign("content", $rs['content']);
            $tpl->assign("contenttab0", $rs['contenttab0']);
            $tpl->assign("contenttab1", $rs['contenttab1']);




            if ($rs['image']) {
                $tpl->assign("image_first", $cache_image_path . resizeimage1(600, 600, $dir_path . '/' . $rs['image']));
                $tpl->assign("bigimage_first", $rs['image']);
                $tpl->assignGlobal("thumb_image_first", $cache_image_path . resizeimage1(100, 100, $dir_path . '/' . $rs['image']));
                $tpl->assign("image", $site_address . $cache_image_path . resizeimage1(600, 600, $dir_path . '/' . $rs['image']));
            }


            $tpl->assignGlobal("id_product", $rs['id_product']);
			
			
            $tpl->assign("proDetail.thumbimage", $this->sliderImage($rs['id_product']));
			
			
            //$this->sliderImage($rs['id_product']);

            $attr = $objProduct->getAttrDetail($rs['id_category'], $rs['attr']);
            foreach ($attr as $key => $val) {
                $tpl->newBlock("attr");
                $tpl->assign("attrname", $key);
                $tpl->assign("attrvalue", $val);
                $i++;
            }

            $tpl->assignGlobal("link_detail_pro", $site_address . $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
			
			$product_ver = json_decode($rs['product_ver']);

			foreach ($product_ver as $key => $value) {
				$tpl->newBlock("phienban");
				$tpl->assign("tenphienban", $value->tenphienban);
				$tpl->assign("giaphienban", number_format((float)($value->giaphienban)));
				$tpl->assign("giaphienbanlotcop", number_format((float)($value->giaphienban + $giaphukien )));
				
				$tpl->assign("gianumber", $value->giaphienban);
				$tpl->assign("motaphienban", $value->motaphienban);
			}			
			
			$sql = "SELECT * FROM faq WHERE active=1 AND (id_category IN (".Category::getParentId($idc).") OR groupcat LIKE '%:".$idc.":%') ORDER BY thu_tu DESC, id_faq ASC";

			$dbf = $DB->query($sql);

			while($rsf = mysqli_fetch_array($dbf)){
				$tpl->newBlock("faq_item");
				$tpl->assign("id_faq", $rsf['id_faq']);
				$tpl->assign("name", $rsf['name']);
				$tpl->assign("content", $rsf['content']);
			}

			 

            $this->sp_cungdanhmuc($rs['id_category']);

            $this->sp_lienquan($rs['spcungloai']);


            include_once("legacy/modules/comment.php");
            $tpl->assignGlobal("comments", getComments($id, 'product', 'id_product'));
			
        }
    }

    private function sp_cungdanhmuc($id_category) {
        global $DB, $id, $tpl, $objProduct, $dir_path, $cache_image_path, $langLabel;

        $db = $objProduct->other_product($id, $id_category);
        $i = 0;
        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }

            if ($rs['id_product'] > 0) {
                $i++;

                $tpl->newBlock("other_products");

                $tpl->assign("name", $rs['name']);
                $tpl->assign("ma", $rs['ma']);
                if ($rs['icon'])
                    $tpl->assign("icon", '<div class="' . $rs['icon'] . '">' . $rs['texticon'] . '</div>');


                if (intval($rs['pricekm']) > 0) {
                    $tpl->assign("price", number_format((float)($rs['pricekm'])) . ' đ');
                    $tpl->assign("pricekm", number_format((float)($rs['price'])) . ' đ');
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
                        $tpl->assign("price", number_format((float)($rs['price'])) . ' đ');
                    } else {
                        $tpl->assign("price", $rs['price']);
                    }
                }
                $tpl->assign("ma", $rs['ma']);
                $tpl->assign("ttkhuyenmai", $rs['ttkhuyenmai']);
                if ($rs['image'])
                    $tpl->assign("image", '<img src="' . $cache_image_path . cropimage(400, 400, $dir_path . '/' . $rs['image']) . '" alt="' . $rs['name'] . '" width="100%" >');
                
                $tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
            }
        }
    }

    private function sp_lienquan($listid) {
        global $DB, $idc, $tpl, $objProduct, $dir_path, $cache_image_path;

        if (strlen($listid) != "")
            $tpl->newBlock("sp_lienquan");

        $db = $objProduct->splienquan($listid);
        $i = 0;
        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }

            if ($rs['id_product'] > 0) {
                $tpl->newBlock("sp_lienquan_item");
                $tpl->assign("ma", $rs['ma']);
                $tpl->assign("name", $rs['name']);
                if ($rs['image'])
                    $tpl->assign("image", '<img src="' . $cache_image_path . cropimage(600, 400, $dir_path . '/' . $rs['image']) . '" alt="' . $rs['name'] . '" width="100%">');

                if ($rs['icon'])
                    $tpl->assign("icon", '<div class="' . $rs['icon'] . '">' . $rs['texticon'] . '</div>');
                $tpl->assign("ttkhuyenmai", strstrim(strip_tags($rs['ttkhuyenmai']), 10));

                if (intval($rs['pricekm']) > 0) {
                    $tpl->assign("price", number_format((float)($rs['pricekm'])) . ' đ');
                    $tpl->assign("pricekm", number_format((float)($rs['price'])) . ' đ');
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
                        $tpl->assign("price", number_format((float)($rs['price'])) . ' đ');
                    } else {
                        $tpl->assign("price", $rs['price']);
                    }
                }

                $tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
            }
        }
    }
	
    private function getManufacture($id) {
        global $DB;
		
		$id = intval($id);
		
        $sql = "SELECT * FROM product_manufacture WHERE id = $id";
        $db = $DB->query($sql);
        $str = "";
        if($rs = mysqli_fetch_array($db)) {
			$str = $rs['name'];
            
        }

        return $str;


    }	


    private function sliderImage($id_product) {
        global $DB, $tpl, $dir_path, $cache_image_path, $site_address;
        $id_value = $id_product;
        include_once (__DIR__ . '/manager/dataprovider/db_sys_image.php');
        $sysImage = new dbSysImage();
        $db = $sysImage->getList('', "product", "id_product", $id_value, "");
        $thumbimage = '';
        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }

            if ($rs['image']) {
                $tpl->newBlock("slider_image");

                $tpl->assign("image", $cache_image_path . resizeimage1(600, 600, $dir_path . '/' . $rs['image']));
                //  $tpl->assign("thumb_image", $cache_image_path . resizeimage1(98, 58, $dir_path . '/' .  $rs['image']));
                $tpl->assign("bigimage", $site_address . '/' . $rs['image']);
                //$tpl->assign("image1",$cache_image_path.resizeimage(477,358,$dir_path.'/'.$rs['image']))	;
                $thumbimage .= '<div class="swiper-slide"><img src="' . $cache_image_path . cropimage(60 * 2, 53 * 2, $dir_path . '/' . $rs['image']) . '" width="100%"/></div>';
            }
        }
        return $thumbimage;
    }

}

?>