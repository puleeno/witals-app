<?php

//Develop Team: Binh Minh group
//binhminhgroup@gmail.com
//error_reporting(E_ERROR);

defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );
function catHome(){
	global $DB, $dir_path, $cache_image_path,$CONFIG, $language;
	$tpl1 = new TemplatePower($mobile_temp . $CONFIG['template_dir']."/cathome.htm");
	$tpl1->prepare();
	langsite($tpl1);
	$id 	= intval ($_GET['id']);
	$idc 	= intval ($_GET['idc']);	
	
	
	
	
	$sql = "SELECT * FROM category WHERE active=1 AND vitri LIKE '%:inhome:%' $language ORDER BY thu_tu ASC, name ASC";

	$db_cat = $DB->query($sql);
	
	
        
	while($rs_cat = mysqli_fetch_array($db_cat)){
		
		$tpl1->assign("cat_name", $rs_cat['name']);
		$tpl1->assign("cat_link", $dir_path . '/' . $rs_cat['url']);
		$tpl1->assign("cat_intro", $rs_cat['intro']);		
		
		$db = dbMenu::listSubCat($rs_cat['id_category']);
        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }

			
            $tpl1->newBlock("cell_cat_home");
                
            $tpl1->assign("catname",$rs['name']);
            $tpl1->assign("catintro", strstrim(strip_tags($rs['intro']),30)) ;
            
            
            $tpl1->assign("link",$dir_path.'/'.$rs['url']);
            if($rs['image'])
                $tpl1->assign("catimage",'<img src="'.$cache_image_path.cropimage(600,400,$dir_path.'/'.$rs['image']).'" alt="'.$rs['name'].'" width="100%" >');
            
            if($rs['imageadv'])
                $tpl1->assign("catimageadv",'<img src="' . $rs['imageadv'] .'" width="100%" >');
	
			
		}
		
            

            
            
            
                
	}
	
	return $tpl1->getOutputContent();

}



?>