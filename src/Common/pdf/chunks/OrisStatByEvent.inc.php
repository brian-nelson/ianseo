<?php

$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
$pdf->setPhase('');

$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
$pdf->AddPage();

foreach($PdfData->Data as $EvCode => $MyRow) {
	$tmp=array(
		$MyRow['Name'],
		$MyRow['Number'],
		$MyRow['Countries'],
		$MyRow['Teams'],
		);
	$pdf->printDataRow($tmp);
}
