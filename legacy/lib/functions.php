<?php
//Develop Team: Binh Minh group
//binhminhgroup@gmail.com
//error_reporting(E_ERROR);

defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );



//Check file

function is_remote($file_name) {

  return strpos($file_name, '://') > 0 ? 1 : 0;

}



function is_remote_file($file_name) {

  return is_remote($file_name) && preg_match("#\.[a-zA-Z0-9]{1,4}$#", $file_name) ? 1 : 0;

}



function is_local_file($file_name) {

  return !is_remote($file_name) && strpos($file_name, '/') !== false && preg_match("#\.[a-zA-Z0-9]{1,4}$#", $file_name) ? 1 : 0;

}



function check_remote_media($remote_media_file) {

  global $config;

  return is_remote($remote_media_file) && preg_match("#\.[".$CONFIG['allowed_mediatypes_match']."]+$#i", $remote_media_file) ? 1 : 0;

}

function check_local_media($local_media_file) {

  global $config;

  return !is_remote($local_media_file) && strpos($local_media_file, '/') !== false && preg_match("#\.[".$CONFIG['allowed_mediatypes_match']."]+$#i", $local_media_file) ? 1 : 0;

}



function check_remote_thumb($remote_thumb_file) {

  return is_remote($remote_thumb_file) && preg_match("#\.[gif|jpg|jpeg|png|bmp]+$#is", $remote_thumb_file) ? 1 : 0;

}



function check_local_thumb($remote_thumb_file) {

  return !is_remote($local_thumb_file) && strpos($local_thumb_file, '/') !== false && preg_match("#\.[gif|jpg|jpeg|png]+$#i", $local_thumb_file) ? 1 : 0;

}



function get_file_extension($file_name) {

  preg_match("/(.+)\\.(.+)/", basename($file_name), $regs);

  return strtolower($regs[2]);

}



function get_file_name($file_name) {

  preg_match("/(.+)\\.(.+)/", basename($file_name), $regs);

  return $regs[1];

}



function check_media_type($file_name) {

  global $config;

  return (in_array(get_file_extension($file_name), $CONFIG['allowed_mediatypes_array'])) ? 1 : 0;

}



function check_thumb_type($file_name) {

  return (preg_match("#(gif|jpg|jpeg|png)$#is", $file_name)) ? 1 : 0;

}



function check_executable($file_name) {

  if (substr(PHP_OS, 0, 3) == "WIN" && !preg_match("/\.exe$/i", $file_name)) {

    $file_name .= ".exe";

  }

  elseif (substr(PHP_OS, 0, 3) != "WIN") {

    $file_name = preg_replace("/\.exe$/i", "", $file_name);

  }

  return $file_name;

}

function remote_file_exists($url) { // similar to file_exists(), checks existence of remote files

  $url = trim($url);

  if (!preg_match("=://=", $url)) $url = "http://$url";

  if (!($url = @parse_url($url))) {

    return false;

  }

  if (!preg_match("/http/i", $url["scheme"])) {

    return false;

  }

  $url['port'] = (!isset($url['port'])) ? 80 : $url['port'];

  $url['path'] = (!isset($url['path'])) ? "/" : $url['path'];

  $fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30);

  if (!$fp) {

    return false;

  }

  else {

    $head = "";

    $httpRequest = "HEAD ".$url['path']." HTTP/1.1\r\n"

                  ."HOST: ".$url['host']."\r\n"

                  ."Connection: close\r\n\r\n";

    fputs($fp, $httpRequest);

    while (!feof($fp)) {

      $head .= fgets($fp, 1024);

    }

    fclose($fp);



    preg_match("=^(HTTP/\d+\.\d+) (\d{3}) ([^\r\n]*)=", $head, $matches);

    if ($matches[2] == 200) {

      return true;

    }

  }

}



// $p=trang so,$url duong link,$sql,$maxpage=so trang duoc hien thi, $maxitem= so ban ghi/trang





// fix chieu dai va chieu rong cua file anh theo mot co nhat dinh

function file_exit($image)

{

	global $CONFIG;

	if ($image)

	{

		$src=$CONFIG['upload_image_path'].$image;

		if ($image && file_exists($src))

		{

			return true;

		}

		else

		{

			return false;

		}

	}

	else

	{

		return false;

	}

}

