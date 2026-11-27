<?php
//Develop Team: Binh Minh group
//binhminhgroup@gmail.com
//error_reporting(E_ERROR);
defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

class db_driver {

    var $obj = array("sql_database" => "",
        "sql_user" => "root",
        "sql_pass" => "",
        "sql_host" => "localhost",
        "sql_port" => "",
        "persistent" => "0",
        "sql_tbl_prefix" => "ibf_",
        "cached_queries" => array(),
        'debug' => 0,
    );

    var $mysqli;
    var $mysqli_result;

    var $query_count = 0;
    var $record_row = array();
    var $return_die = 0;
    var $error = "";
    var $failed = 0;

    /* ======================================================================== */

    // Connect to the database
    /* ======================================================================== */
    function connect() {
        try {
            $this->mysqli = new mysqli($this->obj['sql_host'], $this->obj['sql_user'], $this->obj['sql_pass'], $this->obj['sql_database']);


            /* Set the desired charset after establishing a connection */
            $this->mysqli->set_charset('utf8');
        } catch (Exception $e) {
            echo ("ERROR: Cannot find database " . $this->obj['sql_database']);
        }
    }

    /* ======================================================================== */

    // Process a query
    /* ======================================================================== */
    function query($the_query, $bypass = 0) {
        //--------------------------------------
        // Change the table prefix if needed
        //--------------------------------------
        if ($bypass != 1) {
            if ($this->obj['sql_tbl_prefix'] != "ibf_") {
                $the_query = preg_replace("/ibf_(\S+?)([\s\.,]|$)/", $this->obj['sql_tbl_prefix'] . "\\1\\2", $the_query);
            }
        }
        if ($this->obj['debug']) {
            global $Debug, $ibforums;
            $Debug->startTimer();
        }
        $this->mysqli_result = mysqli_query($this->mysqli, $the_query);

        if (!$this->mysqli_result) {
            $this->fatal_error("mySQL query error: $the_query");
        }
        if ($this->obj['debug']) {
            $endtime = $Debug->endTimer();
            if (preg_match("/^select/i", $the_query)) {
                $eid = mysqli_query($this->mysqli, "EXPLAIN $the_query");
                $ibforums->debug_html .= "<table width='95%' border='1' cellpadding='6' cellspacing='0' bgcolor='#FFE8F3' align='center'>
										   <tr>
										   	 <td colspan='8' style='font-size:14px' bgcolor='#FFC5Cb'><b>Select Query</b></td>
										   </tr>
										   <tr>
										    <td colspan='8' style='font-family:courier, monaco, arial;font-size:14px;color:black'>$the_query</td>
										   </tr>
										   <tr bgcolor='#FFC5Cb'>
											 <td><b>table</b></td><td><b>type</b></td><td><b>possible_keys</b></td>
											 <td><b>key</b></td><td><b>key_len</b></td><td><b>ref</b></td>
											 <td><b>rows</b></td><td><b>Extra</b></td>
										   </tr>\n";
                while ($array = mysqli_fetch_array($eid)) {
                    $type_col = '#FFFFFF';
                    if ($array['type'] == 'ref' or $array['type'] == 'eq_ref' or $array['type'] == 'const') {
                        $type_col = '#D8FFD4';
                    } else if ($array['type'] == 'ALL') {
                        $type_col = '#FFEEBA';
                    }
                    $ibforums->debug_html .= "<tr bgcolor='#FFFFFF'>
											 <td>$array[table]&nbsp;</td>
											 <td bgcolor='$type_col'>$array[type]&nbsp;</td>
											 <td>$array[possible_keys]&nbsp;</td>
											 <td>$array[key]&nbsp;</td>
											 <td>$array[key_len]&nbsp;</td>
											 <td>$array[ref]&nbsp;</td>
											 <td>$array[rows]&nbsp;</td>
											 <td>$array[Extra]&nbsp;</td>
										   </tr>\n";
                }
                if ($endtime > 0.1) {
                    $endtime = "<span style='color:red'><b>$endtime</b></span>";
                }
                $ibforums->debug_html .= "<tr>
										  <td colspan='8' bgcolor='#FFD6DC' style='font-size:14px'><b>mySQL time</b>: $endtime</b></td>
										  </tr>
										  </table>\n<br />\n";
            } else {
                $ibforums->debug_html .= "<table width='95%' border='1' cellpadding='6' cellspacing='0' bgcolor='#FEFEFE'  align='center'>
										 <tr>
										  <td style='font-size:14px' bgcolor='#EFEFEF'><b>Non Select Query</b></td>
										 </tr>
										 <tr>
										  <td style='font-family:courier, monaco, arial;font-size:14px'>$the_query</td>
										 </tr>
										 <tr>
										  <td style='font-size:14px' bgcolor='#EFEFEF'><b>mySQL time</b>: $endtime</span></td>
										 </tr>
										</table><br />\n\n";
            }
        }
        $this->query_count++;
        $this->obj['cached_queries'][] = $the_query;
        return $this->mysqli_result;
    }

