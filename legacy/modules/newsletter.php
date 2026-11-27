<?php

//Nguyen Minh
//email:nguyensyminh@gmail.com

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

if ($_POST['code'] == 'save') {
    
   // if ($_SESSION['imagesercurity'] == strtolower(compile_post('sercurity')) || (strtolower(compile_post('sercurity'))=='none' )) {
        
            $c = array();
            /*
            $c['table_name'] = compile_post('table_name');
            $c['id_item_name'] = compile_post('id_item_name');
            $c['id_item_val'] = compile_post('id_item_val');
            */
            $c['email'] = compile_post('email');
            $c['phone'] = compile_post('phone');
            
            $b = $DB->compile_db_insert_string($c);
            $sql = "INSERT INTO newsletter (" . $b['FIELD_NAMES'] . ") VALUES (" . $b['FIELD_VALUES'] . ")";
            $DB->query($sql);
            
            echo "1";
        
    //}else{
    //    echo "0";
    //}
}

?>
