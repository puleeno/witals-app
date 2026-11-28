<?php

//Nguyen Van Binh
//suongmumc@gmail.com

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

function spnoibat($idc, $catname) {

    global $DB, $dir_path, $cache_image_path, $CONFIG, $logo, $clsUrl;
    $tpl1 = new TemplatePower("templates/spnoibat.htm");
    $tpl1->prepare();
    $idc = intval($idc);
    $tpl1->newBlock('spnoibat');
    $tpl1->assign("id", $idc);
    $tpl1->assign("catname", $catname);
    proCat($idc, $tpl1);


    return $tpl1->getOutputContent();
}

function proCat($idc, $tpl) {
    global $DB, $dir_path, $cache_image_path, $SETTING, $clsUrl;


    $db = (new dbProduct)->itemList($idc, 15);
    $i = 0;
    foreach ($db as $rs) {        if (!is_array($rs)) { continue; }

        if ($rs['id_product'] > 0) {
            $i++;
            $tpl->newBlock("products");
            if ($i == 1)
                $tpl->assign("marginleft0", "marginleft0");
            $tpl->assign("name", $rs['name']);
            if ($rs['km'] > 0) {
                $tpl->assign("km", '<div class="icon-off">' . $rs['km'] . '%</div>');
            }

            if (intval($rs['pricekm']) > 0) {
                $tpl->assign("price", "Giá KM: " . number_format((float)($rs['pricekm'])) . ' đ');
                $tpl->assign("pricekm", "Giá: " . number_format((float)($rs['price'])) . ' đ');
            } else {
                if (intval($rs['price']) > 0) {
                    $tpl->assign("price", "Giá: " . number_format((float)($rs['price'])) . ' đ');
                } else {
                    if (intval($rs['price']) == 0) {
                        
                    } else {
                        $tpl->assign("price", $rs['price']);
                    }
                }
            }


            if ($rs['image']) {
                $tpl->assign("image", '<img src="' . $cache_image_path . resizeimage1(220, 150, $dir_path . '/' . $rs['image']) . '" alt="' . $rs['name'] . '" width="100%">');
            }
            if ($rs['icon']) {
                $tpl->assign("icon", '<div class="' . $rs['icon'] . '">' . $rs['texticon'] . '</div>');
            }
            $tpl->assign("ttkhuyenmai", strstrim(strip_tags($rs['ttkhuyenmai']), 10));

            $tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
            if ($i >= 5) {
                $i = 0;
                $tpl->assign("br", '<div class="c20"></div>');
            }
        }
    }
}

?>