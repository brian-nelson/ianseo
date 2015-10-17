<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Fun_PrintOuts.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/OrisFunctions.php');
require_once('Common/pdf/PdfChunkLoader.php');

if (!isset($_SESSION['TourId']) && isset($_REQUEST['TourId']))
{
	CreateTourSession($_REQUEST['TourId']);
}

$isCompleteResultBook = true;

$pdf = new ResultPDF(get_text('ResultClass','Tournament'));

$PdfData=getQualificationIndividual();
$rankData=$PdfData->rankData;
require_once(PdfChunkLoader('QualShootoffIndividual.inc.php'));

$PdfData=getQualificationTeam();
$rankData=$PdfData->rankData;
require_once(PdfChunkLoader('QualShootoffTeam.inc.php'));

if (isset($_REQUEST['TourId']))
{
	EraseTourSession();
}

$pdf->Output();

?>