function uploadflash($inputname,$thumnail='yes',$dich=''){

	global $CONFIG,$_FILES,$tpl,$category,$par_page;

	$a=array();

	if($dich==''){

		$dich=$CONFIG['root_path'].$CONFIG['upload_image_path'];

	}else {

		$dich=$dich;

	}

	if($category==''){

		$category=$par_page;

	}

	if ($_FILES[$inputname]['size']){

		$in_image=get_new_file_name($_FILES[$inputname]['name'],clean_url(get_file_name($_FILES[$inputname]['name'])));

		$file_upload=new Upload($dich,'jpg,gif,png,bmp,swf');

		if ($file_upload->upload_file($inputname,2,$in_image))

		{



			if($thumnail=='yes'){

				$thumbnail=create_thumb($dich, $file_upload->file_name);

				if ($thumbnail)

				{

					$a['small_image']=$thumbnail['thumb'];

					$a['normal_image']=$thumbnail['normal'];

				}

				else

				{

					$msg.="Kh&#244;ng t&#7841;o &#273;&#432;&#7907;c &#7843;nh thumbnail ! Xem l&#7841;i &#273;&#7883;nh d&#7841;ng file !<br>";

				}

			}

		}

		else

		{

			$msg.=$file_upload->get_upload_errors()."<br>";

		}

	}else{

		//Kiem tra remote url

		$img_url=compile_post($inputname.'_url');

		$img_url=trim($img_url);

		if ($img_url!="")

		{

			if (remote_file_exists($img_url))

			{

				if (check_remote_thumb($img_url))

				{

					//that remote file 's valid

					$a['image']=$img_url;

					if ($thumbnail=create_thumb_url($img_url))

					{

						$a['small_image']=$thumbnail['thumb'];

						$a['normal_image']=$thumbnail['normal'];

					}

					else

					{

						$msg.="Kh&#244;ng t&#7841;o &#273;&#432;&#7907;c &#7843;nh thumbnail ! H&#227;y xem l&#7841;i &#273;&#7883;nh d&#7841;ng file !<br>";

					}



				}

				else

				{

					$msg.="&#272;&#7883;nh d&#7841;ng file kh&#244;ng &#273;&#250;ng!<br>";

				}

			}

			else

			{

				$msg.="Kh&#244;ng truy nh&#7853;p &#273;&#432;&#7907;c file theo URL &#273;&#227; nh&#7853;p !<br>";

			}

		}

	}

	$tpl->newBlock("msg");

	$tpl->assign("msg",$msg);

	return $a;

}



// upload image

function uploadimagenothumb($inputname,$dich=''){

	global $CONFIG,$_FILES,$tpl,$category,$par_page;

	$a=array();

	if($dich==''){

		$dich=$CONFIG['root_path'].$CONFIG['upload_image_path'];

		$dich1=$CONFIG['upload_image_path'];

	}else {

		$dich=$dich;

	}

	if($category==''){

		$category=$par_page;

	}

	if ($_FILES[$inputname]['size']){

		$in_image=get_new_file_name($_FILES[$inputname]['name'],clean_url(get_file_name($_FILES[$inputname]['name'])));

		$file_upload=new Upload($dich,'jpg,gif,png,bmp,swf');

		if ($file_upload->upload_file($inputname,2,$in_image))

		{

			//Da upload thanh cong

			//Tao thumbnail

			$a['image']=$dich1.$file_upload->file_name;





		}

		else

		{

			$msg.=$file_upload->get_upload_errors()."<br>";

		}

	}



	$tpl->newBlock("msg");

	$tpl->assign("msg",$msg);

	return $a;

}







// $lastfile=image - $lastnormal= normal_image , $lastsmall=small_image

function deleteimage($lastfile='',$lastnormal='',$lastsmall='',$dich=''){

	global $CONFIG;

	if($dich==''){

		$dich='';

	}else {

		$dich=$dich;

	}

	if ($lastfile||$lastnormal||$lastsmall)

	{

		if ($lastfile && file_exists($dich.$lastfile))

		{

			unlink($dich.$lastfile);

		}

		if ($lastnormal && file_exists($dich.$lastnormal))

		{

			unlink($dich.$lastnormal);

		}

		if ($lastsmall && file_exists($dich.$lastsmall))

		{

			unlink($dich.$lastsmall);

		}

	}

}

