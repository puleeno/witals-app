<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');
$tpl = new TemplatePower($CONFIG['template_dir'] . "/info.htm");
$tpl->prepare();
langsite();
$id = intval($_GET['id']);

$idc = intval($_GET['idc']);




$tpl->assignGlobal("pathpage", '<div class="crumb"><div class="grid"><a href="/' . $lang_dir . '"><i class="fa fa-home"></i></a> <i class="fa fa-angle-right"></i> ' . Get_Main_Cat_Name_path($idc) . '</div><div class="c5"></div></div>');

$tpl->assignGlobal("dir_path", $dir_path);


$tpl->assignGlobal("slideshow", slidechild());


$tpl->assignGlobal("catname", $rs_cat['name']);
$tpl->assignGlobal("catcontent", $rs_cat['content']);

  include_once("legacy/modules/left_right_col.php");
  $leftcol = left_right_col();
  $tpl->assignGlobal("leftcol", $leftcol);
  


$info = new Info;

$info->_int();

$tpl->printToScreen();

class Info {
    public function _int() {
        global $idc,$id;
		if($id > 0){
			  $this->content($id,$idc);
		}else{
        	$this->getContent($idc);
		}

    }
    private function getContent($idc) {

        global $DB, $tpl, $dir_path, $dir_lang, $lang;

        $sql = "SELECT * FROM info WHERE active=1 AND id_category = $idc ORDER BY thu_tu LIMIT 0,1";
        $db = $DB->query($sql);

        $sub_idc = $idc;

        if ($rs = mysqli_fetch_array($db)) {

            $this->content($rs['id_info'], $rs['id_category']);

            $sub_idc = $rs['id_category'];

        } else {

            $sql1 = "SELECT * FROM category WHERE active=1 AND parentid = $sub_idc ORDER BY thu_tu LIMIT 0,1";

            $db1 = $DB->query($sql1);

            if ($rs1 = mysqli_fetch_array($db1)) {

                $this->getContent($rs1['id_category']);

            }
        }
    }

    private function content($id, $idc) {

        global $DB, $tpl, $lang, $dir_lang, $dir_path;

        $sql = "SELECT * FROM info WHERE active=1 AND id_info = $id";

        $db = $DB->query($sql);

        if ($rs = mysqli_fetch_array($db)) {

            $tpl->newBlock("info");

            $tpl->assign(array(

                'name' => $rs['name'],

                'content' => $rs['content']

            ));

            $this->otherArticle($rs['id_info'], $idc);

        }

    }



    private function otherArticle($id, $idc) {

        global $DB, $tpl, $dir_path;

        $sql = "SELECT * FROM info WHERE active=1 AND (id_category IN(" . Category::getParentId($idc) . " ) OR groupcat LIKE '%:" . $idc . ":%') AND id_info<>$id ORDER BY thu_tu ASC, name ASC";

        $db = $DB->query($sql);
        if (mysqli_num_rows($db) > 0)            
            $tpl->newBlock("otherarticle");
        
        while ($rs = mysqli_fetch_array($db)) {

            $tpl->newBlock("otherarticle_item");

            $tpl->assign("name", $rs['name']);

            $tpl->assign("link_detail", $dir_path . "/" . url_process::getUrlCategory($rs['id_category']) . $rs['url']);

        }

    }
}



?>