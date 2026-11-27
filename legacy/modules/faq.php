<?php



defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );

$tpl = new TemplatePower($CONFIG['template_dir']."/faq.htm");

$tpl->prepare();

langsite();

$idc 	= intval ($_GET['idc']);



$tpl->assignGlobal("dir_path",$dir_path);

$tpl->assignGlobal("pathpage",Get_Main_Cat_Name_path($idc));









$catinfo = Category::categoryInfo($idc);

$tpl->assignGlobal("catname",$catinfo['name']);



$faq = new clsfaq;

$faq->faq("listanswer");



if($_POST['code'] == 'save')

{

	if ($_SESSION['imagesercurity'] == strtolower(compile_post('sercurity')))

	{

		$a = array();

		$a['customer_name']			= compile_post('name');

		$a['customer_phone']	= compile_post('phonenumber');

		$a['customer_email']		= compile_post('email');

		$a['name']					= compile_post('title');		

		$a['title']					= compile_post('title');		

		$a['content']				= compile_post('other_request');

		$a['ngay_dang'] 			= time();

		$a['lang']					= $lang;

		

		$b=$DB->compile_db_insert_string($a);

		

		$sql="INSERT INTO faq (".$b['FIELD_NAMES'].") VALUES (".$b['FIELD_VALUES'].")";



		$DB->query($sql);

		

		$tpl->assignGlobal ("_ROOT.message","Message:".$LANG[$lang]->_message_send);

		  

		  

		  include_once('legacy/lib/class.phpmailer.php');

		  include_once('legacy/lib/phpmailer.lang-en.php');

	      $subject = "Thông tin liên hệ từ ".$a['name'] ." - ".date('d/m/Y H:i',$a['createdate']);



			try{

				 sendmail($CONFIG['site_email'],$subject,$message, " southtravel.vn");

			}

			catch(exception $e){}		  

		  

		  

	}

	else{

		$tpl->assignGlobal ("_ROOT.message","<span class='red error'>". $LANG[$lang]->_captcha_error ." </span>");



	}

}











$tpl->printToScreen();











class clsfaq{

	public function faq($block){

		global $DB, $tpl, $idc, $lang;

		$idc = intval($idc);



		$languageWhere ='';

		if ($lang!='' ) $languageWhere =  "AND lang = '$lang'";

		

		$sql = "SELECT * FROM faq WHERE active=1  $languageWhere ORDER BY thu_tu DESC, name ASC";
		

		$db = paging::pagingFonten($_GET['p'],$dir_path."/".url_process::getUrlCategory($idc),$sql,8,30);

		

		//$db = $DB->query($sql);

		while($rs = mysqli_fetch_array($db['db'])){

			$tpl->newBlock($block)	;

			$tpl->assign("name",$rs['name']);

			$tpl->assign("content",$rs['content']);

			$tpl->assign("id",$rs['id_faq']);

			$tpl->assign("anchor", clean_url($rs['name']));

		}

		

		$tpl->assign("pages",$db['pages']);

	}

	

	public function insertFaq(){

		global $DB, $tpl, $idc, $lang;

		$idc = intval($idc);



		$languageWhere ='';

		if ($lang!='' ) $languageWhere =  "AND lang = '$lang'";



		

	}

}







?>