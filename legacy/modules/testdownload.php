<?php

/*
.---------------------------------------------------------------------------.
|  Software: PHPFileDownload - PHP File download class                      |
|   Version: 1.0                                                            |
|   Contact: via sourceforge.net support pages                              |
|      Info: PHPFileDownload sourceforge.net                                |
|   Support: http://phpeasydownload.sourceforge.net                         |
| ------------------------------------------------------------------------- |
|     Admin: Poojitha Jayasinghe  (project admininistrator)                 |
|   Authors: Poojitha Jayasinghe                                            |
|                                                                           |
|   Founder: Poojitha Jayasinghe (original founder)                         |
| Copyright (c) 20010-2011, Poojitha Jayasinghe. All Rights Reserved.       |
|                                                                           |
| ------------------------------------------------------------------------- |
|   License: Distributed under the MIT License                              |
|            http://www.opensource.org/licenses/mit-license.php             |
| This program is distributed in the hope that it will be useful - WITHOUT  |
| ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or     |
| FITNESS FOR A PARTICULAR PURPOSE.                                         |
| ------------------------------------------------------------------------- |

*/

/**
 * PHPFileDownload - PHP File download class
 * NOTE: Requires PHP version 5 or later
 * @package PHPFileDownload
 * @author Poojitha Jayasinghe
 * @copyright 2010 - 2011 Poojitha Jayasinghe
 * @version 1.0
 * @license MIT
 */


/*
 * Create a heading array and data array as below
 * Give the file name(if kept null it will take the current date/time  as the file name
 * Give the file type as csv or xls (if you give null it will be csv by default)
 * You can even use the setData() ,setFileName(), setHeader() methods to change file data at runtime
 * Finally run exportData() to download data as a file.

 * Example-1

$headingArray = array('ID',"Name");
$dataArray = array(
                '1'=>array("001","Bill Gates"),
                '2'=>array("002","Linus Travolds")
            );


$fileDown = new PHPFileDownload($headingArray,$dataArray,'myFileName','csv');
$fileDown->exportData();


 *  If you want to download a file in the server give the filepath and fileName as in the example2
 *  Example-2

    $fileDown = new PHPFileDownload(null,null,'myFileName',null,'mypath/myfile.txt');
    $fileDown->exportData();

 *
 */

$fileDown = new PHPFileDownload(null,null,'myFileName',null,'daily.php');
    $fileDown->exportData();


class PHPFileDownload {

    var $headers;
    var $fileName;
    var $data;
    var $fileType;
    var $filePath;

    
    /*
     * see above examples to see how data is passed
     */
    function __construct($headers = null,$data = null,$fileName = null,$fileType = null,$filePath = null) {

        if($headers == null) {
            $this->setHeader(array());
        }else {
            $this->setHeader($headers);
        }
        if($fileName == null) {
            $this->setFileName(date("Y-m-d_H:i:s")); // default file name
        }else {
            $this->setFileName($fileName);
        }

        if($data  == null) {
            $this->setData(array());
        }else {
            $this->setData($data);
        }

        if($fileType == null) {
            $this->setFileType("csv"); // default file type
        }else {
            $this->setFileType($fileType);
        }

        if($filePath != null) {
            $this->setFilePath($filePath); 
            $this->setFileType("file");

        }
    }
    
    public function setFileName($fileName) {
        $this->fileName = $fileName;
    }

    public function getFileName() {

        $fileType = $this->getFileType();
        switch ($fileType ) {
            case 'text/csv'                     :   $this->fileName = $this->fileName . ".csv";
                                                    break;
                                                
            case 'application/vnd.ms-excel'     :   $this->fileName = $this->fileName . ".xls";

            case 'file'                         :   $this->fileName = basename($this->getFilePath());
                                                    break;

            default                             :   echo "Invalid File Type";
                                                    break;
        }

        return $this->fileName;
    }
    public function setHeader($headers) {
        $this->headers = $headers;
    }

    public function getHeader() {
        
        $fileType = $this->getFileType();
        switch ($fileType ) {
            case 'text/csv'                     :   $this->headers = '"' . implode('","',$this->headers) . '"';
                                                    break;

            case 'application/vnd.ms-excel'     :   $this->headers = '<tr><th>' . implode('</th><th>',$this->headers) . '<th></tr>';
                                                    break;

            case 'file'                         :   $this->headers = '';
                                                    break;

            default:                                echo "Invalid File Type";
                                                    break;
        }
        return $this->headers;

    }

    public function setFileType($fileType) {

        switch ($fileType) {
            case  'csv' :   $this->fileType =  'text/csv';
                            break;

            case  'xls' :   $this->fileType =  'application/vnd.ms-excel';
                            break;

             case 'file':   $this->fileType = filetype($this->getFilePath());
                            break;
                        
            default:        echo "Invalid File Type";
                            break;
        }
    }

    public function getFileType() {
        return $this->fileType;
    }
    public function setData($data) {
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }

    public function setFilePath($path) {
        $this->filePath = $path;
    }

    public function getFilePath() {
        return $this->filePath;
    }
    /*
     * Creates a csv string if you want to use another
     * character instead of , change the $seperator variable
     */
    public function getCsvDataString() {
      
        $csvData = "";
        $seperator = '","';

        foreach ($this->data as $row) {
            foreach($row as $key=>&$value) {
                $value = str_replace('"', '""', $value); // handle quotes inside fields
            }
            $csvData  =  $csvData . '"' . implode($seperator,$row) . '"'. "\n";
        }

        return $csvData;
    }

     /*
     * Microsoft excel has the ability to undestand HTML table
     * so we create an html table and save it as a filename.xls
     */
    
    public function getXlsDataString() {
        
        foreach ($this->data as $row) {
            $table = $table.  '<tr><td>' . implode('</td><td>',$row) . '</td></tr>';

        }
        return $table;
    }
   

    public function exportData(){

        if($this->getFileType() == 'text/csv') {
            $dataContents = $this->getHeader() . "\n" . $this->getCsvDataString();
            $this->executeHeaders();
            echo $dataContents;
            
        }elseif($this->getFileType() == 'application/vnd.ms-excel') {
            $dataContents = "<table border =1>";
            $dataContents = $dataContents . $this->getHeader() . $this->getXlsDataString();
            $dataContents =  $dataContents.  "</table>";
            $this->executeHeaders();
            echo $dataContents;


        }elseif($this->getFileType() == 'file') {
            $this->executeHeaders();
            set_time_limit(0);
            readfile($this->getFilePath());
            
        }
        
        
	}

    public function executeHeaders() {

        header("Pragma: no-cache");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: " . $this->getFileType());
        header("Content-type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . $this->getFileName() . '";');
        header("Content-Transfer-Encoding: binary");
    }


}