<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Fun_PrintOuts.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/OrisFunctions.php');
require_once('Common/pdf/PdfChunkLoader.php');
checkACL(array(AclEliminations,AclIndividuals), AclReadOnly);

if (!isset($_SESSION['TourId']) AND isset($_REQUEST['TourId'])) {
	CreateTourSession($_REQUEST['TourId']);
}

$evList = array();
if(isset($_REQUEST["Events"])) {
    if(is_array($_REQUEST["Events"])) {
        foreach($_REQUEST["Events"] as $evRaw) {
            $evList[] = str_replace('|', '@', $evRaw);
        }
    } else {
        $evList[] = str_replace('|', '@', $_REQUEST["Events"]);
    }
}

$isCompleteResultBook = true;
$pdf = new ResultPDF(get_text('ResultClass','Tournament'));

$PdfData=getEliminationIndividual($evList, false, false);
$rankData = $PdfData->rankData;
require_once(PdfChunkLoader('ElimShootoffIndividual.inc.php'));

if (isset($_REQUEST['TourId'])) {
	EraseTourSession();
}

$pdf->Output();

?>