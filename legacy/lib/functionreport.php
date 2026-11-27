<?php
function get_timestamp_form_binh($ngay,$thang,$nam)
{
	return mktime(0, 0, 0, $thang, $ngay, $nam);
}
function strip_date($datestring){
	$a=array();
	$a=explode("/",$datestring);
	return mktime(0, 0, 0, $a[1], $a[0], $a[2]);
}
?>