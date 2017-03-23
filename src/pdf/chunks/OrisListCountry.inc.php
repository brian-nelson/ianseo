<?php

$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
$pdf->setPhase('');

$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
$pdf->AddPage();
$pdf->Bookmark($PdfData->IndexName, 0);

foreach($PdfData->Data['Items'] as $Rows) {
	$pdf->SamePage(count($Rows) + 2);
	$pdf->lastY += 1;
	$first=true;

	foreach($Rows as $MyRow) {
		$tmp=array(
			'',
			$MyRow->NationCode,
			$MyRow->Nation
			);

		if(!$first) {
			$tmp[0]='';
			$tmp[1]='';
			$tmp[2]='';
		}

		$pdf->printDataRow($tmp);

		$first=false;

		if(!isset($PdfData->HTML)) continue;

		$PdfData->HTML['Countries'][$MyRow->NationCode]['Description']=$MyRow->Nation;
	}
}

?>