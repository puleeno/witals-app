<?php 

defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );

	class Cart{
		public function addItem($session, $id_prod, $prod_cat, $quantity){
			global $DB;
			$qty = $this->check_item($session, $id_prod, $prod_cat); 
            if($qty == 0) { 
                $query = "INSERT INTO cart (session, id_prod, prod_cat, quantity, time) VALUES "; 
                $query .= "('$session', '$id_prod', '$prod_cat', '$quantity', '".time()."') "; 
                $DB->query($query); 
            } else { 
                $quantity += $qty; 
                $query = "UPDATE cart SET quantity='$quantity' WHERE session='$session' AND "; 
                $query .= "id_prod='$id_prod' AND prod_cat='$prod_cat' "; 
                $DB->query($query); 
            }
		}	
		
		public function updateItem($session,$id_cart,$quantity){
			global $DB;
			$query = "UPDATE cart SET quantity='$quantity' WHERE session='$session' "; 
            $query .= "AND id_cart='$id_cart' "; 
            $DB->query($query); 	
		}	
		
		public function deleteItem($session, $id_cart){
			global $DB;
			$query = "DELETE FROM cart WHERE session='$session' AND id_cart='$id_cart' "; 
            $DB->query($query); 
		}
		
		function clearCart($session) { 
			global $DB;
            $query = "DELETE FROM cart WHERE session='$session' "; 
            $DB->query($query); 
        }
		
		public function totalCart($session, $id_prod, $prod_cat){
			global $DB;
			$query = "SELECT * FROM cart WHERE session='$session' "; 
            $result = $DB->query($query); 
            if(mysqli_num_rows($result) > 0) { 
                while($row = mysql_fetch_object($result)) { 
					$prod_cat=$row->prod_cat;
					$id_prod=$row->id_prod;
					
                    $query = "SELECT * FROM $prod_cat WHERE id_$prod_cat='$id_prod' "; 
                    $invResult = $DB->query($query); 
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
		
		function count_Cart($session){
			global $DB;
			$sql = "SELECT * FROM cart WHERE session = '$session'";
			$db = $DB->query($sql);
			$quantity = 0;
			while($rs = mysqli_fetch_array($db)){
				$quantity += $rs['quantity'];
			}
			return $quantity;
		}
		
		function check_item($session, $id_prod, $prod_cat) { 
			global $DB;
            $query = "SELECT * FROM cart WHERE session='$session' AND id_prod=$id_prod AND prod_cat='$prod_cat' "; 
            $result = $DB->query($query); 
             
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
		
		function showCart($session) { 
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
				if($row_inventory->pricekm > 0){
					$contents["price"][$count] = $row_inventory->pricekm; 
				}else{
                	$contents["price"][$count] = $row_inventory->price; 
				}
				$contents["don_vi"][$count] = $row_inventory->don_vi; 
				
				$contents["image"][$count] = $row_inventory->image; 
				$contents["xuatxu"][$count] = $row_inventory->xuatxu; 
				$contents["kichthuoc"][$count] = $row_inventory->kichthuoc; 
				$contents["model"][$count] = $row_inventory->model; 
				$contents["thu_tu"][$count] = $row_inventory->thu_tu; 
				
                $contents["quantity"][$count] = $row['quantity']; 
				if($row_inventory->pricekm > 0){
                	$contents["total"][$count] = ($row_inventory->pricekm * $row['quantity']); 
				}else{
					$contents["total"][$count] = ($row_inventory->price * $row['quantity']); 
				}
                $count++; 
            } 
            $total = $this->totalCart($session, $id_prod, $prod_cat); 
            $contents["final"] = $total; 
			$contents["count"]=$count-1;
            return $contents; 
        } 
		
		public function selectListOrder(){
			global $DB;
			$sql="SELECT * FROM member_order";
			$b=$DB->query($sql);
			$data = array(); 
			while ($a=mysqli_fetch_array($b)){	
				$data[] = $a;
			}
			return $data;
		}
		
		public function selectByIdOrder($id){
			global $DB;
			$id = intval($id);
			$sql="SELECT * FROM member_order WHERE id_order = $id";
			$b=$DB->query($sql);	
			$rs = mysqli_fetch_array($b);
			return $rs;
		}
		
		function deleteOrder($id){
			global $DB;
			$id = intval($id);
			$DB->query("DELETE FROM member_order WHERE id_order= $id");
		}
		
		function changeStatus($id,$status){
			global $DB;
			$id 		= intval($id);
			$status 	= intval($status);
			$DB->query("UPDATE member_order SET status = $status WHERE id_order = $id");
		}
		function updateViewStatus($id){
			global $DB;
			$id = intval($id);
			$DB->query("UPDATE member_order SET xem=1 WHERE id_order = $id");
		}
	}
	
?>