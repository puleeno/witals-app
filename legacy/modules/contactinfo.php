<?php
//Nguyen Van Binh
//suongmumc@gmail.com
defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );
$tpl = new TemplatePower($CONFIG['template_dir']."/contactinfo.htm");
$tpl->prepare();
$tpl->assignGlobal("dir_path",$dir_path);
langsite();
	$idc = intval($_GET['idc']);
	$catinfo = Category::categoryInfo($idc);
	$tpl->assignGlobal("catname",$catinfo['name']);
	$tpl->assignGlobal("catcontent",$catinfo['content']);
	if($catinfo['image']){
		$tpl->assignGlobal("catimage",'<img src="'.$dir_path.'/image.php?w=679&src='.$catinfo['image'].'" alt="'.$catinfo['name'].'" />');
	}

$sql = "SELECT * FROM contactinfo WHERE active=1 ";
$db = $DB->query($sql);
if($rs = mysqli_fetch_array($db)){
	$tpl->assign("contentcontact",$rs['content']);
	$tpl->assign("title",$rs['title']);
	$tpl->assign("address",$rs['address']);
	$tpl->assignGlobal("latitude",$rs['latitude']);
	$tpl->assignGlobal("longitude",$rs['longitude']);
}
$tpl->printToScreen();
?>