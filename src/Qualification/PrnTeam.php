<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Fun_PrintOuts.php');
require_once('Common/OrisFunctions.php');
require_once('Common/pdf/PdfChunkLoader.php');

// ATTENTION!
// MUST BE called $PdfData
$PdfData=getDivClasTeam();

if (!isset($_SESSION['TourId']) && isset($_REQUEST['TourId']))
{
	//$_SESSION['TourId']=$_REQUEST['TourId'];
	CreateTourSession($_REQUEST['TourId']);
}
checkACL(AclQualification, AclReadOnly);

if(!isset($isCompleteResultBook))
	$pdf = new ResultPDF($PdfData->Description);

require_once(PdfChunkLoader('DivClasTeam.inc.php'));


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