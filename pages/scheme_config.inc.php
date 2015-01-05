<?

$OUT = TRUE;
$page = "rex2json";
$subpage = "scheme_config";
$addon_id = "999";
$errortxt = "";

$function = rex_request( "function", "string" );
$module_id = rex_request( "module_id", "int" );
$module_name = rex_request( "module_name", "string" );
$xml_scheme = rex_request( "xml_scheme", "string" );


/****************************************************
 * Funktionsausgaben
 ****************************************************/
if ($function == "edit_scheme") 
{
	$sql = new sql;
	$sql->setQuery("SELECT xml_scheme FROM rex_".$addon_id."_rex2json WHERE module_id=$module_id;");
	$xml_scheme = $sql->getValue( "xml_scheme" );

	echo "<div style='line-height:18px;'>";
	echo "Hier k&ouml;nnen Sie dem gew&auml;hlten Modul ein JSON-Schema zuweisen.<br/>Jedes Mal, wenn Sie die index.php mit dem Parameter \"&amp;asjson=1\" aufrufen, werden die Artikel-Slices JSON-formatiert ausgegeben.<br/>Jedes Modul ben&ouml;tigt also ein entsprechendes JSON-Schema in dem die gespeicherten Modul-Variablen ausgegeben werden.";
	echo "</div>";
	
	echo "<form class='a-r2f' action='index.php?page=$page&subpage=$subpage&module_id=$module_id&function=save_scheme' method='post' >";
	echo "<div><br/><strong>Modul-ID: $module_id - $module_name</strong><br/><br/>";
	echo "<textarea style='width:500px; height:300px' name='xml_scheme'>$xml_scheme</textarea><br/>";
	echo "<input type='submit' value='Schema speichern' style='width: 150px; height:21px; border:1px solid #777777;	background: #cccccc;' />";
	echo "</div>";
	
	$OUT = FALSE;
}
else if ($function == "save_scheme") 
{
	$sql = new sql;
	$sql->setQuery("INSERT INTO rex_".$addon_id."_rex2json (`module_id` ,`xml_scheme`) VALUES ('$module_id', '$xml_scheme');");
	
	if ( $sql->hasError() ) 
	{
		$sql->setQuery("UPDATE rex_".$addon_id."_rex2json SET xml_scheme = '$xml_scheme' WHERE module_id=$module_id ");
	}

	$errortxt = "JSON Schema f&uuml;r Modul $module_id wurde gespeichert.";
}


/****************************************************
 * Ausgabe einer Fehlermeldung oder eines Statustextes
 ****************************************************/
if ($errortxt != "") {
?>
<div>
	<h3 style="color:#FF0000; margin-bottom:10px;"><?=$errortxt ?></h3>
</div>
<?
}

/****************************************************
 * Ausgabe aller verfügbaren Module
 ****************************************************/
if ($OUT)
{
  /*
  if ($info != '')
    echo rex_info($info);

  if ($warning != '')
    echo rex_warning($warning);

  if ($warning_block != '')
    echo rex_warning_block($warning_block);
  */

  $list = rex_list::factory('SELECT id, name FROM '.$REX['TABLE_PREFIX'].'module ORDER BY name');
  $list->setCaption($I18N->msg('module_caption'));
  $list->addTableAttribute('summary', $I18N->msg('module_summary'));
  $list->addTableColumnGroup(array(40, 40, '*', 215));

  $tdIcon = '<span class="rex-i-element rex-i-module"><span class="rex-i-element-text">###name###</span></span>';
  $thIcon = '';
//  $thIcon = '<a class="rex-i-element rex-i-module-add" href="'. $list->getUrl(array('function' => 'add')) .'"'. rex_accesskey($I18N->msg('create_module'), $REX['ACKEY']['ADD']) .'><span class="rex-i-element-text">'.$I18N->msg('create_module').'</span></a>';
  $list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
//  $list->setColumnParams($thIcon, array('function' => 'edit', 'modul_id' => '###id###'));

  $list->setColumnLabel('id', 'ID');
  $list->setColumnLayout('id', array('<th class="rex-small">###VALUE###</th>','<td class="rex-small">###VALUE###</td>'));

  $list->setColumnLabel('name', $I18N->msg('module_description'));
//  $list->setColumnParams('name', array('function' => 'edit', 'modul_id' => '###id###'));

//  $list->addColumn($I18N->msg('module_functions'), $I18N->msg('delete_module'));
  $list->addColumn($I18N->msg('module_functions'), "Schema zuweisen/ &auml;ndern");
  $list->setColumnParams($I18N->msg('module_functions'), array('subpage' => $subpage, 'function' => 'edit_scheme', 'module_id' => '###id###', 'module_name' => '###name###'));
//  $list->addLinkAttribute($I18N->msg('module_functions'), 'onclick', 'return confirm(\''.$I18N->msg('delete').' ?\')');

  $list->setNoRowsMessage($I18N->msg('modules_not_found'));

  $list->show();
}
?>
