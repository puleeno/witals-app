<?php 
// Nguyen Binh
// suongmumc@gmail.com
defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );	
    $id = intval($_GET['id']);
	$idc = intval($_GET['idc']);
	
	
	if(strlen($url_filename)>0 && intval($idc)>0 && intval($id)<1){
		header( "Location: ".$site_address.$dir_path."/index.php?page=404");
		exit;
	}
	if($page1==''){
		header( "Location: ".$site_address.$dir_path."/index.php?page=404");
	}
	
	if(($idc>0 || $id > 0) ){
		
		$ch = $getUrl->type_Info($cateinfo['data_type']);
		if($id > 0){
			$real_url = $getUrl->check_link($ch['tableitem'],$ch['id_item'],$id);
		}elseif($url_filename==''){
			$pg = $getUrl->check_page($uri);
			
			if($pg == 'page' || $pg =='trang'){
			
			}else{
				
				$real_url = $getUrl->check_link('category','id_category',0,$idc);
				
			}
		}
		if($pg == 'page' || $pg =='trang'){
		}else{
			if($uri != '/'.$real_url ){
				header( "HTTP/1.1 301 Moved Permanently" ); 
				header( "Location: ".$site_address.$dir_path."/".$real_url);
				exit;	
			}
		}
		
	}
	// end url - process 
?>