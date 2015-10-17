<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/pdf/OrisBracketPDF.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_PrintOuts.php');
require_once('Common/OrisFunctions.php');
require_once('Common/pdf/PdfChunkLoader.php');

$Events='';
if(isset($_REQUEST["Event"]) && $_REQUEST["Event"][0]!=".") $Events = $_REQUEST["Event"];
if(!empty($EventRequested)) $Events=$EventRequested;


$PdfData=getBracketsTeams($Events,
	true,
	isset($_REQUEST["ShowTargetNo"]),
	isset($_REQUEST["ShowSchedule"]),
	isset($_REQUEST["ShowSetArrows"])
	);

if(!isset($isCompleteResultBook))
	$pdf = new OrisBracketPDF($PdfData->Code, $PdfData->Description);
else
	$pdf->setOrisCode('', $PdfData->Description);

require_once(PdfChunkLoader('OrisBracketTeam.inc.php'));

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