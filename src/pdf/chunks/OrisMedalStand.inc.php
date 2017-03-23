<?php

$pdf->setOrisCode($PdfData->Code,$PdfData->Description);
$pdf->setPhase($PdfData->Phase);

if($PdfData->Version) {
	$pdf->setComment(trim("Vers. {$PdfData->Version} ({$PdfData->VersionDate}) {$PdfData->VersionNote}"));
}

$pdf->AddPage();
$pdf->Bookmark($PdfData->IndexName, 0);
$pdf->setDocUpdate($PdfData->LastUpdate);
$pdf->SetXY(OrisPDF::leftMargin,$pdf->lastY);
$pdf->SetTopMargin($pdf->lastY);

$pdf->SetLineWidth(0.1);
$pdf->SetFont('','b');
$pdf->Cell(10, 14, "Rank", 1, 0, 'C', 0);
$pdf->Cell(45, 14, "NOC", 1, 0, 'L', 0);
$pdf->Cell(40, 7,  "Individual", 1, 0, 'C', 0);
$pdf->Cell(40, 7,  "Team", 1, 0, 'C', 0);
$pdf->Cell(40, 7,  "Total", 1, 0, 'C', 0);
$pdf->Cell(15, 7, "Rank by", 'TLR', 1, 'C', 0);
$pdf->SetX($pdf->GetX()+55);
$pdf->Cell(10, 7,  "G", 1, 0, 'C', 0);
$pdf->Cell(10, 7,  "S", 1, 0, 'C', 0);
$pdf->Cell(10, 7,  "B", 1, 0, 'C', 0);

$pdf->Cell(10, 7,  "Tot", 1, 0, 'C', 0);

$pdf->Cell(10, 7,  "G", 1, 0, 'C', 0);
$pdf->Cell(10, 7,  "S", 1, 0, 'C', 0);
$pdf->Cell(10, 7,  "B", 1, 0, 'C', 0);
$pdf->Cell(10, 7,  "Tot", 1, 0, 'C', 0);

$pdf->Cell(10, 7,  "G", 1, 0, 'C', 0);
$pdf->Cell(10, 7,  "S", 1, 0, 'C', 0);
$pdf->Cell(10, 7,  "B", 1, 0, 'C', 0);
$pdf->Cell(10, 7,  "Tot", 1, 0, 'C', 0);
$pdf->Cell(15, 7, "Total", 'BLR', 1, 'C', 0);

$MyRank=0;
$MyPos=0;
$TmpOldValue=0;
$TotArray=array_fill(0,12,0);

$pdf->SetLineWidth(0.1);

foreach($PdfData->CountryList as $CountryCode => $item) {
	$MyPos++;
	if($TmpOldValue != $item->U[1]*10000+$item->U[2]*100+$item->U[3]) {
		$MyRank=$MyPos;
		$TmpOldValue = $item->U[1]*10000+$item->U[2]*100+$item->U[3];
	}

	// Dati della tabella
   	$pdf->SetFont('','');
	$pdf->Cell(10, 7, $MyRank, 1, 0, 'R', 0);
	$tmp=$pdf->getCellPaddings();
	$pdf->setCellPaddings($tmp['L'],$tmp['T'],0,$tmp['B']);
	$pdf->Cell(8, 7,  $CountryCode , 'TLB', 0, 'L', 0);
	$pdf->setCellPaddings(1,$tmp['T'],$tmp['R'],$tmp['B']);
	$pdf->Cell(37, 7,  $item->Name, 'TRB', 0, 'L', 0);
	$pdf->setCellPaddings($tmp['L'],$tmp['T'],$tmp['R'],$tmp['B']);

	$pdf->Cell(10, 7,   $item->I[1] ? $item->I[1] : '', 1, 0, 'R', 0);
	$pdf->Cell(10, 7,   $item->I[2] ? $item->I[2] : '', 1, 0, 'R', 0);
	$pdf->Cell(10, 7,   $item->I[3] ? $item->I[3] : '', 1, 0, 'R', 0);
	$pdf->Cell(10, 7,   ($tot = array_sum($item->I)) ? $tot : '', 1, 0, 'R', 0);
	$TotArray[0] += $item->I[1];
	$TotArray[1] += $item->I[2];
	$TotArray[2] += $item->I[3];
	$TotArray[3] += $tot;

	$pdf->Cell(10, 7,   $item->T[1] ? $item->T[1] : '', 1, 0, 'R', 0);
	$pdf->Cell(10, 7,   $item->T[2] ? $item->T[2] : '', 1, 0, 'R', 0);
	$pdf->Cell(10, 7,   $item->T[3] ? $item->T[3] : '', 1, 0, 'R', 0);
	$pdf->Cell(10, 7,   ($tot = array_sum($item->T)) ? $tot : '', 1, 0, 'R', 0);
	$TotArray[4] += $item->T[1];
	$TotArray[5] += $item->T[2];
	$TotArray[6] += $item->T[3];
	$TotArray[7] += $tot;

	$pdf->Cell(10, 7,   $item->U[1] ? $item->U[1] : '', 1, 0, 'R', 0);
	$pdf->Cell(10, 7,   $item->U[2] ? $item->U[2] : '', 1, 0, 'R', 0);
	$pdf->Cell(10, 7,   $item->U[3] ? $item->U[3] : '', 1, 0, 'R', 0);
	$pdf->Cell(10, 7,   $tot=array_sum($item->U), 1, 0, 'R', 0);
	$TotArray[8] += $item->U[1];
	$TotArray[9] += $item->U[2];
	$TotArray[10] += $item->U[3];
	$TotArray[11] += $tot;

	$pdf->Cell(15, 7,   (count(array_keys($PdfData->colTots, $tot))>1 ? '= ' : '') . $PdfData->colRank[$CountryCode], 1, 1, 'R', 0);

}
$pdf->SetY($pdf->GetY()+0.25);
$pdf->SetFont('','B');
$pdf->Cell(55, 7, "Total: ", 1, 0, 'R', 0);
foreach($TotArray as $value)
	$pdf->Cell(10, 7,   $value, 1, 0, 'R', 0);



?>