<?php 
defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );
	
class Url{
	
	private $url_table = 'url';
	private $url_id = 'id';
	
	public function insert($url,$table,$id_category,$id_item=0,$data_type,$alias_url=''){
		global $lang,$DB;
		$module_info = $this->typeInfo($data_type);
		$a = array();		
		$a['url'] 		= $url;
		$a['dt_table'] 		= $table;
		$a['id_category'] 	= $id_category;
		$a['id_item'] 		= $id_item;
		$a['data_type'] 	= $data_type;
		$a['alias_url'] 	= $alias_url;
		$a['page'] 			= $module_info['page'];
		$a['real_url']  	= $this->builUrlRealUrl($table,$id_category,$id_item,$data_type);
		$a['lang'] 			= $lang;

		$b=$DB->compile_db_insert_string($a);
		$sql="INSERT INTO ".$this->url_table." (".$b['FIELD_NAMES'].") VALUES (".$b['FIELD_VALUES'].")";
		$db = $DB->query($sql);
		if($db) return true;
		
	}
	
	public function update($url,$table,$id_category,$id_item,$data_type){
		global $lang,$DB;
		$a = array();		
		$a['url'] 			= $url;
		$a['dt_table'] 		= $table;
		$a['id_item'] 		= $id_item;
		$a['id_category']	= $id_category;
		$a['data_type'] 	= $data_type;
		$a['alias_url'] 	= $alias_url;
		$a['lang'] 			= $lang;
		
		$b=$DB->compile_db_update_string($a);
		$sql="UPDATE ".$this->url_table." SET ".$b." WHERE table = '".$table."' AND id_item = ".$id_item;
		$db=$DB->query($sql);
		if($db) return true;
	}
	
	public function delete($table,$id_category,$id_item=0){
		global $DB;
		$dk = '';
		$id_item = intval($id_item);

		$dk = " AND id_item = ".$id_item." ";	

		if($id_category > 0 ){
			$dk.= " AND id_category =$id_category ";
		}
		if($id_category > 0 || $id_item >0){
			$sql = "DELETE FROM ".$this->url_table. " WHERE dt_table = '".$table."' $dk ";
			$db = $DB->query($sql);
		}
		if($db) return true;
	}
	
	public function processUrl($url,$n=0){
		global $lang,$DB;
		$sql = "SELECT * FROM " . $this->url_table . " WHERE url='$url'";
		$url = trim($url);
		$db = $DB->query($sql);
		$n++;
		$copy = '-';
		if($rs = mysqli_fetch_array($db)){
			$extensionurl = get_file_extension($url);
			$a = strlen($extensionurl) + 1;
			$trimextension = substr($url,0,strlen($url) - $a );
			
			if(strlen(get_file_extension($url)) > 0 ){
				$url_new = $trimextension.$copy.$n.'.'.$extensionurl;	
			}else{
				$str = explode('/', $url);
				
				if(substr($url,strlen($url) - 1,strlen($url)) == '/'){
					$url = substr($url,0,strlen($url)-strlen($str)-1);
					$url_new = $url.$copy.$n.'/';	
				}else{
					$url_new = $url.$copy.$n;		
				}
			}
			$url_new = $this->processUrl($url_new,$n);
		}else{
			$url_new = $url;	
		}
		return $url_new;
	}
	
	public function builUrl($str,$exten='',$auto=1){
		
		if($auto==1){
			return $this->processUrl($this->clean_url(trim($str)).$exten);
		}else{
			$str=str_replace("&nbsp;","-",$str);
			$str=str_replace("&#160;","",$str);
			$str=str_replace("&nbsp;"," ",$str);
			$str=str_replace("&#39;"," ",$str);
			$str=str_replace("  "," ",$str);
			$str=str_replace(" ","-",$str);
			$str=str_replace("--","-",$str);
			$str=str_replace("---","-",$str);
			$str = str_replace(array('\r|\t|\n','  ','    ','     ','	'), '-', $str);
			return strtolower(trim($str));
		}
	}
	
