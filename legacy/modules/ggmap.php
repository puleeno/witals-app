<?php

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');


$tpl = new TemplatePower("templates/ggmap.htm");

$tpl->prepare();
langsite();

if ($lang == 'en')
    $sql = "SELECT * FROM contactinfo WHERE active=1 AND id=2";
else
    $sql = "SELECT * FROM contactinfo WHERE active=1 AND id=1";

$db = $DB->query($sql);

if ($rs = mysqli_fetch_array($db)) {

    $tpl->assignGlobal("companyname", $rs['name']);
    $tpl->assignGlobal("contentcontact", $rs['content']);

    $tpl->assignGlobal("title", $rs['title']);

    $tpl->assignGlobal("address", $rs['address']);

    $tpl->assignGlobal("latitude", $rs['latitude']);

    $tpl->assignGlobal("longitude", $rs['longitude']);
}


$tpl->printToScreen();
?>