<?php
defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');
$tpl = new TemplatePower($mobile_temp.$CONFIG['template_dir'] . "/footer.htm");
$tpl->prepare();
$tpl->assignGlobal("dir_path", $dir_path);
langsite();


$tpl->assignGlobal('bottompage', str_replace($topa, $topb, $CONFIG['bottompage']));

$tpl->assign("_ROOT.hotlinesupport", $SETTING->hotlinesupport);
$tpl->assign("_ROOT.addresssupport", $SETTING->addresssupport);
$tpl->assign("_ROOT.emailsupport", $SETTING->emailsupport);
$tpl->assignGlobal("hotline", $SETTING->hotline);
$tpl->assignGlobal("emailsupport", $SETTING->emailsupport);
$tpl->assignGlobal(array(
    'facebook' => $SETTING->facebook,
    'youtube' => $SETTING->youtube,
    'blog' => $SETTING->blog,
    'googleplus' => $SETTING->googleplus,
    'fanpage' => $SETTING->fanpage,
    'companyname' => $SETTING->companyname,
    'companyaddress' => $SETTING->companyaddress,
    'companyphone' => $SETTING->companyphone,
    'companyemail' => $SETTING->companyemail,
    'companywebsite' => $SETTING->companywebsite,
	'instagram' => $SETTING->instagram
	
));
// copyright 
$footer1 = dbStatic::textFooter();
$tpl->assignGlobal("footername", $footer1['name']);
$tpl->assignGlobal("textfooter", $footer1['content']);
$copyright = dbStatic::copyright();
$copyright_text = trim((string)($copyright['content'] ?? ''));
if ($copyright_text === '') {
    $copyright_text = 'Copyright © 2023 by Xanhvina';
}
$tpl->assignGlobal("copyright", $copyright_text);
$tpl->assignGlobal("twitter", $SETTING->twitter ?? '');


$tpl->assign("_ROOT.boxmenufooter1", boxmenufooter(1));
$tpl->assign("_ROOT.boxmenufooter2", boxmenufooter(2));

$tpl->assign("_ROOT.boxmenufooter3", boxmenufooter(3));


$tpl->assign("_ROOT.menufooter", menufooter());
$tpl->assign("_ROOT.menuMobile", menuMobile());
if (strtolower(compile_post('addnewsletter'))=="save"){
	$newsletter = trim(strtolower(compile_post('newsletter')));
	$db = $DB->query("SELECT * FROM newsletter WHERE email =  '$newsletter' ");
	if (mysqli_num_rows($db) > 0)
		alert($langLabel['_regiter_successful']); // "Đăng ký thành công !";
	else{
		$db = $DB->query("INSERT INTO newsletter(name,email) VALUES ('NEWS LETTER','$newsletter')");
		alert($langLabel['_regiter_successful']);
	}
}

if ($_SESSION['cart_arr'])
    $cart_arr = $_SESSION['cart_arr'];
$quantity=0;
foreach ($cart_arr as $rs) {    if (!is_array($rs)) { continue; }

    $soluong = intval($rs['quantity']);
    $quantity += $soluong;
}

$tpl->assignGlobal("quantity",intval($quantity));






$tpl->printToScreen();
?>