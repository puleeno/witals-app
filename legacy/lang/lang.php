<?php


defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');
$langLabel = array();

function langsite($tplLang = '') {

    global $lang, $tpl, $LANG, $langLabel;
    if ($tplLang == '')
        $tplLang = $tpl;


    if ($LANG) {

        foreach ($LANG as $lg => $l) {            if (!is_array($l)) { continue; }


            if ($lang == 'en') {
                $langLabel["$lg"] = $l['en'];
                $tplLang->assignGlobal($lg, $l['en']);
            } elseif ($lang == 'cn') {
                $langLabel["$lg"] = $l['cn'];
                $tplLang->assignGlobal($lg, $l['cn']);
            }elseif ($lang == 'kr') {
                $langLabel["$lg"] = $l['kr'];
                $tplLang->assignGlobal($lg, $l['kr']);
            } else {
                $langLabel["$lg"] = $l['default'];
                $tplLang->assignGlobal($lg, $l['default']);
            }
        }
    }
}

function langLabel() {

    global $lang, $LANG, $langLabel;


    foreach ($LANG as $lg => $l) {        if (!is_array($l)) { continue; }


        if ($lang == 'en')
            $langLabel["$lg"] = $l['en'];
        elseif ($lang == 'cn')
            $langLabel["$lg"] = $l['cn'];
        elseif ($lang == 'kr')
            $langLabel["$lg"] = $l['kr'];
        else
            $langLabel["$lg"] = $l['default'];
    }
}

?>