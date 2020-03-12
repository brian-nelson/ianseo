<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');

checkACL(array(AclIndividuals, AclTeams), AclReadOnly);

$isCompleteResultBook = true;

$pdf = new ResultPDF(get_text('MedalStanding'));

include 'PDFMedalStanding.php';
$pdf->AddPage();
include 'PDFMedalList.php';

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