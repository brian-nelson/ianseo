<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

CheckTourSession(true);

require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');

require_once('Common/ods/ods.php');

$excel = new ods();

//$TXT=array();

require_once('ResultInd.inc.php');
require_once('ResultTeam.inc.php');

$excel->save($_SESSION['TourCode'].'.ods', 'a');
die();

//$zip = new ZipArchive();
//
//$filename = tempnam("/tmp", "FontModule.zip");
//
//if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
//    exit("cannot open <$filename>\n");
//}
//
//foreach($TXT as $Event => $Data) {
//	$zip->addFromString("$Event.txt", $Data);
//}
//
///*
//echo "numfiles: " . $zip->numFiles . "\n";
//echo "status:" . $zip->status . "\n";
//*/
//
//$zip->close();
//
//header('Content-type: application/octet-stream');
//header("Content-Disposition: attachment; filename=\"" . $_SESSION['TourCode'] . '.zip' . "\"");
//readfile($filename);

?>