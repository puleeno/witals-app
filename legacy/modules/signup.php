<?php


defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');
$tpl = new TemplatePower($CONFIG['template_dir'] . "/signup.htm");
$tpl->prepare();
langsite();

$sql = "SELECT * FROM coutry";
while($rs = mysqli_fetch_array($db)){
	$tpl->newBlock("country");
	$tpl->assign("name",$rs['name']);
}


$tpl->printToScreen();

?>