<?php
defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );
class usersOnline { 
    var $timeout = 100; 
    var $count = 0; 
    function usersOnline () { 
        $this->timestamp = time(); 
        $this->ip = $this->ipCheck(); 
        $this->new_user(); 
        $this->delete_user(); 
        $this->count_users(); 
    } 
     
    function ipCheck() { 
    /* 
    This function checks if user is coming behind proxy server. Why is this important? 
    If you have high traffic web site, it might happen that you receive lot of traffic 
    from the same proxy server (like AOL). In that case, the script would count them all as 1 user. 
    This function tryes to get real IP address. 
    Note that getenv() function doesn't work when PHP is running as ISAPI module 
    */ 
        if (getenv('HTTP_CLIENT_IP')) { 
            $ip = getenv('HTTP_CLIENT_IP'); 
        } 
        elseif (getenv('HTTP_X_FORWARDED_FOR')) { 
            $ip = getenv('HTTP_X_FORWARDED_FOR'); 
        } 
        elseif (getenv('HTTP_X_FORWARDED')) { 
            $ip = getenv('HTTP_X_FORWARDED'); 
        } 
        elseif (getenv('HTTP_FORWARDED_FOR')) { 
            $ip = getenv('HTTP_FORWARDED_FOR'); 
        } 
        elseif (getenv('HTTP_FORWARDED')) { 
            $ip = getenv('HTTP_FORWARDED'); 
        } 
        else { 
            $ip = $_SERVER['REMOTE_ADDR']; 
        } 
        return $ip; 
    } 
     
    function new_user() { 
        $insert = mysql_query ("INSERT INTO useronline(timestamp, ip) VALUES ('$this->timestamp', '$this->ip')"); 
    } 
     
    function delete_user() { 
        $delete = mysql_query ("DELETE FROM useronline WHERE timestamp < ($this->timestamp - $this->timeout)"); 
    } 
     
    function count_users() { 
        $count = mysql_num_rows ( mysql_query("SELECT DISTINCT ip FROM useronline")); 
        return $count; 
    } 
	
} 

?>