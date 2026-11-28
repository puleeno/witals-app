<?php

//Nguyen Van Binh

//suongmumc@gmail.com



defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );

$tpl = new TemplatePower($CONFIG['template_dir']."/photo.htm");

$tpl->prepare();

$id 	= intval ($_GET['id']);

$idc 	= intval ($_GET['idc']);

langsite();

$tpl->assignGlobal("pathpage",Get_Main_Cat_Name_path($idc));

$tpl->assignGlobal("dir_path",$dir_path);


$catinfo = Category::categoryInfo($idc);
$tpl->assignGlobal("catname", $catinfo['name']);
$tpl->assignGlobal("catlink",$dir_path.'/'.$catinfo['url']);
if ($catinfo['image']){
			$tpl->assignGlobal("image", '<img  src="' . $cache_image_path . resizeimage(1200, 600, $dir_path . '/' . $catinfo['image']) . '" alt="' . $catinfo['name'] . '" style="max-width:100%" />');
		}
if(strlen($catinfo['content']) > 7){
	$tpl->assignGlobal("catcontent", '<div style="padding:10px 0px">'.$catinfo['content'].'</div>');
}

$photo = new Photo;





$tpl->printToScreen();



class Photo{

	public function __construct(){

		global $DB, $tpl, $dklang, $dir_path,$idc,$id;		

		

		

		$sql = "SELECT * FROM category WHERE active=1 AND parentid=$idc";

		$db = $DB->query($sql);

		if(mysqli_num_rows($db)>1){

			$tpl->newBlock("album");

			$this->album($idc);

		}else{

			$tpl->newBlock("photocell");

			$this->cellPhoto($idc,$id);

		}

		

		//$tpl->newBlock("photoslide");

	}

	private function cellPhoto($idc,$id=0){

		global $DB, $tpl, $dklang, $dir_path,$SETTING,$cache_image_path;		
		$idc = intval ($idc);
		$sql = "SELECT * FROM photo WHERE active=1 AND id_category IN(".Category::getParentId($idc).") ORDER BY id_photo DESC";
		$db = paging::pagingFonten($_GET['p'],$dir_path."/".url_process::getUrlCategory($idc),$sql,8,$SETTING->photopage);
		$i=0;
	
		$thumb ='';
		while($rs = mysqli_fetch_array($db['db'])){

			if($rs['image']){
				$i++;				
				$tpl->newBlock("cellPhoto")	;
				if($i==1) $tpl->assign("marginleft0","");
				$tpl->assign("name",$rs['name']);
				$tpl->assign("content",$rs['content']);
				if($id == $rs['id_photo']) $tpl->assign("loadphoto","thumb1");
					$image = $dir_path."/".$rs['image'];
					$image = str_replace("//","/",$image);
					$tpl->assign("image",'<img src="'.$cache_image_path.resizeimage1(690,518,$image).'"  alt="'.$rs['name'].'" title="'.$rs['name'].'" width="100%" />');
					$thumb .='<li style="padding-right:20px;"><img src="'.$cache_image_path.resizeimage1(216,162,$image).'"  alt="'.$rs['name'].'" title="'.$rs['name'].'"  style="border:solid 1px #d6d6d6; "/></li>';
			}
			if($i>=4) $i=0;
		}
		$tpl->assign("photocell.thumb",$thumb);
		$tpl->assignGlobal("pages",$db['pages']);

	}

	

	private function album($idc){
			global $DB, $tpl, $language, $dir_path,$SETTING,$cache_image_path;
			$idc = intval($idc);
			$sql = "SELECT * FROM category WHERE parentid = $idc ORDER BY thu_tu ASC, name ASC ";
			$db = paging::pagingFonten($_GET['p'],$dir_path."/".url_process::getUrlCategory($idc),$sql,8,intval($SETTING->albumpage));
			$i=0;
			while($rs = mysqli_fetch_array($db['db'])){
				$i++;
				$tpl->newBlock("listAlbum");
				$tpl->assign("num_photo",$this->countPhoto($rs['id_category']));
				if($i==1) $tpl->assign("marginleft0","marginleft0");
				if($rs['image']){
					 $tpl->assign("image", '<img  src="'.$cache_image_path.  cropimage(420, 266, $dir_path . '/' . $rs['image']) . '" alt="' . $rs['name'] . '" width="100%" />');
				}

				$tpl->assign("name",$rs['name']);
				$tpl->assign("link",$dir_path."/".$rs['url']);
				$tpl->assign("mobile-break",'<div class="mobile-break c30"></div>');
				if($i%2==0) $tpl->assign("tab-break",'<div class="tab-break c30"></div>');
				if($i%3==0) $tpl->assign("pad-break",'<div class="pad-break c30"></div>');
				if($i%3==0) $tpl->assign("pc-break",'<div class="pc-break c30"></div>');
			}
			
			$tpl->assignGlobal('pages',$db['pages']);
	}
	private function countPhoto($idc){
		global $DB;
		$sql = "SELECT * FROM photo WHERE active=1 AND id_category = $idc";
		$db = $DB->query($sql);
		return mysqli_num_rows($db);
	}
	

	

}

?>