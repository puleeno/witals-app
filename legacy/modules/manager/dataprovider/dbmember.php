<?php

// Nguyen Binh
// suongmumc@gmail.com
// error_reporting(E_ALL);
defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

class dbMember {

    private $table = "member";
    private $id_item = "id_member";
    
    
    public function insertMember($data) {
        global $DB;
        if ($data) {
            $b = $DB->compile_db_insert_string($data);
            $sql = "INSERT INTO member (". $b['FIELD_NAMES'] .") VALUES (" . $b['FIELD_VALUES'] . ")";
            try {
                $DB->query($sql);
                return 1;
            } catch (Exception $ex) {
                return $ex;
            }
        }
        return 0;
    }
    
    public function updateMember($memberid,$data) {
        global $DB;
        $memberid =intval($memberid);
        if ($data) {
            $b=$DB->compile_db_update_string($data);
            $sql="UPDATE member SET ".$b." WHERE id_member = $memberid";
            try {
                $db=$DB->query($sql);
                return 1;
            } catch (Exception $ex) {
                return $ex;
            }
        }
        return 0;
    }    

    
        public function changePass($memberid,$oldpass,$newpass) {
        global $DB;
        $memberid =intval($memberid);
        $sql="UPDATE member SET password ='$newpass' WHERE id_member = $memberid AND password ='$oldpass'";
        try {
            $db=$DB->query($sql);
            return 1;
        } catch (Exception $ex) {
            return $ex;
        }
        return 0;
    }    

    
    
    public function checkExistedEmail($email) {
        global $DB, $SETTING;
        $id = intval($id);
        $sql = "SELECT * FROM member WHERE email = '$email'";
        $db = $DB->query($sql);
        $rs = mysqli_fetch_array($db);
        if (intval($rs['id_member']) > 0)
            return 1;
        return 0;
    }    
    
    public function checkExistedUserName($username) {
        global $DB, $SETTING;
        $id = intval($id);
        $sql = "SELECT * FROM member WHERE username = '$username'";
        $db = $DB->query($sql);
        $rs = mysqli_fetch_array($db);
        if (intval($rs['id_member']) > 0)
            return 1;
        return 0;
    }        
    
    public function checkMemberLogin($email,$password) {
        global $DB;
        $sql = "SELECT * FROM member WHERE active=1 AND (email = '$email' OR username='$email') AND password = '$password'";

        $db = $DB->query($sql);
        $rs = mysqli_fetch_array($db);
        return $rs;
    }        
    
    
    public function listMember($keyword) {
        global $DB, $SETTING, $dir_path, $_GET;
        $strWhere = "";
        if ($keyword != '')
            $strWhere .=" name LIKE '%" . $keyword . "%' ";
        
        $sql = "SELECT * FROM member WHERE 1=1 $strWhere ORDER BY id_member DESC";
        $db1 = paging::pagingFonten($_GET['p'], $dir_path ."/". url_process::getUrlCategory($idc), $sql, 8, $limit);
        while ($rs = mysqli_fetch_array($db1['db'])) {
            $data[] = $rs;
        }
        $data['pages'] = $db1['pages'];
        return $data;
    }

    public function memberDetail($id) {
        global $DB, $SETTING;
        $id = intval($id);
        $sql = "SELECT * FROM member WHERE id_member = $id";
        $db = $DB->query($sql);
        $rs = mysqli_fetch_array($db);
        return $rs;
    }
    
    
    public function summaryForMember($memberid, $ngay_bat_dau1, $ngay_bat_dau2, $product_type_id, $status) {
        global $DB;
        
        $data = array();
        $strWhere = "";
        $memberid = intval($memberid);
        if ($memberid > 0) {
            $strWhere .= " AND id_member = $memberid";
        }

        if ($ngay_bat_dau1 != '') {
            $ngay_bat_dau1 = string_to_microtime($ngay_bat_dau1);
            $strWhere .= " AND ngay_bat_dau >= $ngay_bat_dau1";
        }
        if ($ngay_bat_dau2 != '') {
            $ngay_bat_dau2 = string_to_microtime($ngay_bat_dau2);
            $strWhere .= " AND ngay_bat_dau <= $ngay_bat_dau2";
        }

        $product_type_id = intval($product_type_id);
        if ($product_type_id > 0)
            $strWhere .= " AND product_type_id = $product_type_id";
        
        if ($status == 'onsite') {
            $strWhere .= " AND ngay_ket_thuc >=" . time();
        }

        if ($status == 'expired') {
            $strWhere .= " AND ngay_ket_thuc <=" . time();
        }
        $sql = "SELECT * FROM product WHERE active=1 AND removed=0  $strWhere  ORDER BY id_product DESC";
        
        $db=$DB->query($sql);
        while ($rs = mysqli_fetch_array($db))
            $data[] = $rs;
        return $data;
    }

}

?>