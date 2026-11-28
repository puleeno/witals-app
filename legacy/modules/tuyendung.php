<?php

//Develop Team: Binh Minh group
//binhminhgroup@gmail.com

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

if ($_POST['code'] == 'save') {
    error_reporting(0);
    $id_tuyendung = compile_post('id_tuyendung');
    $a = array();
    $a['name'] = compile_post('td_name');
    $a['phone'] = compile_post('td_phone');
    $a['email'] = compile_post('td_email');
    $a['address'] = compile_post('td_address');
    $a['message'] = compile_post('td_message');
    $a['subject'] = compile_post('td_subject');
    $a['createdate'] = time() + $CONFIG['time_offset'];
    $a['xem'] = 0;
    $a['url'] = $site_address . $id_tuyendung;

    if ($_FILES['td_file']['size']) {

        require ("legacy/lib/upload.php");
        $fi = File::uploadfile('td_file');
        $a['file'] = $CONFIG['upload_media_path'] . $fi['file'];
    }

    $b = $DB->compile_db_insert_string($a);
    $sql = "INSERT INTO tuyendung_request (" . $b['FIELD_NAMES'] . ") VALUES (" . $b['FIELD_VALUES'] . ")";

    $db = $DB->query($sql);
    if ($db)
        echo '<div class="mess-box-alert">Hồ sơ của bạn đã được gửi thành công !</div>';
    else
        echo '<div class="mess-box-alert red">Lỗi không gửi được, bấm F5 để làm lại !</div>';
}else {

    $id = intval($_GET['id']);
    $idc = intval($_GET['idc']);
    $template_name = $cateinfo['template_name'];
    if ($template_name)
        $tpl = new TemplatePower($CONFIG['template_dir'] . "/$template_name.htm");
    else
        $tpl = new TemplatePower($CONFIG['template_dir'] . "/tuyendung.htm");
    $tpl->prepare();

    langsite();

    $tpl->assignGlobal("dir_path", $dir_path);
    $tpl->assignGlobal("site_address", $site_address);
    $tpl->assignGlobal("slideshow", slidechild());

    $tpl->assignGlobal("pathpage", '<div class="grid"><div class="crumb"><a href="/' . $lang_dir . '"><i class="fa fa-home"></i></a> <i class="fa fa-angle-right"></i> ' . Get_Main_Cat_Name_path($idc) . '</div><div class="c5"></div></div>');




    $tpl->assignGlobal("menutab", menutab($root_idc));

    $tpl->assignGlobal("catname", $rs_cat['name']);
    $tpl->assignGlobal("catlink", $dir_path . '/' . $rs_cat['url']);

    if (strlen($rs_cat['intro']) > 7)
        $tpl->assignGlobal("catintro", '<div style="padding:5px 0px">' . $rs_cat['intro'] . '</div>');
    if (strlen($rs_cat['content']) > 7)
        $tpl->assignGlobal("catcontent", '<div style="padding:5px 0px">' . $rs_cat['content'] . '</div>');

    /*
      include_once("legacy/modules/left_right_col.php");
      $leftcol = left_right_col();
      $tpl->assignGlobal("leftcol", $leftcol);

      $tpl->assignGlobal("slideshow", slidechild());
     */


    include_once("legacy/modules/db.provider/db.tuyendung.php");
    $objTD = new dbTuyendung();

    $tuyendung = new clsTuyenDung();


    $tpl->printToScreen();
}


class clsTuyenDung {

    private $numberpage = 8;
    private $numberitempage = 15;

    public function __construct() {
        global $DB, $tpl, $dir_path, $idc, $id, $cache_image_path, $rs_cat, $root_idc;

        if (!$id) {
            if (Category::checkChildCat($idc)) {

                $this->catList();
            } else {

                $this->itemList();
            }
        } else {
            $this->itemDetail();
        }
    }

