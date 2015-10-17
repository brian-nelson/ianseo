<?php
$TargetFace=(isset($_REQUEST['tf']) && $_REQUEST['tf']==1);

$pdf->HideCols = $PdfData->HideCols;

$StartLetter = ".";
$ShowStatusLegend = false;
if (isset($PdfData->Data['Items']) && count($PdfData->Data['Items'])>0)
{
	foreach($PdfData->Data['Items'] as $Group => $Rows) {
		if(!$pdf->SamePage(20)) 
			$pdf->AddPage();

	   	$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->Cell(190, 6,  $Rows[0]->EventCode, 1, 1, 'C', 1);

		$pdf->SetFont($pdf->FontStd,'B',7);
		$pdf->Cell(10, 4, $PdfData->Data['Fields']['Bib'], 1, 0, 'C', 1);
		$pdf->Cell(41, 4, $PdfData->Data['Fields']['Athlete'], 1, 0, 'L', 1);
		$pdf->Cell(54, 4, $PdfData->Data['Fields']['Nation'], 1, 0, 'L', 1);
		$pdf->Cell(7, 4,  $PdfData->Data['Fields']['Session'], 1, 0, 'C', 1);
		$pdf->Cell(11, 4, $PdfData->Data['Fields']['TargetNo'], 1, 0, 'C', 1);
		if(!$PdfData->HideCols && !$TargetFace)
		{
			$pdf->Cell(11, 4, $PdfData->Data['Fields']['AgeClass'], 1, 0, 'C', 1);
			$pdf->Cell(8, 4, $PdfData->Data['Fields']['SubClass'], 1, 0, 'C', 1);
		}
		$pdf->Cell(12 + ($PdfData->HideCols==true ? 22 : 0), 4, $PdfData->Data['Fields']['DivDescription'], 1, 0, 'C', 1);
		$pdf->Cell(12 + ($PdfData->HideCols==true ? 21 : 0), 4, $PdfData->Data['Fields']['ClDescription'], 1, 0, 'C', 1);

		if ($TargetFace)
		{
			$pdf->Cell(19, 4, $PdfData->Data['Fields']['TargetFace'], 1, 0, 'C', 1);
		}

		//Disegna i Pallini
		if(!$PdfData->HideCols)
		{
			$pdf->DrawParticipantHeader();
		   	$pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->Cell(10, 4, $PdfData->Data['Fields']['Status'], 1, 0, 'C', 1);
		}
		$pdf->Cell(1,  4,  '', 0, 1, 'C', 0);
		$pdf->SetFont($pdf->FontStd,'',1);
		$pdf->Cell(190, 0.5,  '', 1, 1, 'C', 0);

		foreach($Rows as $MyRow) {
			$secondaryTeam = (is_null($MyRow->NationCode2) ? 1 : 2);
			if ($secondaryTeam==2 && !is_null($MyRow->NationCode3))
			{
				$secondaryTeam=3;
			}
			if (!$pdf->SamePage(4*$secondaryTeam)) {
				$pdf->AddPage();

				$pdf->SetFont($pdf->FontStd,'B',10);
				$pdf->Cell(190, 6,  $Rows[0]->EventCode, 1, 1, 'C', 1);
				$pdf->SetXY(170,$pdf->GetY()-6);
			   	$pdf->SetFont($pdf->FontStd,'I',6);
				$pdf->Cell(30, 6, $PdfData->Continue, 0, 1, 'R', 0);

				$pdf->SetFont($pdf->FontStd,'B',7);
				$pdf->Cell(10, 4, $PdfData->Data['Fields']['Bib'], 1, 0, 'C', 1);
				$pdf->Cell(41, 4, $PdfData->Data['Fields']['Athlete'], 1, 0, 'L', 1);
				$pdf->Cell(54, 4, $PdfData->Data['Fields']['Nation'], 1, 0, 'L', 1);
				$pdf->Cell(7, 4,  $PdfData->Data['Fields']['Session'], 1, 0, 'C', 1);
				$pdf->Cell(11, 4, $PdfData->Data['Fields']['TargetNo'], 1, 0, 'C', 1);
				if(!$PdfData->HideCols && !$TargetFace)
				{
					$pdf->Cell(11, 4, $PdfData->Data['Fields']['AgeClass'], 1, 0, 'C', 1);
					$pdf->Cell(8, 4, $PdfData->Data['Fields']['SubClass'], 1, 0, 'C', 1);
				}
				$pdf->Cell(12 + ($PdfData->HideCols==true ? 22 : 0), 4, $PdfData->Data['Fields']['DivDescription'], 1, 0, 'C', 1);
				$pdf->Cell(12 + ($PdfData->HideCols==true ? 21 : 0), 4, $PdfData->Data['Fields']['ClDescription'], 1, 0, 'C', 1);

				if ($TargetFace)
				{
					$pdf->Cell(19, 4, $PdfData->Data['Fields']['TargetFace'], 1, 0, 'C', 1);
				}

				//Disegna i Pallini
				if(!$PdfData->HideCols)
				{
					$pdf->DrawParticipantHeader();
				   	$pdf->SetFont($pdf->FontStd,'B',7);
					$pdf->Cell(10, 4, $PdfData->Data['Fields']['Status'], 1, 0, 'C', 1);
				}
				$pdf->Cell(1,  4,  '', 0, 1, 'C', 0);
				$pdf->SetFont($pdf->FontStd,'',1);
				$pdf->Cell(190, 0.5,  '', 1, 1, 'C', 0);
			}

		   	$pdf->SetFont($pdf->FontStd,'',7);
			$pdf->Cell(10, 4 * $secondaryTeam,  $MyRow->IsAthlete ? $MyRow->Bib : '', 1, 0, 'R', 0);
		   	$pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->Cell(41, 4 * $secondaryTeam,  $MyRow->Athlete, 1, 0, 'L', 0);
		   	$pdf->SetFont($pdf->FontStd,'',7);
		   	$pdf->Cell(8, 4,  $MyRow->NationCode, 'LTB', 0, 'C', 0);
			$pdf->Cell(46, 4,  $MyRow->Nation . ($MyRow->EnSubTeam==0 ? "" : " (" . $MyRow->EnSubTeam . ")"), 'RTB', 0, 'L', 0);
			if($secondaryTeam>=2)
			{
				$secTmpX=$pdf->GetX();
				$secTmpY=$pdf->GetY();
				$pdf->SetXY($secTmpX-54,$secTmpY+4);
		   		$pdf->Cell(8, 4,  $MyRow->NationCode2, 'LTB', 0, 'C', 0);
				$pdf->Cell(46, 4,  $MyRow->Nation2, 'RTB', 0, 'L', 0);
				$pdf->SetXY($secTmpX,$secTmpY);
			}
			if($secondaryTeam==3)
			{
				$secTmpX=$pdf->GetX();
				$secTmpY=$pdf->GetY();
				$pdf->SetXY($secTmpX-54,$secTmpY+8);
		   		$pdf->Cell(8, 4,  $MyRow->NationCode3, 'LTB', 0, 'C', 0);
				$pdf->Cell(46, 4, $MyRow->Nation3, 'RTB', 0, 'L', 0);
				$pdf->SetXY($secTmpX,$secTmpY);
			}
			$pdf->Cell(7, 4 * $secondaryTeam,  $MyRow->IsAthlete ? $MyRow->Session : '', 1, 0, 'R', 0);
			$pdf->Cell(11, 4 * $secondaryTeam,  $MyRow->IsAthlete ? $MyRow->TargetNo : '', 1, 0, 'R', 0);
			if(!$PdfData->HideCols && !$TargetFace)
			{
				$pdf->Cell(11, 4 * $secondaryTeam,  ($MyRow->AgeClass), 1, 0, 'C', 0);
				$pdf->Cell(8, 4 * $secondaryTeam,  ($MyRow->SubClass), 1, 0, 'C', 0);
			}
			$pdf->Cell(12 + ($PdfData->HideCols==true ? 22 : 0), 4 * $secondaryTeam, ($PdfData->HideCols==true ? $MyRow->DivDescription : $MyRow->DivCode), 1, 0, 'C', 0);
			$pdf->Cell(12 + ($PdfData->HideCols==true ? 21 : 0), 4 * $secondaryTeam, ($PdfData->HideCols==true ? $MyRow->ClDescription : $MyRow->ClassCode), 1, 0, 'C', 0);

			if ($TargetFace)
			{
				$pdf->Cell(19,4* $secondaryTeam,get_text($MyRow->TfName,'Tournament','',true),1,0,'C',0);
			}
			//Disegna i Pallini per la partecipazione
			if(!$PdfData->HideCols)
			{
				if(!$MyRow->IsAthlete) {
					$pdf->DrawParticipantDetails(-1);
				} elseif($secondaryTeam==1) {
					$pdf->DrawParticipantDetails($MyRow->IC, $MyRow->IF, $MyRow->TC, $MyRow->TF, $MyRow->TM);
				} elseif($secondaryTeam>=2) {
					$pdf->DrawParticipantDetails($MyRow->IC, $MyRow->IF);
					$secTmpX=$pdf->GetX();
					$secTmpY=$pdf->GetY();
					$pdf->SetXY($secTmpX-14,$secTmpY+4);
					$pdf->DrawParticipantDetails(0, 0, $MyRow->TC, $MyRow->TF, $MyRow->TM);
					$pdf->SetXY($secTmpX,$secTmpY);
				} else {

				}

				$pdf->SetDefaultColor();
				$pdf->SetFont($pdf->FontStd, '', 7);
				$ShowStatusLegend = ($ShowStatusLegend || ($MyRow->Status!=0));
				$pdf->Cell(10, 4 * $secondaryTeam,  ($MyRow->Status==0 ? '' : ($MyRow->Status)) , 1, 0, 'C', 0);
			}
			$pdf->Cell(1,  4 * $secondaryTeam,  '', 0, 1, 'C', 0);

			if(!isset($PdfData->HTML)) continue;

			$PdfData->HTML['Letters'][$MyRow->EventCode][]=array(
				$MyRow->Athlete,
				$MyRow->TargetNo,
				$MyRow->NationCode,
				$MyRow->Nation,
				$MyRow->EvCode ? $MyRow->EventName : $MyRow->DivDescription . ' ' . $MyRow->ClDescription,
				$MyRow->SesName ? $MyRow->SesName : $PdfData->Data['Fields']['Session'].' '. $MyRow->Session,
				);
		}

		$pdf->SetY($pdf->GetY()+5);
	}
}

// Legenda per la partecipazione alle varie fasi
if(!$PdfData->HideCols) {
	$pdf->DrawPartecipantLegend();
	// Legenda per lo stato di ammisisone alle gare
	if($ShowStatusLegend) $pdf->DrawStatusLegend();
}

?>