<?php 

// Nguyen Binh
// suongmumc@gmail.com

error_reporting(E_ALL);

defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );

$tpl=new TemplatePower("templates/order_view.htm");
$tpl->prepare();


langsite();

$tpl->assignGlobal("dir_path", $dir_path);

$tpl->assignGlobal("lang", $lang);
$tpl->assignGlobal("lang_dir", $lang_dir);


require "legacy/lib/shoppingcart.php";
$cart=new Cart;
$order=new Order1;
$id=intval($_GET['cid']);

if ( $id)
{
	$tpl->newBlock("viewcart");
	$sql="Select * from orders where id_order=".$id;
	$a=$DB->query($sql);
	$info=array();
	if ($b=mysqli_fetch_array($a))
	{
		$session=$b['session'];
		$tpl->assign("id_order",$b['id_order']);
		$tpl->assign("name",$b['name']);
		$tpl->assign("phone",$b['phone']);
		$tpl->assign("code",$b['code']);
		$tpl->assign("email",$b['email']);
		$tpl->assign("address",$b['address']);
		$tpl->assign("addinfo",$b['addinfo']);
		$tpl->assign("status".$b['status'],"selected='selected'");
		$tpl->assign("time",date('d/m/Y H:i',$b['time']))	;	
		$tpl->assign("g_email",$b['g_email']);
		$tpl->assign("g_address",$b['g_address']);
		$tpl->assign("g_addinfo",$b['g_addinfo']);
		$tpl->assign("g_name",$b['g_name']);
		$tpl->assign("g_phone",$b['g_phone']);

		$cont=$cart->display_contents($b['session']);
		$x=0;
		for ($i=0;$i<=$cont['count'];$i++)
		{
			$x++;
			$tpl->newBlock("list_row_cart");
			$tpl->assign("tt",$x);
			$tpl->assign("name",$cont['name'][$i]);			
			$pro_if = getLinkPro($cont['id_prod'][$i]);
			$tpl->assign("link",$dir_path.'/'.$pro_if['link']);
			$tpl->assign("image",$pro_if['image']);
			
			$tpl->assign("price",number_format((float)($cont['price'][$i]),0,',','.'));
			$tpl->assign("quantity",$cont['quantity'][$i]);
			$tpl->assign("total",number_format((float)($cont['total'][$i]),0,',','.'));
			$info['id']=$cont['id_cart'][$i];
			$info['cart']=$info['cart'];
		}
		$tpl->assignGlobal("final",number_format((float)($cont['final']),0,',','.'));
	}
}

$tpl->printToScreen();



function getLinkPro($id){
	global $DB,$cache_image_path,$dir_path;
	$id = intval($id);
	$pro = array();
	$sql = "SELECT * FROM product WHERE id_product = $id";
	$db = $DB->query($sql);
	if($rs = mysqli_fetch_array($db)){
		$pro['link'] = url_process::getUrlCategory($rs['id_category']).$rs['url'];
		$pro['image'] = '<img src="'.$cache_image_path.resizeimage(120,120,$dir_path.'/'.$rs['image']).'" class="cartimage"/>';
	}
	return $pro;
		
}


?>