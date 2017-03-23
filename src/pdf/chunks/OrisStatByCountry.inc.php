<?php
$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
$pdf->setPhase('');

$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
$pdf->AddPage();
$pdf->Bookmark($PdfData->IndexName, 0);

$ONLINE=isset($PdfData->HTML);
$Total = array("M"=>0,"W"=>0,"Of"=>0);
$CountryCnt=0;
foreach($PdfData->Data['Items'] as $MyRow) {
	if($ONLINE and !$MyRow->IsAthlete) continue;
	$tmp=array(
		$MyRow->NationCode,
		$MyRow->NationName,
		$MyRow->M . "#",
		$MyRow->W . "#",
		($MyRow->M+$MyRow->W) . "#",
		"",
		$MyRow->Of . "#",
		($MyRow->M+$MyRow->W+$MyRow->Of) . "#",
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
		$Total["M"] . "#",
		$Total["W"] . "#",
		($Total["M"]+$Total["W"]) . "#",
		"",
		$Total["Of"]  . "#",
		($Total["M"]+$Total["W"]+$Total["Of"] ) . "#",
);
$pdf->printDataRow($tmp);
?>