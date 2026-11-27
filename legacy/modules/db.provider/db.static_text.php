<?php 
// Nguyen Binh
// suongmumc@gmail.com 
// error_reporting(E_ALL);
defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );
class dbStaticText{
	function getList($code_name){
		global $DB;
                $data = array();
                $strWhere = "";
                if ($code_name)  $strWhere  = " AND code_name='$code_name'";
		$sql = "SELECT * FROM static_text WHERE 1=1 $strWhere";
		$db = $DB->query($sql);
                while ($rs = mysqli_fetch_array($db))
                     $data[] = $rs;
		return $data;
	}
	function getById($id_static){
		global $DB;
		$sql = "SELECT * FROM static_text WHERE id_static = $id_static";
		$db = $DB->query($sql);
                $rs = mysqli_fetch_array($db);
		return $rs;
	}
        

}
?>