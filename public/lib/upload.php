<?php
defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );
if (!function_exists("is_uploaded_file")) {
  function is_uploaded_file($file_name) {
    if (!$tmp_file = @get_cfg_var('upload_tmp_dir')) {
      $tmp_file = tempnam('','');
      $deleted = @unlink($tmp_file);
      $tmp_file = dirname($tmp_file);
    }
    $tmp_file .= '/'.basename($file_name);
    return (ereg_replace('/+', '/', $tmp_file) == $file_name) ? 1 : 0;
  }

  function move_uploaded_file($file_name, $destination) {
    return (is_uploaded_file($file_name)) ? ((copy($file_name, $destination)) ? 1 : 0) : 0;
  }
}
define('CHMOD_FILES',0666);
class Upload {

  var $upload_errors = array();
  var $accepted_mime_types = array();
  var $accepted_extensions = array();
  var $upload_mode = 3;

  var $upload_path = '';
  var $allowed_mediatypes;

  var $field_name;
  var $file_name;
  var $extension;

  var $image_size = 0;
  var $image_size_ok = 0;
  
  var $max_image_width;
  var $max_image_height;
  var $max_size = 100000;
	
	/*
	$upload_path: Upload path
	$allowed_mediatypes: vd: "jpg,gif,png,pdf"
	$upload_mode:	1: overwrite files
					2: upload with new file name
					3: no upload file
	$max_size: max size of file
	$max_image_width: 
	$max_image_height: check 2 those params with image file
	
	
	*/
	
  function Upload($upload_path,$allowed_mediatypes='',$upload_mode=2,$max_size=1000000,$max_image_width=3500,$max_image_height=3500) {
    global $config;

	$this->upload_path=$upload_path;
	$this->allowed_mediatypes=$allowed_mediatypes;
	$this->upload_mode=$upload_mode;
	$this->max_size=$max_size*1024;
	$this->max_image_width=$max_image_width;
	$this->max_image_height=$max_image_height;
    $this->set_allowed_filetypes();
  }

  function check_image_size() {
    $this->image_size = @getimagesize($this->upload_file);
    $ok = 1;
    if ($this->image_size[0] > $this->max_image_width) {
      $ok = 0;
      $this->set_error("Invalid Image Width !");
    }

    if ($this->image_size[1] > $this->max_image_height) {
      $ok = 0;
      $this->set_error('Invalid Image Height !');
    }
    return $ok;
  }
  function copy_file() {
	  $ok=1;
    switch ($this->upload_mode) {
    case 1: // overwrite mode
      if (file_exists($this->upload_path.$this->file_name)) {
        @unlink($this->upload_path.$this->file_name);
      }
      $ok = move_uploaded_file($this->upload_file, $this->upload_path.$this->file_name);
	 
      break;
    case 2: // create new with incremental extention
      $n = 2;
      $copy = "";
      while (file_exists($this->upload_path.$this->name.$copy.".".$this->extension)) {
        $copy = "_".$n;
        $n++;
      }
      $this->file_name = $this->name.$copy.".".$this->extension;
      $ok = move_uploaded_file($this->upload_file, $this->upload_path.$this->file_name);

      break;
    case 3: // do nothing if exists, highest protection
    default:
      if (file_exists($this->upload_path.$this->file_name)) {
       $this->set_error("File 's already exists ! ");
	   
       $ok = 0;
	   
      }
      else {
        $ok = move_uploaded_file($this->upload_file, $this->upload_path.$this->file_name);
      }
	  
      break;
    }
    @chmod($this->upload_path.$this->file_name, CHMOD_FILES);

    return $ok;
  }

  function check_max_filesize() {
    if ($this->HTTP_POST_FILES[$this->field_name]['size'] > $this->max_size) {
      return false;
    }
    else {
      return true;
    }
  }

