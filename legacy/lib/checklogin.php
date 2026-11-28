<?php 



defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );

class member_info{

	function login($email,$password){

  		global $DB,$CONFIG;

		$sql="SELECT * FROM member WHERE (email='".$email."') AND (password='".$password."') AND active = 1";



		$db=$DB->query($sql);

		if($rs=mysqli_fetch_array($db)){

			$logintime = time()+$CONFIG['time_offset'];

				$_SESSION["session_memberemail"] = $rs['email'];				

				$_SESSION["session_member_id"] = $rs['id_member'];

				$_SESSION["session_member_logintime"] = $logintime;

				$_SESSION["membername"]=$rs['name'];

				$_SESSION["telephone"]=$rs['telephone'];

				//$_SESSION['password'] = $rs['password'];

				return true;

		}else{

			session_unset();

			@session_destroy();	

			return false;	

		}

  }

  function logout(){

	session_unset();

	@session_destroy();	

  }

  function checklogin(){

		global $_SESSION;

		if($_SESSION["session_memberemail"] && $_SESSION["session_member_id"])  {

			return true;	

		}else{

			return false;	

		}

  }

  function showLogin(){

	  echo '<script language="javascript">

		$(document).ready(function(){

			showlogin();

		});

	</script>'	;

  }

  function Get_Info_Member($id){

		global $DB;

		$id=intval($id);

		$sql="SELECT * FROM member WHERE id_member=$id"	;

		$db=$DB->query($sql);

		$a=array();

		if($rs=mysqli_fetch_array($db)){

			$a['name'] 		= $rs['name'];

			$a['telephone'] 	= $rs['telephone'];	

			$a['address'] 	= $rs['address'];

			$a['otherinfo'] 	= $rs['otherinfo'];	

			$a['chucvu'] 	= $rs['chucvu'];	

		}

		return $a;

	}

	function fill_main_show($idc){

		global $DB;

		$idc = intval($idc);

		$sql = "SELECT * FROM category WHERE id_category = $idc AND localnews=1";

		$db = $DB->query($sql);

	  	if(mysqli_num_rows($db) > 0){

			return $idc;	

		}else{

			$sql1 = "SELECT * FROM category WHERE active=1 AND id_category = $idc";

			$db1=$DB->query($sql1);

			if($rs1=mysqli_fetch_array($db1)){

				$sql2="SELECT *  FROM category WHERE active=1 AND id_category = $rs1[parentid] AND localnews=1"	;

				$db2=$DB->query($sql2);

				if($rs2=mysqli_fetch_array($db2)){

					return fill_main_show($rs2['id_category']);

				}

				

			}

		}

	}

	

	

	

}

?>