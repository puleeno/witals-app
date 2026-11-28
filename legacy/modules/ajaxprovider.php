

<?php

//Develop Team: Binh Minh group
//binhminhgroup@gmail.com


defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

switch ($_GET['code']) {
    

    
    case "getDistrict":
        $id = $_GET['id'];
        $selectedid = $_GET['selectedid'];
        echo locationProvider::bindDistrict($id, $selectedid);
        break;    

    case "loadMoreProduct":
        $pagenum = $_GET['pagenum'];
        $idcat = $_GET['idcat'];
		
		loadMoreProduct($idcat,$pagenum * 20 );
		
        break;     
    
}

function loadMoreProduct($idc, $limFrom=0) {
		global $DB, $CONFIG, $cache_image_path;
		
		$limFrom = intval($limFrom);
		$limTo = 20;
		
		$tpl = new TemplatePower($CONFIG['template_dir'] . "/ajaxprovider.htm");
		$tpl->prepare();
		
		
		$tpl->newBlock("productList");
        
		$sql = "SELECT * FROM product WHERE active=1 AND (id_category IN (" . Category::getParentId($idc) . ") OR groupcat LIKE '%:" . $idc . ":%') LIMIT $limFrom, $limTo";
		
		
		$db=$DB->query($sql);
        
        while ($rs = mysqli_fetch_array($db)){

 
                $tpl->newBlock("product_item");

                $tpl->assign("name", $rs['name']);
                $tpl->assign("intro", $rs['intro']);
                $tpl->assign("ma", $rs['ma']);
                $tpl->assign("ttkhuyenmai", $rs['ttkhuyenmai']);

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
                    if ($discount > 0)
                        $tpl->assign("discount-tags", "<div class='discount-tags'>-$discount%</div>");
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

		$tpl->printToScreen();
		
		

}

class locationProvider {
	
    public static function bindDistrict($provinceid, $selectedid) {
			global $DB;
			
			$provinceid = intval($provinceid);
			$sql = "SELECT * FROM vn_district WHERE provinceid = $provinceid";
			$db = $DB->query($sql);
			
			$result_str = '<option value="">----Chọn quận huyện----</option>';
			
			while($rs = mysqli_fetch_array($db) ) {
				if ($selectedid == $rs['districtid'])
					$result_str.= '<option value="' . $rs['districtid'] . '" selected >' . $rs['name'] . '</option>';
				else
					$result_str.= '<option value="' . $rs['districtid'] . '">' . $rs['name'] . '</option>';
				
			}
			
			return  $result_str;

    }
}


?>