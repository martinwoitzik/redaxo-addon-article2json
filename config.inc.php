<?php
/**
 * Rex2JSON
 * Redaxo-Inhalte als JSON ausgeben
 *
 * @author m[PUNKT]woitzik[AT]gmx[PUNKT]de Martin Woitzik
 * @package redaxo4.x
 */

// addon identifier
$mypage = 'rex2json';
$basedir = dirname(__FILE__);


$REX['ADDON']['rxid'][$mypage] = '999';
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['name'][$mypage] = "REXtoJSON";
$REX['ADDON']['perm'][$mypage] = 'rex2json[]';
$REX['ADDON']['version'][$mypage] = '1.0';
$REX['ADDON']['author'][$mypage] = 'Martin Woitzik <m.woitzik[AT]gmx[PUNKT]de>';

// Berechtigungen
$REX['PERM'][] = 'rex2json[]';

// Unterseiten konfigurieren
$REX['ADDON'][$mypage]['SUBPAGES'] = array();
$REX['ADDON'][$mypage]['SUBPAGES'][] = array ('scheme_config', "Modulschemas konfigurieren" );
$REX['ADDON'][$mypage]['SUBPAGES'][] = array ('export', "Daten exportieren" );

// CSS einbinden
rex_register_extension('PAGE_HEADER', 'rex_ar2json_insertCss');
function rex_ar2json_insertCss($params)
{
  return $params['subject'] .'  <link rel="stylesheet" type="text/css" href="../files/rex2json/rex2json.css" />'. "\n";
}

// Hauptklasse Rex2Flash einbinden
require $basedir . '/classes/class.Rex2JSON.php';
Rex2JSON::instance();

?>