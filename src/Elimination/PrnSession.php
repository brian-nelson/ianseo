<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/OrisFunctions.php');
require_once('Common/pdf/PdfChunkLoader.php');
checkACL(AclEliminations, AclReadOnly);

if(!empty($_REQUEST['isORIS'])) {
    CD_redirect('OrisStartList.php'.go_get());
    die();
}

$events = array();
$isPool = false;
if(isset($_REQUEST["EventCode"])) {
    if(is_array($_REQUEST["EventCode"])) {
        $events = $_REQUEST["EventCode"];
    } else {
        $events[] = $_REQUEST["EventCode"];
    }
}

if(!empty($_REQUEST["isPool"])) {
    $isPool = true;
}

// ATTENTION!
// MUST BE called $PdfData
$PdfData=getStartList(false, $events, true, false, $isPool);

if(!isset($isCompleteResultBook))
	$pdf = new ResultPDF(get_text('StartlistSession','Tournament') . ' - ' . get_text('Elimination'));

require_once(PdfChunkLoader('ElimStartList.inc.php'));

if(!isset($isCompleteResultBook)) {
	$pdf->Output();
}
