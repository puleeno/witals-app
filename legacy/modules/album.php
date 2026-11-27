<?php

//Develop Team: Binh Minh group
//binhminhgroup@gmail.com


defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

$id = intval($_GET['id']);
$idc = intval($_GET['idc']);
$template_name = $cateinfo['template_name'];
if ($template_name)
    $tpl = new TemplatePower($CONFIG['template_dir'] . "/$template_name.htm");
else
    $tpl = new TemplatePower($CONFIG['template_dir'] . "/album.htm");
$tpl->prepare();

$tpl->assignGlobal("dir_path", $dir_path);
$tpl->assignGlobal("site_address", $site_address);


langsite();

$tpl->assignGlobal("catname", strip_tags($rs_cat['name']));
$tpl->assignGlobal("catcontent", $rs_cat['content']);
$tpl->assignGlobal("catlink", $dir_path . '/' . $rs_cat['url']);

$tpl->assignGlobal("menubar", menubar());
include_once("legacy/modules/left_right_col.php");
$leftcol = left_right_col();
$tpl->assignGlobal("leftcol", $leftcol);
/*
include_once("legacy/modules/submenu.php");
$tpl->assignGlobal("submenu", submenu());
*/
$tpl->assignGlobal("slideshow", slidechild());


$tpl->assignGlobal("pathpage", '<div class="crumb"> <div class="grid"> <a href="/' . $lang_dir . '"> '. $langLabel['_home'] .' </a> <i class="fa fa-angle-right"></i> ' . Get_Main_Cat_Name_path($idc) . '</div></div>');


include_once("legacy/modules/db.provider/db.album.php");
$objAlbum = new dbAlbum();
$album = new clsAlbum();



$tpl->printToScreen();

class clsAlbum {

    public function __construct() {
        global $DB, $tpl, $dir_path, $idc, $id, $cache_image_path;

        if (!$id) {
            if (Category::checkChildCat($idc))
                $this->listCat($idc);
            else
                $this->listItem($idc);
        } else
            $this->itemDetail();
    }

    public function listCat($idc) {
        global $DB, $tpl, $cache_image_path, $idc, $dir_path, $SETTING, $objAlbum;

        $tpl->newBlock("listCat");

        $db = Category::getChildCat($idc);

        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }

            $tpl->newBlock("cat_item_list");
            $tpl->assign("name", strip_tags($rs['name']));
            $tpl->assign("content", $rs['content']);

            $tpl->assign("link", $dir_path . '/' . $rs['url']);
            if ($rs['image'])
                $tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(600, 400, $dir_path . '/' . $rs['image']) . '" alt="' . $rs['name'] . '" width="100%" />');

            $db1 = $objAlbum->itemList($rs['id_category'], 16);
            foreach ($db1 as $rs1) {
                if ($rs1['id_category'] > 0) {
                    $tpl->newBlock("catlistAlbum");
                    $tpl->assign("name", $rs1['name']);
                    if ($rs1['image'])
                        $tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(600, 400, $dir_path . '/' . $rs1['image']) . '" alt="' . $rs1['name'] . '" width="100%" />');
                    $tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs1['id_category']) . $rs1['url']);
                }
            }
        }
    }

    public function listItem($id_cat) {

        global $DB, $tpl, $cache_image_path, $objAlbum, $dir_path, $rs_cat, $SETTING;

        $db = $objAlbum->itemList($id_cat, 9);

        $tpl->newBlock("albums");
        $tpl->assign("catname", strip_tags($rs_cat['name']));

        if (strlen($rs_cat['content']) > 7)
            $tpl->assign("catcontent", $rs_cat['content']);

        $i = 0;

        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }

            if ($rs['id_album'] > 0) {

                $tpl->newBlock("listalbum");

                $i++;
                $tpl->assign(array(
                    'name' => $rs['name'],
                    'intro' => strstrim(strip_tags($rs['intro']), 30),
                    'link_detail' => $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']
                ));

                if ($rs['image'])
                    $tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(600, 400, $dir_path . '/' . $rs['image']) . '" alt="' . $rs['name'] . '" width="100%" />');
            }
        }


        $tpl->assignGlobal("pages", $db['pages']);
    }

    public function itemDetail() {

        global $DB, $objAlbum, $id, $idc, $tpl, $dir_path, $cache_image_path;

        $tpl->newBlock("detail");


        $rs = $objAlbum->itemDetail($id);

        if ($rs) {
            $tpl->assign(array(
                'name' => $rs['name'],
                'intro' => $rs['intro'],
                'content' => $rs['content'],
            ));



            if ($rs['image']) {
                $tpl->assign("image_first", '<img src="' . $cache_image_path . cropimage(730, 510, $dir_path . '/' . $rs['image']) . '" alt="' . $rs['name'] . '" width="100%">');
                $tpl->assign("thumb_image_first", '<img src="' . $cache_image_path . cropimage(106, 75, $dir_path . '/' . $rs['image']) . '" alt="' . $rs['name'] . '" class="gallery-thumbs-img" width="100%">');
            }
            $this->sliderImage($rs['image_list']);


            $newsold = $objAlbum->item_other($idc, $id);
            if (count($newsold) > 0) {

                $i = 0;
                foreach ($newsold as $rse) {                    if (!is_array($rse)) { continue; }

                    if ($rse['id_album'] > 0) {
                        $tpl->newBlock("other_item");

                        $tpl->assign(array(
                            'name' => $rse['name'],
                            'link_detail' => $dir_path . '/' . url_process::getUrlCategory($rse['id_category']) . $rse['url']
                        ));

                        if ($rse['image'])
                            $tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(600, 400, $dir_path . '/' . $rse['image']) . '" alt="' . $rse['name'] . '" width="100%" />');

                        $i++;
                    }
                }
            }
        }
    }

    private function sliderImage($image_list) {
        global $DB, $tpl, $dir_path, $cache_image_path;

        $images = json_decode($image_list);

        foreach ($images as $rs) {
            $tpl->newBlock("slider_image");
            $tpl->assign(array(
                'name' => $rs->image_name,
                'thu_tu' => $rs->image_thu_tu,
                'image_desc' => $rs->image_desc
            ));

            $tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(400, 320, $dir_path . '/' . $rs->image_path) . '" alt="' . $rs->image_name . '" title="' . $rs->image_name . '" width="100%" />');

            $tpl->assign("bigimage_url", $cache_image_path . resizeimage(1200, 1200, $dir_path . '/' . $rs->image_path));
        }

        $tpl->assignGlobal("item_paged", $db['pages']);
    }

}

?>