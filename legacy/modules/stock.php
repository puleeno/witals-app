<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');



$stockcode = ($_GET['ss']);
if ($stockcode == "") $stockcode = "AAA";


/*
$curl = curl_init();

curl_setopt($curl, CURLOPT_HTTPHEADER, array('accept: application/json', 'Authorization: Bearer '.$SETTING->apikey));

curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($curl, CURLOPT_URL, 'http://api.vics.com.vn/ext/Stocks/'.$stockcode);


$result = curl_exec($curl);
if ($result === FALSE) {
    echo "cCURL ERROR";
} else {
	$aaa = json_decode($result);
}
*/

$sql = "SELECT * FROM StockPrice WHERE stockCode = '$stockcode'";
$db = $DB->query($sql);

if($rs = mysqli_fetch_array($db)){

	$priceBasic = floatval($rs['priceBasic'])/1000;
	$priceCurrent = floatval($rs['priceCurrent'])/1000;

	$priceCurrent = number_format((float)($priceCurrent),2);

	$change = $priceCurrent - $priceBasic;
	$change = number_format((float)($change),2);
	$changePercent = number_format((float)(floatval($change / $priceBasic) * 100),1);

	if ($change > 0)  $arrow = "up-green";
	if ($change < 0)  $arrow = "down-red";
	if ($change == 0)  $arrow = "yellow-nochange";

	echo "<span>$priceCurrent</span> <div class='$arrow'> $change ($changePercent%)</div> ";

	
}





?>
