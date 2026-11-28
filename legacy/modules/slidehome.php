<?php

error_reporting(E_ERROR);
defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

function slideShow() {
    global $DB, $dir_path, $cache_image_path, $CONFIG, $logo, $language, $mobile_temp;
    $tpl1 = new TemplatePower($mobile_temp . $CONFIG['template_dir'] . "/slidehome.htm");

    $tpl1->prepare();
    langsite($tpl1);
    $tpl1->assignGlobal('dir_path', $dir_path);
    $id = intval($_GET['id']);

    $idc = intval($_GET['idc']);
    $tpl1->newBlock("show_slideshow");

    $sql = "SELECT * FROM category WHERE active = 1 $language AND vitri LIKE '%:slideshow:%' ORDER BY thu_tu ASC, name ASC LIMIT 0,1";

    $db = $DB->query($sql);

    if ($rs = mysqli_fetch_array($db)) {

        $dblg = $logo->logoList($rs['id_category']);

        foreach ($dblg as $rslg) {            if (!is_array($rslg)) { continue; }


            if ($rslg['image']) {

                $tpl1->newBlock("slide_home_item");

				$tpl1->assign("name", $rslg['name']);

                if (strlen($rslg['comment']) > 7) {
                    $tpl1->assign("description", $rslg['comment'] );
                }

                $tpl1->assign("image", '<img src="' . $rslg['image'] . '" alt="' . $rslg['name'] . '" width="100%" style="display:block"/>');
                $tpl1->assign("link", $rslg['link']);
                $tpl1->assign("target", $rslg['target']);
            }
        }
    }


    $tpl1->newBlock("slidemobile");

    $sql = "SELECT * FROM category WHERE active = 1 $language AND vitri LIKE '%:slidemobile:%' ORDER BY thu_tu ASC, name ASC LIMIT 0,1";

    $db = $DB->query($sql);

    if ($rs = mysqli_fetch_array($db)) {

        $dblg = $logo->logoList($rs['id_category']);

        foreach ($dblg as $rslg) {            if (!is_array($rslg)) { continue; }


            if ($rslg['image']) {

                $tpl1->newBlock("slidemobile_item");

				$tpl1->assign("name", $rslg['name']);

                if (strlen($rslg['comment']) > 7) {
                    $tpl1->assign("description", $rslg['comment'] );
                }

                $tpl1->assign("image", '<img src="' . $rslg['image'] . '" alt="' . $rslg['name'] . '" width="100%" style="display:block"/>');
                $tpl1->assign("link", $rslg['link']);
                $tpl1->assign("target", $rslg['target']);
            }
        }
    }

    return $tpl1->getOutputContent();
}

function slidechild() {

    global $DB, $dir_path, $language, $cache_image_path, $CONFIG, $logo,$catname;

    $tpl1 = new TemplatePower($CONFIG['template_dir'] . "/slidehome.htm");
    $tpl1->prepare();
    $tpl1->assignGlobal("dir_path", $dir_path);
    $id = intval($_GET['id']);
    $idc = intval($_GET['idc']);
    $tpl1->newBlock("slidechild");
	$tpl1->assignGlobal("catname",$catname);
    $sql = "SELECT * FROM sys_image WHERE id_category = $idc";
    $db_img = $DB->query($sql);
	$i=0;
	while ($rslg = mysqli_fetch_array($db_img)) {
		if ($rslg['image']) {
			if ($i==1)
				$tpl1->newBlock("pic_mobile");
			else
				$tpl1->newBlock("piccat");

			$srcimg = str_replace("//", "/", $dir_path . '/' . $rslg['image']);
			$tpl1->assign("image", '<img src="' . $srcimg . '" alt="' . $rslg['name'] . '" width="100%"/>');
			$tpl1->assign("name", $rslg['name']);
			$tpl1->assign("link", $rslg['link']);
			$tpl1->assign("target", $rslg['target']);
		}

		$i++;
	}

	/*
    if (mysqli_num_rows($db_img) < 2 && mysqli_num_rows($db_img) >= 1) {
        if ($rslg = mysqli_fetch_array($db_img)) {
            $tpl1->newBlock("piccat");
            if ($rslg['image']) {
                $srcimg = str_replace("//", "/", $dir_path . '/' . $rslg['image']);
                $tpl1->assign("image", '<img style="display:block" src="' . $srcimg . '" alt="' . $rslg['name'] . '" width="100%"/>');
            }
        }
    } else if (mysqli_num_rows($db_img) >= 2) {
        $tpl1->newBlock("slidecat");
        while ($rslg = mysqli_fetch_array($db_img)) {
            if ($rslg['image']) {
                $i++;
                $tpl1->newBlock("list_slide");
                $srcimg = str_replace("//", "/", $dir_path . '/' . $rslg['image']);
                $tpl1->assign("image", '<img src="' . $srcimg . '" alt="' . $rslg['name'] . '" width="100%"/>');
                $tpl1->assign("link", $rslg['link']);
                $tpl1->assign("target", $rslg['target']);
            }
        }
    } else {
        $tpl1->newBlock("nopic");
    }
	*/

	/*
    $root_idc = Category::get_root_category_id($idc);
    $root_cat = Category::categoryInfo($root_idc);
    $tpl1->assignGlobal("cat_name", $root_cat['name']);
    $tpl1->assignGlobal("cat_link", $dir_path . '/' . $root_cat['url']);
	*/
    return $tpl1->getOutputContent();
}

?>