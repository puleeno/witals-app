<?php
defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );
if (extension_loaded('gd2')) {
  trigger_error('GD image library not available', E_USER_ERROR);
}
    
// http://php.net/imagetypes
// http://php.net/exif-imagetype
if (!defined('IMAGETYPE_GIF')) {
    define('IMAGETYPE_GIF', 1);
    define('IMAGETYPE_JPEG', 2);
    define('IMAGETYPE_PNG', 3);
}

function image_file_to_extension($file, $imagetype) {
    $file = explode('.', $file);
    array_pop($file);
    return implode('.', $file).image_type_to_extension($imagetype);
}

// http://php.net/image-type-to-mime-type
if (!function_exists('image_type_to_mime_type')) {
	function image_type_to_mime_type($imagetype) {
		switch($imagetype) {
			case IMAGETYPE_GIF  : return 'image/gif';
			case IMAGETYPE_JPEG : return 'image/jpeg';
			case IMAGETYPE_PNG  : return 'image/png';
			default             : return false;
		}
	}
}

// http://php.net/image_type_to_extension
if(!function_exists('image_type_to_extension')) {
	function image_type_to_extension($imagetype, $include_dot=true) {
		$dot = $include_dot ? '.' : '';
		switch($imagetype) {
			case IMAGETYPE_GIF  : return $dot.'gif';
			case IMAGETYPE_JPEG : return $dot.'jpg';
			case IMAGETYPE_PNG  : return $dot.'png';
			default             : return false;
		}
	}
}

/**
 * resize_image()
 *
 * Create a file containing a resized image
 *
 * @param  $src_file the source file
 * @param  $dest_file the destination file
 * @param  $new_size the size of the square within which the new image must fit
 * @param  $method the method used for image resizing
 * @return 'true' in case of success
*/
function resize_image($src_file, &$dest_file, $new_size, $method, $thumb_use, $watermark='')
{
    global $CONFIG, $ERROR;
    if (!($imginfo = getimagesize($src_file))) return false;
        
    // height/width
    if ($thumb_use == 'ht') {
        $ratio = $imginfo[1] / $new_size;
    } elseif ($thumb_use == 'wd') {
        $ratio = $imginfo[0] / $new_size;
    } else {
        $ratio = max($imginfo[0], $imginfo[1]) / $new_size;
    }
    $ratio = max($ratio, 1.0);
    $dest_info[0] = intval($imginfo[0] / $ratio);
    $dest_info[1] = intval($imginfo[1] / $ratio);
    $dest_info['quality'] = 80;//quality to create new image
    $dest_info['im_options'] = '-antialias';
    if ($watermark) {
        $dest_info['watermark']['text'] = $watermark;
    }

    $dest_file = Graphic::resize($src_file, $dest_info, $dest_file, $imginfo);
    if ($dest_file < 0) return false;

    // Set mode of uploaded picture
    chmod($dest_file, 0666);
    // We check that the image is valid
    $imginfo = getimagesize($dest_file);
    if (!$imginfo) {
        $ERROR = 'RESIZE FAILED';
        unlink($dest_file);
        return false;
    }
    return true;
}

//Resize image -> create thumbnail
function create_thumb($filepath, $filename)
{
    global $CONFIG;

    $image = $filepath . $filename;
    $normal = $filepath . 'n-' . $filename;
    $thumb = $filepath . 't-' . $filename;
    $imagesize = getimagesize($image);
    if (!file_exists($thumb)) {
        if (!resize_image($image, $thumb, $CONFIG['auto_thumbnail_dimension'], 'gd2', 'wd', ''))
		{
            return false;
		}
		else
		{
			$thumbnail['thumb']=basename($thumb);
		}
    }

    if (max($imagesize[0], $imagesize[1]) > $CONFIG['max_image_width'] && !file_exists($normal)) {
        if (!resize_image($image, $normal, $CONFIG['max_thumb_width'], 'gd2', 'wd', ''))
		{
            return false;
		}
		else
		{
			$thumbnail['normal']=basename($normal);
		}
    }

    //$image_filesize = filesize($image);
    //$total_filesize = $image_filesize + (file_exists($normal) ? filesize($normal) : 0) + filesize($thumb);
    return $thumbnail;
}

