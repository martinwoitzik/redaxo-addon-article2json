<?php
/**
 * Rex2JSON
 * Redaxo-Inhalte als JSON ausgeben
 *
 * @author m[PUNKT]woitzik[AT]gmx[PUNKT]de Martin Woitzik
 * @package redaxo4.x
 */

$mypage = 'rex2json';
$addon_file_source = 'include/addons/' . $mypage . '/css';
$addon_file_dest = '../files/'.$mypage;
 
// DELETE/UPDATE DATABASE
$sql = new sql;
$sql->setQuery("DROP TABLE rex_999_rex2json");

// DELETE/UPDATE MODULES
// -none-

// DELETE/UPDATE PAGES
// CSS Datei aus files/rex2json/  loeschen
function rex2json_uninstaller($addon_file_source, $addon_file_dest)
{
	$dir = opendir($addon_file_source);
	while (($filename = readdir($dir)) !== false)
	{
		if ($filename != '.' AND $filename != '..')
		{
			if (!is_dir($addon_file_source . '/' . $filename))
			{
			unlink($addon_file_dest . '/' . $filename);
			}
			else
			{
			developer_uninstaller($addon_file_source . '/' . $filename, $addon_file_dest . '/' . $filename);
			}
		}
	}
	closedir($dir);
}

rex2json_uninstaller( $addon_file_source, $addon_file_dest );
rmdir( $addon_file_dest );

// REGENERATE SITE
// -none- vielleicht den gesamten Cache leeren?


$REX['ADDON']['install']['rex2json'] = 0;

?>