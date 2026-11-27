<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

$id = intval($_GET['id']);
$idc = intval($_GET['idc']);
$template_name = $cateinfo['template_name'];
if ($template_name)
    $tpl = new TemplatePower($CONFIG['template_dir'] . "/$template_name.htm");
else
    $tpl = new TemplatePower($CONFIG['template_dir'] . "/article.htm");
$tpl->prepare();


$tpl->assignGlobal("dir_path", $dir_path);
$tpl->assignGlobal("site_address", $site_address);


$tpl->assignGlobal("pathpage", '<div class="crumb"> <div class="grid"> <a href="/' . $lang_dir . '"> '. $langLabel['_home'] .' </a> <i class="fa fa-angle-right"></i> ' . Get_Main_Cat_Name_path($idc) . '</div></div>');


langsite();
$tpl->assignGlobal("cat_name", $rs_cat['name']);
if (strlen($rs_cat['content']) > 10) $tpl->assignGlobal("cat_content", $rs_cat['content']);


$article = new dbArticle();

if (!$id) {

    CatList();
} else {
    ItemDetail();
}

$tpl->printToScreen();



function CatList() {

    global $DB, $tpl, $cache_image_path, $article, $dir_path, $idc, $SETTING;

    $db = $article->itemList($idc, 10);

    $tpl->newBlock("newsCat");

    $i = 0;
    $ids = "0";
    foreach ($db as $rs) {        if (!is_array($rs)) { continue; }

        if ($rs['id_article'] > 0) {
            $ids .= ",$rs[id_article]";
            $tpl->newBlock("news_list");
            
            
            $tpl->assign(array(
                'id' => $rs['id_article'],
                'name' => $rs['name'],
                'author_name' => $rs['author_name'],
                'author_info' => $rs['author_info'],
                
                'intro' => strip_tags($rs['intro']),
                'newsdate' => date("H:i d/m/Y", $rs['ngay_dang']),
                'content' => ($rs['content']),
                'link_detail' => $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']
            ));
            if ($rs['image'])
                $tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(200, 200, $dir_path . '/' . $rs['image']) . '" alt="' . $rs['name'] . '" width="100%" />');
            
            
            $i++;
            
            
        }
    }
    $tpl->assign("newsCat.page", $db['pages']);

}

function ItemDetail() {

    global $DB, $article, $id, $idc, $tpl, $dir_path, $cache_image_path;

    $tpl->newBlock("newsDetail");

    $rs = $article->itemDetail($id);
    if ($rs) {
        $tpl->assign(array(
            'name' => $rs['name'],
            'author_name' => $rs['author_name'],
            'author_info' => $rs['author_info'],
            'intro' => $rs['intro'],
            'content' => $rs['content'],
        ));

        if ($rs['showdate'] == 1)
            $tpl->assign("createdate", '<div class="createdate">' . date('d/m/Y H:i', $rs['ngay_dang']) . '</div>');

        $newsold = $article->item_other($idc, $id);
        if (count($newsold) > 0) {
            foreach ($newsold as $rse) {                if (!is_array($rse)) { continue; }

                if ($rse['id_article'] > 0) {
                    $tpl->newBlock("newsold_list");
                    $tpl->assign("name", $rse['name']);
                    $tpl->assign("author_name", $rse['author_name']);
                    $tpl->assign("author_info", $rse['author_info']);
                    $tpl->assign("link_detail", $dir_path . "/" . url_process::getUrlCategory($rse['id_category']) . $rse['url']);
                }
            }
        }
    }
}



?>