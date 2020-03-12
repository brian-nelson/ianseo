<?php

$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
$pdf->setPhase($PdfData->Phase);

$OldEvent='';
$OldTarget='';
$First=true;

if(!empty($PdfData->Data['Items'])) {
	foreach($PdfData->Data['Items'] as $MyRows) {
		foreach($MyRows as $MyRow) {

			if($OldEvent!=$MyRow->EventCode) {
				// Each Event starts on a new page
				$OldEvent=$MyRow->EventCode;
				$pdf->setEvent($MyRow->EventName);
				$Version='';
				if($MyRow->DocVersion) {
					$Version=trim('Vers. '.$MyRow->DocVersion . " ($MyRow->DocVersionDate) $MyRow->DocNotes");
				}
				$pdf->setComment($Version);
				$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
				$pdf->AddPage();
				if($First and (empty($pdf->CompleteBookTitle) or $pdf->CompleteBookTitle!=$PdfData->IndexName)) {
					$pdf->Bookmark($PdfData->IndexName, 0);
					$pdf->CompleteBookTitle=$PdfData->IndexName;
				}
				$First=false;
				$pdf->Bookmark($MyRow->EventName, 1);
				$OldTarget='';
			}

			$TgNo=substr($MyRow->TargetNo,0,-1);
			$Col1=substr($MyRow->TargetNo,-1,1) . "  #";
			if($OldTarget!=$TgNo) {
				// separates the new target
				$pdf->SamePage($MyRow->SesAth4Target + 2, 3.5, $pdf->lastY); // because we must take into account the last previous row AND the separator
				$pdf->lastY += 3.5;
				if($PdfData->BisTarget && ($TgNo > $PdfData->NumEnd))
					$Col1= 'bis ' . str_pad((substr($MyRow->TargetNo,0,-1)-$PdfData->NumEnd),3,"0",STR_PAD_LEFT) . $Col1;
				else
					$Col1=$MyRow->TargetNo . "  #";
				$OldTarget=$TgNo;
			}

			$pdf->printDataRow(array(
					ltrim($Col1, '0'),
					$MyRow->Athlete,
					$MyRow->NationCode,
					$MyRow->Nation,
					$PdfData->IsRanked ? ($MyRow->Ranking ? $MyRow->Ranking : '-').'    #' : '',
					$MyRow->DOB,
					));

			if(!isset($PdfData->HTML)) continue;

			$PdfData->HTML['sessions'][$MyRow->Session]['Description']=($MyRow->SesName ? $MyRow->SesName : $PdfData->Data['Fields']['Session'] . ' ' . $MyRow->Session);
			// may go for several events...
			if(empty($PdfData->HTML['sessions'][$MyRow->Session]['Targets'][$TgNo][$MyRow->TargetNo])) {
				$PdfData->HTML['sessions'][$MyRow->Session]['Targets'][$TgNo][$MyRow->TargetNo]=array(
					(!empty($PdfData->BisTarget) && (intval(substr($MyRow->TargetNo,1)) > $PdfData->NumEnd) ? 'bis ' . (substr($MyRow->TargetNo,0,-1)-$PdfData->NumEnd) . substr($MyRow->TargetNo,-1,1)  : $MyRow->TargetNo),
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
	}
}

