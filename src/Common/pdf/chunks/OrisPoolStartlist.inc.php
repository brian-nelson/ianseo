<?php


$PdfData->HeaderWidthPool[1]=array(15, $PdfData->HeaderWidthPool[1]);
unset($PdfData->HeaderPool[4]);

$OldEvent='#@#@#';
$targetNo=-1;

$OldMatchPhase=-1;

$First=true;
foreach($PdfData->Data['Items'] as $EvCode => $MyRows) {
	if(!$MyRows) {
		continue;
	}

	$check=current($MyRows);
	$pdf->SetDataHeader($PdfData->HeaderPool, $PdfData->HeaderWidthPool);

	$start=current($MyRows);

	$pdf->setEvent($start->EventName ? $start->EventName : $start->EvOdfCode);
	$pdf->setPhase("Elimination Pools");

	$pdf->setOrisCode('C51A', 'Start List by Schedule');
	//$pdf->AddPage();
	if($First and (empty($pdf->CompleteBookTitle) or $pdf->CompleteBookTitle!=$PdfData->IndexName)) {
		$pdf->Bookmark($PdfData->IndexName, 0);
		$pdf->CompleteBookTitle=$PdfData->IndexName;
	}
	$First=false;
	$pdf->Bookmark($start->EventName, 1);

	$targetNo = -1;
	$OldEvent = $start->EventCode;
	$OldMatchPhase=-1;

	$pdf->addPage();
	$OldDateTime='';

	if($check->EvElimType==4) {
		foreach($MyRows as $k => $MyRow) {
			// if left and right archers are not there and it is more than the 1/4th then skip
			$Opp=($k%2 ? $k-1 : $k+1);
			if(!$MyRow->Bib and !$MyRows[$Opp]->Bib and $MyRow->GrPhase>4) {
				continue;
			}

			// prints the date and time...
			if($OldDateTime!=$MyRow->ScheduledStart) {
				$OldFont=$pdf->getFontSizePt();
				$pdf->SetFont('', 'b', $OldFont+3);
				$pdf->SetY($pdf->lastY+3.5);
				$pdf->Cell(0, 5, $MyRow->ScheduledStart, '',1);
				$pdf->lastY+=7;
				$pdf->SetFont('', '', $OldFont);
				$OldDateTime=$MyRow->ScheduledStart;
				$pdf->lastY+=0.5;
			}

			if($MyRow->FinMatchNo%2 == 0) {
				$pdf->lastY+=2;
				$tgt=intval($MyRow->TargetNo);
				$PoolDesc=$PdfData->MatchTitlesWAShort[intval($MyRow->FinMatchNo/2)*2];
			} else {
				$tgt='';
				$PoolDesc='';
			}
			$athlete=$MyRow->FirstName . ' ' . $MyRow->Name;
			if(!trim($athlete)) {
				if(!empty($PdfData->MatchSlotsWA[$MyRow->FinMatchNo])) {
					$athlete=$PdfData->MatchSlotsWA[$MyRow->FinMatchNo];
				}
			}

			$row=array(
				$tgt,
				$PoolDesc,
				$athlete,
				$MyRow->NationCode,
				$MyRow->Nation,
				$MyRow->Ranking."#",
				$MyRow->DOB,
			);

			$pdf->printDataRow($row);
		}
	} else {
		foreach($MyRows as $k => $MyRow) {
			// if left and right archers are not there and it is more than the 1/4th then skip
			$Opp=($k%2 ? $k-1 : $k+1);
			if(!$MyRow->Bib and !$MyRows[$Opp]->Bib and $MyRow->GrPhase>4) {
				continue;
			}

			// prints the date and time...
			if($OldDateTime!=$MyRow->ScheduledStart) {
				$OldFont=$pdf->getFontSizePt();
				$pdf->SetFont('', 'b', $OldFont+3);
				$pdf->SetY($pdf->lastY+3.5);
				$pdf->Cell(0, 5, $MyRow->ScheduledStart, '',1);
				$pdf->lastY+=7;
				$pdf->SetFont('', '', $OldFont);
				$OldDateTime=$MyRow->ScheduledStart;
				$pdf->lastY+=0.5;
			}

			if($MyRow->FinMatchNo%2 == 0) {
				$pdf->lastY+=2;
				$tgt=intval($MyRow->TargetNo);
				$PoolDesc=$PdfData->MatchTitlesShort[intval($MyRow->FinMatchNo/2)*2];
			} else {
				$tgt='';
				$PoolDesc='';
			}
			$athlete=$MyRow->FirstName . ' ' . $MyRow->Name;
			if(!trim($athlete)) {
				if(!empty($PdfData->MatchSlots[$MyRow->FinMatchNo])) {
					$athlete=$PdfData->MatchSlots[$MyRow->FinMatchNo];
				}
			}

			$row=array(
				$tgt,
				$PoolDesc,
				$athlete,
				$MyRow->NationCode,
				$MyRow->Nation,
				$MyRow->Ranking."#",
				$MyRow->DOB,
			);

			$pdf->printDataRow($row);
		}
	}
}
