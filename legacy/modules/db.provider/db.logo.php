<?php

// Nguyen Binh

// suongmumc@gmail.com

// error_reporting(E_ALL);

defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );

class dbLogo{

	function logo(){

		global $DB, $language;

		$sql = "SELECT l.*, c.name AS catename FROM logo AS l, category AS c WHERE l.id_category = c.id_category AND c.vitri LIKE '%:logosite:%' AND l.active=1 $language ORDER BY l.thu_tu ASC, l.name ASC LIMIT 0,1";

		$db = $DB->query($sql);

		$rs = mysqli_fetch_array($db);

		return $rs;

	}



	function banner(){

		global $DB, $language;

		$sql = "SELECT l.*, c.name AS catename FROM logo AS l, category AS c WHERE l.id_category = c.id_category AND c.vitri LIKE '%:banner:%' AND l.active=1 $language ORDER BY l.thu_tu ASC, l.name ASC LIMIT 0,1";

		$db = $DB->query($sql);

		$rs = mysqli_fetch_array($db);

		return $rs;

	}



	function advhome1(){

		global $DB;

		$sql = "SELECT l.*, c.name AS catename FROM logo AS l, category AS c WHERE l.id_category = c.id_category AND c.vitri LIKE '%:advhome1:%' AND l.active=1 AND c.active=1 ORDER BY l.thu_tu ASC, l.name ASC ";

		$db = $DB->query($sql);

$data = array();
		while($rs = mysqli_fetch_array($db)){

			$data[] = $rs;

		}

		return $data;

	}

	function advhome2(){

		global $DB;

		$sql = "SELECT l.*, c.name AS catename FROM logo AS l, category AS c WHERE l.id_category = c.id_category AND c.vitri LIKE '%:advhome2:%'  AND l.active=1 AND c.active=1 ORDER BY l.thu_tu ASC, l.name ASC ";

		$db = $DB->query($sql);

$data = array();
		while($rs = mysqli_fetch_array($db)){

			$data[] = $rs;

		}

		return $data;

	}
	function partner(){

		global $DB, $language;

		$sql = "SELECT l.*, c.name AS catename FROM logo AS l, category AS c WHERE l.id_category = c.id_category AND c.vitri LIKE '%:partner:%'  AND l.active=1 $language AND c.active=1 ORDER BY l.thu_tu ASC, l.name ASC ";

		$db = $DB->query($sql);

$data = array();
		while($rs = mysqli_fetch_array($db)){

			$data[] = $rs;

		}

		return $data;

	}

	function logoscrollhome(){

		global $DB;

		$sql = "SELECT l.*, c.name AS catename FROM logo AS l, category AS c WHERE l.id_category = c.id_category AND c.vitri LIKE '%:logoscrollhome:%'  AND l.active=1 AND c.active=1 ORDER BY l.thu_tu ASC, l.name ASC ";

		$db = $DB->query($sql);

$data = array();
		while($rs = mysqli_fetch_array($db)){

			$data[] = $rs;

		}

		return $data;

	}



	function logoList($idc, $limit = 5){

		global $DB	;

		$idc = intval($idc);

                $strLimit = "";

                if ($limit) $strLimit = "LIMIT 0,$limit";



		$sql = "SELECT * FROM logo WHERE active=1 AND (id_category IN(".Category::getParentId($idc).") OR groupcat LIKE '%:".$idc.":%') ORDER BY thu_tu DESC, name ASC $strLimit";

		$db = $DB->query($sql);

$data = array();
		while($rs = mysqli_fetch_array($db)){

			 $data[] = $rs;

		}

		return $data;

	}





}

?>