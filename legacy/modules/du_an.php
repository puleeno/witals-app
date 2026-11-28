<?php



defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');



$id = intval($_GET['id']);

$idc = intval($_GET['idc']);

$template_name = $cateinfo['template_name'];

if ($template_name)

    $tpl = new TemplatePower($CONFIG['template_dir'] . "/$template_name.htm");

else

    $tpl = new TemplatePower($CONFIG['template_dir'] . "/du_an.htm");

$tpl->prepare();



$tpl->assignGlobal("dir_path", $dir_path);

$tpl->assignGlobal("site_address", $site_address);



$root_cat = Category::categoryInfo($idc);

$catname = $root_cat['name'];

$tpl->assignGlobal("slideshow", slidechild());



$tpl->assignGlobal("pathpage", '<div class="crumb"><div class="grid"><a href="/' . $lang_dir . '"> '. $langLabel['_home'] .' </a> <i class="fa fa-angle-right"></i> ' . Get_Main_Cat_Name_path($idc) . '</div><div class="c5"></div></div>');



langsite();



$objDuan = new dbDuAn();







$tpl->assignGlobal("catname", $rs_cat['name']);

$tpl->assignGlobal("catlink", $dir_path . '/' . $rs_cat['url']);



if (strlen($rs_cat['intro']) > 7)

	$tpl->assignGlobal("catintro", '<div style="padding:5px 0px">' . $rs_cat['intro'] . '</div>');

if (strlen($rs_cat['content']) > 7)

	$tpl->assignGlobal("catcontent", '<div style="padding:5px 0px">' . $rs_cat['content'] . '</div>');





if ($rs_cat['image'])

	$tpl->assignGlobal("catimage", '<img  src="' .  $rs_cat['image'] . '" />');



if ($rs_cat['image'])

	$tpl->assignGlobal("catimageurl",$rs_cat['image']);



if (strlen($rs_cat['content_text']) > 7)

	$tpl->assignGlobal("content_text", $rs_cat['content_text']);







$DA = new DuAn;



$tpl->printToScreen();



class DuAn{

	function __construct(){

		global $DB, $tpl, $dir_path, $cache_image_path, $SETTING,$objDuan,$idc, $id;

		if($id>0){

			$tpl->newBlock("detail");

			$this->detail($id);

		}else{

			$tpl->newBlock("lstcat");

			$this->lstDuAn($idc);

		}

	}

	

	function detail($id){

		global $DB, $tpl, $dir_path, $cache_image_path, $SETTING,$objDuan,$idc;

		$rs = $objDuan->itemDetail($id);

		$tpl->assign("name",$rs['name']);

		$tpl->assign("sophong",$rs['sophong']);

		$tpl->assign("dientich",$rs['dientich']);

		$tpl->assign("intro",$rs['intro']);

		if($rs['imageadv'])

			$tpl->assign("imageadv", '<img src="' . $cache_image_path . resizeimage1(576, 428, $dir_path . '/' . $rs['imageadv']) . '"  width="100%" alt="' . $rs['name'] . '" style="display:block">');

		

		if( $rs['fileimage'])			

			$tpl->assign("fileimage", '<img src="' . $cache_image_path . resizeimage1(576, 428, $dir_path . '/' . $rs['fileimage']) . '"  width="100%" alt="' . $rs['name'] . '" style="display:block">');

		

		$tienich = json_decode($rs['tienich']);

		foreach($tienich as $ti){

			$tpl->newBlock("tienich");

			$tpl->assign("tienich",$ti);

		}

		

		$this->sliderImageDA($rs['id_duan']);

	}

	

	function lstDuAn($idc){

		global $DB, $tpl, $dir_path, $cache_image_path, $SETTING,$objDuan;

		$db = $objDuan->itemList($idc);

		foreach($db as $rs){		    if (!is_array($rs)) { continue; }


			if($rs['id_duan']>0){

				$tpl->newBlock("lstDuAn");

				$tpl->assign("name",$rs['name']);

				$tpl->assign("sophong",$rs['sophong']);

				$tpl->assign("dientich",$rs['dientich']);

				if ($rs['image']) {

					$image = $cache_image_path . cropimage(576,387, $dir_path . '/' . $rs['image']);

					$tpl->assign("image", '<img  src="' . $image . '" alt="' . $rs['name'] . '" width="100%">');

				}

				

				$tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']);

			}

		}

	}	

	function sliderImageDA($id_duan) {

        global $DB, $tpl, $dir_path, $cache_image_path;

        $id_value = intval($id_duan);

        include_once (__DIR__ . '/manager/dataprovider/db_sys_image.php');

        $sysImage = new dbSysImage();

        $db = $sysImage->getList('', "du_an", "id_duan", $id_value, "");

		

        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }


            if ($rs['image']) {

				$tpl->newBlock("lstimage");

				$tpl->assign("image",'<img src="'.$cache_image_path . cropimage(372, 236, $dir_path . '/' . $rs['image']).'" width="100%" />');
				$image = $dir_path."/".$rs['image'];

					$image = str_replace("//","/",$image);

				$tpl->assign("bigimage",$image);

				$tpl->assign("name",$rs['name']);

				

				

            }

        }

	

    }

}



?>