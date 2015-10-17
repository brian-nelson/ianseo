<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');

echo $FileContent = false;
if(isset($_REQUEST["FileName"]) && isset($_REQUEST["FileSize"]))
{
	$fileName = urldecode($_REQUEST["FileName"]);
	$fileSize = intval($_REQUEST["FileSize"]);
	if(is_file($CFG->DOCUMENT_PATH . $fileName) && filesize($CFG->DOCUMENT_PATH . $fileName) == $fileSize)
		$FileContent = file_get_contents($CFG->DOCUMENT_PATH . $fileName); 
}

if ($FileContent !== false)
{
	$FileContent=gzcompress($FileContent,9);	// Comprimo con gzip
	if (!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'IE') === false) {
		header('Cache-Control: no-store, no-cache, must-revalidate');
	    header('Pragma: no-cache');
	} else {
	    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	    header('Pragma: public');
	}		

	header('Content-Disposition: attachment; filename=Download.gz');
	header('Content-Type: multipart/compressed ; method=application/gzip');
	header('Content-Transfer-Encoding: escaped-8bit');
	header('Content-Length: ' . strlen($FileContent));
	print $FileContent;
}

?>