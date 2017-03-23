<?php

$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
$pdf->setPhase($PdfData->Phase);

$OldEvent='';
$OldTarget='';
$First=true;
$OldTeam='#@#@#';
$OldEvent='#@#@#';
foreach($PdfData->Data['Items'] as $Group) {
	foreach($Group as $MyRow) {
		if($OldEvent != $MyRow->EventCode) {
			$pdf->setEvent($MyRow->EventName);
			$pdf->AddPage();
			$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
			$OldTeam='#@#@#';
			$OldEvent = $MyRow->EventCode;
		}

		$TgtNo=ltrim(($PdfData->BisTarget && (intval(substr($MyRow->TargetNo,1)) > $PdfData->NumEnd) ? str_pad((substr($MyRow->TargetNo,0,-1)-$PdfData->NumEnd),3,"0",STR_PAD_LEFT) . substr($MyRow->TargetNo,-1,1) . ' bis'  : $MyRow->TargetNo), 'O');

		if($OldTeam != $MyRow->NationCode) {
			$pdf->SamePage($MyRow->cNumber + 1);
			$pdf->lastY += 3.5;
			$pdf->printDataRow(array(
					$MyRow->NationCode,
					$MyRow->Nation,
					'#'.ltrim($TgtNo,'0'),
					$PdfData->IsRanked ? ($MyRow->Ranking ? $MyRow->Ranking : '-').'    #' : '',
					$MyRow->DOB,
					$MyRow->Athlete
			));
			$OldTeam = $MyRow->NationCode;
		} else {
			$pdf->printDataRow(array(
					"",
					"",
					'#'.ltrim($TgtNo,'0'),
					$PdfData->IsRanked ? ($MyRow->Ranking ? $MyRow->Ranking : '-').'    #' : '',
					$MyRow->DOB,
					$MyRow->Athlete
			));
		}

		if(!isset($PdfData->HTML)) continue;

		$PdfData->HTML['Events'][$MyRow->EventCode]['Description']=$MyRow->EventName;
		// may go for several events...
		if(empty($PdfData->HTML['Events'][$MyRow->EventCode]['Countries'][$MyRow->NationCode])) {
			$PdfData->HTML['Events'][$MyRow->EventCode]['Countries'][$MyRow->NationCode]=array();
		}
		$PdfData->HTML['Events'][$MyRow->EventCode]['Countries'][$MyRow->NationCode][]=array(
				$MyRow->NationCode,
				$MyRow->Nation,
				$TgtNo,
				$MyRow->Ranking,
				$MyRow->DOB,
				$MyRow->Athlete,
					$MyRow->SesName ? $MyRow->SesName : $PdfData->Data['Fields']['Session'].' '. $MyRow->Session
				);
	}
}

