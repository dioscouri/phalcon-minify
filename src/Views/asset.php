<?php
$lastModified = time();
$etagFile = md5_file( $file );

//set last-modified header
//header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");
//set etag-header
header("Etag: $etagFile");
//make sure caching is turned on
header('Cache-Control: public');
// set content type header
header('Content-type: '. \Web::instance()->mime($file) );

$string = \Base::instance()->read( $file );
echo (string) trim($string);

?>