function create_thumb_photo($filepath, $filename)
{
    global $CONFIG;

    $image = $filepath . $filename;
    $normal = $filepath . 'n-' . $filename;
    $thumb = $filepath . 't-' . $filename;
    $imagesize = getimagesize($image);
    if (!file_exists($thumb)) {
        if (!resize_image($image, $thumb, $CONFIG['auto_thumbnail_dimension'], 'gd2', 'wd', ''))
		{
            return false;
		}
		else
		{
			$thumbnail['thumb']=basename($thumb);
		}
    }

    if (max($imagesize[0], $imagesize[1]) > $CONFIG['max_image_width'] && !file_exists($normal)) {
        if (!resize_image($image, $normal, 700, 'gd2', 'wd', ''))
		{
            return false;
		}
		else
		{
			$thumbnail['normal']=basename($normal);
		}
    }

    //$image_filesize = filesize($image);
    //$total_filesize = $image_filesize + (file_exists($normal) ? filesize($normal) : 0) + filesize($thumb);
    return $thumbnail;
}
//create thumbnail for an image via url of that image
function create_thumb_url($file_url)
{
    global $CONFIG;
	//$file_name=get_file_name($file_url);
	$file_name=basename($file_url);
	$file_extension=get_file_extension($file_name);
	$new_file_name=get_new_file_name($file_name,"$file_name_".time());
	
	$filepath=$CONFIG['root_path'].$CONFIG['upload_media_path'];
	$image=$file_url;
    $normal = $filepath . 'n-' . $new_file_name;
	 $image1 = $filepath . 'b-' . $new_file_name;
    $thumb = $filepath . 't-' . $new_file_name;
	$imagesize = getimagesize($image);
	if (!file_exists($image)) {
		if($imagesize[0]>700){
			resize_image($image, $image1, '700', 'gd2', 'wd', '');
			$thumbnail['image']='' . $new_file_name;
		}else{
			resize_image($image, $image1, $imagesize[0], 'gd2', 'wd', '');	
			$thumbnail['image']='' . $new_file_name;
		}
	}
   
	
    if (!file_exists($thumb)) {
        if (!resize_image($image, $thumb, $CONFIG['auto_thumbnail_dimension'], 'gd2', 'wd', ''))
		{
            return false;
		}
		else
		{
			$thumbnail['thumb']='t-' . $new_file_name;
		}
    }

    if (max($imagesize[0], $imagesize[1]) > $CONFIG['max_image_width'] && !file_exists($normal)) {
        if (!resize_image($image, $normal, $CONFIG['max_image_width'], 'gd2', 'wd', ''))
		{
            return false;
		}
		else
		{
			$thumbnail['normal']='n-' . $new_file_name;
		}
    }

    //$image_filesize = filesize($image);
    //$total_filesize = $image_filesize + (file_exists($normal) ? filesize($normal) : 0) + filesize($thumb);
    return $thumbnail;
}

function url_upload($file_url)
{
    global $CONFIG;
	//$file_name1=get_file_name($file_url);
	$file_name=basename($file_url);
	$file_extension=get_file_extension($file_name);
	$vowels = array("autopro-","vnm");
 	$file_name = str_replace($vowels, "", $file_name);
	$file_name=get_new_file_name($file_name,$file_name);
	
	$filepath=$CONFIG['root_path'].$CONFIG['dir_autoupload'];
	$image=$file_url;
	$len=strlen($file_name);
	
	
	
	$len1=strlen($file_extension);
  	$binh_filename="xepro-".time().'-'.substr($file_name,0,$len-$len1);
	 $image1 = $filepath .  $binh_filename;
  
	$imagesize = getimagesize($image);
	if (!file_exists($image)) {
		if($imagesize[0]>700){
			resize_image($image, $image1, '700', 'gd2', 'wd', '');
			$urlimage['image']='' .  $binh_filename.'jpeg';
		}else{
			resize_image($image, $image1, $imagesize[0], 'gd2', 'wd', '');	
			$urlimage['image']='' .  $binh_filename.'jpeg';
		}
	}
   
    return $urlimage;
}


/*=======================================
GD2
=========================================*/
class Graphic {

