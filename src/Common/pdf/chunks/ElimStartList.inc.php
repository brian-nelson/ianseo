<?php

$Indices=array('bis','ter','quat', 'quin', 'sex', 'sept', 'oct');

$ShowStatusLegend = false;
$CurSession=-1;
$OldTarget='';

foreach($PdfData->Data['Items'] as $MyRows) {
	foreach($MyRows as $MyRow) {
		$NumEnd=$MyRow->NumTargets;

		$athlete=$MyRow->FirstName . ' ' . $MyRow->Name;
		$titlePools=$MyRow->SesName;
		$Check = $MyRow->ElSession . $MyRow->Session;
		if($MyRow->EvElimType==3) {
			$Check = $MyRow->GrPhase;
			$titlePools=$PdfData->MatchTitles[intval($MyRow->FinMatchNo/2)*2];
			if(!trim($athlete)) {
				$athlete=$PdfData->MatchSlots[$MyRow->FinMatchNo];
			}
		} elseif($MyRow->EvElimType==4) {
			$Check = $MyRow->GrPhase;
			$titlePools=$PdfData->MatchTitlesWA[intval($MyRow->FinMatchNo/2)*2];
			if(!trim($athlete)) {
				$athlete=$PdfData->MatchSlotsWA[$MyRow->FinMatchNo];
			}
		}

		if ($CurSession != $Check || !$pdf->SamePage(4) || ($MyRow->TargetLetter=='A' && !$pdf->SamePage(16))) {
			if($CurSession!=-1) {
				$pdf->SetXY(10,$pdf->GetY()+5);
			}

			$TmpSegue=false;

			if($CurSession == $Check) {
				$TmpSegue = !$pdf->SamePage(4);
				if($MyRow->TargetLetter=='A' && !$pdf->SamePage(16)) {
					$TmpSegue=true;
					$pdf->AddPage();
				}
			} else {
				if(!$pdf->SamePage(16)) {
					$pdf->AddPage();
				}
				$CurSession = $Check;
			}

		    $pdf->SetFont($pdf->FontStd,'B',10);

			//$NumEnd=($MyRow->Session == 0 ? 12 : 8);
			//debug_svela($MyRow, true);

			$pdf->Cell(190, 6, $titlePools, 1, 1, 'C', 1);
			$OldTarget='';
			if($TmpSegue)
			{
				$pdf->SetXY(170,$pdf->GetY()-6);
			    $pdf->SetFont($pdf->FontStd,'I',6);
				$pdf->Cell(30, 6, $PdfData->Continue, 0, 1, 'R', 0);
			}
		    $pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->Cell(11, 4,  $PdfData->Data['Fields']['TargetNo'], 1, 0, 'C', 1);
			$pdf->Cell(10, 4,  $PdfData->Data['Fields']['Bib'], 1, 0, 'C', 1);
			$pdf->Cell(60, 4,  $PdfData->Data['Fields']['Athlete'], 1, 0, 'L', 1);
			$pdf->Cell(68, 4,  $PdfData->Data['Fields']['NationCode'], 1, 0, 'L', 1);
			$pdf->Cell(41, 4,  $PdfData->Data['Fields']['EventName'], 1, 0, 'C', 1);

			$pdf->Cell(1, 4,  '', 0, 1, 'C', 0);
			$OldTeam='';
			$FirstTime=false;
		}

		$NumTarget= intval($MyRow->TargetNo);
		if($NumTarget>$NumEnd) {
			$NumTarget = (($NumTarget-1) % ($NumEnd)) + 1;
		}

		$pdf->SetFont($pdf->FontStd,'B',8);
		if($OldTarget != substr($MyRow->TargetNo,0,-1)) {
			$TargetToPrint=$MyRow->TargetNo;
			if($MyRow->TargetNo and $NumTarget!=intval($MyRow->TargetNo)) {
				$TargetToPrint = $NumTarget . $Indices[ceil(intval($MyRow->TargetNo)/($NumEnd))-2] . '-' . substr($MyRow->TargetNo,-1,1);
			}
			$OldTarget = substr($MyRow->TargetNo,0,-1);
			$pdf->SetFont($pdf->FontStd,'',1);
			$pdf->Cell(190, 0.5,  '', 0, 1, 'C', 0);
			$pdf->SetFont($pdf->FontStd,'B',8);
			$pdf->Cell(7, 4, (substr(ltrim($TargetToPrint,'0'),0,-1)), 'LTB', 0, 'R', 0);
			$pdf->Cell(4, 4,  (substr($MyRow->TargetNo,-1,1)), 'RTB', 0, 'R', 0);
		} else {
			$pdf->Cell(7, 4,  '', 0, 0, 'R', 0);
			$pdf->Cell(4, 4,  (substr($MyRow->TargetNo,-1,1)), 1, 0, 'R', 0);
		}
	    $pdf->SetFont($pdf->FontStd,'',7);
		$pdf->Cell(10, 4,  $MyRow->Bib, 1, 0, 'R', 0);
		$pdf->Cell(60, 4,  $athlete, 1, 0, 'L', 0);


		$pdf->Cell(8, 4,  $MyRow->NationCode, 'LTB', 0, 'C', 0);
		$pdf->Cell(60, 4,  $MyRow->Nation, 'RTB', 0, 'L', 0);
		$pdf->Cell(41, 4,  $MyRow->EventName, 1, 0, 'C', 0);

		$pdf->Cell(1, 4,  '' , 0, 1, 'C', 0);

		if(!isset($PdfData->HTML)) continue;

		$PdfData->HTML['sessions'][$MyRow->EventName]['Description']=$MyRow->EventName . ' ' . ($MyRow->SesName ? $titlePools : $PdfData->Data['Fields']['Session'] . ' ' . $MyRow->Session);
		$PdfData->HTML['sessions'][$MyRow->EventName]['Targets'][$TargetToPrint][]=array(
            ltrim(substr($TargetToPrint,0,-1),'0').substr($MyRow->TargetNo,-1),
			$athlete,
			$MyRow->NationCode,
			$MyRow->Nation,
			$MyRow->EventName,
		);
	}
}