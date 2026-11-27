<?php



	$counter=array();

	$day=date("j");

	

	if (!isset($_COOKIE['membervisted']) && $_COOKIE['membervisted']!='visisted'){

		$sql="SELECT * FROM today";

		$db=$DB->query($sql);

		if($rs=mysqli_fetch_array($db)){

				

			if($day==$rs['day']){

	

				$DB->query("UPDATE today SET value=value+1, day='".$day."'");

				$DB->query("UPDATE count SET value=value+1");

			}else{

				$DB->query("UPDATE yesterday SET value=$rs[value]");

				$DB->query("UPDATE today SET value=1, day='".$day."'");

			}

		}

		setcookie('membervisted','visisted',time()+7200);

		//$ok='1';

	}	

	

	$sql="SELECT * FROM today";

	$db=$DB->query($sql);

	if($rs=mysqli_fetch_array($db)){

		$counter['today']=number_format($rs['value'],0,'.','.');

	}

	$sql="SELECT * FROM yesterday";

	$db=$DB->query($sql);

	if($rs=mysqli_fetch_array($db)){

		$counter['yesterday']=number_format($rs['value'],0,'.','.');

	}

	$sql="SELECT * FROM count";

	$db=$DB->query($sql);

	if($rs=mysqli_fetch_array($db)){

		$counter['total']=number_format($rs['value'],0,'.','.');

	}

	



 

?>