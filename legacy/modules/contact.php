<?php

//Develop Team: Binh Minh group
//binhminhgroup@gmail.com

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');
/*
require_once "recaptchalib.php";
*/
if ($_POST['code'] == 'save') {
	
/*
	$secret = "6LdBXrsUAAAAAKC_jH98W4hFxwf-km8RaCkC5b_M";
	$response = null;
	$reCaptcha = new ReCaptcha($secret);

	if ($_POST["g-recaptcha-response"]) {
		$response = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"],$_POST["g-recaptcha-response"]);
	}
	
	if ($response != null && $response->success) {
*/


	if ( strtolower($_SESSION['imagesercurity']) == strtolower(compile_post('sercurity'))) {
		
        $a = array();
        $a['fullname'] = compile_post('yourname');
        $a['email'] = compile_post('email');
        $a['phone'] = compile_post('phone');
        $a['address'] = compile_post('address');
        $a['subject'] = compile_post('subject');
        $a['other_request'] = compile_post('other_request');
        $a['createdate'] = time() + $CONFIG['time_offset'];
        ;
        $tensp = compile_post('tensp');
        $title_contact = "Thông tin liên hệ: ";
        if (strlen($tensp) > 5) {
            $title_contact = "Đăng ký đại lý: ";
            
            $a['tensp'] = $tensp;
			
			$a['nguoidaidien'] = compile_post('nguoidaidien');
			$a['website'] = compile_post('website');
			$a['sodkkd'] = compile_post('sodkkd');
			
			
            
            $pro_message = '<tr>
               <td height="30" colspan="2" align="left" valign="middle"><strong> Thông tin đăng ký</strong></td>
    </tr>
	
		<tr>
               <td height="30" align="left" style="width:200px;" valign="middle">Tên doanh nghiệp :</td>
               <td height="30" align="left" valign="middle"><strong>' . $a['fullname'] . '</strong></td>
      </tr>
	
      <tr>
               <td height="30" align="left" style="width:200px;" valign="middle">Tên người đại diện :</td>
               <td height="30" align="left" valign="middle"><strong>' . $a['nguoidaidien'] . '</strong></td>
      </tr>
	  
	  <tr>
               <td height="30" align="left" style="width:200px;" valign="middle">Số ĐKKD :</td>
               <td height="30" align="left" valign="middle"><strong>' . $a['sodkkd'] . '</strong></td>
      </tr>
	  
	  <tr>
               <td height="30" align="left" style="width:200px;" valign="middle">Website chính thức :</td>
               <td height="30" align="left" valign="middle"><strong>' . $a['website'] . '</strong></td>
      </tr>
      
      

';
        }
        $message = '
<table width="100%" border="1" cellspacing="0" cellpadding="0">
     <tr>
               <td height="30" colspan="2" align="left" valign="middle"><strong> ' . $title_contact . ' </strong></td>
    </tr>
    ' . $pro_message . '
             <tr>
               <td height="30" align="left" style="width:200px;" valign="middle">Họ tên:</td>
               <td height="30" align="left" valign="middle"><strong>' . $a['fullname']  . '</strong></td>
      </tr>
        <tr>
          <td height="30" align="left" valign="middle">E-mail:</td>
          <td height="30" align="left" valign="middle"><strong>' . $a['email'] . '</strong></td>
      </tr>
      
    <tr>
          <td height="30" align="left" valign="middle">Điện thoại:</td>
          <td height="30" align="left" valign="middle"><strong>' . $a['phone'] . '</strong></td>
      </tr>
        <tr>
          <td height="30" align="left" valign="middle"> Địa chỉ:</td>
          <td height="30" align="left" valign="middle"><strong>' . $a['address'] . '</strong></td>
      </tr>
      <tr>
               <td height="30" align="left" valign="middle"><strong>Nội dung liên hệ</strong>: </td>
               <td height="30" align="left" valign="middle"><i>' . $a['other_request'] . " (Loại xe: ". $a['subject'] . ')</i></td>
      </tr>
</table>';
        $c = array();
        $c['name'] = $title_contact . $a['fullname'];
        $c['content'] = $message;
        $c['email'] = $a['email'];
        $c['createdate'] = $a['createdate'];
        $b = $DB->compile_db_insert_string($c);
        $sql = "INSERT INTO contact (" . $b['FIELD_NAMES'] . ") VALUES (" . $b['FIELD_VALUES'] . ")";
        $DB->query($sql);
        echo "1";
        $subject = $title_contact . $a['name'] . " - " . date('d/m/Y H:i', $a['createdate']);
        try {
            sendmail($CONFIG['site_email'], $subject, $message, $site_address);
        } catch (exception $e) {
            
        }
    } else {
        echo "-1";
    }
}else if($_POST['regsubmit'] == 1){
	$a = array();
	$a['name'] = compile_post('name');
	$a['phone'] = compile_post('phone');
	$a['email'] = compile_post('email');
	$a['note'] = compile_post('note');
	$a['createdate'] = time() + $CONFIG['time_offset'];
	
	
	
	$message = '<table width="600" border="1">
	  <tr>
		<th style="border:solid 1px #CCC" colspan="2">Đăng ký tham quan nhà mẫu</th>
	  </tr>
	  
	  <tr>
		<td style="border:solid 1px #CCC; width:150">Họ tên</td>
		<td style="border:solid 1px #CCC">'.$a['name'].'</td>
	  </tr>
	  <tr>
		<td style="border:solid 1px #CCC">Điện thoại</td>
		<td style="border:solid 1px #CCC">'.$a['phone'].'</td>
	  </tr>
	  <tr>
		<td style="border:solid 1px #CCC">Email</td>
		<td style="border:solid 1px #CCC">'.$a['email'].'</td>
	  </tr>
	  <tr>
		<td style="border:solid 1px #CCC">Ghi chú</td>
		<td style="border:solid 1px #CCC">'.$a['note'].'</td>
	  </tr>
	</table>';
	$c = array();
	$title_contact = "Đăng ký tham quan nhà mẫu: ";
	$c['name'] = $title_contact . $a['fullname'];
	$c['content'] = $message;
	$c['email'] = $a['email'];
	$c['createdate'] = $a['createdate'];
	$b = $DB->compile_db_insert_string($c);
	$sql = "INSERT INTO contact (" . $b['FIELD_NAMES'] . ") VALUES (" . $b['FIELD_VALUES'] . ")";
	$DB->query($sql);
	$subject = $title_contact . $a['name'] . " - " . date('d/m/Y H:i', $a['createdate']);
	try {
		sendmail($CONFIG['site_email'], $subject, $message, $site_address);
	} catch (exception $e) {
		
	}
	echo '1';
} else {
    $tpl = new TemplatePower("templates/contact.htm");
    $tpl->prepare();
	$idc = intval($_GET['idc']);
    langsite();
	$rs_cat = Category::categoryInfo($idc);
    $tpl->assignGlobal("dir_path", $dir_path);
    $tpl->assignGlobal("site_address", $site_address);
	$catname = $rs_cat['name'];
    $tpl->assignGlobal("slideshow", slidechild());
$tpl->assignGlobal("pathpage", '<div class="crumb"><div class="grid"><a href="/' . $lang_dir . '"><i class="fa fa-home"></i></a> <i class="fa fa-angle-right"></i> ' . Get_Main_Cat_Name_path($idc) . '</div><div class="c5"></div></div>');
	

    $tpl->assignGlobal("catname", $rs_cat['name']);
    $tpl->assignGlobal("catcontent", $rs_cat['content']);
    $tpl->assignGlobal("intro", $rs_cat['intro']);

    $tpl->assignGlobal("leftcol", $leftcol);
    $tpl->assignGlobal("catlink", $dir_path . '/' . $rs_cat['url']);
    if ($rs_cat['image']) {
        $tpl->assignGlobal("image", '<img  src="' . $cache_image_path . resizeimage(1200, 600, $dir_path . '/' . $rs_cat['image']) . '" alt="' . $rs_cat['name'] . '" style="max-width:100%" />');
    }
	
	$sql = "SELECT * FROM contactinfo WHERE active=1 $language ";

	
    $db = $DB->query($sql);
    if ($rs = mysqli_fetch_array($db)) {
        $tpl->assignGlobal("contactname", $rs['name']);
        $tpl->assignGlobal("contentcontact", $rs['content']);
    }
    $db_contact = dbStatic::getInWhere("contact");
    //var_dump($db_contact);
    foreach ($db_contact as $rs_contact) {        if (!is_array($rs_contact)) { continue; }

        $tpl->newBlock("contactItem");
        $tpl->assign("name", $rs_contact['name']);
        $tpl->assign("content", $rs_contact['content']);
    }
    $tpl->printToScreen();
}
?>