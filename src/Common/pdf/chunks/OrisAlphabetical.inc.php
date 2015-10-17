<?php
$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
$pdf->setPhase('');
$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
$pdf->AddPage();

$ONLINE=isset($PdfData->HTML);

foreach($PdfData->Data['Items'] as $LetterGroup => $Rows) {
	foreach($Rows as $MyRow) {
		if($ONLINE and !$MyRow->IsAthlete) continue;
		$pdf->printDataRow(array(
			$MyRow->Athlete,
			$MyRow->IsAthlete ? $MyRow->NationCode : '',
			$MyRow->IsAthlete ? $MyRow->Nation : '',
			$MyRow->IsAthlete ? $MyRow->DOB : '',
			$MyRow->IsAthlete ? $MyRow->TargetNo : '',
			$MyRow->EventName
		));
	}
	$pdf->lastY += 3.5;

	if(!$ONLINE) continue;

	foreach($Rows as $MyRow) {
		if(!$MyRow->IsAthlete) continue;
		$PdfData->HTML['Letters'][$MyRow->FirstLetter][]=array(
			$MyRow->Athlete,
			$MyRow->TargetNo,
			$MyRow->NationCode,
			$MyRow->Nation,
			$MyRow->EvCode ? $MyRow->EventName : $MyRow->DivDescription . ' ' . $MyRow->ClDescription,
			$MyRow->SesName,
			);
	}
}
?>