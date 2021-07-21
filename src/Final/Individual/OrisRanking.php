<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/pdf/OrisPDF.inc.php');
require_once('Common/Lib/Fun_PrintOuts.php');
require_once('Common/OrisFunctions.php');
require_once('Common/pdf/PdfChunkLoader.php');
checkACL(AclIndividuals, AclReadOnly);

$EventRequested=(!empty($EventRequested) ? $EventRequested : '');

$PdfData=getRankingIndividual($EventRequested, true);

if(!isset($isCompleteResultBook)) {
	$pdf = new OrisPDF('C76A', $PdfData->Description);
} else {
	$pdf->setOrisCode('', $PdfData->Description);
	$pdf->SetAutoPageBreak(true,(OrisPDF::bottomMargin+$pdf->extraBottomMargin));
}

require_once(PdfChunkLoader('OrisRankIndividual.inc.php'));

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