<?php 
	function define_module($type){
		$str=array();
		switch($type){
			case 'news':	
				$str['page']='news';
				$str['tableitem']='news';
				$str['id_item']='id_news';
				$str['adminpage']='news';
				$str['titlemenu']='Tin tức';
				$str['data_type']="news";
				break;
				
			case 'product':	
				$str['page']='product';
				$str['tableitem']='product';
				$str['id_item']='id_product';
				$str['adminpage']='product';
				$str['titlemenu']='Sản phẩm';
				$str['data_type']="product";
				break;
				
			case 'video':	
				$str['page']='video';
				$str['tableitem']='video';
				$str['id_item']='id_video';
				$str['adminpage']='video';
				$str['titlemenu']='Video';
				$str['data_type']="video";
				break;
			case 'info':	
				$str['page']='info';
				$str['tableitem']='info';
				$str['id_item']='id_info';
				$str['adminpage']='info';
				$str['titlemenu']='Nội dung';
				$str['data_type']="info";
				
				break;
			case 'logo':	
				$str['page']='logo';
				$str['tableitem']='logo';
				$str['id_item']='id_logo';
				$str['adminpage']='logo';
				$str['titlemenu']='logo';
				$str['data_type']="logo";
				break;
			
			
			case 'forms':	
				$str['page']='forms';
				$str['tableitem']='forms';
				$str['id_item']='id';
				$str['adminpage']='create_form';
				$str['titlemenu']='forms';
				$str['data_type']="forms";
				break;
			case 'photo':	
				$str['page']='photo';
				$str['tableitem']='photo';
				$str['id_item']='id_photo';
				$str['adminpage']='photo';
				$str['titlemenu']='Trang ảnh';
				$str['data_type']="photo";
				break;
			case 'download':	
				$str['page']='download';
				$str['tableitem']='download';
				$str['id_item']='id_download';
				$str['adminpage']='download';
				$str['titlemenu']='trang download';
				$str['data_type']="download";
				break;
			case 'home':	
				$str['page']='home';
				$str['tableitem']='';
				$str['id_item']='';
				$str['adminpage']='';
				$str['titlemenu']='Trang chủ';
				$str['data_type']="home";
				break;
			case 'contact':	
				$str['page']='contact';
				$str['tableitem']='';
				$str['id_item']='';
				$str['adminpage']='contact';
				$str['titlemenu']='Liên hệ';
				$str['data_type']="contact";
				break;
				
			case 'faq':	
				$str['page']='faq';
				$str['tableitem']='faq';
				$str['id_item']='id_faq';
				$str['adminpage']='faq';
				$str['titlemenu']='Hỏi đáp';
				$str['data_type']="faq";
				break;
			
			case 'link':	
				$str['page']='';
				$str['tableitem']='';
				$str['id_item']='';
				$str['adminpage']='';
				$str['titlemenu']='liên kết';
				$str['data_type']="link";
				break;
				
				
		}
		return $str;
	}
?>