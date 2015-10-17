<?php

$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
$pdf->setPhase('');

$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
$pdf->AddPage();

$ONLINE=isset($PdfData->HTML);

$AddPage=false;

foreach($PdfData->Data['Items'] as $Rows) {
	if($AddPage and !empty($_REQUEST['SinglePage'])) $pdf->addpage();
	$AddPage=true;
	$pdf->SamePage(count($Rows) + 2);
	$pdf->lastY += 3.5;
	$first=true;

	foreach($Rows as $MyRow) {
		if($ONLINE and !$MyRow->IsAthlete) continue;
		$tmp=array(
			$MyRow->NationCode,
			$MyRow->Nation,
			$MyRow->Athlete,
			($MyRow->IsAthlete ? $MyRow->DOB . "   #" : ''),
			$MyRow->IsAthlete ? $MyRow->TargetNo : '',
			$MyRow->EventName
			);

		if(!$first) {
			$tmp[0]='';
			$tmp[1]='';
		}

		$pdf->printDataRow($tmp);

		$first=false;

		if(!$ONLINE or !$MyRow->IsAthlete) continue;

		$PdfData->HTML['Countries'][$MyRow->NationCode]['Description']=$MyRow->Nation;
		$PdfData->HTML['Countries'][$MyRow->NationCode]['Archers'][]=array(
			$MyRow->Athlete,
			$MyRow->TargetNo,
			$MyRow->EvCode ? $MyRow->EventName : $MyRow->DivDescription . ' ' . $MyRow->ClDescription,
			$MyRow->SesName,
			);
	}
}

?>