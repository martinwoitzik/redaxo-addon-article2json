<?php
/**
 * rex2json Redaxo fuer JSON konfigurieren
 * @author m[PUNKT]woitzik[AT]gmx[PUNKT]de Martin Woitzik
 * @package redaxo4
 */

$mode = rex_request('mode', 'string', '');

switch ( $mode) {
   case 'changelog': $file = '_changelog.txt'; break;
   case 'todo': $file = '_todo.txt'; break;
   default: $file = '_readme.txt';
}
?>
<a href="?page=addon&amp;subpage=help&amp;addonname=rex2json">ReadMe</a> |
<a href="?page=addon&amp;subpage=help&amp;addonname=rex2json&amp;mode=changelog">ChangeLog</a> |
<a href="?page=addon&amp;subpage=help&amp;addonname=rex2json&amp;mode=todo">ToDo</a>
<br /><br />
<?php
echo str_replace( '+', '&nbsp;&nbsp;+', file_get_contents(dirname( __FILE__) . '/' . $file));
?>
<br /><br />
<hr />
<br />
<p>rex2json Addon by Martin Woitzik | Kontakt: &lt;m[PUNKT]woitzik[AT]gmx[PUNKT]de&gt;</p>