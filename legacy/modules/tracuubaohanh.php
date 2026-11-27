<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');
$tpl = new TemplatePower($CONFIG['template_dir'] . "/tracuubaohanh.htm");
$tpl->prepare();
langsite();
error_reporting(E_ERROR);
$id = intval($_GET['id']);

$idc = intval($_GET['idc']);


$tpl->assignGlobal("dir_path", $dir_path);


$tpl->assignGlobal("pathpage", '<div class="crumb"> <div class="grid"> <a href="/' . $lang_dir . '"> '. $langLabel['_home'] .' </a> <i class="fa fa-angle-right"></i> Tra cứu bảo hành </div></div>');

$tpl->assignGlobal("slideshow", slidechild());

$tpl->assignGlobal("catname", $rs_cat['name']);
$tpl->assignGlobal("catlink", $dir_path . '/' . $rs_cat['url']);

if (strlen($rs_cat['intro']) > 7)
    $tpl->assignGlobal("catintro", '<div style="padding:5px 0px">' . $rs_cat['intro'] . '</div>');
if (strlen($rs_cat['content']) > 7)
    $tpl->assignGlobal("catcontent", '<div style="padding:5px 0px">' . $rs_cat['content'] . '</div>');


$imei = clean_value($_GET['imei']);
if (strlen($imei) > 1){
	$tpl->newBlock("Baohanh");	
	$sql = "SELECT * FROM baohanh  WHERE (imei = '$imei' OR mobile LIKE '%$imei%' OR dien_thoai LIKE '%$imei%') ";	
	$db = $DB->query($sql);
	while($rs = mysqli_fetch_array($db)){
		
		$tpl->newBlock("lstBaohanh");	
		
		$tpl->assign("imei",$rs['imei']);
		$tpl->assign("ngay_bh",$rs['ngay_bh']);
		$tpl->assign("makh",$rs['makh']);
		$tpl->assign("so_phieu_ban",$rs['so_phieu_ban']);
		$tpl->assign("ma_hang",$rs['ma_hang']);
		

	}
	
}



$daily = new Daily();
$daily->_int();
$tpl->printToScreen();

class Daily {
    public function _int() {
        global $idc,$tpl;
		$id_pro = intval($_GET['i']);
		
		$tpl->assignGlobal("listProvince", $this->listProvince());
		
		$this->lstDaily($id_pro);
    }
	function lstDaily($id_pro){
		global $idc,$lang,$tpl,$DB,$langLabel;
		
		if($lang == 'en'){
			$dklang = " AND c.lang='en' ";
		}else{
			$dklang = " AND c.lang<>'en' ";
		}
		$prv = intval($_GET['prv']);
		$dst = intval($_GET['dst']);
		$mapkw = clean_value($_GET['mapkw']);
		
		$strWhere = " ";
		if ($prv > 0 ) $strWhere .= " AND provinceid = $prv ";
		if ($dst > 0 ) $strWhere .= " AND districtid = $dst ";
		if ($mapkw != "" ) $strWhere .= " AND name LIKE '%$mapkw%' ";
		
		$sql = "SELECT * FROM daily  WHERE active=1 AND baohanh=1  $strWhere ORDER BY thu_tu DESC, name DESC";
		
		$db = $DB->query($sql);
		
		$tpl->assign("record_count", mysqli_num_rows($db) );
		
		while($rs = mysqli_fetch_array($db)){
			$tpl->newBlock("lstDaily");	
			$tpl->assign("dailyname",$rs['name']);
			$tpl->assign("address",$rs['address']);
			
			$tpl->assign("phone",$rs['phone']);
			$tpl->assign("email",$rs['email']);
			
			$tpl->assign("latitude",$rs['latitude']);
			$tpl->assign("longitude",$rs['longitude']);
		}
	}
	
    public function listProvince() {
		global $DB;
		
		$result_str = '';
		
		$sql = "SELECT * FROM vn_province ORDER BY provinceid";
		$db = $DB->query($sql);
		while($rs = mysqli_fetch_array($db) ) {
			$result_str.= '<option value="' . $rs['provinceid'] . '">' . $rs['name'] . '</option>';
			
		}
		return  $result_str;
    }	
	
	
	

}



?>