// upload file

function uploadfile($inputname,$dich=''){

	global $CONFIG,$_FILES,$tpl,$category,$par_page;

	$a=array();

	if($dich==''){

		$dich=$CONFIG['root_path'].$CONFIG['upload_media_path'];

	}else {

		$dich=$dich;

	}

	if($category==''){

		$category=$par_page;

	}

	if ($_FILES[$inputname]['size']){

		$in_image=get_new_file_name($_FILES[$inputname]['name'],$_FILES[$inputname]['name']);

		$file_upload=new Upload($dich,$CONFIG['allowed_mediatypes']);

		if ($file_upload->upload_file($inputname,2,$in_image))

		{

			$a['file']=$file_upload->file_name;

		}

		else

		{

			$msg.=$file_upload->get_upload_errors()."<br>";

		}

	}

	$tpl->newBlock("msg");

	$tpl->assign("msg",$msg);

	return $a;

}

function deletefile($lastfile='',$dich=''){

	global $CONFIG;

	if($dich==''){

		$dich=$CONFIG['root_path'].$CONFIG['upload_media_path'];

	}else {

		$dich=$dich;

	}

	if ($lastfile)

	{

		if ($lastfile && file_exists($dich.$lastfile))

		{

			unlink($dich.$lastfile);

		}



	}

}

//doc thu

function getwday($number){



	switch($number){

		case 0:

			$thu="Ch&#7911; nh&#7853;t";

			break;

		case 1:

			$thu="Th&#7913; hai";

			break;

		case 2:

			$thu="Th&#7913; ba";

			break;

		case 3:

			$thu="Th&#7913; t&#432;";

			break;

		case 4:

			$thu="Th&#7913; n&#259;m";

			break;

		case 5:

			$thu="Th&#7913; s&#225;u";

			break;

		case 6:

			$thu="Th&#7913; b&#7849;y";

			break;

	}

	return $thu;

}

function getwday_eng($number){



	switch($number){

		case 0:

			$thu="Sun";

			break;

		case 1:

			$thu="Mon";

			break;

		case 2:

			$thu="Tue";

			break;

		case 3:

			$thu="Web";

			break;

		case 4:

			$thu="Thu";

			break;

		case 5:

			$thu="Fri";

			break;

		case 6:

			$thu="Sat";

			break;

	}

	return $thu;

}



function strstrim($str,$maxlen=10){

	$str=strip_tags($str,"<br><strong>");

	$a=trim($str);

	$len=strlen($a);

	$string='';

	$dem=0;

	for($i=0;$i<$len;$i++){

		if($a[$i]==' '){

			$dem++;

		}

		if($dem<=$maxlen){

			$string.=$a[$i];

		}

	}

	if($dem>$maxlen){

		$string.="&nbsp;...";

	}

	return $string;



}



function RemoveSign($str)

