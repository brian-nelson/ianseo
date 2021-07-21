<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/OrisPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/OrisFunctions.php');
require_once('Common/pdf/PdfChunkLoader.php');
checkACL(AclEliminations, AclReadOnly);

$events = array();
$isPool = false;
if(isset($_REQUEST["EventCode"])) {
    if(is_array($_REQUEST["EventCode"])) {
        $events = $_REQUEST["EventCode"];
    } else {
        $events[] = $_REQUEST["EventCode"];
    }
}

if(!empty($_REQUEST["isPool"])) {
    $isPool = true;
}

// ATTENTION!
// MUST BE called $PdfData
$PdfData=getStartList(true, $events, true, false, $isPool);

if(!isset($isCompleteResultBook))
	$pdf = new OrisPDF('C51A', 'Start List by Target');
else
	$pdf->setOrisCode('', 'Start List by Target');

require_once(PdfChunkLoader('OrisElimStartList.inc.php'));

if(!isset($isCompleteResultBook)) {
	if(isset($_REQUEST['ToFitarco'])) {
		$Dest='D';
		if (isset($_REQUEST['Dest']))
			$Dest=$_REQUEST['Dest'];
		$pdf->Output($_REQUEST['ToFitarco'],$Dest);
	} else {
		$pdf->Output();
	}
}

