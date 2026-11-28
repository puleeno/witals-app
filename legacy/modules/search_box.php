<?php
//error_reporting(E_ALL);

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');


function search_box() {

    global $DB, $dir_path, $cache_image_path, $CONFIG, $logo, $lang_dir, $lang;
    $tpl1 = new TemplatePower("templates/search_box.htm");
    $tpl1->prepare($tpl1);
    
    $tpl1->newBlock("search_box");
    
    $tpl1->assignGlobal("dir_path", $dir_path);
    langsite($tpl1);
    $tpl1->assignGlobal("lang", $lang);
    $tpl1->assignGlobal("lang_dir", $lang_dir);
    
    $qr = $_GET['qr'];
    $lv = intval($_GET['lv']);
    $pv = intval($_GET['pv']);
    $dt = intval($_GET['dt']);
    $pd = intval($_GET['pd']);

    $tpl1->assign("qr", $qr);

    
    $sbox = new clsSearchBox();
    $sbox->_int();

    $tpl1->assign("linhvuc_str", $sbox->bindLinhVuc($lv));
    $tpl1->assign("province_str", $sbox->bindProvince($pv));
    $tpl1->assign("dientich_str", $sbox->bindDienTich($dt));
    $tpl1->assign("product_str", $sbox->bindMaterial($pd));

    return $tpl1->getOutputContent();
}

function search_box_mobile() {

    global $DB, $dir_path, $cache_image_path, $CONFIG, $logo;
    $tpl1 = new TemplatePower("templates/search_box.htm");
    $tpl1->prepare($tpl1);
    
    $tpl1->newBlock("search_box_mobile");
    
    $tpl1->assignGlobal("dir_path", $dir_path);
    langsite($tpl1);
    $tpl1->assignGlobal("lang", $lang);
    $tpl1->assignGlobal("lang_dir", $lang_dir);
    
    $qr = $_GET['qr'];
    $lv = intval($_GET['lv']);
    $pv = intval($_GET['pv']);
    $dt = intval($_GET['dt']);
    $pd = intval($_GET['pd']);

    
    /*$tpl1->newBlock('spnoibat');*/
    $tpl1->assign("qr", $qr);

    
    $sbox = new clsSearchBox();
    $sbox->_int();

    $tpl1->assign("linhvuc_str", $sbox->bindLinhVuc($lv));
    $tpl1->assign("province_str", $sbox->bindProvince($pv));
    $tpl1->assign("dientich_str", $sbox->bindDienTich($dt));
    $tpl1->assign("product_str", $sbox->bindMaterial($pd));

    return $tpl1->getOutputContent();
}


class clsSearchBox{
    
    
    public function _int() {
        global $tpl, $id, $pid, $dir_path;
        
    }    
    
	
    public function bindLinhVuc($selectedid) {
        global $DB, $language;
        $result_str = '';

        $sql = "SELECT * FROM linhvuc WHERE active = 1 $language ORDER BY thu_tu,id_linhvuc";
        
        $db = $DB->query($sql);
        while ($rs = mysqli_fetch_array($db)) {
            if ($selectedid == $rs['id_linhvuc'])
                $result_str.= '<option value="' . $rs['id_linhvuc'] . '" selected>' . $rs['name'] . '</option>';
            else
                $result_str.= '<option value="' . $rs['id_linhvuc'] . '">' . $rs['name'] . '</option>';
        }
        return $result_str;
    }
    
    public function bindDienTich($selectedid) {
        global $DB, $language;
        $result_str = '';

        $sql = "SELECT * FROM price_range WHERE active = 1 $language ORDER BY thu_tu,id";
        $db = $DB->query($sql);
        while ($rs = mysqli_fetch_array($db)) {
            if ($selectedid == $rs['id'])
                $result_str.= '<option value="' . $rs['id'] . '" selected>' . $rs['name'] . '</option>';
            else
                $result_str.= '<option value="' . $rs['id'] . '">' . $rs['name'] . '</option>';
        }
        return $result_str;
    }    
    

    public function bindProvince($selectedid) {
		global $lang;
		
        include_once('legacy/modules/db.provider/vnLocation.php');
        
        $objProvince = new vnLocation();

        $db = $objProvince->getProvinceList();
        $result_str = '';

		
		
        foreach ($db as $rs) {            if (!is_array($rs)) { continue; }

			if ($lang == 'en')
				$province_name  = RemoveSign($rs['name']);			
			else
				$province_name  = $rs['name'];			
			
            if ($selectedid == $rs['provinceid'])
                $result_str.= '<option value="' . $rs['provinceid'] . '" selected>' . $province_name  . '</option>';
            else
                $result_str.= '<option value="' . $rs['provinceid'] . '">' . $province_name . '</option>';
        }
        return $result_str;
    }
    
    
    public function bindMaterial($selectedid) {
        global $DB, $language;
        $result_str = '';

        $sql = "SELECT * FROM product_material WHERE active = 1 $language ORDER BY thu_tu,id";
        $db = $DB->query($sql);
        while ($rs = mysqli_fetch_array($db)) {
            if ($selectedid == $rs['id'])
                $result_str.= '<option value="' . $rs['id'] . '" selected>' . $rs['name'] . '</option>';
            else
                $result_str.= '<option value="' . $rs['id'] . '">' . $rs['name'] . '</option>';
        }
        return $result_str;
        
    }    
}
    

?>