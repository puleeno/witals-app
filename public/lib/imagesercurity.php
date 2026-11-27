<?php

session_start();

$recaptcha = $_GET["recaptcha"];

if ($recaptcha == "n"){
	$text= $_SESSION['imagesercurity'];
}else{
	$text=strtoupper(substr(md5(rand(1,999)),0,3));
	$_SESSION['imagesercurity']=$text;
}




/*
$text_low=strtolower($text);

$_SESSION['imagesercurity']=$text_low;
*/

header("Content-type: image/png");

$im = @imagecreate(42, 30)  or die("Cannot Initialize new GD image stream");

$background_color = imagecolorallocate($im, 235, 236, 240);

$text_color = imagecolorallocate($im, 121, 121, 123);

$font='arial.ttf';
		$red = imagecolorallocate($im, 255, 0, 0);                  // red
		$white = imagecolorallocate($im, 177, 181, 241);			// white
		$line=imagecolorallocate($im, 131, 131, 135);				// gray

	    imagerectangle ($im,   0,  0, 41, 28, $line);

		imageline ($im,   5,  1, 15, 28, $white);

		imageline ($im,   28,  1, 30, 28, $white);

		imageline ($im,   35,  1, 45, 28, $white);

		imageline ($im,   30,  1, 40, 28, $white);

		imageline ($im,   1,  10, 41, 10, $white);

		imageline ($im,   1,  10, 41, 10, $white);

		imageline ($im,   1,  5, 41, 5, $white);

		imagestring($im, 7, 5, 5,  $text, $text_color);

imagepng($im);

imagedestroy($im);



?>