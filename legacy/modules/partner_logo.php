<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

function partner_logo() {
    global $DB, $dir_path, $cache_image_path, $CONFIG, $logo;
    $tpl1 = new TemplatePower($CONFIG['template_dir'] . "/partner_logo.htm");
    $tpl1->prepare();
    $partner = new dbLogo;
    $db = $partner->partner();
    $i = 0;
    if (count($db) > 0) {
        $tpl1->newBlock("partner");
        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }

            if ($rs['id_logo'] > 0) {
                $i++;
                $tpl1->newBlock("list_partner");
                if ($rs['image']) {
                    $tpl1->assign("image", '<a href="' . $rs['link'] . '" target="' . $rs['target'] . '"><img src="' . $cache_image_path . resizeimage1(320,210, $dir_path . '/' . $rs['image'],false) . '" width="100%"  alt="' . $rs['name'] . '"></a>');
                }
                $tpl1->assign("link", $rs['link']);
                $tpl1->assign("target", $rs['target']);
            }
        }
    }

    return $tpl1->getOutputContent();
}

?>