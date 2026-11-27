<?php
defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

error_reporting(E_ERROR);

$tpl = new TemplatePower("templates/payment.htm");
$tpl->prepare();
langsite();
$tpl->assignGlobal("pathpage", Get_Main_Cat_Name_path($idc));
$tpl->assignGlobal("dir_path", $dir_path);
$tpl->assignGlobal("site_address", $site_address);

$idt = intval($_GET["idt"]);

$client_ip = $_SERVER ['REMOTE_ADDR'];
$tpl->assignGlobal("ip_address",$client_ip );

$tpl->assignGlobal("payment_url", $SETTING->payment_url );
$tpl->assignGlobal("payment_merchantid", $SETTING->payment_merchantid );
$tpl->assignGlobal("payment_accesscode", $SETTING->payment_accesscode );
$tpl->assignGlobal("payment_hashcode", $SETTING->payment_hashcode );


$dieukhoan = dbStatic::getInWhere("dieukhoan");
foreach($dieukhoan as $rs_dieukhoan){    if (!is_array($rs_dieukhoan)) { continue; }

	$tpl->assignGlobal("dieukhoan_name", $rs_dieukhoan['name']);
	$tpl->assignGlobal("dieukhoan_content", $rs_dieukhoan['content']);	
}

$sql = "SELECT * FROM orders WHERE id_order = '$idt'" ;
$db=$DB->query($sql);

if($rs = mysqli_fetch_array($db)){
	
	if (intval($rs['id_order']) ==0 ) {		
		header("HTTP/1.1 301 Moved Permanently"); 		
		header("Location: $site_address");
	}


	$tpl->assignGlobal("order_no", $rs['id_order']);
	$tpl->assignGlobal("total_amount", number_format((float)($rs['total'])));
        
	$amount = intval($rs['total']) ;
	$tpl->assignGlobal("amount",  $amount );

}
$tpl->printToScreen();

?>