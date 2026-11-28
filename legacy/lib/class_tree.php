<?php

/*defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );

class class_tree extends cat_tree 

{

	function get_cat_tree($parent = 0,$category,$id_category,$data_type)

	{

		global $DB,$tree,$lang; // add $cat_old

		$raw = $DB->query("select * from ".$category." where parentid='$parent' and data_type='$data_type' and lang='".$lang."' order by thu_tu asc,$id_category desc");

		if ($DB->get_affected_rows() > 0) {

			$this->n++;

		} else {

			return;

		} while ($result = mysqli_fetch_array($raw)) {

			for($i = 0;$i < $this->n;$i++) {

				$tree[$result[$id_category]]['name'] .= '-- ';

			}

			$tree[$result[$id_category]]['name'] .= $result['name'];

			$this->get_cat_tree($result[$id_category],$category,$id_category,$data_type);

		}

		// all childs listed, remove --

		$this->n--;

	}

	function get_cat_tree1($parent = 0,$category,$id_category)

	{

		global $DB,$tree1; // add $cat_old

		$raw = $DB->query("select * from ".$category." where parentid='$parent' and lang='".$lang."' order by thu_tu asc,$id_category desc");

		if ($DB->get_affected_rows() > 0) {

			$this->n++;

		} else {

			return;

		} while ($result = mysqli_fetch_array($raw)) {

			for($i = 0;$i < $this->n;$i++) {

				$tree1[$result[$id_category]]['name'] .= '-- ';

			}

			$tree1[$result[$id_category]]['name'] .= $result['name'];

			$this->get_cat_tree1($result[$id_category],$category,$id_category);

		}

		// all childs listed, remove --

		$this->n--;

	}

	function get_cat_string_admin1($id_cat,$par_page,$category,$id_category)

	{

		global $DB,$catstring2;

		if ($id_cat==0)

			return;

		else

		{

			if ($this->current_idcat==$id_cat)

			{

				$showclass="class='link_name'";

			}

			else

			{

				$showclass="class='link_name'";

			}

			$sql="select * from ".$category." where $id_category=".$id_cat." and lang='".$lang."'";

			$a=$DB->query($sql);

			if ($b=mysqli_fetch_array($a))

			{

				$catstring2 = ">&nbsp;<a href='main.php?page=".$par_page."&pid=".$id_cat."' $showclass>".$b['name']."</a>  ".$catstring2;

				$this->get_cat_string_admin1($b['parentid'],$par_page,$category,$id_category);

			}

		}

	

	}

	function get_cat_string_admin($id_cat,$par_page,$category,$id_category,$data_type)

	{

		

		global $DB,$catstring2,$lang;

		if ($id_cat==0)

			return;

		else

		{

			if ($this->current_idcat==$id_cat)

			{

				$showclass="class='link_name'";

			}

			else

			{

				$showclass="class='link_name'";

			}

			$sql="select * from ".$category." where $id_category=".$id_cat." and data_type='$data_type' and lang='".$lang."'";

			$a=$DB->query($sql);

			if ($b=mysqli_fetch_array($a))

			{

				$catstring2 = ">&nbsp;<a href='main.php?page=".$par_page."&pid=".$id_cat."' $showclass>".$b['name']."</a>  ".$catstring2;

				$this->get_cat_string_admin($b['parentid'],$par_page,$category,$id_category,$data_type);

			}

		}

	

	}

	function get_cat_string_client($id_cat,$par_page,$category,$id_category,$idc='idc')

	{

		global $DB,$catstring2,$_SESSION,$lang;

		if ($id_cat==0)

			return;

		else

		{

			if ($this->current_idcat==$id_cat)

			{

				$showclass="class='link_name'";

			}

			else

			{

				$showclass="class='link_name'";

			}

			$sql="select * from ".$category." where $id_category=".$id_cat." and lang='".$lang."'";

			$a=$DB->query($sql);

			if ($b=mysqli_fetch_array($a))

			{

				$catstring2 = ">&nbsp;<span $showclass><a href='?page=".$par_page."&".$idc."=".$id_cat."' >".$b['name'.$_SESSION['lang']]."</a></span>  ".$catstring2;

				$this->get_cat_string_client($b['parentid'],$par_page,$category,$id_category,$idc);

			}

		}

	

	}

}*/

?>