<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

class dbSysImage{


    public function insertImage($data){
        global $DB;
        if ($data){
            $b=$DB->compile_db_insert_string($data);
            $sql="INSERT INTO sys_image (".$b['FIELD_NAMES'].") VALUES (".$b['FIELD_VALUES'].")";
            $DB->query($sql);
        }
    }

    public function updateImage($id,$data){
        global $DB;
        if ($data){
            $b=$DB->compile_db_update_string($data);
            $sql="UPDATE member SET ".$b." WHERE id = $id";
            $DB->query($sql);
        }
    }


    public function deleteById($id) {
        global $DB;
        $sql = "DELETE FROM sys_image WHERE id =" . $id . "";
        $db = $DB->query($sql);
        $rs = mysqli_fetch_array($db);
        return $rs;
    }

    public function deleteMulti($id_category, $table_name, $id_item, $id_value,$type_code) {
        global $DB;

        $strWhere = "";
        if (intval($id_category) > 0)
            $strWhere .=" AND id_category = $id_category";

        if ($type_code)
            $strWhere .=" AND type_code = '$type_code'";

        if ($table_name != '' && $id_item != '')
            $strWhere .=" AND table_name = '$table_name' AND id_item = '$id_item' AND id_value = $id_value";

        $sql = "DELETE FROM sys_image WHERE 1=1 $strWhere";
        $db = $DB->query($sql);
        $rs = mysqli_fetch_array($db);
        return $rs;
    }

    public function getList($id_category, $table_name = '', $id_item = '', $id_value = '' ,$type_code = '') {
        global $DB;
        $strWhere = "";
        if (intval($id_category) > 0)
            $strWhere .=" AND id_category = $id_category";

        if ($table_name != '' && $id_item != '')
            $strWhere .=" AND table_name = '$table_name' AND id_item = '$id_item' AND id_value = $id_value";

        if ($type_code)
            $strWhere .=" AND type_code = '$type_code'";

        $sql = "SELECT * FROM sys_image WHERE active = 1 $strWhere ORDER BY thu_tu ";
        $db = $DB->query($sql);
        while ($row = mysqli_fetch_array($db))
            $data[] = $row;
        return $data;
    }


}

?>