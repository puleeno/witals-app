<?php
defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );
	/*-------------------------------------------------------------------------*/
	//
	// Redirect using HTTP commands, not a page meta tag.
	//
	/*-------------------------------------------------------------------------*/
	
	function boink_it($url)
	{
		// Ensure &amp;s are taken care of
		
		$url = str_replace( "&amp;", "&", $url );
		@flush();
		echo("<html><head><meta http-equiv='refresh' content='0; url=$url'></head><body></body></html>");
		exit();
		exit();
	}
	function boink_it3($url)
	{
		// Ensure &amp;s are taken care of
		
		$url = str_replace( "&amp;", "&", $url );
		@flush();
		echo("<html><head><meta http-equiv='refresh' content='3; url=$url'></head><body></body></html>");
		exit();
		exit();
	}	
	
	//////Location
	function redir($to) {
		echo "<SCRIPT LANGUAGE=\"JavaScript\">\n";
		echo "window.location = '$to'\n";
		echo "</SCRIPT>";
	}    
	/*-------------------------------------------------------------------------*/
	//
	// Create a random 8 character password
	//
	/*-------------------------------------------------------------------------*/
	
	function make_password()
	{
		$pass = "";
		$chars = array(
			"1","2","3","4","5","6","7","8","9","0",
			"a","A","b","B","c","C","d","D","e","E","f","F","g","G","h","H","i","I","j","J",
			"k","K","l","L","m","M","n","N","o","O","p","P","q","Q","r","R","s","S","t","T",
			"u","U","v","V","w","W","x","X","y","Y","z","Z");
	
		$count = count($chars) - 1;
	
		srand((double)microtime()*1000000);

		for($i = 0; $i < 8; $i++)
		{
			$pass .= $chars[rand(0, $count)];
		}
	
		return($pass);
	}
    
    function is_number($number="")
    {
    
    	if ($number == "") return -1;
    	
    	if ( preg_match( "/^([0-9]+)$/", $number ) )
    	{
    		return $number;
    	}
    	else
    	{
    		return "";
    	}
    }

?>