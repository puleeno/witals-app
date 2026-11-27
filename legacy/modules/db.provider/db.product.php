<?php
//Develop Team: Binh Minh group
//binhminhgroup@gmail.com
//error_reporting(E_ERROR);

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

class dbProduct {

    public function itemList($idc, $limit = 6, $filter = '', $orderby = '') {

        global $DB, $SETTING, $dir_path, $_GET;
        $idc = intval($idc);

        if ($_GET['qr']) {

            $qr = clean_value($_GET['qr']);

            $search = " AND name LIKE '%" . $qr . "%' ";
        }
        if ($orderby != '') {
            $orderby = $orderby;
        } else {
            $orderby = 'ORDER BY thu_tu DESC, name ASC';
        }
        $data = array();

        $hang = explode("/", $_SERVER['REQUEST_URI']);
        if ($hang[1] == 'hang') {
            $hang_id = $this->getManufacture($hang[2]);
        } else {
            $hang_id = 0;
        }
        if ($hang_id > 0) {
            $sql = "SELECT * FROM product WHERE active=1 AND id_manufacture = " . intval($hang_id) . "  $filter $search $orderby";
            $db1 = paging::pagingFonten($_GET['p'], $dir_path . "/hang/" . $hang[2] . '/', $sql, 8, 200);
        } else {
            $sql = "SELECT * FROM product WHERE active=1 AND (id_category IN (" . Category::getParentId($idc) . ") OR groupcat LIKE '%:" . $idc . ":%') $filter $search $orderby";
            $db1 = paging::pagingFonten($_GET['p'], $dir_path . "/" . url_process::getUrlCategory($idc), $sql, 8, $limit);
        }

        while ($rs = mysqli_fetch_array($db1['db']))
            $data[] = $rs;

        $data['pages'] = $db1['pages'];

        return $data;
    }

    public function itemDetail($id) {

        global $DB, $SETTING;

        $id = intval($id);

        $sql = "SELECT * FROM product WHERE id_product = $id";

        $db = $DB->query($sql);

        $rs = mysqli_fetch_array($db);

        return $rs;
    }

    public function item_other($idc, $id) {

        global $DB, $SETTING;

        $idc = intval($idc);

        $id = intval($id);

        $sql = "SELECT * FROM product WHERE active = 1 AND id_product <> $id AND (id_category IN(" . Category::getParentId($idc) . ") OR groupcat LIKE '%:" . $idc . ":%') ORDER BY RAND() LIMIT 0,16";

        $db = $DB->query($sql);

$data = array();
        while ($rs = mysqli_fetch_array($db)) {

            $data[] = $rs;
        }

        return $data;
    }

    /* public function other_product($tags, $id) {

      global $DB, $SETTING,$idc;

      $idc = intval($idc);

      $id = intval($id);

      if($tags){
      $sql ="SELECT * FROM product
      WHERE name LIKE '%$tags%'  AND id_product <> $id AND active = 1
      ORDER BY IF( name LIKE '$tags%',0,
      IF( name LIKE '%$tags', 1, 2 ) ) LIMIT 6";
      }else{
      $sql = "SELECT * FROM product WHERE active = 1 AND  (id_category IN(" . Category::getParentId($idc) . ") OR groupcat LIKE '%:" . $idc . ":%') AND id_product <> $id  ORDER BY id_product DESC LIMIT 0,6";
      }

      $db = $DB->query($sql);

$data = array();
      while ($rs = mysqli_fetch_array($db)) {

      $data[] = $rs;

      }

      return $data;

      } */

    public function other_product($id, $id_category, $limit = 6) {
        global $DB, $SETTING;
        $id_category = intval($id_category);
        $id = intval($id);

        $limit = intval($limit);

        $sql = "SELECT * FROM product WHERE active = 1 AND  (id_category IN(" . Category::getParentId($id_category) . ")) AND id_product <> $id  ORDER BY RAND() LIMIT 0, $limit ";

        $db = $DB->query($sql);

$data = array();
        while ($rs = mysqli_fetch_array($db))
            $data[] = $rs;

        // $data['pages'] = $db['pages'];
        return $data;
    }

    public function splienquan($listID) {

        global $DB, $SETTING;
        $idp = explode(",", $listID);
        $lstID = '0';
        foreach ($idp as $id) {
            $lstID .= ',' . intval($id);
        }

        $sql = "SELECT * FROM product WHERE active = 1 AND id_product IN(" . $lstID . ") ORDER BY id_product DESC LIMIT 0,10";
        $db = $DB->query($sql);
$data = array();
        while ($rs = mysqli_fetch_array($db)) {
            $data[] = $rs;
        }
        return $data;
    }

