<?php 


defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );

class dbStatic{

	static function getInWhere($inwhere){

		global $DB,$language;

		$sql = "SELECT * FROM static WHERE inwhere='$inwhere' $language";

		$db = $DB->query($sql);

$data = array();
                while($rs = mysqli_fetch_array($db))
                    $data[] = $rs;	

		return $data;

	}

        

	static function copyright(){

		global $DB,$language;

		$sql = "SELECT * FROM static WHERE inwhere='copyright' $language";

		$db = $DB->query($sql);

		$rs = mysqli_fetch_array($db);

		return $rs;

	}
	
	function page404(){

		global $DB,$language;

		$sql = "SELECT * FROM static WHERE inwhere='page404' $language";

		$db = $DB->query($sql);

		$rs = mysqli_fetch_array($db);

		return $rs;

	}
	
	
	static function textFooter(){

		global $DB,$language;

		$sql = "SELECT * FROM static WHERE inwhere='footer' $language";

		$db = $DB->query($sql);

		$rs = mysqli_fetch_array($db);

		return $rs;

	}

	function muahangtuxa(){

		global $DB;

		$sql = "SELECT * FROM static WHERE inwhere='muahangtuxa'";

		$db = $DB->query($sql);

		$rs = mysqli_fetch_array($db);

		return $rs;

	}
	function addressphone(){

		global $DB;

		$sql = "SELECT * FROM static WHERE inwhere='addressphone'";

		$db = $DB->query($sql);

		$rs = mysqli_fetch_array($db);

		return $rs;

	}
	
	function generalproduct(){

		global $DB;

		$sql = "SELECT * FROM static WHERE inwhere='generalproduct'";

		$db = $DB->query($sql);

		$rs = mysqli_fetch_array($db);

		return $rs;

	}


	function fanpage(){

		global $DB;

		$sql = "SELECT * FROM static WHERE inwhere='fanpage'";

		$db = $DB->query($sql);

		$rs = mysqli_fetch_array($db);

		return $rs;

	}

	function textChapnhanthanhtoan(){

		global $DB;

		$sql = "SELECT * FROM static WHERE inwhere='thanhtoan'";

		$db = $DB->query($sql);

		$rs = mysqli_fetch_array($db);

		return $rs;

	}

}

?>