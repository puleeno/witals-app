<?php
//Nguyen Van Binh
//suongmumc@gmail.com
error_reporting(0);
defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );

$id = intval($_GET['id']);
$idc = intval($_GET['idc']);

$tpl = new TemplatePower($CONFIG['template_dir']."/forms.htm");
$tpl->prepare();
require_once('legacy/lib/builForm.php');
$form = new builForm;
$f = $form->_init($id);



if($_POST['gone'] == 1){
	foreach($f['input'] as $in){
		$data[$in] = $_POST[$in];
	}
	$a = array();
	
	$a['json_content'] = addslashes(json_encode($data));	
	$a['content'] = $form->builResultForm($id,$data);	
	$a['createdate'] = time() + $CONFIG['time_offset'];
	$a['subject'] = $f['form_name'];
	$a['viewed'] = 0;
	$a['form_id'] = $form_id;
	if($a){
		$b=$DB->compile_db_insert_string($a);
		$sql="INSERT INTO forms_result (".$b['FIELD_NAMES'].") VALUES (".$b['FIELD_VALUES'].")";
		$DB->query($sql);
		
		// send email
		include_once('legacy/lib/class.phpmailer.php');
		include_once('legacy/lib/phpmailer.lang-en.php');
		
		
		// send mail to noitifition email
		if($f['send_to_email'] == 1){
			$subject = $f['subject_email'];
			sendmail($f['to_email'],$subject,$a['content'], $site_address);
		}
		// send mail to register
		if($f['autoresponder'] == 1){
			$autoresponder_email = $form->autoresponder_email($id,$data);
			$subject = $f['autoresponder_subject'];
			sendmail($f['to_email'],$subject,$autoresponder_email, $site_address);
		}
		
		if($f['after_submit']  == 'text'){
			Message::showMessage("success",$f['display_text']);
		}else{
			Message::showMessage("success",'1'.$f['display_text']);
			redir($f['url_redirect']);
		}
		
	}
	
}else{
	$tpl->newBlock("showform");
	$tpl->assign("formID",$f['formID']);
	$tpl->assign("id",$f['id']);
	$tpl->assign("layout",$f['layout']);	
}


$tpl->printToScreen();
?>