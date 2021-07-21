<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/OrisPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
include_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');
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

$PdfData=getEliminationIndividual($events, true, $isPool);

if(!isset($isCompleteResultBook)) {
	$pdf = new OrisPDF($PdfData->Code, $PdfData->Description);
} else {
	$pdf->setOrisCode('', $PdfData->Description);
}

require_once(PdfChunkLoader('OrisElimIndividual.inc.php'));

if(!isset($isCompleteResultBook))
{
	if(isset($_REQUEST['ToFitarco']))
	{
		$Dest='D';
		if (isset($_REQUEST['Dest']))
			$Dest=$_REQUEST['Dest'];
		$pdf->Output($_REQUEST['ToFitarco'],$Dest);
	}
	else
		$pdf->Output();
}

?>