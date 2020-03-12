<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/OrisPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/Fun_PrintOuts.php');
require_once('Common/OrisFunctions.php');
require_once('Common/pdf/PdfChunkLoader.php');
checkACL(AclQualification, AclReadOnly);

$EventRequested=(!empty($EventRequested) ? $EventRequested : '');

$PdfData=getQualificationIndividual($EventRequested, true, true);
$rankData=$PdfData->rankData;

if(!isset($isCompleteResultBook))
	$pdf = new OrisPDF($PdfData->Code, $PdfData->Description);
else
	$pdf->setOrisCode('', $PdfData->Description);

require_once(PdfChunkLoader('OrisQualIndividual.inc.php'));

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