<?php

//Nguyen Van Binh

//suongmumc@gmail.com

error_reporting(E_ERROR);

defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );



class builForm{

	function getById($id){

		global $DB;

		$id = intval($id);

		$sql = "SELECT * FROM forms WHERE id = $id";

		$db = $DB->query($sql);

		return mysqli_fetch_array($db);

	}

	

	function getListField($form_id){

		global $DB;

		$form_id = intval($form_id);

		$sql = "SELECT * FROM form_field WHERE form_id = $form_id";

		$db = $DB -> query($sql);

$data = array();
		while($rs = mysqli_fetch_array($db)){

			$data[] = $rs;	

		}

		

		return $data;

	}

	

	static function builInput($data){

		global $dir_path,$DB;

		$input = '';

		if($data['allow_empty_value'] == 0){

			$classnotnull = ' notNull';

			

		}

		

		switch($data['control_type']){

			case "text"	:

				$input = '<input name="'.$data['column_name'].'" control_type="text" type="text" style="'.$data['input_style'].'" class="'.$data['control_css_class'].$classnotnull.'" value="'.$data['default_value'].'" placeholder="'.$data['placeholder'].'" id="'.$data['column_name'].'" data-alert="'.$data['error_message'].'">';

				break;

			case "email"	:

				$input = '<input name="'.$data['column_name'].'"  control_type="email" type="text" style="'.$data['input_style'].'" class="'.$data['control_css_class'].$classnotnull.'" value="'.$data['default_value'].'" placeholder="'.$data['placeholder'].'" id="'.$data['column_name'].'" data-alert="'.$data['error_message'].'">';

				break;

				

			case "submitbuttom":

				$input = '<button name="'.$data['column_name'].'" control_type="submitbuttom" type="text" style="'.$data['input_style'].'" class="'.$data['control_css_class'].'" id="'.$data['column_name'].'" >'.$data['field_caption'].'</button>';

				break;

				

			case "textarea" :

				$input = '<textarea placeholder="'.$placeholder.'" control_type="textarea" name="'.$data['column_name'].'" type="text" style="'.$data['input_style'].'" class="'.$data['control_css_class'].$classnotnull.'"  id="'.$data['column_name'].'" data-alert="'.$data['error_message'].'">'.$data['default_value'].'</textarea>';

				break;

				

			case "captcha" :

				$input = '<img src="'.$dir_path.'/lib/imagesercurity.php"  name="imgCaptcha" align="absmiddle" id="imgCaptcha" style="margin-right:5px;"  /><input name="'.$data['column_name'].'" control_type="captcha" type="text" style="'.$data['input_style'].'" class="'.$data['control_css_class'].$classnotnull.'" value="'.$data['default_value'].'" id="'.$data['column_name'].'" placeholder="'.$data['placeholder'].'" data-alert="'.$data['error_message'].'">';

				break;



			case "radio" :

				$radio = explode(';',strip_tags($data['data_source_text']));

				foreach($radio as $rd){

					$rdio = explode(":",strip_tags($rd));

					$input.= '<label><input control_type="radio" name="'.$data['column_name'].'" type="radio" style="'.$data['input_style'].'" class="'.$data['control_css_class'].$classnotnull.'" value="'.$rdio[0].'" placeholder="'.$data['placeholder'].'" id="'.$data['column_name'].'" data-alert="'.$data['error_message'].'">'.$rdio[1].'</label><br>';	

				}

				break;

			case "checkbox" :

				$radio = explode(';',strip_tags($data['data_source_text']));

				foreach($radio as $rd){

					$rdio = explode(":",strip_tags($rd));

					$input.= '<label><input control_type="checkbox" name="'.$data['column_name'].'[]" type="checkbox" style="'.$data['input_style'].'" class="'.$data['control_css_class'].$classnotnull.'" value="'.$rdio[0].'" placeholder="'.$data['placeholder'].'" id="'.$data['column_name'].'" data-alert="'.$data['error_message'].'">'.$rdio[1].'</label><br>';	

				}

				break;

			case "dropdownlist" :

				$input= '<select control_type="dropdownlist" name="'.$data['column_name'].'" type="checkbox" style="'.$data['input_style'].'" class="'.$data['control_css_class'].$classnotnull.'" id="'.$data['column_name'].'" data-alert="'.$data['error_message'].'">';

				if($data['data_source_type'] == 'option'){

					$radio = explode(';',strip_tags($data['data_source_text']));

					foreach($radio as $rd){

						$rdio = explode(":",strip_tags($rd));

						$input.='<option value="'.$rdio[0].'">'.$rdio[1].'</option>';

						

					}

				}else if($data['data_source_type'] == 'sqlquery'){

					if(strlen($data['data_source_text'])>5){

						try{

							$db = $DB->query($data['data_source_text']);

							while($rs =@ mysqli_fetch_array($db)){

								$input.='<option value="'.$rs[0].'">'.$rs[1].'</option>';	

							}

						}catch (Exception $e) {

							

						}

					}

				}

				$input.= '</select>';

				break;

		}

		return $input;

	}

