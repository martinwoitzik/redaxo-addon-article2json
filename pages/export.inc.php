<?php

class MyZipArchive extends ZipArchive{
    public function addDirectory($dir, $base = 0) {
        foreach(glob($dir . '/*') as $file) {
            if(is_dir($file)) {
                //$this->addDirectory($file, $base);
            } else {
                $this->addFile($file, substr($file, $base));
            }
        }
    }
}


$rooturl = 'http://localhost/Bilderfest/cms/index.php?article_id=';

$json = '{';
$json .= '"homepage": [';
$json .= file_get_contents($rooturl.'1&asxml=1');
$json .= '],';
$json .= '"news": [';
$json .= file_get_contents($rooturl.'4&asxml=1');
$json .= '],';

$json .= '"portfolio": [';
$projects = OOArticle::getArticlesOfCategory(2);
for ($i=1; $i<count($projects); $i++) {
    $json .= '[';
    $project = $projects[$i];
    $json .= file_get_contents($rooturl.$project->getId().'&asxml=1');
    $json .= ($i < count($projects)-1) ? '],' : ']';
}
$json .= '],';

$json .= '"projekte_homepage": [';
$projects = json_decode(file_get_contents($rooturl.'22&asxml=1'));
for ($i=0; $i<count($projects); $i++) {
    $json .= '[';
    $json .= file_get_contents($rooturl.$projects[$i].'&asxml=1');
    $json .= ($i < count($projects)-1) ? '],' : ']';
}
$json .= '],';

$json .= '"unternehmen": {';
$json .= '"leftcolumn": [';
$json .= file_get_contents($rooturl.'3&asxml=1&ctype=1');
$json .= '], "rightcolumn": [';
$json .= file_get_contents($rooturl.'3&asxml=1&ctype=2');
$json .= ']},';

$json .= '"team": {';
$json .= '"leftcolumn": [';
$json .= file_get_contents($rooturl.'8&asxml=1&ctype=1');
$json .= '], "rightcolumn": [';
$json .= file_get_contents($rooturl.'8&asxml=1&ctype=2');
$json .= ']},';

$json .= '"kontakt": {';
$json .= '"leftcolumn": [';
$json .= file_get_contents($rooturl.'5&asxml=1&ctype=1');
$json .= '], "rightcolumn": [';
$json .= file_get_contents($rooturl.'5&asxml=1&ctype=2');
$json .= ']},';

$json .= '"imprint": {';
$json .= '"leftcolumn": [';
$json .= file_get_contents($rooturl.'62&asxml=1&ctype=1');
$json .= '], "rightcolumn": [';
$json .= file_get_contents($rooturl.'62&asxml=1&ctype=2');
$json .= ']},';

// generate menu
$json .= '"menu": [';
// GENERATE MENU FROM REDAXO
$root = OOCategory::getCategoryById( 1 );
$categories = $root->getChildren( TRUE );
$i = 0;
foreach ($categories as $cat) {
    if ($i > 0)  $json .= ',';
    $json .= '{"id": "'.$cat->getId().'", "name": "'.$cat->getName().'", "url": "'.$cat->getUrl().'"}';
    $i++;
}
$json .= ']';

$json .= '}';

// DATEI ALS .JSON IM ORDNER /files ABSPEICHERN
$fp = fopen('../app/app.json', 'w');
fwrite($fp, $json);
fclose ( $fp);
?>

<p>
Datei 'app.json' erfolgreich geschrieben.<br/><br/>&nbsp;
</p>

<?php
/*
$zip = new MyZipArchive();
$zip->open('../app/app.zip', ZipArchive::CREATE);
$path = '../files/';
$zip->addDirectory(
    $path, // The path to the folder you wish to archive
    strlen($path) + 1 // The string length of the base folder
);
$zip->close();
*/
?>

<p>
ZIP-Archiv 'app.zip' erfolgreich exportiert. Die App kann nun aktualisiert werden.
</p>

