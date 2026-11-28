<?php
defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');
$idc = intval($_GET['idc']);
$id = intval($_GET['id']);
require_once('legacy/modules/db.provider/db.download.php');
$download = new dbDownload;

if ($_GET['code'] == 'download') {
    $dl = new Download($idc);
    $dl->act_download($id);
} else {
    $tpl = new TemplatePower($CONFIG['template_dir'] . "/download.htm");
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


    $dl = new Download($idc);
	if($id){
		$tpl->newBlock("downloadDetail");
		$dl->downloadDetail($id);
	}else{
    	$dl->list_download($idc);
	}
    $tpl->printToScreen();
}

class Download {

    function list_download() {

        global $DB, $idc, $tpl, $download, $dir_path;

        $dl = $download->selectList($idc, 14);
		$i=0;
        foreach ($dl as $rs) {            if (!is_array($rs)) { continue; }


            if ($rs['id_download'] > 0) {
				$i++;
                $tpl->newBlock("listDownload");
				if($i<=9) $tpl->assign("stt",'0'.$i);
				else  $tpl->assign("stt",$i);
                $tpl->assign(array(
                    'name' => $rs['name'],
                    'intro' => strstrim(strip_tags($rs['content']),40),
					'ngay_dang' => date('H:i | d/m/y',$rs['ngay_dang']),
					'catname' => $rs['catname'],
                    'link_download1' => $rs['files'],
                    'link_download' => $dir_path.'/index4.php?page=download&code=download&idc='.$rs['id_category'].'&id='.$rs['id_download'],
					'link_detail' =>$dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url'],
                    'id' => $rs['id_download'],
					'fileicon' => getIconFile($rs['files'])
                ));
            }
        }
		$tpl->assignGlobal("pages",$dl['pages']);
    }
	
	function downloadDetail($id){
		global $DB, $idc, $tpl, $download, $dir_path;

        $rs = $download->selectById($id);
		$tpl->assign(array(
			'name' => $rs['name'],
			'intro' => $rs['content'],
			'ngay_dang' => date('H:i | d/m/y',$rs['ngay_dang']),
			'catname' => $rs['catname'],
			'link_download1' => $rs['files'],
			'link_download' => $dir_path.'/index4.php?page=download&code=download&idc='.$rs['id_category'].'&id='.$rs['id_download'],
			'link_detail' =>$dir_path . '/' . url_process::getUrlCategory($rs['id_category']) . $rs['url'],
			'id' => $rs['id_download'],
			'fileicon' => getIconFile($rs['files'])
		));
	}
	
    function act_download($id) {

        global $DB, $download;

        $rs = $download->selectById($id);

        if ($rs['files'] == '' || empty($rs['files'])) {

            exit;
        }



        $filename = $rs['files']; //the file is 2 folder down. e.g. data/stack/bla.xlsx

        $file = $filename;

        $extension = end(explode('.', $filename));


        ini_set("display_errors", 1);

        //  echo $filename;
        //  echo "<br/>";
        //  echo $extension;
        //  echo filesize($filename);
        //  echo "<br/>";

        include('legacy/lib/upload_definitions.php');

        $mimeType = $mime_type_match[$extension];

        //  echo $mimeType;

        header('Content-Description: File Transfer');

        header('Content-Type: ' . $mimeType);

        header('Content-Disposition: attachment; filename=' . basename($file));

        header('Content-Transfer-Encoding: binary');

        header('Expires: 0');

        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

        header('Pragma: public');

        header('Content-Length: ' . filesize($file));

        ob_clean();

        flush();

        readfile($filename);

        exit;
    }

}

function getIconFile($filename){
	$file_ex = pathinfo($filename, PATHINFO_EXTENSION);	
	$fileicon = '/images/file.png';
	switch($file_ex){
		case 'doc':
			$fileicon = '/images/word.png';
			break;
		case 'docx':
			$fileicon = '/images/word.png';
			break;
		case 'xls':
			$fileicon = '/images/xls.png';
			break;
		case 'xlsx':
			$fileicon = '/images/xls.png';
			break;
		case 'pdf':
			$fileicon = '/images/pdf.png';
			break;
		case 'tpp':
			$fileicon = '/images/tpp.png';
			break;
		case 'tppx':
			$fileicon = '/images/tpp.png';
			break;
		default:
			$fileicon = '/images/file.png';
			break;
			
	}
	return $fileicon;
}
?>