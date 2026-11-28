<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

class Street {

    function insertProvince($data) {
        global $DB, $tpl;
        if ($data) {
            $b = $DB->compile_db_insert_string($data);
            $sql = "INSERT INTO vn_province (" . $b['FIELD_NAMES'] . ") VALUES (" . $b['FIELD_VALUES'] . ")";
            $DB->query($sql);
        }
    }
    function updateProvince($id, $data) {
        global $DB, $tpl;
        $id = intval($id);
        if ($data) {
            $b = $DB->compile_db_update_string($data);
            $sql = "UPDATE vn_province SET " . $b . " WHERE provinceid = $id";
            $db = $DB->query($sql);
        }
    }
    
    function getProvinceID($id) {
        global $DB;
        $id = intval($id);
        $sql = "SELECT * FROM vn_province WHERE provinceid = $id";
        $db = $DB->query($sql);
        $rs = mysqli_fetch_array($db);
        return $rs;
    }        
    
    
    function getListProvince($keyword) {
        global $DB;
        if ($keyword)
            $sql = "SELECT * FROM vn_province WHERE name LIKE '%$keyword%' ORDER BY name ";
        else
            $sql = "SELECT * FROM vn_province ORDER BY name ";
        $db = $DB->query($sql);
        while ($row = mysqli_fetch_array($db))
            $data[] = $row;
        return $data;
    }
    
    
    
    function insertDistrict($data) {
        global $DB, $tpl;
        if ($data) {
            $b = $DB->compile_db_insert_string($data);
            $sql = "INSERT INTO vn_district (" . $b['FIELD_NAMES'] . ") VALUES (" . $b['FIELD_VALUES'] . ")";
            $DB->query($sql);
        }
    }
    
    function updateDistrict($id, $data) {
        global $DB, $tpl;
        $id = intval($id);
        if ($data) {
            $b = $DB->compile_db_update_string($data);
            $sql = "UPDATE vn_district SET " . $b . " WHERE districtid = $id";
            $db = $DB->query($sql);
        }
    }
    
    function getListDistrict($keyword, $provinceid) {
        global $DB;
        
        $strProvince = "";
        //if ($provinceid != '')
            $strProvince = "AND provinceid='$provinceid'";
        
        if ($keyword)
            $sql = "SELECT * FROM vn_district WHERE name LIKE '%$keyword%' $strProvince  ORDER BY name ";
        else
            $sql = "SELECT * FROM vn_district WHERE 1=1 $strProvince ORDER BY name";
        $db = $DB->query($sql);
        while ($row = mysqli_fetch_array($db))
            $data[] = $row;
        return $data;
    }
    
    function getDistrictID($id) {
        global $DB;
        $id = intval($id);
        $sql = "SELECT * FROM vn_district WHERE districtid = $id";
        $db = $DB->query($sql);
        $rs = mysqli_fetch_array($db);
        return $rs;
    }        

    
    function insertWard($data) {
        global $DB, $tpl;
        if ($data) {
            $b = $DB->compile_db_insert_string($data);
            $sql = "INSERT INTO vn_ward (" . $b['FIELD_NAMES'] . ") VALUES (" . $b['FIELD_VALUES'] . ")";
            $DB->query($sql);
        }
    }
    
    
    function updateWard($id, $data) {
        global $DB, $tpl;
        $id = intval($id);
        if ($data) {
            $b = $DB->compile_db_update_string($data);
            $sql = "UPDATE vn_ward SET " . $b . " WHERE wardid = $id";
            $db = $DB->query($sql);
        }
    }
    
    function getListWard($districtid) {
        global $DB;
        
        
        $strProvince = "AND districtid='$districtid'";
        
        if ($keyword)
            $sql = "SELECT * FROM vn_ward WHERE name LIKE '%$keyword%' $strProvince  ORDER BY name ";
        else
            $sql = "SELECT * FROM vn_ward WHERE 1=1 $strProvince ORDER BY name";
        $db = $DB->query($sql);
        while ($row = mysqli_fetch_array($db))
            $data[] = $row;
        return $data;
    }
    
