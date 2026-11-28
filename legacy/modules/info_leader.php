<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');


$tpl = new TemplatePower($CONFIG['template_dir'] . "/info_leader.htm");

$tpl->prepare();
langsite();
$id = intval($_GET['id']);

$idc = intval($_GET['idc']);
$tpl->assignGlobal("pathpage", '<div class="crumb"><a href="/' . $lang_dir . '"><i class="fa fa-home"></i></a> <i class="fa fa-angle-right"></i> ' . Get_Main_Cat_Name_path($idc) . '</div>');

$tpl->assignGlobal("dir_path", $dir_path);

$tpl->assignGlobal("slideshow", slidechild());

$tpl->assignGlobal("menutab", menutab($root_idc));

$tpl->assignGlobal("catname", $rs_cat['name']);




$info = new InfoLeader();

$info->_int();

$tpl->printToScreen();

class InfoLeader {

    public function _int() {
        global $idc, $id, $rs_cat, $tpl, $cache_image_path, $root_idc;

        $this->itemList($idc,2);
        $this->itemList($idc,1);
        $this->itemList($idc,0);

        $this->itemDetail($idc,2);
        $this->itemDetail($idc,1);
        $this->itemDetail($idc,0);

		$this->otherCat($root_idc,$idc);

    }

    private function itemList($idc,$level=0) {
        global $DB, $tpl, $lang, $dir_lang, $dir_path, $cache_image_path;

        $level = intval($level);

        $sql = "SELECT * FROM info_leader WHERE active=1 AND id_category = $idc AND level = $level";
        $db = $DB->query($sql);
        $i=0;
        while ($rs = mysqli_fetch_array($db)) {

            $tpl->newBlock("itemList$level");
            $i++;
            $tpl->assign("stt", $level .'_'. $i );
            $tpl->assign(array(
                'name' => $rs['name'],

                'dob' => $rs['dob'],
                'position' => $rs['position'],
                'branch' => $rs['branch'],

                'intro' => $rs['intro'],
                'content' => $rs['content']
            ));

            if ($rs['image'])
                $tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(400, 400, $dir_path . '/' . $rs['image'], true) . '" alt="' . $rs['name'] . '" width="100%"/>');

        }
    }

    private function itemDetail($idc,$level=0) {
        global $DB, $tpl, $lang, $dir_lang, $dir_path, $cache_image_path;

        $level = intval($level);

        $sql = "SELECT * FROM info_leader WHERE active=1 AND id_category = $idc AND level = $level";
        $db = $DB->query($sql);
        $i=0;
        while ($rs = mysqli_fetch_array($db)) {

            $tpl->newBlock("itemDetail");
            $i++;
            $tpl->assign("stt", $level .'_'. $i );
            $tpl->assign(array(
                'name' => $rs['name'],

                'dob' => $rs['dob'],
                'position' => $rs['position'],
                'branch' => $rs['branch'],

                'intro' => $rs['intro'],
                'content' => $rs['content']
            ));

            if ($rs['image'])
                $tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(400, 400, $dir_path . '/' . $rs['image'], true) . '" alt="' . $rs['name'] . '" width="100%"/>');

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