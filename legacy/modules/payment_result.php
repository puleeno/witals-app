<?php



defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

error_reporting(E_ERROR);


$tpl = new TemplatePower("templates/payment_result.htm");

$tpl->prepare();

langsite();

$tpl->assignGlobal("pathpage", Get_Main_Cat_Name_path($idc));
$tpl->assignGlobal("dir_path", $dir_path);
$tpl->assignGlobal("site_address", $site_address);


$vnp_TmnCode = "KATA0001"; //Mã website tại VNPAY 
//$vnp_HashSecret = "MAXRPVLWAVFARZYLCQGSHMTURANWURMU";  TEST

$vnp_HashSecret = "LCURFKGFEREDRJYVEXKGSKREOLRSLYDA"; 


$vnp_SecureHash = $_GET['vnp_SecureHash'];
$inputData = array();
foreach ($_GET as $key => $value) {
	if (substr($key, 0, 4) == "vnp_") {
		$inputData[$key] = $value;
	}
}
unset($inputData['vnp_SecureHashType']);
unset($inputData['vnp_SecureHash']);
ksort($inputData);
$i = 0;
$hashData = "";
foreach ($inputData as $key => $value) {
	if ($i == 1) {
		$hashData = $hashData . '&' . $key . "=" . $value;
	} else {
		$hashData = $hashData . $key . "=" . $value;
		$i = 1;
	}
}

//$secureHash = md5($vnp_HashSecret . $hashData);
$secureHash = hash('sha256',$vnp_HashSecret . $hashData);

$vnp_TxnRef = clean_value($_GET['vnp_TxnRef']);
$vnp_Amount =  intval($_GET['vnp_Amount']) / 100;
$vnp_OrderInfo = clean_value($_GET['vnp_OrderInfo']);
$vnp_ResponseCode = clean_value($_GET['vnp_ResponseCode']);
$vnp_TransactionNo = clean_value($_GET['vnp_TransactionNo']);
$vnp_BankCode = clean_value($_GET['vnp_BankCode']);
$vnp_PayDate = clean_value($_GET['vnp_PayDate']);

$transStatus = "";
if ($secureHash == $vnp_SecureHash) {
	if ($_GET['vnp_ResponseCode'] == '00') {
		$transStatus = "GD Thanh cong";
	} else {
		$transStatus = "GD Khong thanh cong";
	}
} else {
	$transStatus = "Chu ky khong hop le";
}
		
$tpl->assign("transStatus", $transStatus);

$tpl->assign("trans_result", "Giao dịch thành công ");


$tpl->assign("vnp_TxnRef", $vnp_TxnRef);
$tpl->assign("vnp_OrderInfo", $vnp_OrderInfo);
$tpl->assign("vnp_Amount", number_format((float)($vnp_Amount)));
$tpl->assign("vnp_TransactionNo", $vnp_TransactionNo);
$tpl->assign("vnp_ResponseCode", $vnp_ResponseCode);
$tpl->assign("vnp_BankCode", $vnp_BankCode);
$tpl->assign("vnp_PayDate", $vnp_PayDate);

if ($vnp_TxnRef != ""){
    $sql = "SELECT * FROM orders WHERE id_order = '$vnp_TxnRef' ";
    //echo $sql;
    $db = $DB->query($sql);
    if ($rs = mysqli_fetch_array($db)){
		$id_order = intval($rs['id_order']);
		$order_Amount = intval($rs['total']);
		$pay_status = intval($rs['pay_status']);
	}
	else
        $id_order = 0;

}else{
    $id_order = -1;
}


if ($id_order > 0) {
	
	if($vnp_Amount !=  $order_Amount ){
		$tpl->assign("trans_result", "Số tiền giao dịch không khớp, giao dịch không thành công. ");
		
	}elseif ($pay_status > 0) {
		$tpl->assign("trans_result", "Giao dịch đã được xử lý ");
		
	}else{
		    $data = array();
			$data['last_update'] = time() + $CONFIG['time_offset'];

			$data['transStatus'] = clean_value($transStatus);

			$data['OrderInfo'] = clean_value($vnp_OrderInfo);
			$data['TransactionNo'] = clean_value($vnp_TransactionNo); //00 is success
			$data['ResponseCode'] = clean_value($vnp_ResponseCode);
			$data['BankCode'] = clean_value($vnp_BankCode);
			$data['PayDate'] = clean_value($vnp_PayDate);
			$data['pay_status'] = 1;	//1: Da cap nhat ket qua tra ve binh thuong, 2: cap nhat qua IPN

			//var_dump($data);

			$DBi->updateTableRow("orders", $data, "id_order", $id_order);
			$tpl->assign("trans_result", "Giao dịch thành công");
		
	}

	/* Gui mail cho khach hang */
	/*
	$subject = "Giao dịch online thành công " . " - " . date('d/m/Y H:i', time() + $CONFIG['time_offset']);
	$message = "Xin chào bạn: $fullname. <br/> Chúc mừng bạn đã giao dịch thành công tại . <br/>Hệ thống xin gửi bạn thông tin .Xin cảm ơn bạn! ";
	
	try {
		sendmail($email, $subject, $message, $site_address);
		sendmail($CONFIG['site_email'], $subject, $message, $site_address);
	} catch (exception $e) {
		
	}	
    */

}else{
	$tpl->assign("trans_result", "Không thể cập nhật kết quả giao dịch vào hệ thống ");
	
}

$tpl->printToScreen();



?>