{



	$coDau=array("à","á","ạ","ả","ã","â","ầ","ấ","ậ","ẩ","ẫ","ă","ằ","ắ"

	,"ặ","ẳ","ẵ","è","é","ẹ","ẻ","ẽ","ê","ề","ế","ệ","ể","ễ","ì","í","ị","ỉ","ĩ",

	"ò","ó","ọ","ỏ","õ","ô","ồ","ố","ộ","ổ","ỗ","ơ"

	,"ờ","ớ","ợ","ở","ỡ",

	"ù","ú","ụ","ủ","ũ","ư","ừ","ứ","ự","ử","ữ",

	"ỳ","ý","ỵ","ỷ","ỹ",

	"đ",

	"À","Á","Ạ","Ả","Ã","Â","Ầ","Ấ","Ậ","Ẩ","Ẫ","Ă"

	,"Ằ","Ắ","Ặ","Ẳ","Ẵ",

	"È","É","Ẹ","Ẻ","Ẽ","Ê","Ề","Ế","Ệ","Ể","Ễ",

	"Ì","Í","Ị","Ỉ","Ĩ",

	"Ò","Ó","Ọ","Ỏ","Õ","Ô","Ồ","Ố","Ộ","Ổ","Ỗ","Ơ"

	,"Ờ","Ớ","Ợ","Ở","Ỡ",

	"Ù","Ú","Ụ","Ủ","Ũ","Ư","Ừ","Ứ","Ự","Ử","Ữ",

	"Ỳ","Ý","Ỵ","Ỷ","Ỹ",

	"Đ","ê","ù","à");

	$khongDau=array("a","a","a","a","a","a","a","a","a","a","a"

	,"a","a","a","a","a","a",

	"e","e","e","e","e","e","e","e","e","e","e",

	"i","i","i","i","i",

	"o","o","o","o","o","o","o","o","o","o","o","o"

	,"o","o","o","o","o",

	"u","u","u","u","u","u","u","u","u","u","u",

	"y","y","y","y","y",

	"d",

	"A","A","A","A","A","A","A","A","A","A","A","A"

	,"A","A","A","A","A",

	"E","E","E","E","E","E","E","E","E","E","E",

	"I","I","I","I","I",

	"O","O","O","O","O","O","O","O","O","O","O","O"

	,"O","O","O","O","O",

	"U","U","U","U","U","U","U","U","U","U","U",

	"Y","Y","Y","Y","Y",

	"D","e","u","a");

	return str_replace($coDau,$khongDau,$str);

}







function compile($str){

	$str=trim($str);

	$str = preg_replace("/[^a-zA-Z0-9\s]/", "", $str);

	$str=str_replace("&#160;","",$str);

	$str=str_replace("&nbsp;"," ",$str);

	$str=str_replace("  "," ",$str);

	$str=str_replace(" ","-",$str);

	$str=str_replace("--","-",$str);

	$str=str_replace("---","-",$str);

	//$str = str_replace(array('\r|\t|\n','  ','    ','     ','	'), '-', $str);

	$str = str_replace(array('\r|\t|\n','  ','    ','     ','	'), '-', $str);

	return $str=strtolower($str);

}

function compile_title($str){

	$str=str_replace("-"," ",$str);

	$str=str_replace("/"," - ",$str);

	return $str;

}

function clean_url($text){

	return $str=compile(RemoveSign($text));

}

function sendmail($to,$subject,$contentmail, $name, $attachArr = array(), $reply = '')	{
	global $CONFIG;


	include_once('legacy/lib/class.smtp.php');
	include_once('legacy/lib/class.phpmailer.php');


	$mail = new PHPMailer();
	$mail->CharSet = "UTF-8";
	$mail->IsSMTP(); // set mailer to use SMTP



	if ($CONFIG['smtp_host'] == ""){

		$mail->Host = "smtp.gmail.com";
		$mail->Username = "system.binhminhgroup@gmail.com";
		$mail->Password = "bM!@#$%^";
		$mail->From = "system.binhminhgroup@gmail.com"; // Reply to this email

	}else{
		$mail->Host = $CONFIG['smtp_host'];
		$mail->Username = $CONFIG['smtp_username'];// your SMTP username or your gmail username
		$mail->Password = $CONFIG['smtp_password']; // your SMTP password or your gmail password
		$mail->From = $CONFIG['smtp_username']; // Reply to this email
	}

	$mail->FromName= $CONFIG['site_name']; // Name to indicate where the email came from when the recepient received

	$mail->SMTPSecure = 'ssl';
	$mail->Port = 465; // set the port to use


	//$mail->SMTPSecure = 'tls';
	//$mail->Port = 587; // set the port to use

	$mail->SMTPAuth = true; // turn on SMTP authentication


	$to_arr =  explode(",",$to); //Send to multi email by comma
	foreach($to_arr as $to_email){
		$mail->AddAddress(trim($to_email," "),$name);
	}

	//$mail->AddAddress($to,$name);
	//$mail->AddReplyTo($from,"ITLC");

	$mail->WordWrap = 50; // set word wrap
	$mail->IsHTML(true); // send as HTML
	//$mail->Subject = utf8_decode($subject);
	$mail->Subject = $subject;
	$mail->Body = $contentmail ; //HTML Body
	//$mail->AltBody = "Mail nay duoc goi bang phpmailer class."; //Text Body

	//$mail->SMTPDebug = 2;
	if(!$mail->Send())
	{
		//echo 'Mailer Error: ' . $mail->ErrorInfo;
	}
	else
	{
		echo "";

	}

}

