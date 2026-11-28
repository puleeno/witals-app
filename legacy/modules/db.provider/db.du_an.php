<?php

// error_reporting(E_ALL);
defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

class dbDuAn {

    public function itemList($idc, $limit = 6) {
        global $DB, $SETTING, $dir_path, $_GET;
        $idc = intval($idc);
        
        $qr = $param[0];
        $lv = intval($param[1]);
        $pv = intval($param[2]);
        $dt = intval($param[3]);
        $pd = intval($param[4]);
        
        
        $data = array();
        //$sql = "SELECT d.*,l.name as tenlinhvuc  FROM du_an d,linhvuc l WHERE d.id_linhvuc = l.id_linhvuc AND d.active=1 AND (id_category IN (" . Category::getParentId($idc) . ") OR groupcat LIKE '%:" . $idc . ":%') ORDER BY d.thu_tu DESC, d.name ASC";
        
        //echo $sql; 
        
        
        $sql = "SELECT * FROM du_an  WHERE active=1 AND (id_category IN (" . Category::getParentId($idc) . ") OR groupcat LIKE '%:" . $idc . ":%') ORDER BY thu_tu DESC, name ASC";
        
        $db1 = paging::pagingFonten($_GET['p'], $dir_path . "/" . url_process::getUrlCategory($idc), $sql, 8, $limit);
        while ($rs = mysqli_fetch_array($db1['db']))
            $data[] = $rs;
        
        $data['pages'] = $db1['pages'];
        return $data;
    }
    
    
    public function itemFilter($param,  $limit = 60) {
        global $DB, $SETTING, $dir_path, $_GET;
        
        
        $qr = $param[0];
        $lv = intval($param[1]);
        $pv = $param[2];
        $dt = intval($param[3]);
        $pd = intval($param[4]);
        
        $keywords = clean_value($qr);
        $keywords = trim(strtolower(RemoveSign($keywords)));        
        
        $strWhere = "";
        if (strlen($qr) > 0) 
            $strWhere .= " AND d.name_unsign like '%$keywords%' ";
        
        if ($lv > 0) 
            $strWhere .= " AND d.id_linhvuc = $lv ";
        
        if (strlen($pv) > 0) 
            $strWhere .= " AND d.provinceid = $pv ";
        
        if ($dt > 0) {
            $db_dt = $DB->query("SELECT * FROM price_range WHERE id = $dt");
            if ($rs_dt = mysqli_fetch_array($db_dt)){
                $dt_min = intval($rs_dt['min_value']);
                $dt_max = intval($rs_dt['max_value']);
                $strWhere .= " AND d.dientich >= $dt_min AND d.dientich <= $dt_max ";
            }
            
        }
        if ($pd > 0) $strWhere .= " AND d.vatlieu_phukien like '%,$pd%' ";
        
        $data = array();
        $sql = "SELECT d.*,l.name as tenlinhvuc  FROM du_an d,linhvuc l WHERE d.id_linhvuc = l.id_linhvuc AND d.active=1 $strWhere ORDER BY d.thu_tu DESC, d.name ASC";
        
        //echo $sql; 
        
        $db1 = paging::pagingFonten($_GET['p'], $dir_path . "/danh-sach-du-an", $sql, 8, $limit);
        while ($rs = mysqli_fetch_array($db1['db']))
            $data[] = $rs;
        
        $data['pages'] = $db1['pages'];
        return $data;
    }

    public function itemDetail($id) {
        global $DB, $SETTING;
        $id = intval($id);
        
        $sql = "SELECT *  FROM du_an WHERE active=1 AND id_duan = $id";
        $db = $DB->query($sql);
        $rs = mysqli_fetch_array($db);
        return $rs;
    }

    public function item_other($idc, $ids) {
        global $DB, $SETTING;
        $idc = intval($idc);
        $sql = "SELECT * FROM du_an WHERE active = 1 AND  (id_category IN(" . Category::getParentId($idc) . ") OR groupcat LIKE '%:" . $idc . ":%') AND id_duan NOT IN ($ids) ORDER BY id_duan DESC LIMIT 0,8";
        $db = $DB->query($sql);
$data = array();
        while ($rs = mysqli_fetch_array($db)) 
            $data[] = $rs;
        
        return $data;
    }
    
    public function item_new($idc, $id) {
        global $DB, $SETTING;
        $idc = intval($idc);
        $sql = "SELECT * FROM du_an WHERE active = 1 AND  (id_category IN(" . Category::getParentId($idc) . ") OR groupcat LIKE '%:" . $idc . ":%') AND id_duan > $id ORDER BY id_duan DESC LIMIT 0,8";
        $db = $DB->query($sql);
$data = array();
        while ($rs = mysqli_fetch_array($db)) 
            $data[] = $rs;
        
        return $data;
    }
    
    public function item_old($idc, $id) {
        global $DB, $SETTING;
        $idc = intval($idc);
        $sql = "SELECT * FROM du_an WHERE active = 1 AND  (id_category IN(" . Category::getParentId($idc) . ") OR groupcat LIKE '%:" . $idc . ":%') AND id_duan < $id ORDER BY id_duan DESC LIMIT 0,8";
        $db = $DB->query($sql);
$data = array();
        while ($rs = mysqli_fetch_array($db)) 
            $data[] = $rs;
        
        return $data;
    }    

    
    public function getProvince($provinceid) {
        global $DB, $SETTING;
        
        $sql = "SELECT * FROM vn_province WHERE provinceid = $provinceid";
        $db = $DB->query($sql);
        $rs = mysqli_fetch_array($db);
        return $rs['name'];
    }
    
    
    

}

?>