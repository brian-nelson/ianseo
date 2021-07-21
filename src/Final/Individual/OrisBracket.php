<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
include_once('Common/pdf/OrisBracketPDF.inc.php');
include_once('Common/Fun_FormatText.inc.php');
include_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_PrintOuts.php');
require_once('Common/OrisFunctions.php');
require_once('Common/pdf/PdfChunkLoader.php');
checkACL(AclIndividuals, AclReadOnly);

define("Cella",3);

$Events='';
if(isset($_REQUEST["Event"]) && $_REQUEST["Event"][0]!=".") $Events = $_REQUEST["Event"];
if(!empty($EventRequested)) $Events=$EventRequested;

$PdfData=getBracketsIndividual($Events,
	true,
	isset($_REQUEST["ShowTargetNo"]),
	isset($_REQUEST["ShowSchedule"]),
	true,
	true);

if(!isset($isCompleteResultBook)) {
	$pdf = new OrisBracketPDF('C75A', $PdfData->Description);
} else {
	$pdf->SetAutoPageBreak(false);
	//$pdf->setOrisCode('', $PdfData->Description);
}

$pdf->OrisPages = isset($_REQUEST['OrisAB']) ? $_REQUEST['OrisAB'] : 'AB';

require_once(PdfChunkLoader('OrisBracketIndividual.inc.php'));

//$pdf->setPage($pdf->getNumPages(),true);
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
