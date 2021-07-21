<?php
$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
$pdf->setPhase('');

$pdf->AddPage();
$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
$pdf->Bookmark($PdfData->IndexName, 0);

$ONLINE=isset($PdfData->HTML);
$Total = array("M"=>0,"W"=>0,"Of"=>0);
$CountryCnt=0;
foreach($PdfData->Data['Items'] as $MyRow) {
	if($ONLINE and !$MyRow->IsAthlete) continue;
	$tmp=array(
		$MyRow->NationCode,
		$MyRow->NationName,
		number_format($MyRow->M,0,'','.') . "#",
		number_format($MyRow->W,0,'','.') . "#",
		number_format(($MyRow->M+$MyRow->W),0,'','.') . "#",
		"",
		number_format($MyRow->Of,0,'','.') . "#",
		number_format(($MyRow->M+$MyRow->W+$MyRow->Of),0,'','.') . "#",
	);
	$Total["M"] += $MyRow->M;
	$Total["W"] += $MyRow->W;
	$Total["Of"] += $MyRow->Of;
	$CountryCnt++;

	$pdf->printDataRow($tmp);
	$first=false;

	if(!$ONLINE or !$MyRow->IsAthlete) continue;

	$PdfData->HTML['Countries'][$MyRow->NationCode]['Description']=$MyRow->NationName;
	$PdfData->HTML['Countries'][$MyRow->NationCode]['Numers'][]=array(
		$MyRow->M,
		$MyRow->W,
		$MyRow->Of
		);
}
$pdf->lastY += 2;
$pdf->Line(10, $pdf->lastY, array_sum($PdfData->HeaderWidth)+60, $pdf->lastY);
$pdf->lastY += 2;
$tmp=array(
		"",
		"Total: " . $CountryCnt,
		number_format($Total["M"],0,'','.') . "#",
		number_format($Total["W"],0,'','.') . "#",
		number_format(($Total["M"]+$Total["W"]),0,'','.') . "#",
		"",
		number_format($Total["Of"],0,'','.')  . "#",
		number_format(($Total["M"]+$Total["W"]+$Total["Of"] ),0,'','.') . "#",
);
$pdf->printDataRow($tmp);
