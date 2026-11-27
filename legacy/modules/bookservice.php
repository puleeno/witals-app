<?php

//Nguyen Van Binh
//suongmumc@gmail.com
//error_reporting(E_ALL);
defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

$tpl = new TemplatePower($CONFIG['template_dir'] . "/bookservice.htm");
$tpl->prepare();

$tpl->assignGlobal("dir_path", $dir_path);
langsite();

$tpl->printToScreen();
?>