<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/pdf/OrisPDF.inc.php');
require_once('Common/pdf/OrisBracketPDF.inc.php');

$isCompleteResultBook = true;

$pdf = new OrisBracketPDF('C75A', 'Result Brackets');

if(isset($_REQUEST["IncBrackets"]) && $_REQUEST["IncBrackets"]==1)
	include 'OrisBracket.php';
	
$pdf->SetAutoPageBreak(true,OrisPDF::bottomMargin);

if(isset($_REQUEST["IncRankings"]) && $_REQUEST["IncRankings"]==1)
	include 'OrisRanking.php';


if(isset($_REQUEST['ToFitarco']))
{ 
	$Dest='D';
	if (isset($_REQUEST['Dest']))
		$Dest=$_REQUEST['Dest'];
	$pdf->Output($_REQUEST['ToFitarco'],$Dest);
}
else
	$pdf->Output();
?>