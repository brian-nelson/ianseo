<?php

$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
$pdf->setPhase($PdfData->Phase);

$OldEvent='';
$OldTarget='';
foreach($PdfData->Data['Items'] as $MyRow) {
	if($OldEvent!=$MyRow->EventCode) {
		// Each Event starts on a new page
		$OldEvent=$MyRow->EventCode;
		$pdf->setEvent($MyRow->EventName);
		$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
		$pdf->AddPage();
		$OldTarget='';
	}

	$TgNo=substr($MyRow->TargetNo,0,-1);
	$Col1=substr($MyRow->TargetNo,-1,1) . "  #";
	if($OldTarget!=$TgNo) {
		// separates the new target
		$pdf->SamePage($MyRow->SesAth4Target + 2); // because we must take into account the last previous row AND the separator
		$pdf->lastY += 3.5;
		$Col1=$MyRow->TargetNo . "  #";
		$OldTarget=$TgNo;
	}

	$pdf->printDataRow(array(
			$Col1,
			$MyRow->Athlete,
			$MyRow->NationCode,
			$MyRow->Nation,
			$MyRow->DOB,
			));

	if(!isset($PdfData->HTML)) continue;

	$PdfData->HTML['sessions'][$MyRow->Session]['Description']=($MyRow->SesName ? $MyRow->SesName : $PdfData->Data['Fields']['Session'] . ' ' . $MyRow->Session);
	// may go for several events...
	if(empty($PdfData->HTML['sessions'][$MyRow->Session]['Targets'][$TgNo][$MyRow->TargetNo])) {
		$PdfData->HTML['sessions'][$MyRow->Session]['Targets'][$TgNo][$MyRow->TargetNo]=array(
			$MyRow->TargetNo,
			$MyRow->Athlete,
			$MyRow->NationCode,
			$MyRow->Nation,
			$MyRow->DivDescription . ' ' . $MyRow->ClDescription,
			$MyRow->EventName,
			);
	} elseif(!empty($PdfData->HTML['sessions'][$MyRow->Session]['Targets'][$TgNo][$MyRow->TargetNo][5])) {
		$PdfData->HTML['sessions'][$MyRow->Session]['Targets'][$TgNo][$MyRow->TargetNo][4]=$PdfData->HTML['sessions'][$MyRow->Session]['Targets'][$TgNo][$MyRow->TargetNo][5].', '.$MyRow->EventName;
		unset($PdfData->HTML['sessions'][$MyRow->Session]['Targets'][$TgNo][$MyRow->TargetNo][5]);
	} else {
		$PdfData->HTML['sessions'][$MyRow->Session]['Targets'][$TgNo][$MyRow->TargetNo][4].=', '.$MyRow->EventName;
	}
}

?>