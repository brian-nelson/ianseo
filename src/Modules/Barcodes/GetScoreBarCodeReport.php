<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');

require_once('Common/pdf/ResultPDF.inc.php');

$Sess=intval($_GET['T']);
$Dist=intval($_GET['D']);

$PDF_TITLE='';

$pdf = new ResultPDF($PDF_TITLE);

$Edits=array();
$Edits['SEPARATOR']='---';
$Edits['EDIT ARROWS']='EDIT';
$Edits['EDIT FINAL SCORE']='EDIT2';
$Edits['REMOVE 10']='REM10';
$Edits['REMOVE X/NINE']='REMXNINE';
$Edits['REMOVE BOTH']='REMALL';
$Edits['RESET BOTH']='RESET';

foreach($Edits as $Edit => $Bars) {
	$pdf->Ln(8);
	$pdf->SetFont($pdf->FontStd,'B',16);
	$pdf->Cell(50, 20, $Edit, 0, 0, 'R');
	$pdf->Cell(5, 20, '', 0, 0, 'L', 0);
	$pdf->SetFont('barcode','',40);
	$pdf->Cell(0, 20, mb_convert_encoding("*$Bars*", "UTF-8","cp1252"), 0, 1, 'L', 0);
}
$pdf->sety($pdf->gety()+15);
$pdf->SetFont($pdf->FontStd,'B',11);

$pdf->Cell(0,10,get_text('Credits-BeiterCredits', 'Install'),0,1,'C',0);
$pdf->Image("beiter.png",($pdf->getPageWidth()-30)/2,$pdf->getY(),30,0,'','','M');

$pdf->Output();