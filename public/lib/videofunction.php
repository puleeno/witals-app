<?php
defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );


function showvideoyoutube($url,$width=193, $height='179',$type='youtube',$autoplay="0"){
	if($type=='zing'){
		if($autoplay==1) $autoplay="autoplay=true";
		else $autoplay="autoplay=false";
		$video='<object width="'.$width.'" height="'.$height.'">
		<param name="movie" value="http://static.mp3.zing.vn/skins/mp3_main/flash/channelzPlayer.swf?xmlURL=http://mp3.zing.vn/xml/video/'.$url.'?'.$autoplay.'&wmode=transparent&1" />
		<param name="quality" value="high" />
		<param name="wmode" value="transparent" />
		<embed width="'.$width.'" height="'.$height.'" src="http://static.mp3.zing.vn/skins/mp3_main/flash/channelzPlayer.swf?xmlURL=http://mp3.zing.vn/xml/video/'.$url.'?'.$autoplay.'&wmode=transparent&1" quality="high" wmode="transparent" type="application/x-shockwave-flash"></embed>
		</object>';
	}else{
		if($autoplay==1) $autoplay="&autoplay=1";
		$video='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"           
	codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"
		width="'.$width.'" height="'.$height.'" id="Flash" align="">
		<param name="movie" value="http://www.youtube-nocookie.com/v/'.$url.'?fs=1&amp;hl=en_US'.$autoplay.'">
		<param name="quality" value="high">
		<param name="bgcolor" value="#FFFFFF">     
		<param name="wmode" value="transparent">
		<param name="allowFullScreen" value="true">
		<param name="allowscriptaccess" value="always">
		<embed src="http://www.youtube-nocookie.com/v/'.$url.'?fs=1&amp;hl=en_US'.$autoplay.'" quality="high" bgcolor="#FFFFFF"  width="'.$width.'" height="'.$height.'" name="Flash" align="" wmode="transparent"
				type="application/x-shockwave-flash"
				pluginspage="http://www.macromedia.com/go/getflashplayer">         
		</embed>
	</object>';
	}
	return $video;
}
?>