	public function builUrlRealUrl($table,$id_category,$id_item,$data_type){
		global $lang,$DB;
		$id_category = intval($id_category);
		$id_item = intval($id_item);
		$module_info = $this->typeInfo($data_type);
		$dklang = '';
		if($lang!='') $dklang = "&lang=".$lang;
		if($id_item > 0){
			$url = "?page=".$module_info['page'].'&idc='.$id_category.'&id='.$id_item.$dklang;
		}else{
			$url = "?page=".$module_info['page'].'&idc='.$id_category.$dklang;	
		}
		return $url;
	}
	
	public function getUrlCategory($data_type='',$id_category){
		global $DB;
		$sql = "SELECT * FROM url WHERE id_category = $id_category AND data_type = '".$data_type."'";
		$db = $DB->query($sql);
		if($rs = mysqli_fetch_array($db)){
			return $rs['url'];
		}
	}
	
	public function getUrl($data_type='',$id_category,$id_item=0){
		global $DB,$rewrite,$dir_path,$url_max_number_category;	
		$id_category = intval($id_category);
		$id_item = intval($id_item);
		if($data_type != ''){
			$dk = " AND data_type = '".$data_type."' " ;
		}
		$sql = "SELECT * FROM ".$this->url_table." WHERE  id_category=".$id_category." $dk AND id_item= '" .$id_item. "'";
		$db = $DB->query($sql);
		if($rs = mysqli_fetch_array($db)){
			if($rewrite == 1){
				if($data_type=='link'){
					return $rs['url'];
				}else{
                                    if($id_item > 0){
                                        $str= $dir_path.'/'.$this->getUrl($data_type,$id_category).$rs['url'];
                                        $str = $str=str_replace("//","/",$str);
                                        $str = $str=str_replace("////","/",$str);
                                        $str = $str=str_replace("///","/",$str);
					return $str;	
                                    }else{
                                        $str= $dir_path.'/'.$rs['url'];
                                        $str = $str=str_replace("//","/",$str);
                                        $str = $str=str_replace("////","/",$str);
                                        $str = $str=str_replace("///","/",$str);                                        
                                        return $str;	
                                    }
				}
			}else{
				if($data_type=='link'){
					return $rs['url'];
				}else{
					return $dir_path.'/'.$rs['real_url'];	
				}
			}
		}
	}
	
	
	public function getModuleInfo($url){
		global $DB;
		if(substr($url,0,1) == '/'){
			$url = substr($url,1,strlen($url));
		}
		$sql = "SELECT * FROM url WHERE url='".$url."'";
		$db = $DB->query($sql);
		$rs = mysqli_fetch_array($db); 
		$rs['count'] = mysqli_num_rows($db);
		return $rs;
	}
	
	public function clean_url($text){
		return $str=$this->compile($this->RemoveSign($text));
	}
	
