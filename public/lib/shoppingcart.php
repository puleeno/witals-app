<?php 

defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );

class Cart { 

        function check_item($session, $id_prod, $prod_cat) { 

            $query = "SELECT * FROM cart WHERE session='$session' AND id_prod=$id_prod AND prod_cat='$prod_cat' "; 

            $result = mysql_query($query); 

             

            if(!$result) { 

                return 0; 

            } 



            $numRows = mysqli_num_rows($result); 



            if($numRows == 0) { 

                return 0; 

            } else { 

                $row = mysql_fetch_object($result); 

                return $row->quantity; 

            } 

        } 



        function add_item($session, $id_prod, $prod_cat, $quantity) { 



            $qty = $this->check_item($session, $id_prod, $prod_cat); 

            if($qty == 0) { 

                $query = "INSERT INTO cart (session, id_prod, prod_cat, quantity, time) VALUES "; 

                $query .= "('$session', '$id_prod', '$prod_cat', '$quantity', '".time()."') "; 

                mysql_query($query); 

            } else { 

                $quantity += $qty; 

                $query = "UPDATE cart SET quantity='$quantity' WHERE session='$session' AND "; 

                $query .= "id_prod='$id_prod' AND prod_cat='$prod_cat' "; 

                mysql_query($query); 

            } 

        } 

        function delete_item($session, $id_cart) { 

            $query = "DELETE FROM cart WHERE session='$session' AND id_cart='$id_cart' "; 

            mysql_query($query); 

        } 

         

        function modify_quantity($session, $id_cart, $quantity) { 

            $query = "UPDATE cart SET quantity='$quantity' WHERE session='$session' "; 

            $query .= "AND id_cart='$id_cart' "; 

            mysql_query($query); 

        } 

        function clear_cart($session) { 

            $query = "DELETE FROM cart WHERE session='$session' "; 

            mysql_query($query); 

        } 

        function cart_total($session) { 

            $query = "SELECT * FROM cart WHERE session='$session' "; 

            $result = mysql_query($query); 

            if(mysqli_num_rows($result) > 0) { 

                while($row = mysql_fetch_object($result)) { 

					$prod_cat=$row->prod_cat;

					$id_prod=$row->id_prod;

					

                    $query = "SELECT price,pricekm FROM $prod_cat WHERE id_$prod_cat='$id_prod' "; 

                    $invResult = mysql_query($query); 

                    $row_price = mysql_fetch_object($invResult); 

					if($row_price->pricekm>0){

						$total += ($row_price->pricekm * $row->quantity); 					

					}else{

						$total += ($row_price->price * $row->quantity); 	

					}

                    

					

					

                } 

            } 

            return $total; 

        }

		

        function display_contents($session) { 

			global $DB;

            $count = 0; 

            $query = "SELECT * FROM cart WHERE session='$session' ORDER BY id_cart "; 

            $a = $DB->query($query); 

            while ($row = mysqli_fetch_array($a)) 

			{ 

				$prod_cat=$row['prod_cat'];

				$id_prod=$row['id_prod'];			

                $query = "SELECT * FROM $prod_cat WHERE id_$prod_cat='$id_prod' "; 

                $result_inv = $DB->query($query); 

                $row_inventory = mysql_fetch_object($result_inv); 

				$contents["id_cart"][$count]=$row['id_cart'];

				$contents["id_prod"][$count]=$row['id_prod'];

                $contents["name"][$count] = $row_inventory->name; 

 				$contents["ma"][$count]=$row_inventory->ma;

				if($row_inventory->pricekm>0){

					$contents["price"][$count] = $row_inventory->pricekm; 

				}else{

                	$contents["price"][$count] = $row_inventory->price; 

				}

				$contents["don_vi"][$count] = $row_inventory->don_vi; 

                $contents["quantity"][$count] = $row['quantity']; 

				if($row_inventory->pricekm>0){

                	$contents["total"][$count] = ($row_inventory->pricekm * $row['quantity']); 

				}else{

					$contents["total"][$count] = ($row_inventory->price * $row['quantity']); 

				}

                $count++; 

            } 

            $total = $this->cart_total($session, $id_prod, $prod_cat); 

            $contents["final"] = $total; 

			$contents["count"]=$count-1;

            return $contents; 

        } 

        function num_items($session) { 

            $query = "SELECT * FROM cart WHERE session='$session' "; 

            $result = mysql_query($query); 

            $num_rows = mysqli_num_rows($result); 

            return $num_rows; 

        } 

        function quant_items($session) { 

            $quant = 0; 

            $query = "SELECT * FROM cart WHERE session='$session' "; 

            $result = mysql_query($query); 

            while($row = mysql_fetch_object($result)) { 

                $quant += $row->quantity; 

            } 

            return $quant; 

        } 

    } 

	

	class Order1

	{

		function add_order($session,$name,$tel,$email,$address,$addinfo,$miss,$username,$phone,$g_phone,$g_addinfo,$g_address,$g_name,$g_email)

		{

			global $DB;

			if ($session and $name  and $email and $address )

			{

				$sql="SELECT max(id_order) as code FROM orders";

				$db=$DB->query($sql);

				$rs=mysqli_fetch_array($db);

				$code=intval($rs['code'])+1;

				$sql="INSERT INTO orders (session,name,tel,email,address,addinfo,time,username,phone,code,g_name,g_address,g_email,g_phone,g_addinfo) VALUES ('$session','$name','$tel','$email','$address','$addinfo','".time()."','".$username."','".$phone."','".$code."','".$g_name."','".$g_address."','".$g_email."','".$g_phone."','".$g_addinfo."')";

				

				mysql_query($sql);

				if ($miss)

				{

					$save['username']=$username;

					$save['name']=$name;

					$save['tel']=$tel;

					$save['phone']=$phone;

					$save['email']=$email;

					$save['address']=$address;

					$save['addinfo']=$addinfo;

					$save['code']=$code;

					

					$save['g_name']=$g_name;

					$save['g_phone']=$g_phone;

					$save['g_email']=$g_email;

					$save['g_address']=$g_address;

					$save['g_addinfo']=$g_addinfo;



				}

				$info=array();

				$info['username']=$username;

				$info['name']=$name;

				$info['tel']=$tel;

				$info['phone']=$phone;

				$info['code']=$code;

				$info['email']=$email;

				$info['address']=$address;

				$info['addinfo']=$addinfo;

				

				$info['g_phone']=$g_phone;

				$info['g_email']=$g_email;

				$info['g_address']=$g_address;

				$info['g_addinfo']=$g_addinfo;

				$info['g_name']=$g_name;

				

				return $info;

			}

			else

			{

				echo "Qu&#253; kh&#225;ch &#273;&#227; nh&#7853;p thi&#7871;u th&#244;ng tin c&#7847;n thi&#7871;t !";

				return "";

			}

		}

		function delete_order($id_order)

		{

			global $DB;

			$sql="SELECT session FROM orders WHERE id_order=".$id_order;

			$a=$DB->query($sql);

			if ($b=mysqli_fetch_array($a))

			{

				$session=$b['session'];

				$sql="DELETE FROM cart WHERE session='".$session."'";

				mysql_query($sql);	

			}

			$sql="DELETE FROM orders WHERE id_order='".$id_order."'";

			mysql_query($sql);

		}

		function delete_cart_not_order()

		{

			$timelimit=3600*24*10; //10 days

			$sql="DELETE FROM cart HAVING (time+".$timelimit.")<".time." AND session NOT IN (SELECT session FROM orders)";

			mysql_query($sql);

		}

	

	}

?> 

