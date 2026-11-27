<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

class vnLocation {

    //vn_province
    function getProvinceById($id) {
        global $DB;
        //$id = intval($id);
        $sql = "SELECT * FROM vn_province WHERE provinceid ='" . $id . "'";
        $db = $DB->query($sql);
        $rs = mysqli_fetch_array($db);
        return $rs;
    }

    function getProvinceList() {
        global $DB;
        $sql = "SELECT * FROM vn_province ORDER BY provinceid";
        $db = $DB->query($sql);
$data = array();
        while ($row = mysqli_fetch_array($db)) 
            $data[] = $row;
        return $data;
    }

    
    function getProvinceBDS($provinceid) {
        global $DB;
        $sql = "SELECT count(*) as soluongtin FROM product WHERE provinceid = '$provinceid'";
        $db = $DB->query($sql);
        $rs = mysqli_fetch_array($db);
        return intval($rs['soluongtin']);
    }
    
    
    //vn_district
    function getDistrictById($id) {
        global $DB;
        //$id = intval($id);
        $sql = "SELECT * FROM vn_district WHERE districtid ='" . $id . "'";
        $db = $DB->query($sql);
        $rs = mysqli_fetch_array($db);
        return $rs;
    }

    function getDistrictList($provinceid) {
        global $DB;
        //$provinceid = intval($provinceid);
        $sql = "SELECT * FROM vn_district WHERE provinceid = '$provinceid'";
        $db = $DB->query($sql);
$data = array();
        while ($row = mysqli_fetch_array($db)) 
            $data[] = $row;
        return $data;

    }

    //vn_ward
    function getWardById($id) {
        global $DB;
        //$id = intval($id);
        $sql = "SELECT * FROM vn_ward WHERE wardid ='" . $id . "'";
        $db = $DB->query($sql);
        $rs = mysqli_fetch_array($db);
        return $rs;
    }

    function getWardList($districtid) {
        global $DB;
        //$districtid = intval($districtid);
        $sql = "SELECT * FROM vn_ward WHERE districtid = '$districtid'";
        $db = $DB->query($sql);
$data = array();
        while ($row = mysqli_fetch_array($db)) 
            $data[] = $row;
        return $data;

    }


}

?>