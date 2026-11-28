<?php

//Nguyen Van Binh

//suongmumc@gmail.com



defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );

function manufacture(){

		global $DB, $dir_path, $cache_image_path,$CONFIG,$logo;
		$tpl1 = new TemplatePower($CONFIG['template_dir']."/manufacture.htm");
		$tpl1->prepare();
		
		$sql = "SELECT * FROM product_manufacture WHERE active=1 ORDER BY thu_tu DESC, name ASC";
		$db = $DB->query($sql);
		while($rs = mysqli_fetch_array($db)){
			$tpl1->newBlock("manufacture");
			$tpl1->assign("image",'<img src="'. $cache_image_path . resizeimage1(154, 55, $dir_path . '/' . $rs['image']).'" alt="'.$rs['name'].'" title="'.$rs['name'].'" class="image-manufacture" width="100%" >');	
			$tpl1->assign("link",$dir_path.'/hang/'.clean_url($rs['name']));
			
		}
		return $tpl1->getOutputContent();

}





?>