<?php
/**
 *  Rex2JSON
 *  Redaxo-Inhalte als JSON ausgeben
 *
 *  @author 	m[PUNKT]woitzik[AT]gmx[PUNKT]de Martin Woitzik
 *	@portfolio	www.martinwoitzik.de
 *  @package 	redaxo4.x
 *  @date		16.06.2009
 */
class Rex2JSON
{

	static private $instance = null;
		
	private $isAjaxRequest = false;
	private $ajaxResponse = '';	
	
	
	/**
	  * Singleton-Klasse
	  * es kann nur eine Instanz geben
	  */
	private function __construct()
	{
		global $REX;

        /*
		if (isset($_REQUEST[$this->getParam_ajaxRequest])) 
		{
			$this->isAjaxRequest = true;
		}*/
						
		if (!$REX['REDAXO']) 
		{
			rex_register_extension('OUTPUT_FILTER', array($this, 'outputFilter'));	
			rex_register_extension('OUTPUT_FILTER_CACHE', array($this, 'outputFilter'));	
		}
	}
	
	/**
	  * Instanziiert die Singleton-Klasse
	  *
	  * @return     Eine Referenz auf die Instanz dieser Klasse
	  */
	public static function instance() 
	{
		if (Rex2JSON::$instance === null) {
            Rex2JSON::$instance = new Rex2JSON();
		}
		
		return Rex2JSON::$instance;
	}	
	
	
	/****************************************************
	 * Callback fuer OUTPUT_FILTER
	 ****************************************************/
	public function outputFilter( $params )
	{
		global $REX, $REX_USER;
		$content = $params['subject'];
		
		// Abfrage, ob User noch eingeloggt
		if ($this->isAjaxRequest) {
			if (!isset($REX_USER)) {
				$this->sendAjaxResponseOnOutput('Your session has expired or you have logged out. Please login to continue editing.');
			}
			return $this->ajaxResponse;
		}		
		
		// Abrufen diverser REX Variablen
		$function = rex_request('function', 'string');
		$slice_id = rex_request('slice_id', 'int');
		$page = rex_request('page', 'string');
		$mode = rex_request('mode', 'string');
		$version = rex_request('rex_version', 'int');   // Versions Nummer abfragen (0 = LIVE-Version, 1 = ARBEITS-Version)
        $ctype = rex_request('ctype', 'int');

		$asXML = rex_request('asxml', 'int');
		$menuStructure = rex_request('menustructure', 'int');
		$categoryArticles = rex_request('categoryarticles', 'int');

        if (!$ctype)
            $ctype = 1;

		if ($page != "content" && $page != "structure" && $asXML == 1) 
		{
			// Artikelinhalte XML-formatiert ausgeben			
			$output = $this->createArticleAsXML( $version, $ctype );
		
			return $output;
		}
		else if ($page != "content" && $page != "structure" && $menuStructure == 1) 
		{
			// Menüstruktur als XML ausgeben
			$output = $this->createMenuStructureRec( $version );

			return $output;
		}
		else if ($page != "content" && $page != "structure" && $categoryArticles == 1)
		{
			// Alle Artikel einer Kategorie auslesen
			$catid = rex_request('catid', 'int');
			$output = $this->getCategoryArticles( $catid, $REX['CUR_CLANG'] );
			
			return $output;
		}
		
		return $content;
	}
	
	
	/****************************************************
	 * Ausgabe aller Artikel einer Kategorie als XML
	 ****************************************************/	
	private function getCategoryArticles( $catid, $langid )
	{
		global $REX, $REX_USER;
				
		$version = rex_request( 'rex_version', 'int' );			
		$articles = OOArticle::getArticlesOfCategory( $catid, FALSE, $langid );
		$nl ="\r\n";
		
		$output = '<?xml version="1.0" encoding="utf-8" ?>'.$nl;	
		$output .= '<articles>'.$nl;
		for ($i=1; $i<count($articles); $i++)
		{
			$output .= "  <article id=\"".$articles[$i]->getId()."\">".$nl;
//			$output .= "     <id>".$articles[$i]->getId()."</id>".$nl;
//			$output .= "     <name>".$articles[$i]->getName()."</name>".$nl;
			$output .= $this->createArticleAsXML( 0, 1, $articles[$i]->getId(), FALSE );
			$output .= "  </article>".$nl;

		}
		$output .= '</articles>';
		
		return $output;
	}
	
	
	/****************************************************
	 * Ausgabe der Artikel-Inhalte als XML
	 ****************************************************/	
	private function createArticleAsXML( $version=0, $ctype=1, $artid=-1, $printHeader=FALSE )
	{
		global $REX, $REX_USER;

		// Je nach Artikel-Slice-ID ein anderes XML Schema ausgeben:
		//
		// 1. aus Datenbank die Modulschemas laden (XML Strukturen für jedes Modul bzw. jede Modul-ID)
		// 2. Slices durchlaufen und für jede Slice-Id das zugehörige Modulschema laden (über Slice-Id/Modul-Id)
		// 3. in den Schemas gibt es Strings für die ModulVariablen, z.B. "###1###" für REX_VALUE[1] usw.
		//    diese werden dann durch den entsprechenden Variablen-Aufruf ersetzt z.B. $slice->getValue(1)
		// 4. danach erfolgt die ausgabe der XML-Blöcke für jeden Slice
		
		$article = ($artid == -1) ? $REX['ARTICLE'] : OOArticle::getArticleById( $artid );
		$article_id = ($artid == -1) ? $article->article_id : $article->getId();
		$version = rex_request( 'rex_version', 'int' );
		$slice0 = OOArticleSlice::getFirstSliceForCtype( $ctype, $article_id, false, $version );
		$nl ="\r\n";
		
		// ARTIKEL META-DATEN
		$article_name = $article->getValue("name");
		$article_date = date('d.m.Y',$article->getValue("createdate"));
		$article_meta_image = $article->_art_file;
		$article_viewname = $article->getValue('cat_viewName');

		$article_name = htmlspecialchars($article_name);
		
		if ($printHeader) {
			$output = '<?xml version="1.0" encoding="utf-8" ?>'.$nl;	
			$output .= '<article id="'.$article_id.'" artikelname="'.$article_name.'" datum="'.$article_date.'" meta_image="'.$article_meta_image.'" viewname="'.$article_viewname.'">'.$nl;
		} 
		
		// ARTIKEL SLICES PARSEN
		$sql = new sql();
		$nextslice = $slice0;	
		
		// es darf auch eine LinkList gesetzt werden
		$listAvailable = FALSE;
		if ($nextslice != NULL) {
			$list = $nextslice->getLinkList(1);
			if (!empty($list))	$listAvailable = TRUE;
			$listArr = explode( ",", $list );
		}

		while( $nextslice )
		{		
			$module_id = $nextslice->getModuleId();
			$sliceStatus = SliceOnOff::instance();
			$slice_is_online = 0;
			if ($sliceStatus != NULL)
				$slice_is_online = $sliceStatus->isOnline( $nextslice->getId(), $REX['ARTICLE_ID'], $REX['CLANG'] );
				
			$sql->setQuery("SELECT xml_scheme FROM rex_999_rex2json WHERE module_id=$module_id;");
			$xml_scheme = $sql->getValue( "xml_scheme" );
			
			// Platzhalter im Scheme ersetzen, z.B.
			// 1. ###1### mit $nextslice->getValue(1)
			// 2. ###file3### mit $nextslice->getFile(3)
			// 3. ###isOnline### mit Slice online Status
			// 4. #if#4#...#if4# bedeutet, alles was sich innerhalb der if-Tags befindet, wird nur angezeigt, wenn Value4 != ""
			// 5. ##if#file4#....#if#file4# gleiches wie oben nur für Files
			//
			// Values
			for ($i=1; $i<21; $i++) {
				$replace_str = addSlashes($this->transformString( $nextslice->getValue($i) ));
				if ($replace_str == "") {
					$splitstr = split( "#if#".$i."#", $xml_scheme );
					$xml_scheme = $splitstr[0].$splitstr[2];
				} else {
					$xml_scheme = str_replace( "#if#".$i."#", "", $xml_scheme );
					$xml_scheme = str_replace( "###".$i."###", $replace_str, $xml_scheme );
				}
			}
			// Spezial: mehr als 20 Values
			$val20 = $nextslice->getValue(20);
			$rexname = split("~~",$val20);
			$z = 100;
			for ($i=0; $i<10; $i++) 
			{
				$replace_str = ($i > count($rexname)-1) ? "" : addSlashes($this->transformString( $rexname[$i] ));
					
				if ($replace_str == "") {
					$splitstr = split( "#if#".$z."#", $xml_scheme );
					$xml_scheme = $splitstr[0].$splitstr[2];
				} else {
					$xml_scheme = str_replace( "#if#".$z."#", "", $xml_scheme );
					$xml_scheme = str_replace( "###".$z."###", $replace_str, $xml_scheme );
				}
				$z++;
			}
			
			// Files
			for ($i=1; $i<15; $i++) {
				$replace_str = $nextslice->getFile($i);
				if ($replace_str == "") {
					$splitstr = split( "#if#file".$i."#", $xml_scheme );
					$xml_scheme = $splitstr[0].$splitstr[2];
				} else {
					$xml_scheme = str_replace( "#if#file".$i."#", "", $xml_scheme );
					$xml_scheme = str_replace( "###file".$i."###", $replace_str, $xml_scheme );
				}
			}
			// Lists
			if ($listAvailable) {
				for ($i=1; $i<7; $i++) {
					$replace_str = ($i <= count($listArr)) ? $listArr[$i-1] : "";
					if ($replace_str == "") {
						$splitstr = split( "#if#link".$i."#", $xml_scheme );
						$xml_scheme = $splitstr[0].$splitstr[2];
					} else {
						$xml_scheme = str_replace( "#if#link".$i."#", "", $xml_scheme );
						$xml_scheme = str_replace( "###link".$i."###", $replace_str, $xml_scheme );
					}
				}
			}
			$xml_scheme = str_replace( "###isOnline###", $slice_is_online, $xml_scheme );

			$nextslice = OOArticleSlice::_getSliceWhere('re_article_slice_id = '. $nextslice->_id .' AND clang='. $nextslice->_clang .' AND ctype='. $ctype);
            if ($nextslice) {
                $output .= $xml_scheme.', '.$nl;
            } else {
                $output .= $xml_scheme;
            }
		}

		if ($printHeader)
			$output .= '</article>';
		
		return $output;
	}


