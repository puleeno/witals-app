<?php

//Develop Team: Binh Minh group
//binhminhgroup@gmail.com
//error_reporting(E_ERROR);

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

$idp = intval($_GET['idp']);
$idc = intval($_GET['idc']);


$tpl = new TemplatePower($CONFIG['template_dir'] . "/service_book.htm");
	
$tpl->prepare();

$tpl->assignGlobal("dir_path", $dir_path);
$tpl->assignGlobal("site_address", $site_address);

$tpl->assignGlobal("slideshow", slidechild());

$tpl->assignGlobal("pathpage", '<div class="crumb"> <div class="grid"> <a href="/' . $lang_dir . '"> ' . $langLabel['_home'] . ' </a> <i class="fa fa-angle-right"></i>  Đặt hàng </div></div>');


include_once("legacy/modules/left_right_col.php");
$leftcol = left_right_col();
$tpl->assignGlobal("leftcol", $leftcol);



langsite();
/*
$db = dbProduct::allProduct();
foreach ($db as $rs) {    if (!is_array($rs)) { continue; }

    $tpl->newBlock("tensanpham");
    $tpl->assign("id_product", $rs['id_product']);
    $tpl->assign("name", $rs['name']);
	if ($idp == $rs['id_product']) 
		$tpl->assign("selected", "selected");
}
*/

$contact_order = dbStatic::getInWhere("contact");
foreach($contact_order as $context){    if (!is_array($context)) { continue; }

	$tpl->assignGlobal("contact_name", $context['name']);
	$tpl->assignGlobal("contact_content", $context['content']);	
}


$tpl->printToScreen();





?>