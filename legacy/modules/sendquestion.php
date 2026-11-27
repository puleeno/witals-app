<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

$id = intval($_GET['id']);
$idc = intval($_GET['idc']);


	$template_name = $cateinfo['template_name'];
	if ($template_name)
		$tpl = new TemplatePower($CONFIG['template_dir'] . "/$template_name.htm");
	else
		$tpl = new TemplatePower($CONFIG['template_dir'] . "/sendquestion.htm");
	$tpl->prepare();
	
	$tpl->assignGlobal("dir_path", $dir_path);
	$tpl->assignGlobal("site_address", $site_address);
	$tpl->assignGlobal("pathpage", Get_Main_Cat_Name_path($idc));
	
	langsite();
	if($_POST['act'] == 1){
		if ($_SESSION['imagesercurity'] == strtolower(compile_post('sercurity'))){
			$a = array();
			$a['customer_name'] = compile_post('name');
			$a['customer_email'] = compile_post('email');
			$a['customer_phone'] = compile_post('phonenumber');
			$a['title'] = compile_post('question');
			$a['name'] = compile_post('question');
			$a['lang'] = $lang;
			$a['ngay_dang'] = time() + $CONFIG['time_offset'];
			$a['active'] = 0;
			 $b=$DB->compile_db_insert_string($a);
			  $sql="INSERT INTO faq (".$b['FIELD_NAMES'].") VALUES (".$b['FIELD_VALUES'].")";
			  $DB->query($sql);
			$tpl->newBlock("message");
		}else{
			$tpl->newBlock("message_err_captcha");
		}
	}
	$tpl->printToScreen();

?>