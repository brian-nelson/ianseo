<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclParticipants, AclReadOnly);
include_once('Common/pdf/OrisPDF.inc.php');
include_once('Common/Fun_FormatText.inc.php');
require_once('Common/OrisFunctions.php');
require_once('Common/pdf/PdfChunkLoader.php');

// ATTENTION!
// MUST BE called $PdfData
$PdfData=getStartListAlphabetical('ORIS');

if(!isset($isCompleteResultBook))
	$pdf = new OrisPDF($PdfData->Code, $PdfData->Description);
else
	$pdf->setOrisCode('', $PdfData->Description);

require_once(PdfChunkLoader('OrisAlphabetical.inc.php'));

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