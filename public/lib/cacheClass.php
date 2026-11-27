<?php 

error_reporting(0);
defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );

class clsCache{
	
	private $dir_cache= 'cache/';
	
	
	public function __construc($dir_cache){
			
	}	
	
	function writeCache($content,$filename){
		global $dir_cache;
		$dir_cache=$dir_cache; 
		$File = 'cache/'.$filename.".cache"; 
		$Handle = fopen($File, 'w');
		$Data = $content;
		$ok=fwrite($Handle, $Data); 

		fclose($Handle); 	
	}
	
	function deleteCache(){
		
	
	}
	
}


?>