    public function item_old($idc, $id) {

        global $DB, $SETTING, $id;

        $id = intval($id);

        $idc = intval($idc);

        $sql = "SELECT * FROM product WHERE active = 1 AND  (id_category IN(" . Category::getParentId($idc) . ") OR groupcat LIKE '%:" . $idc . ":%') AND id_product < $id  ORDER BY id_product DESC LIMIT 0,5";

        $db = $DB->query($sql);

$data = array();
        while ($rs = mysqli_fetch_array($db)) {

            $data[] = $rs;
        }

        return $data;
    }

    function getAttr($idc, $attr) {

        global $DB;

        $idc = intval($idc);

        $attribute = json_decode($attr);



        $sql = "SELECT * FROM category WHERE id_category = $idc";

        $db = $DB->query($sql);

        if ($rs = mysqli_fetch_array($db)) {

            $sql1 = "SELECT g.*,p.name AS attrName, p.default_value AS defaultvalue,p.alias_name as aliasname,p.id  FROM group_attribute AS g LEFT JOIN pro_attribute AS p ON(g.id_attr = p.id) WHERE g.id_group = $rs[id_attr] ORDER BY g.thu_tu ASC, p.name";



            $str = '';

            $db1 = $DB->query($sql1);

            while ($rs1 = mysqli_fetch_array($db1)) {

                if ($rs1['attrName']) {



                    if ($attribute->$rs1['id_attr'] > 0) {

                        $str .= '<li>' . $rs1['attrName'] . ': ' . $attribute->$rs1['id_attr'] . '</li>';
                    }
                }
            }

            return $str;
        }
    }

    function getAttrDetail($idc, $attr) {

        global $DB;

        $idc = intval($idc);

        $attribute = json_decode($attr);

        $sql = "SELECT * FROM category WHERE id_category = $idc";

        $db = $DB->query($sql);

        if ($rs = mysqli_fetch_array($db)) {

            $sql1 = "SELECT g.*,p.name AS attrName, p.default_value AS defaultvalue,p.alias_name as aliasname FROM group_attribute AS g LEFT JOIN pro_attribute AS p ON(g.id_attr = p.id) WHERE g.id_group = " . intval($rs['id_attr']) . " ORDER BY g.thu_tu ASC, p.name";

            $db1 = $DB->query($sql1);

            $data = array();

            while ($rs1 = mysqli_fetch_array($db1)) {

                if ($rs1['attrName']) {

                    if ($attribute->$rs1['id_attr'] != '') {



                        $data[$rs1['attrName']] = $attribute->$rs1['id_attr'];
                    }
                }
            }

            return $data;
        }
    }

    function getSizePriceDetail($size_price, $km) {
        global $DB, $tpl;
        $sizePrice = json_decode($size_price);

        $sql = "SELECT * FROM dm_size ORDER BY thu_tu DESC";
        $db = $DB->query($sql);
        while ($rs = mysqli_fetch_array($db)) {
            if ($sizePrice->$rs['id_size'] > 0) {
                $tpl->newBlock("size_price");
                $tpl->assign("name", $rs['name']);
                $tpl->assign("id_size", $rs['id_size']);
                if (intval($km) > 0) {
                    $tpl->assign("size_price_value", number_format((float)($sizePrice->$rs['id_size'] - ($km * $sizePrice->$rs['id_size']) / 100)));
                } else {
                    $tpl->assign("size_price_value", number_format((float)(($sizePrice->$rs['id_size']))));
                }
                $tpl->assign("size_price", number_format((float)($sizePrice->$rs['id_size'])));
                $tpl->assign("price_real", $sizePrice->$rs['id_size']);
                $tpl->assign("id_size", $rs['id_size']);
            }
        }
    }

    function getSizePriceDefault($size_price, $km) {
        global $DB, $tpl;
        $sizePrice = json_decode($size_price);

        $sql = "SELECT * FROM dm_size ORDER BY thu_tu DESC";
        $db = $DB->query($sql);
        while ($rs = mysqli_fetch_array($db)) {
            if ($sizePrice->$rs['id_size'] > 0) {
                $tpl->assign("id_size", $rs['id_size']);
                if (intval($km) > 0) {
                    $price['pricekm'] = number_format((float)($sizePrice->$rs['id_size'] - ($km * $sizePrice->$rs['id_size']) / 100));
                } else {
                    $price['pricekm'] = 0;
                }

                $price['price'] = number_format((float)($sizePrice->$rs['id_size']));

                break;
            }
            //$tpl->assign("price_real",$sizePrice->$rs['id_size']);
        }
        return $price;
    }

    private function getManufacture($alias_name) {
        global $DB;
        $alias_name = clean_value($alias_name);
        $sql = "SELECT * FROM product_manufacture WHERE (alias_name = '" . $alias_name . "')";
        $db = $DB->query($sql);
        if ($rs = mysqli_fetch_array($db)) {
            return $rs['id'];
        } else {
            return 0;
        }
    }

}

?>