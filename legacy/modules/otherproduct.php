<?php
error_reporting(E_ERROR);
defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');
$tpl = new TemplatePower($CONFIG['template_dir'] . "/otherproduct.htm");
$tpl->prepare();
$idc = intval($_GET['idc']);
$id = intval($_GET['id']);
$tpl->assignGlobal("dir_path", $dir_path);
include("legacy/modules/db.provider/db.product.php");
$objProduct = new dbProduct;
langsite();
sp_cungloai();

 function sp_cungloai() {
        global $DB, $idc, $id, $tpl, $objProduct, $dir_path, $cache_image_path, $langLabel;
        $db = $objProduct->other_product($name, $id);
        $i = 0;
        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }

            if ($rs['id_product'] > 0) {
                $i++;
                $tpl->newBlock("other_products");
                $tpl->assign("name", $rs['name']);
                if ($rs['icon']) {
                    $tpl->assign("km", '<div class="' . $rs['icon'] . '">' . $rs['texticon'] . '</div>');
                }
                if (intval($rs['pricekm']) > 0) {
                    $tpl->assign("price", $langLabel['_giakm'] . number_format((float)($rs['pricekm'])) . ' đ');
                    $tpl->assign("pricekm", $langLabel['_giasp'] . number_format((float)($rs['price'])) . ' đ');
                } else {
                    if (intval($rs['price']) > 0) {
                        $tpl->assign("price", $langLabel['_giasp'] . number_format((float)($rs['price'])) . ' đ');
                    } else {
                        $tpl->assign("price", $rs['price']);
                    }
                }
				$tpl->assign("ma", $rs['ma']);
                $tpl->assign("ttkhuyenmai",$rs['ttkhuyenmai']);
				if($rs['image']){
					$tpl->assign("image",'<img src="'.$cache_image_path.resizeimagepng(320,258,$dir_path.'/'.$rs['image']).'" alt="'.$rs['name'].'" width="100%" class="" >')	;

				}
                $tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
                
            }			
        }
		$linerow = 3;
        $cell_row = ceil($i / $linerow);
        $cell_add = ($linerow * $cell_row) - $i;
        for ($j = 0; $j < $cell_add; $j++) $str_cell.="<div class='cell-1-$linerow'></div>";
        $tpl->assignGlobal("str_cell", $str_cell);
   }
	
$tpl->printToScreen();
?>