<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
include_once('Common/pdf/ResultPDF.inc.php');
include_once('Common/Fun_FormatText.inc.php');
include_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_PrintOuts.php');
require_once('Common/OrisFunctions.php');
require_once('Common/pdf/PdfChunkLoader.php');
checkACL(AclIndividuals, AclReadOnly);

$Events='';
if(isset($_REQUEST["Event"]) && $_REQUEST["Event"][0]!=".") {
	$Events=$_REQUEST["Event"];
	// select all children and subchildren of these events
	if(!is_array($Events)) {
		$Events=array($Events);
	}

	if(empty($_REQUEST['ShowChildren'])) {
		$Events = getChildrenEvents($_REQUEST["Event"]);
	}
}

$PdfData=getBracketsIndividual($Events,
	 false,
	 isset($_REQUEST["ShowTargetNo"]),
	 isset($_REQUEST["ShowSchedule"]),
	 isset($_REQUEST["ShowSetArrows"])
	 );

if (!isset($_SESSION['TourId']) && isset($_REQUEST['TourId'])) {
	CreateTourSession($_REQUEST['TourId']);
}

if(!isset($isCompleteResultBook))
	$pdf = new ResultPDF($PdfData->Description);
//$pdf->SetAutoPageBreak(false);

require_once(PdfChunkLoader('BracketIndividual.inc.php'));

if (isset($_REQUEST['TourId']))
{
	EraseTourSession();
}

if(isset($__ExportPDF))
{
	$__ExportPDF = $pdf->Output('','S');
}
elseif(!isset($isCompleteResultBook))
{
	if(isset($_REQUEST['ToFitarco']))
	{
		$Dest='D';
		if (isset($_REQUEST['Dest']))
			$Dest=$_REQUEST['Dest'];

		if ($Dest=='S')
			print $pdf->Output($_REQUEST['ToFitarco'],$Dest);
		else
			$pdf->Output($_REQUEST['ToFitarco'],$Dest);
	}
	else
		$pdf->Output();
}


?>