	/****************************************************
	 * Erzeugt die Menü-Struktur als XML (rekursiv)
	 * (keine Beschränkung in punkto Menütiefe und Anzahl der Sprachen)
	 ****************************************************/
	private function generateNavigation ( $cid, $level, $config, $version ) 
  	{
		global $REX;
	  	
		if (!$cid) {
			//Mit Rootkategorien starten
		  	$categories = OOCategory::getRootCategories();
		} else {
		  	//Unterkategorien
		  	$categories = OOCategory::getChildrenById($cid);
		}
	
		if ($config['expandArticles'] == 1) {
		  	$articles = OOArticle::getArticlesOfCategory($cid);
		  	$articles = array_slice($articles, 1);
			$categories = array_merge($categories, $articles);
		}
	
		if ($categories) 
		{

		  	foreach ($categories as $item) 
			{
			  	$urls = array();
			  	$titles = array();
			  	$isOnline = array();
				$catid = $item->getId();
			  	for ($i=0; $i<count($REX['CLANG']); $i++) 
			 	{					
					$cat = OOCategory::getCategoryById( $catid, $i );
//					$urls[] = $cat->getUrl();
					$urls[] = "index.php?article_id=".$cat->getId();
				 	$titles[] = htmlspecialchars( str_replace( "_", "", $cat->getName() ) );
				 	$isOnline[] = ($cat->isOnline() == 1) ? "true" : "false";
			  	}
			  	$viewName = $cat->_cat_viewName;
//				$panoTitle = $cat->_cat_panoTitle;
			  	$navigation .= '<menuitem viewName="'.$viewName.'" ';
			  	for ($i=0; $i<count($REX['CLANG']); $i++) 
			  	{
					$clangshort = $this->getClangShortcut( $REX['CLANG'][$i] );
				  	$navigation .= 'url_'.$clangshort.'="'.$urls[$i].'&amp;asxml=1&amp;rex_version='.$version.'&amp;clang='.$i.'" ';
				  	$navigation .= 'title_'.$clangshort.'="'.$titles[$i].'" ';
				  	$navigation .= 'isOnline_'.$clangshort.'="'.$isOnline[$i].'" ';
			  	}
			  	$navigation .= ' >'.$nl;	
		
				if (($config['expandAll'] == 1) OR ($active)) 
				{
			  		if ((strtolower(get_class($item)) == 'oocategory') AND (($item->getChildren()) OR (($config['expandArticles'] == 1) AND (count($item->getArticles(true)) > 1)))) {
					$navigation .= $this->generateNavigation( $item->getId(), $level+1, $config, $version );
			  		}
				} // end if  
			
			$navigation .= '</menuitem>';
		  }	// end foreach
	
		} // end if
	
		return $navigation;	  
    }