	public function RemoveSign($str){
		$coDau=array("à","á","ạ","ả","ã","â","ầ","ấ","ậ","ẩ","ẫ","ă","ằ","ắ"
		,"ặ","ẳ","ẵ","è","é","ẹ","ẻ","ẽ","ê","ề","ế","ệ","ể","ễ","ì","í","ị","ỉ","ĩ",
		"ò","ó","ọ","ỏ","õ","ô","ồ","ố","ộ","ổ","ỗ","ơ"
		,"ờ","ớ","ợ","ở","ỡ",
		"ù","ú","ụ","ủ","ũ","ư","ừ","ứ","ự","ử","ữ",
		"ỳ","ý","ỵ","ỷ","ỹ",
		"đ",
		"À","Á","Ạ","Ả","Ã","Â","Ầ","Ấ","Ậ","Ẩ","Ẫ","Ă"
		,"Ằ","Ắ","Ặ","Ẳ","Ẵ",
		"È","É","Ẹ","Ẻ","Ẽ","Ê","Ề","Ế","Ệ","Ể","Ễ",
		"Ì","Í","Ị","Ỉ","Ĩ",
		"Ò","Ó","Ọ","Ỏ","Õ","Ô","Ồ","Ố","Ộ","Ổ","Ỗ","Ơ"
		,"Ờ","Ớ","Ợ","Ở","Ỡ",
		"Ù","Ú","Ụ","Ủ","Ũ","Ư","Ừ","Ứ","Ự","Ử","Ữ",
		"Ỳ","Ý","Ỵ","Ỷ","Ỹ",
		"Đ","ê","ù","à");
		$khongDau=array("a","a","a","a","a","a","a","a","a","a","a"
		,"a","a","a","a","a","a",
		"e","e","e","e","e","e","e","e","e","e","e",
		"i","i","i","i","i",
		"o","o","o","o","o","o","o","o","o","o","o","o"
		,"o","o","o","o","o",
		"u","u","u","u","u","u","u","u","u","u","u",
		"y","y","y","y","y",
		"d",
		"A","A","A","A","A","A","A","A","A","A","A","A"
		,"A","A","A","A","A",
		"E","E","E","E","E","E","E","E","E","E","E",
		"I","I","I","I","I",
		"O","O","O","O","O","O","O","O","O","O","O","O"
		,"O","O","O","O","O",
		"U","U","U","U","U","U","U","U","U","U","U",
		"Y","Y","Y","Y","Y",
		"D","e","u","a");
		return str_replace($coDau,$khongDau,$str);
	}
	
	private function compile($str){
		$str=trim($str);
		$str = preg_replace("/[^a-zA-Z0-9\s]/", "", $str);
		$str=str_replace("&#160;","",$str);
		$str=str_replace("&nbsp;"," ",$str);
		$str=str_replace("&#39;"," ",$str);
		$str=str_replace("  "," ",$str);
		$str=str_replace(" ","-",$str);
		$str=str_replace("--","-",$str);
		$str=str_replace("---","-",$str);
		$str = str_replace(array('\r|\t|\n','  ','    ','     ','	'), '-', $str);
		return $str=strtolower($str);
	}
	public function getMeta($data_type,$idc,$id){
		global $DB;
		$arr = array("logo","boxsupport","");
		if(!in_array($data_type,$arr)){
			$info_item = $this->typeInfo($data_type);
			$table = $info_item['tableitem'];
			$id_item = $info_item['id_item'];
			$meta = array();
			try{
				if($id){
					$sql = "SELECT title,keywords,description,name FROM ".$table." WHERE ".$id_item."=".$id;
				}else if($data_type=='home'){
					$sql = "SELECT title,keywords,description,name FROM category WHERE data_type='home' LIMIT 1";
				}else{
					$sql = "SELECT title,keywords,description,name FROM category WHERE id_category=".$idc;
				}
				
				$db = $DB->query($sql);
				
				if($rs = mysqli_fetch_array($db)){
					
					if($rs['title']!=''){
						$meta['title'] = $rs['title'];	
					}else{
						$meta['title'] = $rs['name'];	
					}
					if($rs['description']=='' || $rs['description'] == '&nbsp;'){
						$meta['description'] = $rs['name']	;
					}else{
						$meta['description'] = $rs['description'];
						
					}
					$meta['keywords'] = $rs['keywords'];
					
				}
			}
			catch (Exception $e)
			{
			// throw new Exception( 'Something really gone wrong', 0, $e);
			}
			
			
		}
		return $meta;
	}
	
	public function typeInfo($type){
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
                            
			case 'tour':	
				$str['page']='tour';
				$str['tableitem']='tour';
				$str['id_item']='id_tour';
				$str['adminpage']='tour';
				$str['titlemenu']='Tour';
				$str['data_type']="tour";
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
}

?>