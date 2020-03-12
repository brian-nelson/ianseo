<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/pdf/ResultPDF.inc.php');
checkACL(AclIndividuals, AclReadOnly);

$isCompleteResultBook = true;

$pdf = new ResultPDF(get_text('BrakRank'));

if(isset($_REQUEST["IncBrackets"]) && $_REQUEST["IncBrackets"]==1)
	include 'PrnBracket.php';

if(isset($_REQUEST["IncBrackets"]) && $_REQUEST["IncBrackets"]==1 && isset($_REQUEST["IncRankings"]) && $_REQUEST["IncRankings"]==1)
	$pdf->AddPage();

if(isset($_REQUEST["IncRankings"]) && $_REQUEST["IncRankings"]==1)
	include 'PrnRanking.php';

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