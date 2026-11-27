<?php 
// Nguyen Binh
// suongmumc@gmail.com
// error_reporting(E_ALL);

defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );
class dbProject{

	public function itemList($idc,$limit=6){
		global $DB, $SETTING,$dir_path,$_GET;
		$idc = intval($idc);
		$data = array();
		$sql = "SELECT * FROM du_an WHERE active=1 AND (id_category IN (".Category::getParentId($idc).") OR groupcat LIKE '%:".$idc.":%') ORDER BY thu_tu DESC, name ASC";
		$db1 = paging::pagingFonten($_GET['p'],$dir_path."/".url_process::getUrlCategory($idc),$sql,8,$limit);
		while($rs = mysqli_fetch_array($db1['db'])){
		  	$data[] = $rs;	
		}
		$data['pages'] = $db1['pages'];
		return $data;
	}

	public function itemDetail($id){
		global $DB, $SETTING;
		$id = intval($id);
		$sql = "SELECT * FROM du_an WHERE id_duan = $id";
		$db = $DB->query($sql);
		$rs = mysqli_fetch_array($db);
		return $rs;
	}

	
   public function itemOther($name=''){
		global $DB, $SETTING,$idc;
		$idc = intval($idc);
		if(strlen($name)>0){
			$sql = "SELECT * FROM du_an WHERE active=1 ORDER BY thu_tu DESC, name ASC LIMIT 10";
			$db = $DB->query($sql);
$data = array();
			while($rs = mysqli_fetch_array($db)){
				$data[] = $rs;
			}
		}
		return $data;
	}
}



?>