	private function getClangShortcut( $lang )
	{
		global $REX;
		
		$shortcut = "";
		if ($lang == "deutsch") {
			$shortcut = "de";
		} else if ($lang == "english") {
			$shortcut = "en";
		} else if ($lang == "français") {
			$shortcut = "fr";
		} else if ($lang == "italiano") {
			$shortcut = "it";
		}
		
		return $shortcut;
	}
	

	/****************************************************
	 * Ausgabe der Menü-Struktur als XML
	 ****************************************************/
	private function createMenuStructureRec( $version=0 )
	{	
		global $REX, $REX_USER;	

	  	$config['expandAll'] = 1;  //1 -> Alle Ebenen werden angezeigt; 0 -> Nur die Unterpunkte des aktuell ausgewählen Punktes werden angezeigt
	  	$config['expandArticles'] = 0;  //1 -> Alle Artikel werden angezeigt; 0 -> Nur die Unterkategorien werden angezeigt

		$nl ="\r\n";
		$output = '<?xml version="1.0" encoding="utf-8" ?>'.$nl;
		$output .= '<menustructure>'.$nl;
				
		$output .= $this->generateNavigation( 1, 1, $config, $version );
		
		$output .= '</menustructure>'.$nl;
	
		return $output;
	}
	
	
	/****************************************************
	 * Wandelt bestimmte Elemente eines Strings XML / Flash konform um
	 ****************************************************/
	private function transformString( $str )
	{
		$str = str_replace("\r\n", "<br/>", $str);
		$str = str_replace("<strong>", "<b>", $str);	
		$str = str_replace("</strong>", "</b>", $str);	
		$str = html_entity_decode( $str );
        if (!mb_check_encoding($str, 'utf-8'))
		    $str = utf8_encode( $str );
		
		return $str;
	}

}

?>