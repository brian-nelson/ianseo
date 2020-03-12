<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');

checkACL(array(AclIndividuals, AclTeams), AclReadOnly);
$isCompleteResultBook = true;

$pdf = new ResultPDF(get_text('BrakRank'));

include './Individual/PrnBracket.php';
$pdf->AddPage();
include './Individual/PrnRanking.php';
$pdf->AddPage();
include './Team/PrnBracket.php';
$pdf->AddPage();
include './Team/PrnRanking.php';

$pdf->Output();


?>