<?php

//Develop Team: Binh Minh group
//binhminhgroup@gmail.com
//error_reporting(E_ERROR);

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');
/*
if (intval($_SESSION['order_id']) <=0 ){
		header("HTTP/1.1 301 Moved Permanently"); 
		header("Location: http://larado.pl");
}
*/
error_reporting(E_ERROR);


$tpl = new TemplatePower("templates/cart_success.htm");
$tpl->prepare();
langsite();
$tpl->assignGlobal("pathpage", Get_Main_Cat_Name_path($idc));
$tpl->assignGlobal("dir_path", $dir_path);
$tpl->assignGlobal("site_address", $site_address);

$tpl->assignGlobal("order_no", $_SESSION['id_order']);

unset($_SESSION['scid']);
unset($_SESSION['order_id']);

$tpl->printToScreen();

?>