    public function catList() {

        global $DB, $tpl, $cache_image_path, $objTD, $dir_path, $idc, $SETTING, $rs_cat;

        $db = Category::getChildCat($idc);
        $tpl->newBlock("catList");

        $i = 0;
        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }


            $tpl->newBlock("catItem");
            $tpl->assign("name", $rs['name']);

            $tpl->assign("content", $rs['content']);
            $tpl->assign("intro", $rs['intro']);

            
			if ($rs['image'])
                $tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(500,350, $dir_path . '/' . $rs['image'], true) . '" alt="' . $rs['name'] . '" width="100%"/>');			

            if ($i % 2 == 0)
                $tpl->newBlock("imgleft");
            else
                $tpl->newBlock("imgright");

            $tpl->assign(array(
                'name' => $rs['name'],
                'content' => $rs['content'],
            ));
			
			$tpl->assign("link", $dir_path . '/' . $rs['url']);
			
            if ($rs['image'])
                $tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(400, 400, $dir_path . '/' . $rs['image'], true) . '" alt="' . $rs['name'] . '" width="100%"/>');
            $i++;
        }
    }

    public function itemList() {

        global $DB, $tpl, $cache_image_path, $objTD, $dir_path, $idc, $SETTING, $rs_cat, $template_name;

        $db = $objTD->newsList($idc);

        $tpl->newBlock("itemList");

        $i = 0;

        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }


            if (intval($rs['id_tuyendung'])) {
                $i++;
                $tpl->newBlock("newsItem");
                $tpl->assign(array(
                    'name' => $rs['name'],
                    'ngayhethan' => date("d/m/Y" ,$rs['ngayhethan']),
                    'noilamviec' => $rs['noilamviec'],
                    
                    'tencongty' => ($rs['tencongty']),
                    'link_detail' => $dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url']
                ));
/*
                if ($rs['image'])
                    $tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(380, 240, $dir_path . '/' . $rs['image'], true) . '" alt="' . $rs['name'] . '" width="100%"/>');
 
 */
            }
        }

        $tpl->assignGlobal("pages", $db['pages']);
    }

    public function itemDetail() {

        global $DB, $objTD, $id, $idc, $tpl, $dir_path, $cache_image_path, $site_address, $rs_cat;

        $tpl->newBlock("itemDetail");
        $rs = $objTD->newsDetail($id);

        if ($rs) {
            $tpl->assign(array(
                'name' => $rs['name'],
                'intro' => $rs['intro'],
                'content' => $rs['content'],
                'content2' => $rs['content2'],
            ));


            $tpl->assign("link_detail", $site_address . "/" . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
            if ($rs['image'])
            $tpl->assign("image", '<img  src="' . $cache_image_path . resizeimage1(190, 105, $dir_path . '/' . $rs['image'], true) . '" alt="' . $rs['name'] . '" />');
            
            
            if ($rs['description'])
                $tpl->assign("description", strip_tags($rs['description']));
            else
                $tpl->assign("description", strip_tags($rs['intro']));
            $tpl->assign("create_date", date('c', $rs['ngay_dang']));

            $tpl->assign("link_detail", $site_address . "/" . url_process::getUrlCategory($rs['id_category']) . $rs['url']);
        }

        $news_other = $objTD->news_other($idc,$rs["id_tuyendung"]);
        foreach ($news_other as $rse) {            if (!is_array($rse)) { continue; }

            if ($rse['id_tuyendung'] > 0) {
                $tpl->newBlock("lstOther");
                $tpl->assign("name", $rse['name']);
                $tpl->assign("intro", strstrim(strip_tags($rs['intro']), 15));
                $tpl->assign("link_detail", $dir_path . "/" . url_process::getUrlCategory($rse['id_category']) . $rse['url']);
                if ($rse['image'])
                    $tpl->assign("image", '<img  src="' . $cache_image_path . cropimage(280, 220, $dir_path . '/' . $rse['image'], true) . '" alt="' . $rse['name'] . '" class="image-news" width="100%"/>');
                
            }
        }
    }

}

?>