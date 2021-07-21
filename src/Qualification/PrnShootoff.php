<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Fun_PrintOuts.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/OrisFunctions.php');
require_once('Common/pdf/PdfChunkLoader.php');
checkACL(array(AclQualification,AclEliminations,AclIndividuals,AclTeams), AclReadOnly);

if (!isset($_SESSION['TourId']) AND isset($_REQUEST['TourId'])) {
	CreateTourSession($_REQUEST['TourId']);
}

$evList = array(0=>array(),1=>array());
if(isset($_REQUEST["Events"])) {
    if(!is_array($_REQUEST["Events"])) {
        $_REQUEST["Events"] = array($_REQUEST["Events"]);
    }
    foreach($_REQUEST["Events"] as $evRaw) {
        if(strpos($evRaw,'|')!== false) {
            list($evCode,$evType) = explode('|',$evRaw);
            if(intval($evType)<=1) {
                $evList[intval($evType)][] = $evCode;
            }
        } else {
            $evList[0][] = $evRaw;
            $evList[1][] = $evRaw;
        }
    }
}

$isCompleteResultBook = true;
$pdf = new ResultPDF(get_text('ResultClass','Tournament'));
if(!isset($_REQUEST["Events"]) OR count($evList[0])!=0) {
    $PdfData = getQualificationIndividual($evList[0]);
    $rankData = $PdfData->rankData;
    require_once(PdfChunkLoader('QualShootoffIndividual.inc.php'));
}
if(!isset($_REQUEST["Events"]) OR count($evList[1])!=0) {
    $PdfData = getQualificationTeam($evList[1]);
    $rankData = $PdfData->rankData;
    require_once(PdfChunkLoader('QualShootoffTeam.inc.php'));
}

if (isset($_REQUEST['TourId'])) {
	EraseTourSession();
}

$pdf->Output();

?>