  function _createfrom($src_file, &$imginfo)
  {
    // try to get more memory for executing large pictures
    @ini_set('memory_limit', '32M');
    if (!$imginfo) $imginfo = getimagesize($src_file);
    if (!$imginfo) return -1;
    if ($imginfo[2] == IMAGETYPE_GIF) {
        if (!function_exists('imagecreatefromgif')) {
            cpg_error('GD running on your server does not support the GIF fileformat');
        }
        return imagecreatefromgif($src_file);
    } else if ($imginfo[2] == IMAGETYPE_JPEG) {
        if (!function_exists('imagecreatefromjpeg')) {
            //cpg_error('GD running on your server does not support the JPEG fileformat');
        }
        return imagecreatefromjpeg($src_file);
    } else {
        return imagecreatefrompng($src_file);
    }
  }

  function resize($src_file, $dest_info, $dest_file, $imginfo=false)
  {
    $src_img = Graphic::_createfrom($src_file, $imginfo);
    if (!$imginfo) return -1;
    if (!$src_img) { return -2; }

    if (!isset($dest_info[2])) { $dest_info[2] = $imginfo[2]; }
    if ($dest_info[2] == IMAGETYPE_GIF && !function_exists('imagegif')) { $dest_info[2] = IMAGETYPE_PNG; }
    $resize = ($dest_info[0] != $imginfo[0] || $dest_info[1] != $imginfo[1]);
    if ($resize) {
        if ($dest_info[2] != IMAGETYPE_GIF && function_exists('imagecreatetruecolor')) {
            $dst_img = imagecreatetruecolor($dest_info[0], $dest_info[1]);
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $dest_info[0], $dest_info[1], $imginfo[0], $imginfo[1]);
        } else {
            // GD 1 :cry:
            $dst_img = imagecreate($dest_info[0], $dest_info[1]);
            imagecopyresized($dst_img, $src_img, 0, 0, 0, 0, $dest_info[0], $dest_info[1], $imginfo[0], $imginfo[1]);
        }
        imagedestroy($src_img);
    } else {
        // Destination image is same size so reduce useless memory usage
        $dst_img =& $src_img;
    }

    if ($dest_file == '') {
        header('Content-type: '.image_type_to_mime_type($dest_info[2]));
    } else {
        $dest_file = image_file_to_extension($dest_file, $dest_info[2]);
    }

//    $dest_info['text'] = 'Uploaded by: djmaze @ cpgnuke.com';
    if (isset($dest_info['watermark'])) {
        $text = $dest_info['watermark']['text'];
        $font = 2;
        $width = strlen($text)*imagefontwidth($font);
        if (function_exists('imagecolorallocatealpha')) {
            $color = imagecolorallocatealpha($dst_img, 255, 255, 255, 60);
        } else {
            $color = imagecolorallocate($dst_img, 255, 255, 255, 60);
        }
        if (($dest_info[0] < $dest_info[1]) && ($dest_info[0] < $width+2)) {
            $x = $dest_info[0]-imagefontheight($font);
            $y = $dest_info[1]-$width-2;
            imagefilledrectangle($dst_img, $x, $y, $dest_info[0], $dest_info[1], $color);
            imagestringup($dst_img, $font, $x, $dest_info[1]-2, $text, ImageColorAllocate($dst_img, 0, 0, 0));
        } else {
            $x = $dest_info[0]-$width-2;
            $y = $dest_info[1]-imagefontheight($font);
            imagefilledrectangle($dst_img, $x, $y, $dest_info[0], $dest_info[1], $color);
            ImageString($dst_img, $font, $x+1, $y, $text, ImageColorAllocate($dst_img, 0, 0, 0));
        }
    }
    if ($dest_info[2] == IMAGETYPE_GIF) {
        imagegif($dst_img, $dest_file);
    } else if ($dest_info[2] == IMAGETYPE_JPEG) {
        if (!isset($dest_info['quality']) || intval($dest_info['quality']) < 10) $dest_info['quality'] = 85;
        imagejpeg($dst_img, $dest_file, intval($dest_info['quality']));
    } else {
        imagepng($dst_img, $dest_file);
    }
    imagedestroy($dst_img);
    return $dest_file; // return same or new filename depending on IMAGETYPE
  }

  function show($src_file, $dest_info)
  {
    Graphic::resize($src_file, $dest_info, '');
    exit;
  }
}

?>