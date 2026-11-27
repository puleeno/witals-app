<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

$id = intval($_GET['id']);
$idc = intval($_GET['idc']);
$template_name = $cateinfo['template_name'];
if ($template_name)
    $tpl = new TemplatePower($CONFIG['template_dir'] . "/$template_name.htm");
else
    $tpl = new TemplatePower($CONFIG['template_dir'] . "/chungkhoantructuyen.htm");
$tpl->prepare();

langsite();

$tpl->assignGlobal("dir_path", $dir_path);
$tpl->assignGlobal("site_address", $site_address);
$tpl->assignGlobal("slideshow", slidechild());

$tpl->assignGlobal("pathpage", '<div class="grid"><div class="crumb"><a href="/' . $lang_dir . '"><i class="fa fa-home"></i></a> <i class="fa fa-angle-right"></i> ' . Get_Main_Cat_Name_path($idc) . '</div><div class="c5"></div></div>');


$tpl->assignGlobal("menutab", menutab($root_idc));

/*
$date1 = time();
$date2 = time() - (60*60*24*30*3) ;


echo  date("Ymd",$date1);
echo  date("Ymd",$date2);
*/


$tpl->assignGlobal("catname", $rs_cat['name']);
$tpl->assignGlobal("catlink", $dir_path . '/' . $rs_cat['url']);

if (strlen($rs_cat['intro']) > 7)
    $tpl->assignGlobal("catintro", '<div style="padding:5px 0px">' . $rs_cat['intro'] . '</div>');
if (strlen($rs_cat['content']) > 7)
    $tpl->assignGlobal("catcontent", '<div style="padding:5px 0px">' . $rs_cat['content'] . '</div>');


$stock = clean_value($_GET['stock']);

if ($stock == "hii"){
	$tpl->assignGlobal("hii", "tab-active");
	$tpl->assignGlobal("tab_aaa", "display:none");
	$tpl->assignGlobal("tab_hii", "display:block");
	
}else{
	$tpl->assignGlobal("aaa", "tab-active");
	$tpl->assignGlobal("tab_aaa", "display:block");
	$tpl->assignGlobal("tab_hii", "display:none");
}


$tpl->printToScreen();
?>
