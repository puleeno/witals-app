<?php 
// Nguyễn Bình - 2013
error_reporting(0);
defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );



    function exportExcell($data,$filename,$sheetname){
        header("Expires: 0");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Content-type: application/vnd.ms-excel;charset:UTF-8");
        header("Content-Type:   text/html; charset=utf-8");
        header("Content-Disposition: attachment; filename=".$filename.".xls");
        header('Content-Transfer-Encoding: binary');
        print "\n"; // Add a line, unless excel error..
        $header = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
           <!--[if gte mso 9]>
        <xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>'.$sheetname.'</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml>
        <![endif]-->
    </head>
    <body>';

        $footer = '</body>
    </html>';
        $body = $data;
        echo  $header.$body.$footer;
    }

?>