  function save_file($check_image) {

    $this->upload_file = $this->HTTP_POST_FILES[$this->field_name]['tmp_name'];
    $ok = 1;
    if (empty($this->upload_file) || $this->upload_file == "none") {
      $this->set_error("No file !");
      $ok = 0;
    }
	
    if (!$this->check_max_filesize()) {
       $this->set_error("Invalid File size !");
       $ok = 0;
    }
	
	if ($check_image)
	{
		if (eregi("image", $this->HTTP_POST_FILES[$this->field_name]['type'])) {
			if (!$this->check_image_size()) {
			  $ok = 0;
			}
		}
	}

    if (!$this->check_file_extension() || !$this->check_mime_type()) {
      $this->set_error("Invalid File Type ! ". " (".$this->extension.", ".$this->mime_type.")");
      $ok = 0;
    }
	
    if ($ok) {
      if (!$this->copy_file()) {
        $this->set_error("File copy error !");
        $ok = 0;
      }
    }

    return $ok;
  }
/*
$field_name: ten truong input file trong form

*/
  function upload_file($field_name, $check_image=0, $file_name = "") {
    global $_COOKIE, $_POST, $_GET,$_FILES;

   
    if (isset($_COOKIE[$field_name]) || isset($_POST  [$field_name]) || isset($_GET   [$field_name])) {
      die("Security violation");
    }

    $this->HTTP_POST_FILES = $_FILES;
    $this->field_name = $field_name;

    if ($file_name != "") {
      ereg("(.+)\.(.+)", $file_name, $regs);
      $this->name = $regs[1];
      ereg("(.+)\.(.+)", $this->HTTP_POST_FILES[$this->field_name]['name'], $regs);
      $this->extension = $regs[2];
      $this->file_name = $this->name.".".$this->extension ;
    }
    else {
      $this->file_name = $this->HTTP_POST_FILES[$this->field_name]['name'];
      $this->file_name = ereg_replace(" ", "_", $this->file_name);
      $this->file_name = ereg_replace("%20", "_", $this->file_name);
      $this->file_name = preg_replace("/[^-\._a-zA-Z0-9]/", "", $this->file_name);

      ereg("(.+)\.(.+)", $this->file_name, $regs);
      $this->name = $regs[1];
      $this->extension = $regs[2];
    }

    $this->mime_type = $this->HTTP_POST_FILES[$this->field_name]['type'];
    preg_match("/([a-z]+\/[a-z\-]+)/", $this->mime_type, $this->mime_type);
    $this->mime_type = $this->mime_type[1];

    if ($this->save_file($check_image)) {
      	return $this->file_name;
    }else {
      	return false;
    }
  }

  function check_file_extension($extension = "") {
    if ($extension == "") {
      $extension = $this->extension;
    }
    if (!in_array(strtolower($extension), $this->accepted_extensions)) {
      return false;
    }
    else {
      return true;
    }
  }

  function check_mime_type() {
    if (!isset($this->accepted_mime_types)) {
      return true;
    }
    if (!in_array($this->mime_type, $this->accepted_mime_types)) {
      return false;
    }
    else {
      return true;
    }
  }

  function set_allowed_filetypes() {

    $this->accepted_extensions = explode(',',$this->allowed_mediatypes);
    $mime_type_match = array();
    include('upload_definitions.php');
	
    foreach ($mime_type_match as $key => $val) {
      if (in_array($key, $this->accepted_extensions)) {
        if (is_array($val)) {
          foreach ($val as $key2 => $val2) {
            $this->accepted_mime_types[] = $val2;
          }
        }
        else {
          $this->accepted_mime_types[] = $val;
        }
      }
    }
  }

  function get_upload_errors() {
    if (empty($this->upload_errors[$this->file_name])) {
      return "";
    }
    $error_msg = "";
    foreach ($this->upload_errors[$this->file_name] as $msg) {
      $error_msg .= "<b>".$this->file_name.":</b> ".$msg."<br />";
    }
    return $error_msg;
  }

  function set_error($error_msg) {
    $this->upload_errors[$this->file_name][] = $error_msg;
  }
} //end of class
?>
