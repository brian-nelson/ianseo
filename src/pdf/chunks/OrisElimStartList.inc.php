<?php

$Indices=array('bis','ter','quat', 'quin', 'sex', 'sept', 'oct');

$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);

$OldEvent='#@#@#';
$targetNo=-1;

$First=true;
foreach($PdfData->Data['Items'] as $MyRow) {
	$NumEnd=($MyRow->Session == 0 ? 12 : 8);
	if((!is_null($MyRow->EventCode) && $OldEvent != $MyRow->EventCode) || (is_null($MyRow->EventCode) && $OldEvent && '#@#@#')) {
		$pdf->setEvent($MyRow->EventName);
		if($MyRow->Session == 0)
			$pdf->setPhase("Elimination Round 1");
		else if($MyRow->Session == 1 && ($MyRow->EvElim1 !=0 && $MyRow->EvElim2 !=0))
			$pdf->setPhase("Elimination Round 2");
		else
			$pdf->setPhase("Elimination Round");

		if(!is_null($MyRow->SesName))
			$pdf->setComment($MyRow->SesName);

		$pdf->setOrisCode('C51A', 'Start List by Target');
		$pdf->AddPage();
		if($First and (empty($pdf->CompleteBookTitle) or $pdf->CompleteBookTitle!=$PdfData->IndexName)) {
			$pdf->Bookmark($PdfData->IndexName, 0);
			$pdf->CompleteBookTitle=$PdfData->IndexName;
		}
		$First=false;
		$pdf->Bookmark($MyRow->EventName, 1);

		$targetNo = -1;
		$OldEvent = $MyRow->EventCode;
	}

	$NumTarget= intval($MyRow->TargetNo);
	if($NumTarget>$NumEnd) {
		$NumTarget = (($NumTarget-1) % ($NumEnd)) + 1;
	}

	if($targetNo != substr($MyRow->TargetNo,0,-1)) {
		$TargetToPrint=$MyRow->TargetNo;
		if($MyRow->TargetNo and $NumTarget!=intval($MyRow->TargetNo)) {
			$TargetToPrint = $NumTarget . $Indices[ceil(intval($MyRow->TargetNo)/($NumEnd))-2] . '-' . substr($MyRow->TargetNo,-1,1);
		}
		$pdf->lastY += 3.5;
		$pdf->printDataRow(array(
				trim($TargetToPrint,'0') . "  #",
				$MyRow->FirstName . ' ' . $MyRow->Name,
				$MyRow->NationCode,
				$MyRow->Nation,
				$MyRow->Ranking,
				$MyRow->DOB
		));
		$targetNo = substr($MyRow->TargetNo,0,-1);
	} else {
		$pdf->printDataRow(array(
				substr($MyRow->TargetNo,-1,1) . "  #",
				$MyRow->FirstName . ' ' . $MyRow->Name,
				$MyRow->NationCode,
				$MyRow->Nation,
				$MyRow->Ranking,
				$MyRow->DOB
		));
	}

	if(!isset($PdfData->HTML)) continue;

	$PdfData->HTML['sessions'][$MyRow->EventName]['Description']=$MyRow->EventName . ' ' . ($MyRow->SesName ? $MyRow->SesName : $PdfData->Data['Fields']['Session'] . ' ' . $MyRow->Session);
	$PdfData->HTML['sessions'][$MyRow->EventName]['Targets'][$TargetToPrint][]=array(
			ltrim(substr($TargetToPrint,0,-1),'0').substr($MyRow->TargetNo,-1),
			$MyRow->FirstName . ' ' . $MyRow->Name,
			$MyRow->NationCode,
			$MyRow->Nation,
			$MyRow->EventName,
	);
}
