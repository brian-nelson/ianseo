<?php
$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
$pdf->setPhase('Entries');
$Version='';
if($PdfData->DocVersion) {
	$Version=trim('Vers. '.$PdfData->DocVersion . " ($PdfData->DocVersionDate) $PdfData->DocVersionNotes");
}
$pdf->setComment($Version);
$pdf->AddPage();
$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
$pdf->Bookmark($PdfData->IndexName, 0);

$ONLINE=isset($PdfData->HTML);
$First=true;
foreach($PdfData->Data['Items'] as $LetterGroup => $Rows) {
	if(!$First) {
		$pdf->lastY += 3.5;
	}
	foreach($Rows as $MyRow) {
		if($ONLINE and !$MyRow->IsAthlete) continue;
		$Tgt=ltrim(!empty($PdfData->BisTarget) && (intval(substr($MyRow->TargetNo,1)) > $PdfData->NumEnd) ? str_pad((substr($MyRow->TargetNo,0,-1)-$PdfData->NumEnd),3,"0",STR_PAD_LEFT) . substr($MyRow->TargetNo,-1,1) . ' bis'  : $MyRow->TargetNo, '0');
		if($MyRow->IsAthlete) {
			$tmp=array(
			$MyRow->Athlete,
			$MyRow->NationCode,
			$MyRow->Nation,
			$PdfData->IsRanked ? ($MyRow->Ranking ? $MyRow->Ranking : '-').'    #' : '',
			$MyRow->DOB.'#',
			$Tgt.'  #',
			$MyRow->EventName);
		} else {
			$tmp=array($MyRow->Athlete, $MyRow->NationCode, $MyRow->Nation, '', '', '', $MyRow->EventName);
		}
		$pdf->printDataRow($tmp);
	}
	$First=false;

	if(!$ONLINE) continue;

	foreach($Rows as $MyRow) {
		if(!$MyRow->IsAthlete) continue;
		$Tgt=ltrim(!empty($PdfData->BisTarget) && (intval(substr($MyRow->TargetNo,1)) > $PdfData->NumEnd) ? str_pad((substr($MyRow->TargetNo,0,-1)-$PdfData->NumEnd),3,"0",STR_PAD_LEFT) . substr($MyRow->TargetNo,-1,1) . ' bis'  : $MyRow->TargetNo, '0');
		$PdfData->HTML['Letters'][$MyRow->FirstLetter][]=array(
			$MyRow->Athlete,
			$Tgt,
			$MyRow->NationCode,
			$MyRow->Nation,
			$MyRow->EvCode ? $MyRow->EventName : $MyRow->DivDescription . ' ' . $MyRow->ClDescription,
			$MyRow->SesName,
			);
	}
}
?>