    function getWardID($id) {
        global $DB;
        $id = intval($id);
        $sql = "SELECT * FROM vn_ward WHERE wardid = $id";
        $db = $DB->query($sql);
        $rs = mysqli_fetch_array($db);
        return $rs;
    }        

    
    
    function insertStreet($data) {
        global $DB, $tpl;
        if ($data) {
            $b = $DB->compile_db_insert_string($data);
            $sql = "INSERT INTO vn_street (" . $b['FIELD_NAMES'] . ") VALUES (" . $b['FIELD_VALUES'] . ")";
            $DB->query($sql);
        }
    }

    function updateStreet($id, $data) {
        global $DB, $tpl;
        $id = intval($id);
        if ($data) {
            $b = $DB->compile_db_update_string($data);
            $sql = "UPDATE vn_street SET " . $b . " WHERE id_street = $id";
            $db = $DB->query($sql);
        }
    }

    function insertStreetLocation($id_street, $provinceid, $districtid) {
        global $DB, $tpl;
        $sql = "INSERT INTO street_location(id_street, provinceid, districtid) VALUES  ($id_street,'$provinceid','$districtid')";
        $DB->query($sql);
    }
    
    function updateStreetLocation($id, $id_street, $provinceid, $districtid) {
        global $DB, $tpl;
        $sql = "UPDATE street_location id_street = $id_street, provinceid = '$provinceid', districtid = '$districtid' WHERE id=$id";
        $DB->query($sql);

    }

    function getId($id) {
        global $DB;
        $id = intval($id);
        $sql = "SELECT * FROM vn_street WHERE id_street = $id";
        $db = $DB->query($sql);
        $rs = mysqli_fetch_array($db);
        return $rs;
    }    
    
    function getList($keyword) {
        global $DB;
        if ($keyword)
            $sql = "SELECT * FROM vn_street WHERE name LIKE '%$keyword%' ORDER BY name ";
        else
            $sql = "SELECT * FROM vn_street ORDER BY name ";
        $db = $DB->query($sql);
        while ($row = mysqli_fetch_array($db))
            $data[] = $row;
        return $data;
    }
    
    
    function getById($id) {
        global $DB;
        $id = intval($id);
        $sql = "SELECT S.*,P.provinceid, P.`name` as pname, D.districtid, D.`name` as dname FROM vn_street S INNER JOIN street_location L ON S.id_street = L.id_street
LEFT JOIN vn_province P ON L.provinceid = P.provinceid LEFT JOIN vn_district D ON L.districtid = D.districtid
WHERE 1=1 s.id_street = $id";
        $db = $DB->query($sql);
        $rs = mysqli_fetch_array($db);
        return $rs;
    }


    function getListBy($provinceid, $districtid) {
        global $DB;
        $strWhere = " ";
        if ($provinceid != '')
            $strWhere .= " AND L.provinceid = '$provinceid' ";
        if ($districtid != '')
            $strWhere .= " AND D.districtid = '$districtid' ";

        $sql = "SELECT DISTINCT S.id_street,S.name FROM vn_street S INNER JOIN street_location L ON S.id_street = L.id_street
LEFT JOIN vn_province P ON L.provinceid = P.provinceid LEFT JOIN vn_district D ON L.districtid = D.districtid
WHERE 1=1  $strWhere";
        
        //echo $sql;
        
        $db = $DB->query($sql);
        while ($row = mysqli_fetch_array($db))
            $data[] = $row;
        return $data;
    }
    
    function getStreetLoc($id_street) {
        global $DB;
        $id_street = intval($id_street);
        $sql = "SELECT L.*,P.provinceid, P.`name` as pname, D.districtid, D.`name` as dname FROM street_location L 
LEFT JOIN vn_province P ON L.provinceid = P.provinceid LEFT JOIN vn_district D ON L.districtid = D.districtid
WHERE id_street = $id_street";
        $db = $DB->query($sql);
        while ($row = mysqli_fetch_array($db))
            $data[] = $row;
        return $data;
    }    

}

?>