function yahoo_nickname_status ($nickname) {

  $response =file_get_contents("http://opi.yahoo.com/online?u=".$nickname."&m=t");

	if($response==$nickname.' is NOT ONLINE'){

		return 0;

	}else{

		return 1;

	}

}

function check_yahoo($nickname,$type)

{

	if($type==0)

	{

		$check_on = "<a href='ymsgr:sendIM?$nickname'><img src='http://opi.yahoo.com/online?u=$nickname&amp;m=g&amp;t=2' border='0'></a>";

	}

	else

	{

		$check_on = "<a href='skype:{$nickname}?chat'><img border='0' src='http://mystatus.skype.com/smallicon/{$nickname}' ></a>";

	}

	return $check_on;

}

function alert($alert)

{

	$alert = "<script languague='javascript'>alert('$alert');</script>";

	echo $alert;

}

function embed_flash($url,$width,$height){

	$str='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"

	codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"

    width="'.$width.'" height="'.$height.'" id="Flash" align="">

    <param name="movie" value="'.$url.'">

    <param name="quality" value="high">

    <param name="bgcolor" value="#FFFFFF">

    <param name="wmode" value="transparent">

    <embed src="'.$url.'" quality="high" bgcolor="#FFFFFF"  width="'.$width.'"

            height="'.$height.'" name="Flash" align="" wmode="transparent"

            type="application/x-shockwave-flash"

            pluginspage="http://www.macromedia.com/go/getflashplayer">

    </embed>

	</object>';

	return $str;

}

function get_timestamp_form_binh($ngay,$thang,$nam)

{

	return mktime(0, 0, 0, $thang, $ngay, $nam);

}

function strip_date($datestring){

	$a=array();

	$a=explode("/",$datestring);

	return mktime(0, 0, 0, $a[1], $a[0], $a[2]);

}

function strip_date_time($datestring){

	$datestring=str_replace(" ","/",$datestring);

	$datestring=str_replace(":","/",$datestring);



	$a=array();

	$a=explode("/",$datestring);

	return mktime($a[3],$a[4],$a[5],  $a[1], $a[0], $a[2]);

}



function upload_url($url){

	//Kiem tra remote url

	global $CONFIG,$_FILES,$tpl,$category,$par_page;

	$a=array();

	if($dich==''){

		$dich=$CONFIG['root_path'].$CONFIG['upload_image_path'];

	}else {

		$dich=$dich;

	}

	$img_url=$url;

	$img_url=trim($img_url);

	if ($img_url!="")

	{

		if (remote_file_exists($img_url))

		{

			if (check_remote_thumb($img_url))

			{

				//that remote file 's valid

				$a['image']=$img_url;

				if ($thumbnail=url_upload($img_url))

				{

					$a['image']=$thumbnail['image'];

				}

				else

				{

					$msg.="Kh&#244;ng t&#7841;o &#273;&#432;&#7907;c &#7843;nh thumbnail ! H&#227;y xem l&#7841;i &#273;&#7883;nh d&#7841;ng file !<br>";

				}



			}

			else

			{

				$msg.="&#272;&#7883;nh d&#7841;ng file kh&#244;ng &#273;&#250;ng!<br>";

			}

		}

		else

		{

			$msg.="Kh&#244;ng truy nh&#7853;p &#273;&#432;&#7907;c file theo URL &#273;&#227; nh&#7853;p !<br>";

		}

	}

	return $a;

}





function trim_url_image($url){

    if(substr($url,0,1) == '/'){

        $url = substr($url,1,strlen($url));

    }

    return $url;

}





function string_to_microtime($datestring){

	$datestring=str_replace(" ","/",$datestring);

	$datestring=str_replace(":","/",$datestring);

	$a=array();

	$a=explode("/",$datestring);

	return mktime($a[3],$a[4],$a[5],  $a[1], $a[0], $a[2]);

}



function getVideoCode($url){

	$video = explode("=",$url)	;

	return $video[1];

}

?>

