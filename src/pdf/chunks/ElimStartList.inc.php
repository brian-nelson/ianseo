<?php

$Indices=array('bis','ter','quat', 'quin', 'sex', 'sept', 'oct');

$ShowStatusLegend = false;
$CurSession=-1;
$OldTarget='';

foreach($PdfData->Data['Items'] as $MyRow) {
	$NumEnd=($MyRow->Session == 0 ? 12 : 8);
	if ($CurSession != $MyRow->ElSession . $MyRow->Session || !$pdf->SamePage(4) || ($MyRow->TargetLetter=='A' && !$pdf->SamePage(16))) {
		if($CurSession!=-1) {
			$pdf->SetXY(10,$pdf->GetY()+5);
		}

		$TmpSegue=false;

		if($CurSession == $MyRow->ElSession . $MyRow->Session) {
			$TmpSegue = !$pdf->SamePage(4);
			if($MyRow->TargetLetter=='A' && !$pdf->SamePage(16)) {
				$TmpSegue=true;
				$pdf->AddPage();
			}
		} else {
			$CurSession = $MyRow->ElSession . $MyRow->Session;
		}

	   	$pdf->SetFont($pdf->FontStd,'B',10);

		$NumEnd=($MyRow->Session == 0 ? 12 : 8);

		$pdf->Cell(190, 6, $MyRow->SesName, 1, 1, 'C', 1);
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
		$TargetToPrint=intval($MyRow->TargetNo);
		if($MyRow->TargetNo and $NumTarget!=intval($MyRow->TargetNo)) {
			$TargetToPrint = $NumTarget . $Indices[ceil(intval($MyRow->TargetNo)/($NumEnd))-2] ;
		}
		$OldTarget = substr($MyRow->TargetNo,0,-1);
		$pdf->SetFont($pdf->FontStd,'',1);
		$pdf->Cell(190, 0.5,  '', 0, 1, 'C', 0);
		$pdf->SetFont($pdf->FontStd,'B',8);
		$pdf->Cell(7, 4, $TargetToPrint, 'LTB', 0, 'R', 0);
		$pdf->Cell(4, 4,  (substr($MyRow->TargetNo,-1,1)), 'RTB', 0, 'R', 0);
	} else {
		$pdf->Cell(7, 4,  '', 0, 0, 'R', 0);
		$pdf->Cell(4, 4,  (substr($MyRow->TargetNo,-1,1)), 1, 0, 'R', 0);
	}
   	$pdf->SetFont($pdf->FontStd,'',7);
	$pdf->Cell(10, 4,  $MyRow->Bib, 1, 0, 'R', 0);
	$pdf->Cell(60, 4,  $MyRow->FirstName . ' ' . $MyRow->Name, 1, 0, 'L', 0);
	$pdf->Cell(8, 4,  $MyRow->NationCode, 'LTB', 0, 'C', 0);
	$pdf->Cell(60, 4,  $MyRow->Nation, 'RTB', 0, 'L', 0);
	$pdf->Cell(41, 4,  $MyRow->EventName, 1, 0, 'C', 0);

	$pdf->Cell(1, 4,  '' , 0, 1, 'C', 0);

	if(!isset($PdfData->HTML)) continue;

	$PdfData->HTML['sessions'][$MyRow->EventName]['Description']=$MyRow->EventName . ' ' . ($MyRow->SesName ? $MyRow->SesName : $PdfData->Data['Fields']['Session'] . ' ' . $MyRow->Session);
	$PdfData->HTML['sessions'][$MyRow->EventName]['Targets'][$TargetToPrint][]=array(
			ltrim($TargetToPrint,'0').'-'.substr($MyRow->TargetNo,-1),
			$MyRow->FirstName . ' ' . $MyRow->Name,
			$MyRow->NationCode,
			$MyRow->Nation,
			$MyRow->EventName,
	);
}
