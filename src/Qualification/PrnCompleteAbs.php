<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');

$isCompleteResultBook = true;

$pdf = new ResultPDF(get_text('ResultClass','Tournament'));
include 'PrnIndividualAbs.php';
$pdf->SetXY(10,$pdf->GetY()+5);
include 'PrnTeamAbs.php';

$pdf->DrawShootOffLegend();

$pdf->Output();


?>