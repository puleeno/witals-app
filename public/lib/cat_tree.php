<?php

defined( '_VALID_NVB' ) or die( 'Direct Access to this location is not allowed.' );



class cat_tree_binh{

	var $n;

	var $current_idcat;

	var $navcat;

	var $subcatlist;

	function cat_tree($current_idcat=0)

	{

		$this->current_idcat=$current_idcat;

		$this->navcat=array();

		$this->subcatlist=array();

	}

	function get_cat_tree($parent = 0,$table,$idcat,$id_tour,$idc)

	{

		global $DB,$tree; // add $cat_old

		$sql="select * from $table where parentid='$parent' and id_tour=$id_tour order by thu_tu asc, $idcat desc";

		$raw = $DB->query($sql);

		// add -- if it has childs

		if ($DB->get_affected_rows() > 0) {

			$this->n++;

		} else {

			return;

		} while ($result = mysqli_fetch_array($raw)) {

		/*

			if ($result['pcatid'] == $childcat_old['pcatid']) {

				continue; // remove  cats from list

			}

			*/

			for($i = 0;$i < $this->n;$i++) {

				$tree[$result[$idcat]]['name'] .= '-- ';

			}

			$tree[$result[$idcat]]['name'] .= $result['name'];

			$this->get_cat_tree($result[$idcat],$table,$idcat,$id_tour);

		}

		// all childs listed, remove --

		$this->n--;

	}

	function get_cat_string($id_catm,$table, $idcat,$id_tour,$idc)

	{

		global $DB,$catstring;

		if ($id_cat==0)

			return;

		else

		{

			$sql="select * from $table where $idcat=".$id_cat." and id_tour=$id_tour";

			$a=$DB->query($sql);

			if ($b=mysqli_fetch_array($a))

			{

				$catstring = $b['name']." > ".$catstring;

				$this->get_cat_string($b['parentid'],$table,$idcat,$id_tour,$idc);

			}

		}



	}

	function get_cat_string_admin($id_cat,$par_page,$table,$idcat,$id_tour,$idc)

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

			$sql="select * from $table where $idcat=".$id_cat." and id_tour=$id_tour";

			$a=$DB->query($sql);

			if ($b=mysqli_fetch_array($a))

			{

				$catstring2 = "<a href='main.php?page=".$par_page."&idtour=$id_tour&idc=$idc&pid=".$id_cat."' $showclass>".$b['name']."</a> > ".$catstring2;

				$this->get_cat_string_admin($b['parentid'],$par_page,$table,$idcat,$id_tour,$idc);

			}

		}



	}

	function get_cat_nav($id_cat,$table,$idcat,$id_tour,$idc)

	{

		global $DB;

		if ($id_cat==0)

		{

			ksort($this->navcat);

			return;

		}

		else

		{

			$sql="select * from $table where $idcat=".$id_cat." and id_tour=$id_tour";

			$a=$DB->query($sql);

			if ($b=mysqli_fetch_array($a))

			{

				$this->navcat[$b[$idcat]]['name']=$b['name'];

				$this->get_cat_nav($b['parentid'],$table,$idcat,$id_tour,$idc);

			}

		}



	}

	function get_sub_cat_list($id_cat,$table,$idcat,$id_tour,$idc)

	{

		global $DB;

		$raw = $DB->query("select * from $table where parentid='$id_cat' and id_tour=$id_tour order by thu_tu asc,$idcat desc");

		if ($DB->get_affected_rows() == 0) {

			return;

		}

		while ($result = mysqli_fetch_array($raw)) {

			$this->subcatlist[$result[$idcat]]['name'] = $result['name'];

			$this->get_sub_cat_list($result[$idcat],$table,$idcat,$id_tour,$idc);

		}

	}





}









class catpt_tree{

	var $n;

	var $current_idcatpt;

	public function __construct($current_idcatpt=0)

	{

		$this->current_idcatpt=$current_idcatpt;

	}

	function get_catpt_tree($parent = 0)

	{

		global $DB,$tree; // add $catpt_old

		$raw = $DB->query("select * from catpt where parentid='$parent' order by id_catpt asc");

		// add -- if it has childs

		if ($DB->get_affected_rows() > 0) {

			$this->n++;

		} else {

			return;

		} while ($result = mysqli_fetch_array($raw)) {

		/*

			if ($result['pcatptid'] == $childcatpt_old['pcatptid']) {

				continue; // remove  catpts from list

			}

			*/

			for($i = 0;$i < $this->n;$i++) {

				$tree[$result['id_catpt']]['name'] .= '-- ';

			}

			$tree[$result['id_catpt']]['name'] .= $result['name'];

			$this->get_catpt_tree($result['id_catpt']);

		}

		// all childs listed, remove --

		$this->n--;

	}

	function get_catpt_string($id_catpt)

	{

		global $DB,$catptstring;

		if ($id_catpt==0)

			return;

		else

		{

			$sql="select * from catpt where id_catpt=".$id_catpt;

			$a=$DB->query($sql);

			if ($b=mysqli_fetch_array($a))

			{

				$catptstring = $b['name']." > ".$catptstring;

				$this->get_catpt_string($b['parentid']);

			}

		}



	}

	function get_catpt_string_admin($id_catpt)

	{

		global $DB,$catptstring2;

		if ($id_catpt==0)

			return;

		else

		{

			if ($this->current_idcatpt==$id_catpt)

			{

				$showclass="class='currentcat'";

			}

			else

			{

				$showclass="";

			}

			$sql="select * from catpt where id_catpt=".$id_catpt;

			$a=$DB->query($sql);

			if ($b=mysqli_fetch_array($a))

			{

				$catptstring2 = "<a href='main.php?act=catpt&pid=".$id_catpt."' $showclass>".$b['name']."</a> > ".$catptstring2;

				$this->get_catpt_string_admin($b['parentid']);

			}

		}



	}





}



?>