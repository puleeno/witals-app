<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');
$tpl = new TemplatePower($CONFIG['template_dir'] . "/staticview.htm");
$tpl->prepare();
		$tpl->assign("name",$staticview_name);
		$tpl->assign("content",$staticview_content);
	
	
$tpl->printToScreen();
?>