<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');


$template_name = $cateinfo['template_name'];

if ($template_name)
    $tpl = new TemplatePower($CONFIG['template_dir'] . "/$template_name.htm");
else
    $tpl = new TemplatePower($CONFIG['template_dir'] . "/sitemap.htm");


$tpl->prepare();
$id = intval($_GET['id']);
$idc = intval($_GET['idc']);
langsite();
$tpl->assignGlobal("pathpage", Get_Main_Cat_Name_path($idc));
$tpl->assignGlobal("dir_path", $dir_path);

//Get root
$root_idc = Category::get_root_category_id($idc);
$root_cat = Category::categoryInfo($root_idc);
if ($root_cat['image']) {
    $tpl->assign("root_cat_image", '<img  src="'.$cache_image_path.  cropimage(690, 165, $dir_path . '/' . $root_cat['image']) . '" alt="' . $root_cat['name'] . '" width="690" />');
}

include_once('plugins/sitemap/sitemap.php');
$tpl->assign("sitemap",sitemap())  ;
$tpl->printToScreen();


?>