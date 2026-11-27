<?php 
// Nguyen Binh
// suongmumc@gmail.com
// error_reporting(E_ALL);
defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );

class dbDownload{
	public function selectList($idc,$limit=30){
		global $DB, $SETTING,$dir_path,$_GET;
		$idc = intval($idc);

		$data = array();
		$sql = "SELECT * FROM download WHERE active=1 AND (id_category IN (".Category::getParentId($idc).") OR groupcat LIKE '%:".$idc.":%') ORDER BY thu_tu DESC, name ASC";
		$db1 = paging::pagingFonten($_GET['p'],$dir_path."/".url_process::getUrlCategory($idc),$sql,8,$limit);
		
		while($rs = mysqli_fetch_array($db1['db'])){
		  	$data[] = $rs;	
		}
		$data['pages'] = $db1['pages'];
		return $data;
	}
	
	public function selectById($id){
		global $DB, $SETTING;
		$id = intval($id);
		$sql = "SELECT * FROM download WHERE id_download = $id";
		$db = $DB->query($sql);
		$rs = mysqli_fetch_array($db);
		return $rs;
	}
	
}

?>