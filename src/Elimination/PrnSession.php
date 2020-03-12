<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/OrisFunctions.php');
require_once('Common/pdf/PdfChunkLoader.php');
checkACL(AclEliminations, AclReadOnly);

// ATTENTION!
// MUST BE called $PdfData
$PdfData=getStartList(false, '', true);

if(!isset($isCompleteResultBook))
	$pdf = new ResultPDF(get_text('StartlistSession','Tournament') . ' - ' . get_text('Elimination'));

require_once(PdfChunkLoader('ElimStartList.inc.php'));

if(!isset($isCompleteResultBook)) {
	$pdf->Output();
}