    /* ======================================================================== */

    // Fetch a row based on the last query
    /* ======================================================================== */
    function fetch_row($mysqli_result) {
        if ($mysqli_result == "") {
            $mysqli_result = $this->mysqli_result;
        }
        $this->record_row = mysqli_fetch_array($mysqli_result, MYSQLI_ASSOC);
        return $this->record_row;
    }

    /* ======================================================================== */

    // Fetch the number of rows affected by the last query
    /* ======================================================================== */
    function get_affected_rows() {
        return mysqli_affected_rows($this->mysqli);
    }

    /* ======================================================================== */

    // Fetch the number of rows in a result set
    /* ======================================================================== */
    function get_num_rows() {
        return mysqli_num_rows($this->mysqli_result);
    }

    /* ======================================================================== */

    // Fetch the last insert id from an sql autoincrement
    /* ======================================================================== */
    function get_insert_id() {
        return mysqli_insert_id($this->mysqli);
    }

    /* ======================================================================== */

    // Return the amount of queries used
    /* ======================================================================== */
    function get_query_cnt() {
        return $this->query_count;
    }

    /* ======================================================================== */

    // Free the result set from mySQLs memory
    /* ======================================================================== */
    function free_result($mysqli_result = "") {
        if ($mysqli_result == "") {
            $mysqli_result = $this->mysqli_result;
        }
        @mysqli_free_result($mysqli_result);
    }

    /* ======================================================================== */

    // Shut down the database
    /* ======================================================================== */
    function close_db() {
        return mysqli_close($this->mysqli);
    }

    /* ======================================================================== */

    // Return an array of tables
    /* ======================================================================== */
    function get_table_names() {
        $result = $this->query("SHOW TABLES FROM " . $this->obj['sql_database']);
        $num_tables = @mysqli_fetch_array($result);

        var_dump($num_tables);die;
$tables = array();
        // for ($i = 0; $i < $num_tables; $i++) {
        //     $tables[] = mysql_tablename($result, $i);
        // }
        // mysql_free_result($result);
        // return $tables;
    }

    /* ======================================================================== */

    // Return an array of fields
    /* ======================================================================== */
    function get_result_fields($mysqli_result = "") {
        if ($mysqli_result == "") {
            $mysqli_result = $this->mysqli_result;
        }
$Fields = array();
        while ($field = mysqli_fetch_field($mysqli_result)) {
            $Fields[] = $field;
        }
        //mysql_free_result($mysqli_result);
        return $Fields;
    }

    /* ======================================================================== */

    // Basic error handler
    /* ======================================================================== */
    function fatal_error($the_error) {
        global $INFO;
        // Are we simply returning the error?
        if ($this->return_die == 1) {
            $this->error = mysqli_error($this->mysqli);
            $this->error_no = mysqli_errno($this->mysqli);
            $this->failed = 1;
            return;
        }
        $the_error .= "\n\nmySQL error: " . mysqli_error($this->mysqli) . "\n";
        $the_error .= "mySQL error code: " . $this->error_no . "\n";
        $the_error .= "Date: " . date("l dS of F Y h:i:s A");
        $out = "<html><head><title>NVB Database Error</title>
    		   <style>P,BODY{ font-family:arial,sans-serif; font-size:11px; }</style></head><body>
    		   <br><br><b></b><br>
			   <font face='arial' size='2'>
    		   " . htmlspecialchars($the_error) . "</font></body></html>";
        echo($out);
        die("");
    }

    /* ======================================================================== */

    // Create an array from a multidimensional array returning formatted
    // strings ready to use in an INSERT query, saves having to manually format
    // the (INSERT INTO table) ('field', 'field', 'field') VALUES ('val', 'val')
    /* ======================================================================== */
    function compile_db_insert_string($data) {
        $field_names = "";
        $field_values = "";
        foreach ($data as $k => $v) {
            //$v = preg_replace( "/'/", "\\'", $v );
            $v = str_replace("'", "\'", $v);
            //$v = preg_replace( "/#/", "\\#", $v );
            //$v = mysql_real_escape_string($v);
            $field_names .= "$k,";
            $field_values .= "'$v',";
        }
        $field_names = preg_replace("/,$/", "", $field_names);
        $field_values = preg_replace("/,$/", "", $field_values);
        return array('FIELD_NAMES' => $field_names,
            'FIELD_VALUES' => $field_values,
        );
    }

    /* ======================================================================== */

    // Create an array from a multidimensional array returning a formatted
    // string ready to use in an UPDATE query, saves having to manually format
    // the FIELD='val', FIELD='val', FIELD='val'
    /* ======================================================================== */
    function compile_db_update_string($data) {
        $return_string = "";
        foreach ($data as $k => $v) {
            //	$v = preg_replace( "/'/", "\\'", $v );
            //$v = mysql_real_escape_string($v);
            $return_string .= $k . "='" . $v . "',";
        }
        $return_string = preg_replace("/,$/", "", $return_string);
        return $return_string;
    }

    /* ======================================================================== */

