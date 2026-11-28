<?php


defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');
$tpl = new TemplatePower($CONFIG['template_dir'] . "/signin.htm");
$tpl->prepare();
langsite();
$tpl->printToScreen();

?>