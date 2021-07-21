<?php

global $CFG; // just in case this file is included from inside a function

//error_reporting(E_ALL);
$pdf->setDocUpdate($PdfData->LastUpdate);

$pdf->SetFont($pdf->FontStd,'B',10);
$pdf->Cell(190, 6, $PdfData->Description, 1, 1, 'C', 1);
   $pdf->SetFont($pdf->FontStd,'B',7);
$pdf->Cell(8, 10,  $PdfData->Rank, 1, 0, 'C', 1);
$pdf->Cell(56, 10, $PdfData->Country, 1, 0, 'C', 1);

$pdf->Cell(42, 5, $PdfData->Individual, 1, 0, 'C', 1);
$pdf->Cell(42, 5, $PdfData->Team, 1, 0, 'C', 1);
$pdf->Cell(42, 5, $PdfData->Total, 1, 1, 'C', 1);
$pdf->SetX($pdf->GetX()+64);
$pdf->Cell(14, 5, $PdfData->Medal_1, 1, 0, 'C', 1);
$pdf->Cell(14, 5, $PdfData->Medal_2, 1, 0, 'C', 1);
$pdf->Cell(14, 5, $PdfData->Medal_3, 1, 0, 'C', 1);
$pdf->Cell(14, 5, $PdfData->Medal_1, 1, 0, 'C', 1);
$pdf->Cell(14, 5, $PdfData->Medal_2, 1, 0, 'C', 1);
$pdf->Cell(14, 5, $PdfData->Medal_3, 1, 0, 'C', 1);
$pdf->Cell(14, 5, $PdfData->Medal_1, 1, 0, 'C', 1);
$pdf->Cell(14, 5, $PdfData->Medal_2, 1, 0, 'C', 1);
$pdf->Cell(14, 5, $PdfData->Medal_3, 1, 1, 'C', 1);

$MyRank=0;
$MyPos=0;
$TmpOldValue=0;

foreach($PdfData->CountryList as $CountryCode => $item) {
	$MyPos++;
	if($TmpOldValue != $item->U[1]*10000+$item->U[2]*100+$item->U[3]) {
		$MyRank=$MyPos;
		$TmpOldValue = $item->U[1]*10000+$item->U[2]*100+$item->U[3];
	}

	// Dati della tabella
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell(8, 5, $MyRank, 1, 0, 'R', 0);
	$pdf->SetFont($pdf->FontStd,'',8);
	if(file_exists($CFG->DOCUMENT_PATH . 'Common/Images/Flags/F_' . $CountryCode . '.png')) {
		$pdf->Cell(10, 5,  '', 1, 0, 'C', 0);
		$TmpX = $pdf->GetX()-10;
		$TmpY = $pdf->GetY();
		$pdf->Image($CFG->DOCUMENT_PATH . 'Common/Images/Flags/F_' . $CountryCode . '.png', $TmpX+3, $TmpY+1 , 4,0);
		$pdf->SetXY($TmpX+10,$TmpY);
	} else {
		$pdf->Cell(10, 5,  $CountryCode, "TBL", 0, 'C', 0);
	}

	$pdf->Cell(46, 5,  $item->Name, "TBR", 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'',7);
	$pdf->Cell(14, 5, $item->I[1], 1, 0, 'C', 0);
	$pdf->Cell(14, 5, $item->I[2], 1, 0, 'C', 0);
	$pdf->Cell(14, 5, $item->I[3], 1, 0, 'C', 0);
	$pdf->Cell(14, 5, $item->T[1], 1, 0, 'C', 0);
	$pdf->Cell(14, 5, $item->T[2], 1, 0, 'C', 0);
	$pdf->Cell(14, 5, $item->T[3], 1, 0, 'C', 0);
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell(14, 5, $item->U[1], 1, 0, 'C', 0);
	$pdf->Cell(14, 5, $item->U[2], 1, 0, 'C', 0);
	$pdf->Cell(14, 5, $item->U[3], 1, 1, 'C', 0);
}

?>
