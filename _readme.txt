<!-- 
/**
 * Rex2Flash
 * Redaxo-Inhalte Flash-kompatibel ausgeben
 * @author m[PUNKT]woitzik[AT]gmx[PUNKT]de Martin Woitzik
 * @package redaxo4.x
 */
-->

<h2>Beschreibung:</h2>
<p>Mit diesem Addon ist es m�glich, Artikel XML-formatiert ausgeben zu lassen. Dies geschieht �ber XML-Schemas, die jedem Modul
zugewiesen werden k�nnen. Im Backend gibt es daf�r den Button "REXtoFLASH".<br/><br/>
Die XML-Inhalte k�nnen im Frontend ganz einfach �ber die Parameter "asxml=1" oder "menustructure=1" ausgegeben werden. (an die index.php anh�ngen!)<br/>
Bei "asxml" werden alle Artikel-Slices eines bestimmten Artikels in die entsprechenden XML-Schematas umgewandelt.<br/>
Bei "menustructure" wird die komplette Men�struktur der Seite als XML-Datei ausgegeben, inkl. Online-Status f�r Slices, Arbeits- oder Liveversion, CType und CLang.
Dies gilt f�r beliebig viele Ebenen und beliebig viele Sprachen!</p>
<p>�ber den Men�punkt "REXtoFLASH" k�nnen den vorhandenen Modulen XML-Schemas zugewiesen werden.<br/>
Ein beispielhaftes Schema ist z.B.:<br/><br/>
<pre>
&lt;item thumb="###file###"&gt;
   &lt;file&gt;###file2###&lt;/file&gt;
   &lt;content&gt;###1###&lt;/content&gt;
&lt;/item&gt;
</pre>
<br/>
in diesem Fall bedeutet das, dass das Attribut "thumb" mit dem Inhalt der Modul-Variable "FILE[1]" gef�llt wird. Der Knoten <file> wird<br/>
mit dem Inhalt der Modul-Variable "FILE[2]" gef�llt. Der Knoten <content> verh�lt sich �hnlich, nur wird dieser mit dem Inhalt der<br/>
Modul-Variable "VALUE[1]" gef�llt.<br/>
Der Wert <code>###X###</code> wird also immer in die entsprechende REX_VALUE[X] umgewandelt.<br/>
Der Wert <code>###fileX###</code> wird immer in die entsprechende REX_FILE[X] umgewandelt.<br/><br/>
Es gibt noch einen Sonderfall, falls eine bestimmte Zeile nur ausgegeben werden soll, wenn die entsprechende REX_VALUE oder REX_FILE Variable
definiert ist. Dies funktioniert folgenderma�en:<br/><br/>
<pre>
&lt;item thumb="###img1###"&gt;
   #if#file2#&lt;file&gt;###img2###&lt;/file&gt;#if#file2#
   #if#1#&lt;content&gt;###1###&lt;/content&gt;#if#1#
&lt;/item&gt;
</pre>
</p>
<br/>
Alle Inhalte, die sich innerhalb der <code>"#if#fileX#.....#if#fileX#"</code> oder innerhalb der <code>"#if#X#.....#if#X#"</code> Syntax befinden, werden nur dann
ausgegeben, wenn die Variable REX_FILE[X] bzw. REX_VALUE[X] definiert ist.
<br/><br/>
<strong>Weitere Parameter:</strong><br/>
- (nur wenn das Addon "slice_onoff" installiert ist)<br/>
<code>###isOnline###</code>  ->  gibt 1 oder 0 zur�ck, je nachdem ob ein Slice den Status "online" oder "offline" besitzt.
<br/>