	function _init($id){

		$forms = $this->getById($id);

		if($forms['id'] > 0){

			

			$layout 		= $forms['layout'];

			$notifi_email 	= $forms['notifi_email'];

			

			/*$f['display_text'] 	= $forms['display_text'];

			$f['url_redirect'] 	= $forms['url_redirect'];

			$f['after_submit'] 	= $forms['after_submit'];

			$f['submit_button_text'] = $forms['submit_button_text'];

			

			$f['id'] 		= $forms['id'];	*/

			$f['formID'] 	= 'form'.$forms['id'];

			$f = $forms;

			$f['formID'] 	= 'form'.$forms['id'];

			$field 			= $this->getListField($forms['id']);

			$input 			= array();

			

			// buil label and input to form

			foreach($field as $rs){			    if (!is_array($rs)) { continue; }


				if($rs['id'] > 0 ){

					$notnullred = '';

					if($rs['allow_empty_value'] == 0){

						$notnullred = '<span class="notNullRed">*</span>';

					}	

					

					$layout = str_replace('{label:'.$rs['id'].':'.$rs['column_name'].'}','<span class="'.$rs['caption_css_class'].'" style="'.$rs['caption_style'].'">'.$rs['field_caption'].' '.$notnullred .'</span>',$layout);

					$layout = str_replace('{input:'.$rs['id'].':'.$rs['column_name'].'}',builForm::builInput($rs),$layout);	

					$input[] = $rs['column_name'];					

					

				}

			}

			$f['input'] = $input;

			// validate not null input

			$layout.='<script> function check_form(){

				var ok = true;

				$(\'#'.$f['formID'].' .error\').remove();

				$(\'#'.$f['formID'].' .notNull\').each(function(){

					if($(this).val() == \'\'){

						$(\'<div class="error">\' + $(this).attr(\'data-alert\') + \'</div>\').insertAfter($(this));

						ok = false;	

					}

				});

				return ok;

			};</script>';

			$f['layout'] = $layout;

			return $f;

		}

	

	}

	

	function builResultForm($id,$data){

		$id = intval($id);

		$forms = $this->getById($id);

		if($forms['id'] > 0){

			$notifi_email 	= $forms['notifi_email'];

			

			$field = $this->getListField($forms['id']);

			$input = array();

			

			// buil label and input to form

			foreach($field as $rs){			    if (!is_array($rs)) { continue; }


				if($rs['id'] > 0 ){

					$notifi_email = str_replace('{label:'.$rs['id'].':'.$rs['column_name'].'}','<span class="" style="'.$rs['caption_style'].'">'.$rs['field_caption'].'</span>',$notifi_email);

					if($rs['control_type'] == 'checkbox'){

						

						$a='';

						foreach($data[$rs['column_name']] as $rd=>$val){

							$a.= '- '.$val."<br>";

						}

						$notifi_email = str_replace('{input:'.$rs['id'].':'.$rs['column_name'].'}',$a,$notifi_email);			

					}else{

						$notifi_email = str_replace('{input:'.$rs['id'].':'.$rs['column_name'].'}',$data[$rs['column_name']],$notifi_email);			

					}

					

				}

			}

		}

	

		return $notifi_email;

	}

	

	function autoresponder_email($id,$data){

		$id = intval($id);

		$forms = $this->getById($id);

		if($forms['id'] > 0){

			$notifi_email 	= $forms['autoresponder_email'];

			

			$field = $this->getListField($forms['id']);

			$input = array();

			

			// buil label and input to form

			foreach($field as $rs){			    if (!is_array($rs)) { continue; }


				if($rs['id'] > 0 ){

					$notifi_email = str_replace('{label:'.$rs['id'].':'.$rs['column_name'].'}','<span class="" style="'.$rs['caption_style'].'">'.$rs['field_caption'].'</span>',$notifi_email);

					if($rs['control_type'] == 'checkbox'){

						

						$a='';

						foreach($data[$rs['column_name']] as $rd=>$val){

							$a.= '- '.$val."<br>";

						}

						$notifi_email = str_replace('{input:'.$rs['id'].':'.$rs['column_name'].'}',$a,$notifi_email);			

					}else{

						$notifi_email = str_replace('{input:'.$rs['id'].':'.$rs['column_name'].'}',$data[$rs['column_name']],$notifi_email);			

					}

					

				}

			}

		}

	

		return $notifi_email;

	}

}



?>