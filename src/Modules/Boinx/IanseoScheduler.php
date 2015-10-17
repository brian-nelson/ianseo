<?php
require_once('./config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Fun_Scheduler.php');

$Scheduler=new Scheduler($TourId);

$XmlDoc=$Scheduler->getScheduleBoinx();

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);
echo $XmlDoc->SaveXML();
