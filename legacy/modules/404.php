<?php 
defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );
$tpl = new TemplatePower("templates/404.htm");

$tpl->prepare();


langsite();	

$tpl->assignGlobal("lang", $lang);
$tpl->assignGlobal("lang_dir", $lang_dir);


$tpl->printToScreen();	


?>