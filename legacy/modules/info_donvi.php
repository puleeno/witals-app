<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');



$tpl = new TemplatePower($CONFIG['template_dir'] . "/info_donvi.htm");

$tpl->prepare();
langsite();
$id = intval($_GET['id']);

$idc = intval($_GET['idc']);
$tpl->assignGlobal("pathpage", '<div class="crumb"><a href="/' . $lang_dir . '"><i class="fa fa-home"></i></a> <i class="fa fa-angle-right"></i> ' . Get_Main_Cat_Name_path($idc) . '</div>');

$tpl->assignGlobal("dir_path", $dir_path);

$tpl->assignGlobal("slideshow", slidechild());



$info = new InfoDonVi();

$info->_int();

$tpl->printToScreen();

class InfoDonVi {

    public function _int() {
        global $idc, $id, $rs_cat, $tpl, $cache_image_path, $root_idc;
        
        if (intval($id)>0){
            $this->itemDetail($id);
        }else{
            $tpl->newBlock("catDetail");
            
            $tpl->assign(array(
                'name' => $rs_cat['name'],
                'content' => $rs_cat['content'],
                'intro' => $rs_cat['intro']
            ));
            
            $tpl->assignGlobal("menutab", menutab($root_idc));
			
			$this->otherCat($root_idc,$idc);
            
        }
        
        
    }


    private function itemDetail($id) {

        global $DB, $tpl, $lang, $dir_lang, $dir_path, $cache_image_path;

        $sql = "SELECT * FROM info_donvi WHERE active=1 AND id_info = $id";
        
        $db = $DB->query($sql);
        if($rs = mysqli_fetch_array($db)) {
            
            $tpl->newBlock("itemDetail");
            $tpl->assign(array(
                'name' => $rs['name'],
                'title' => $rs['title'],
                'intro' => $rs['intro'],
                'content' => $rs['content']
            ));
            
            $this->sliderImage($rs['id_info']);
            

        }
    }
    
    private function sliderImage($id_value) {
        global $DB, $tpl, $dir_path, $cache_image_path;
        $id_value = intval($id_value);
        
        include_once (__DIR__ . '/manager/dataprovider/db_sys_image.php');
        $sysImage = new dbSysImage();
        $db = $sysImage->getList('', "info", "id_info", $id_value, "");
        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }

            if ($rs['image']) {
                $tpl->newBlock("slider_image");

                $tpl->assign("image", $cache_image_path . cropimage(400, 250, $dir_path . '/' . $rs['image']));
                $tpl->assign("bigimage", $dir_path . '/' . $rs['image']);
                
                
            }
        }

    }
	
	private function otherCat($idc,$id_cat) {

        global $DB, $tpl, $dir_path, $cache_image_path;
        $sql = "SELECT * FROM category WHERE active=1 AND parentid = $idc AND id_category <> $id_cat ORDER BY thu_tu";
        $db = $DB->query($sql);
        while ($rs = mysqli_fetch_array($db)) {
            
            $tpl->newBlock("otherCategory");
            
            $tpl->assign(array(
                'name' => $rs['name'],
                'content' => strstrim(strip_tags($rs['content']),103),
                'link' => '/' . $rs['url'],
            ));
            if ($rs['image'])
                $tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(400,400, $dir_path . '/' . $rs['image'], true) . '" alt="' . $rs['name'] . '" width="100%"/>');
        }
    }
    
    

}

?>