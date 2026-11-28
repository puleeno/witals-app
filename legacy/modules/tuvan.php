<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

$id = intval($_GET['id']);
$idc = intval($_GET['idc']);
$template_name = $cateinfo['template_name'];
if ($template_name)
    $tpl = new TemplatePower($CONFIG['template_dir'] . "/$template_name.htm");
else
    $tpl = new TemplatePower($CONFIG['template_dir'] . "/tuvan.htm");
$tpl->prepare();

$tpl->assignGlobal("dir_path", $dir_path);
$tpl->assignGlobal("site_address", $site_address);

$rs_cat = Category::categoryInfo($idc);
langsite();

$tpl->assignGlobal("menubar", menubar());

include_once("legacy/modules/left_right_col.php");
$leftcol = left_right_col();
$tpl->assignGlobal("leftcol", $leftcol);

$tpl->assignGlobal("slideshow", slidechild());

$tpl->assignGlobal("menutab", menutab($idc));

$rs_cat = Category::categoryInfo($idc);
langsite();

$tpl->assignGlobal("pathpage", '<div class="grid"><div class="crumb"><a href="/' . $lang_dir . '"><i class="fa fa-home"></i></a> <i class="fa fa-angle-right"></i> ' . Get_Main_Cat_Name_path($idc) . '</div><div class="c5"></div></div>');

$tuvan = new dbTuVan();
$catinfo = Category::categoryInfo($idc);
$tpl->assignGlobal("catname", $catinfo['name']);
$tpl->assignGlobal("catlink", $dir_path . '/' . $catinfo['url']);
/*
  if ($catinfo['image']) {
  $tpl->assignGlobal("image", '<img  src="' . $cache_image_path . resizeimage(1200, 600, $dir_path . '/' . $catinfo['image']) . '" alt="' . $catinfo['name'] . '" style="max-width:100%" />');
  }
 */

if (strlen($catinfo['content']) > 7) {
    $tpl->assignGlobal("catcontent", '<div style="padding:10px 0px">' . $catinfo['content'] . '</div>');
}
if (!$id) {
    itemCatList();
} else {
   itemDetail();
}

$tpl->printToScreen();


function itemCatList() {

    global $DB, $tpl, $cache_image_path, $tuvan, $dir_path, $idc, $SETTING,  $rs_cat, $template_name;

    $dbnews = $tuvan->itemList($idc, $SETTING->newsinpage);

    $tpl->newBlock("newsCat");
   
    $i = 0;
    $ids = "0";
	$n=0;
    foreach ($dbnews as $rs) {        if (!is_array($rs)) { continue; }

        if ($rs['id_tuvan'] > 0) {
            $ids .= ",$rs[id_tuvan]";
           
 			$i++;
			
				$tpl->newBlock("news_list");
			
			$tpl->assign(array(
				'name' => $rs['name'],
				'content' => ($rs['content']),
				'link_detail' => $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']
			));
			if ($rs['showdate'] == 1)
				$tpl->assign("createdate", '<div class="createdate"><i class="fa fa-calendar" aria-hidden="true"></i> ' . date('d/m/Y', $rs['ngay_dang']) . '</div>');
		
			$tpl->assign("intro",strstrim(strip_tags($rs['intro']),25));
			 if ($rs['image'])
				$tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(431, 269, $dir_path . '/' . $rs['image']) . '" alt="' . $rs['name'] . '" class="image-news" width="100%"/>');
				
				
		
        }
    }
    $tpl->assignGlobal("pages", $dbnews['pages']);
}

function itemDetail() {

    global $DB, $tuvan, $id, $idc, $tpl, $dir_path, $cache_image_path, $clsUrl, $rs_cat;

    $tpl->newBlock("newsDetail");
    $tpl->assignGlobal("cat_name", $rs_cat['name']);
    $rs = $tuvan->newsDetail($id);
    if ($rs) {
        $tpl->assign(array(
            'name' => $rs['name'],
            'intro' => $rs['intro'],
            'content' => $rs['content'],
        ));

        if ($rs['showdate'] == 1)
				$tpl->assign("createdate", '<div class="createdate"><i class="fa fa-calendar" aria-hidden="true"></i> ' . date('d/m/Y', $rs['ngay_dang']) . '</div>');

         $tpl->assign("link_detail", $dir_path . "/" . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
		if ($rs['image'])
				$tpl->assign("image",  $cache_image_path . resizeimage(800, 600, $dir_path . '/' . $rs['image']));
		if($rs['description'])		
			$tpl->assign("description",strip_tags($rs['description']));
		else
			$tpl->assign("description",strip_tags($rs['intro']));
		$tpl->assign("create_date",date('c', $rs['ngay_dang']));
		
	
		
		$newsold = $tuvan->otherItem();
		foreach ($newsold as $rse) {		    if (!is_array($rse)) { continue; }

			if ($rse['id_tuvan'] > 0) {
				$tpl->newBlock("lstOther");
				$tpl->assign("name", $rse['name']);
				$tpl->assign("intro", $rse['intro']);
				$tpl->assign("link_detail", $dir_path . "/" . url_process::getUrlCategory($rse['id_category']) . $rse['url']);
				if ($rse['image'])
				$tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(420, 270, $dir_path . '/' . $rse['image'], true) . '" alt="' . $rse['name'] . '" class="image-news" width="100%"/>');
				if($rse['showdate']==1){
					$tpl->assign("createdate", '<span class="createdate">('.date('d/m/Y' ,$rse['ngay_dang']).')</span>');
				}
			}
		}
		
		
		
    }
}

function checkChildCat($idc) {
    global $DB;
    $idc = intval($idc);

    $sql = "SELECT * FROM category WHERE parentid = $idc";
    $db = $DB->query($sql);
    if (mysqli_num_rows($db) > 0)
        return true;
    else
        return false;
}

?>