<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');



$id = intval($_GET['id']);

$idc = intval($_GET['idc']);

$template_name = $cateinfo['template_name'];

if ($template_name)
    $tpl = new TemplatePower($CONFIG['template_dir'] . "/$template_name.htm");
else
    $tpl = new TemplatePower($CONFIG['template_dir'] . "/service.htm");

$tpl->prepare();



$tpl->assignGlobal("dir_path", $dir_path);

$tpl->assignGlobal("site_address", $site_address);
$root_cat = Category::categoryInfo($idc);
$catname = $root_cat['name'];
$tpl->assignGlobal("slideshow", slidechild());

include_once("legacy/modules/left_right_col.php");
$leftcol = left_right_col();
$tpl->assignGlobal("leftcol", $leftcol);

langsite();



$tpl->assignGlobal("pathpage", '<div class="crumb"><div class="grid"><a href="/' . $lang_dir . '"> ' . $langLabel['_home'] . ' </a> <i class="fa fa-angle-right"></i> ' . Get_Main_Cat_Name_path($idc) . '</div><div class="c5"></div></div>');



$objService = new dbService();



$service = new clsService();

class clsService {

    private $numberpage = 8;
    private $numberitempage = 15;

    public function __construct() {

        global $DB, $tpl, $dir_path, $idc, $id, $cache_image_path, $rs_cat, $root_idc;



        if (!$id) {

            if (Category::checkChildCat($idc)) {



                $tpl->assignGlobal("catname", $rs_cat['name']);

                $tpl->assignGlobal("catlink", $dir_path . '/' . $rs_cat['url']);



                if (strlen($rs_cat['intro']) > 7)
                    $tpl->assignGlobal("catintro", '<div style="padding:5px 0px">' . $rs_cat['intro'] . '</div>');

                if (strlen($rs_cat['content']) > 7)
                    $tpl->assignGlobal("catcontent", '<div style="padding:5px 0px">' . $rs_cat['content'] . '</div>');



                $this->catList();
            } else {

                $tpl->assignGlobal("menutab", menutab($root_idc));

                $this->itemList();
            }
        } else {

            $tpl->assignGlobal("menutab", menutab($root_idc));

            $this->itemDetail();
        }
    }

    public function catList() {



        global $DB, $tpl, $cache_image_path, $objService, $dir_path, $idc, $SETTING, $rs_cat;



        $db = Category::getChildCat($idc);

        $tpl->newBlock("catList");



        $i = 0;

        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }




            $i++;



            $tpl->newBlock("catItem");

            $tpl->assign("name", $rs['name']);



            $tpl->assign("content", $rs['content']);

            $tpl->assign("intro", $rs['intro']);



            $tpl->assign("link", $dir_path . '/' . $rs['url']);



            if ($rs['image'])
                $tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(600, 400, $dir_path . '/' . $rs['image'], true) . '" alt="' . $rs['name'] . '" width="100%"/>');



            if ($i % 2 == 0)
                $tpl->newBlock("imgleft");
            else
                $tpl->newBlock("imgright");



            $tpl->assign(array(
                'name' => $rs['name'],
                'content' => $rs['content'],
                'link' => '/' . $rs['url'],
            ));

            if ($rs['image'])
                $tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(600, 400, $dir_path . '/' . $rs['image'], true) . '" alt="' . $rs['name'] . '" width="100%"/>');



            $dbItem = $objService->itemList($rs['id_category']);



            $sublink = "";

            foreach ($dbItem as $rsItem) {

                if ($rsItem['id_service'] > 0) {

                    $sublink .= "<a href='" . $dir_path . '/' . url_process::getUrlCategory($rsItem['id_category']) . $rsItem['url'] . "'> $rsItem[name] |</a>";
                }
            }

            $tpl->assign("sublink", $sublink);
        }
    }

    public function itemList() {



        global $DB, $tpl, $cache_image_path, $objService, $dir_path, $idc, $SETTING, $rs_cat, $template_name;



        $db = $objService->itemList($idc);



        $tpl->newBlock("itemList");

        $tpl->assignGlobal("catname", $rs_cat['name']);
        $tpl->assignGlobal("catlink", $dir_path . '/' . $rs_cat['url']);

        if (strlen($rs_cat['intro']) > 7)
            $tpl->assignGlobal("catintro", '<div style="padding:5px 0px">' . $rs_cat['intro'] . '</div>');

        if (strlen($rs_cat['content']) > 7)
            $tpl->assignGlobal("catcontent", '<div style="padding:5px 0px">' . $rs_cat['content'] . '</div>');

        $i = 0;



        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }

            if ($rs['id_service'] > 0) {
                $i++;
                $tpl->newBlock("serviceItem");

                $tpl->assign(array(
                    'name' => $rs['name'],
                    'intro' => ($rs['intro']),
                    'content' => ($rs['content']),
                    'link_detail' => $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']
                ));



                if ($rs['image'])
                    $tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(600, 400, $dir_path . '/' . $rs['image'], true) . '" alt="' . $rs['name'] . '" width="100%"/>');



                if ($rs['imageadv'])
                    $tpl->assign("imageadv", '<img  src="' . $rs['imageadv'] . '" alt="' . $rs['name'] . '" />');
            }
        }

        $tpl->assignGlobal("pages", $db['pages']);
    }

    public function itemDetail() {



        global $DB, $objService, $id, $idc, $tpl, $dir_path, $cache_image_path, $site_address, $rs_cat;



        $tpl->newBlock("itemDetail");

        $rs = $objService->itemDetail($id);



        if ($rs) {

            $tpl->assign(array(
                'name' => $rs['name'],
                'intro' => $rs['intro'],
                'content' => $rs['content'],
            ));



            $tpl->assign("link_detail", $site_address . "/" . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
        }



        $sql = "SELECT * FROM service WHERE active=1 AND id_category = $idc AND id_service <> $id ORDER BY thu_tu";

        $db = $DB->query($sql);



        if (mysqli_num_rows($db) > 0)
            $tpl->newBlock("otherList");



        while ($rs = mysqli_fetch_array($db)) {



            $tpl->newBlock("otherItem");



            $tpl->assign(array(
                'name' => $rs['name'],
                'content' => strstrim(strip_tags($rs['content']), 103),
                'link_detail' => $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']
            ));
        }
    }

}

$tpl->printToScreen();
?>