    // Test to see if a field exists by forcing and trapping an error.
    // It ain't pretty, but it do the job don't it, eh?
    // Posh my ass.
    // Return 1 for exists, 0 for not exists and jello for the naked guy
    // Fun fact: The number of times I spelt 'field' as 'feild'in this part: 104
    /* ======================================================================== */
    function field_exists($field, $table) {
        $this->return_die = 1;
        $this->error = "";
        $this->query("SELECT COUNT($field) as count FROM $table");
        $return = 1;
        if ($this->failed) {
            $return = 0;
        }
        $this->error = "";
        $this->return_die = 0;
        $this->error_no = 0;
        $this->failed = 0;
        return $return;
    }

}

// end class
class db_driveri {

    var $obj = array("sql_database" => "",
        "sql_user" => "root",
        "sql_pass" => "",
        "sql_host" => "localhost",
        "sql_port" => "",
        "persistent" => "0",
        "sql_tbl_prefix" => "ibf_",
        "cached_queries" => array(),
        'debug' => 0,
    );

    var $mysqli_result;
    var $mysqli;

    var $query_count = 0;
    var $record_row = array();
    var $return_die = 0;
    var $error = "";
    var $failed = 0;

    function connect() {
        $this->mysqli = mysqli_connect($this->obj['sql_host'], $this->obj['sql_user'], $this->obj['sql_pass'], $this->obj['sql_database']
        );
        /* if ( !mysqli_select_db($this->obj['sql_database'], $this->mysqli) )
          {
          echo ("ERROR: Cannot find database ".$this->obj['sql_database']);
          } */
    }

    function query($sql) {
        $result = mysqli_query($this->mysqli, $sql);
        return $result;
    }

    function close_db() {
        @mysqli_close($this->mysqli);
    }

}

class DBi {

    public function insertTableRow($table, $data) {
        global $mysqli;

        $placeholders = '';
        $placeparams = '';

        // Check for $table or $data not set
        if (empty($table) || empty($data)) {
            return false;
        }
        // Cast $data and $format to arrays
        $data = (array) $data;
        $value = array();
        $param_type = "";
        foreach ($data as $k => $v) {
            $placeholders .= " $k,";
            $placeparams .= " ?,";
            // $value[] = str_replace('\\', "", $v);
            $value[] = $v;
            $param_type.= $this->_determineType($v);
        }
        $placeholders = substr($placeholders, 0, -1);
        $placeparams = substr($placeparams, 0, -1);
        $values = array_merge((array) $param_type, $value);
        $sql = "INSERT INTO {$table} ({$placeholders}) VALUES ($placeparams) ";
        $stmt = $mysqli->prepare($sql);
        // Dynamically bind values
        call_user_func_array(array($stmt, 'bind_param'), $this->ref_values($values));
        // Execute the query
        $stmt->execute();
        // Check for successful insertion and return last id
        if ($stmt->affected_rows) {
            return $stmt->insert_id;
        }
        return $stmt->error;
    }

    public function updateTableRow($table, $data, $keyfields, $keyvalues) {
        global $mysqli;
        $placeholders = '';

        // Check for $table or $data not set
        if (empty($table) || empty($data)) {
            return false;
        }
        // Cast $data and $format to arrays
        $data = (array) $data;
        $value = array();
        $param_type = "";
        foreach ($data as $k => $v) {
            $placeholders.= " $k = ?,";
            //$value[] = str_replace('\\', "", $v);
            $value[] = $v;
            $param_type.= $this->_determineType($v);
        }
        $placeholders = substr($placeholders, 0, -1);

        //nhieu tham so dieu kien
        $keyfields = (array)$keyfields;
        $keyvalues = (array)$keyvalues;
        $where_condition = " 1=1 ";
        $i= 0;

        foreach($keyfields as $keyfield){
            $where_condition .= " AND $keyfield = ? ";
            $keyvalue = $keyvalues[$i];

            $value[] = $keyvalue;
            $param_type.= $this->_determineType($keyvalue);
            $values = array_merge((array) $param_type, $value);
        }

        $sql = "UPDATE {$table} SET {$placeholders} WHERE $where_condition ";
        $stmt = $mysqli->prepare($sql);
        // Dynamically bind values
        call_user_func_array(array($stmt, 'bind_param'), $this->ref_values($values));
        // Execute the query
        //echo $sql;
        try {
            $stmt->execute();
        } catch (Exception $e) {
            echo "Error: $e";
        }
        // Check for successful insertion
        if ($stmt->affected_rows) {
            return true;
        }
        return false;
    }

    private function ref_values($array) {
        $refs = array();
        foreach ($array as $key => $value) {
            $refs[$key] = &$array[$key];
        }
        return $refs;
    }

    private function _determineType($item) {
        switch (gettype($item)) {
            case 'NULL':
            case 'string':
                return 's';
                break;
            case 'boolean':
            case 'integer':
                return 'i';
                break;
            case 'blob':
                return 'b';
                break;
            case 'double':
                return 'd';
                break;
        }
        return '';
    }

}

?>