<?php
/**
 * Rex2JSON
 * Redaxo-Inhalte als JSON ausgeben
 *
 * @author m[PUNKT]woitzik[AT]gmx[PUNKT]de Martin Woitzik
 * @package redaxo4.x
 */

$mypage = "rex2json";
$addon_file_source = 'include/addons/' . $mypage . '/css';
$addon_file_dest = '../files/'.$mypage;

if (intval(PHP_VERSION) < 5) {
	$REX['ADDON']['installmsg']['rex2json'] = 'Dieses Addon ben&ouml;tigt PHP 5!';
	$REX['ADDON']['install']['rex2json'] = 0;
}
else
{
	// CREATE/UPDATE DATABASE
	$sql = new sql;

	// Tabelle erstellen - wenn noch nicht existent
	$sql->setQuery("CREATE TABLE `rex_999_rex2json` (
					`module_id` INT( 10 ) NOT NULL ,
					`xml_scheme` TEXT ,
					PRIMARY KEY ( `module_id` ) 
					) ");
	
	// CREATE/UPDATE MODULES
	// -none-
	
	// CREATE/UPDATE PAGES
	// -none-
	
	// REGENERATE SITE
	// -none-
	
	// COPY FILES
	// CSS-Datei im Ordner files/rex2json/ anlegen
	if (!is_dir($addon_file_dest)){@mkdir($addon_file_dest);}
	function rex2flash_installer($addon_file_source, $addon_file_dest)
	{
		$dir = opendir($addon_file_source);
		while (($filename = readdir($dir)) !== false)
		{
			if ($filename != '.' AND $filename != '..')
			{
				if (!is_dir($addon_file_source . '/' . $filename))
				{
				copy($addon_file_source . '/' . $filename, $addon_file_dest . '/' . $filename);
				}
				else
				{
				@mkdir($addon_file_dest . '/' . $filename);
				rex2flash_installer($addon_file_source . '/' . $filename, $addon_file_dest . '/' . $filename);
				}
			}
		}
		closedir($dir);
	}
	rex2flash_installer( $addon_file_source, $addon_file_dest );
	
	
	$REX['ADDON']['install']['rex2json'] = 1;
	// ERRMSG IN CASE: $REX['ADDON']['installmsg']['example'] = 'Error occured while installation';
}

?>