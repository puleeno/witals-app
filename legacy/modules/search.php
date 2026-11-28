<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');
$tpl = new TemplatePower($CONFIG['template_dir'] . "/info_search.htm");
$tpl->prepare();

langsite();



include_once __DIR__ . '/manager/dataprovider/search_content.php';
$objSearch = new search_content();

$rawKeywords = (string)($_GET['qr'] ?? ($_GET['keyword'] ?? ''));
$keywords = clean_value($rawKeywords);
$tpl->assignGlobal("keywords", $rawKeywords);
$keywords = trim(strtolower($objSearch->RemoveSign($keywords)));
$tpl->assignGlobal("h_keywords", $keywords);

$db = array();

if (strlen($keywords) < 3)
    $tpl->newBlock("short_keyword");
else
    $db = $objSearch->getListItem($keywords, 1);

if (!is_array($db) || count($db['data'] ?? []) <= 0)
    $tpl->newBlock("no_result");



foreach (($db['data'] ?? array()) as $rs) {    if (!is_array($rs)) { continue; }

    if (intval($rs['id_search'])>0){
        
        $tpl->newBlock("item_list");
        $tpl->assign("name", $rs['name']);
        $tpl->assign("intro", strstrim(strip_tags($rs['intro']), 100));
        $tpl->assign("link_detail", $dir_path . '/' . url_process::getUrlCategory($rs['id_category']).$rs['url']);
        if ($rs['image'])
            $tpl->assign("image", '<div style="border: solid 1px #EEE;"><img width="100%" src="' . $cache_image_path . resizeimage1(250, 250, $dir_path . '/' . $rs['image']) . '" alt="' . $rs['name'] . '" /></div>');

        $tpl->assign("cat_path", Get_Main_Cat_Name_path($rs['id_category']));    
    }
}

$tpl->assignGlobal("pages", $db['pages'] ?? '');

if ($keywords != '') {
    $tpl->newBlock("keywords");
    $tpl->newBlock("highlight_list");
}

$tpl->printToScreen();
?>