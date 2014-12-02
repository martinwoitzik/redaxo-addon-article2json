<?php

error_reporting(0);
//error_reporting(E_ALL);


$mypage = "rex2json";
$subpage = rex_request('subpage', 'string');


include $REX["INCLUDE_PATH"]."/layout/top.php";

if ($subpage == "scheme_config") {
	rex_title( "JSON Schemas zuweisen", $REX['ADDON'][$mypage]['SUBPAGES'] );
	include $REX["INCLUDE_PATH"]."/addons/$mypage/pages/scheme_config.inc.php";
} else if ($subpage == "export") {
    rex_title( "Daten exportieren", $REX['ADDON'][$mypage]['SUBPAGES'] );
    include $REX["INCLUDE_PATH"]."/addons/$mypage/pages/export.inc.php";
} else {
	rex_title( "JSON Schemas zuweisen", $REX['ADDON'][$mypage]['SUBPAGES'] );
	include $REX["INCLUDE_PATH"]."/addons/$mypage/pages/scheme_config.inc.php";
}

include $REX["INCLUDE_PATH"]."/layout/bottom.php";


?>
}