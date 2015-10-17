<?php

$pdf->HideCols = $PdfData->HideCols;

$SinglePage=isset($_REQUEST['SinglePage']);
$TargetFace=(isset($_REQUEST['tf']) && $_REQUEST['tf']==1);
$ShowStatusLegend = false;
$FirstTime=true;
$OldTeam='';
$OldSession='qwe';
if (isset($PdfData->Data['Items']) && count($PdfData->Data['Items'])>0)
{
	foreach($PdfData->Data['Items'] as $Country => $Rows) {
		if($SinglePage and !$FirstTime) {
			$pdf->AddPage();
			$FirstTime=true;
		}

		foreach($Rows as $MyRow) {
	//		if($MyRow->Session!=$OldSession) {
	//			$pdf->sety($pdf->gety()+1);
	//		}
	//		$OldSession=$MyRow->Session;

			if(isset($_REQUEST["NewPage"]) and $OldTeam != $MyRow->NationCode and $OldTeam) {
				$pdf->AddPage();
				$FirstTime=true;
			}
			if ($FirstTime || !$pdf->SamePage(4)) {
			   	$pdf->SetFont($pdf->FontStd,'B',7);
				$pdf->Cell(54, 4, $PdfData->Data['Fields']['Nation'], 1, 0, 'L', 1);
				$pdf->Cell( 7, 4, $PdfData->Data['Fields']['Session'], 1, 0, 'C', 1);
				$pdf->Cell(11, 4, $PdfData->Data['Fields']['TargetNo'], 1, 0, 'C', 1);
				$pdf->Cell(10, 4, $PdfData->Data['Fields']['Bib'], 1, 0, 'C', 1);
				$pdf->Cell(41, 4, $PdfData->Data['Fields']['Athlete'], 1, 0, 'L', 1);
				if(!$PdfData->HideCols && !$TargetFace)
				{
					$pdf->Cell(11, 4, $PdfData->Data['Fields']['AgeClass'], 1, 0, 'C', 1);
					$pdf->Cell( 8, 4, $PdfData->Data['Fields']['SubClass'], 1, 0, 'C', 1);
				}
				$pdf->Cell(12+ ($PdfData->HideCols==true ? 22 : 0), 4, $PdfData->Data['Fields']['DivDescription'], 1, 0, 'C', 1);
				$pdf->Cell(12+ ($PdfData->HideCols==true ? 21 : 0), 4, $PdfData->Data['Fields']['ClDescription'], 1, 0, 'C', 1);

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
				$pdf->Cell(1, 4,  '', 0, 1, 'C', 0);
				$OldTeam='';
				$FirstTime=false;
			}
			if($OldTeam != $MyRow->NationCode)
			{
			   	$pdf->SetFont($pdf->FontStd,'B',1);
				$pdf->Cell(190, 1,  '', 0, 1, 'C', 0);
				$pdf->SetFont($pdf->FontStd,'B',7);
				$pdf->Cell(8, 4,  $MyRow->NationCode, 'LTB', 0, 'C', 0);
				$pdf->Cell(46, 4,  $MyRow->Nation, 'RTB', 0, 'L', 0);
				$OldTeam = $MyRow->NationCode;
			}
			else
			{
				$pdf->Cell(54, 4,  '', 0, 0, 'C', 0);
			}
		   	$pdf->SetFont($pdf->FontStd,'',7);
			$pdf->Cell(7, 4,  ($MyRow->Session && $MyRow->IsAthlete ? $MyRow->Session : ''), 1, 0, 'R', 0);
			$pdf->Cell(11, 4,  ($MyRow->IsAthlete ? $MyRow->TargetNo : ''), 1, 0, 'R', 0);
			$pdf->Cell(10, 4,  ($MyRow->IsAthlete ? $MyRow->Bib : ''), 1, 0, 'R', 0);
			$pdf->Cell(41, 4,  $MyRow->Athlete . ($MyRow->EnSubTeam==0 ? "" : " (" . $MyRow->EnSubTeam . ")"), 1, 0, 'L', 0);
			if(!$PdfData->HideCols && !$TargetFace)
			{
				$pdf->Cell(11, 4,  ($MyRow->AgeClass), 1, 0, 'C', 0);
				$pdf->Cell(8, 4,  ($MyRow->SubClass), 1, 0, 'C', 0);
			}
			$pdf->Cell(12 + ($PdfData->HideCols==true ? 22 : 0), 4,  ($PdfData->HideCols==true ? $MyRow->DivDescription : $MyRow->DivCode), 1, 0, 'C', 0);
			$pdf->Cell(12 + ($PdfData->HideCols==true ? 21 : 0), 4,  ($PdfData->HideCols==true ? $MyRow->ClDescription : $MyRow->ClassCode), 1, 0, 'C', 0);

			if ($TargetFace)
			{
				$pdf->Cell(19, 4, get_text($MyRow->TfName,'Tournament','',true), 1, 0, 'C', 0);
			}

		//Disegna i Pallini per la partecipazione
			if(!$PdfData->HideCols)
			{
				if(!$MyRow->IsAthlete)
					$pdf->DrawParticipantDetails(-1);
				elseif($MyRow->secTeam==0)
					$pdf->DrawParticipantDetails($MyRow->IC, $MyRow->IF, $MyRow->TC, $MyRow->TF, $MyRow->TM);
				elseif($MyRow->secTeam==1)
					$pdf->DrawParticipantDetails($MyRow->IC, $MyRow->IF);
				elseif($MyRow->secTeam==2)
					$pdf->DrawParticipantDetails(0, 0, $MyRow->TC, $MyRow->TF, $MyRow->TM);
				else
					$pdf->Cell(14,4,' ');

				$pdf->SetDefaultColor();
				$pdf->SetFont($pdf->FontStd,'',7);
				$ShowStatusLegend = ($ShowStatusLegend || ($MyRow->Status!=0));
				$pdf->Cell(10, 4,  ($MyRow->Status==0 ? '' : ($MyRow->Status)) , 1, 0, 'C', 0);
			}

			$pdf->Cell(1, 4,  '', 0, 1, 'C', 0);

			if(!isset($PdfData->HTML)) continue;

			$PdfData->HTML['Countries'][$MyRow->NationCode]['Description']=$MyRow->Nation;
			$PdfData->HTML['Countries'][$MyRow->NationCode]['Archers'][]=array(
				$MyRow->Athlete,
				$MyRow->TargetNo,
				$MyRow->DivDescription . ' ' . $MyRow->ClDescription,
				$MyRow->SesName ? $MyRow->SesName : $PdfData->Data['Fields']['Session'].' '. $MyRow->Session,
				);
		}
	}
}

//Legenda per la partecipazione alle varie fasi
if(!$PdfData->HideCols)
{
	$pdf->DrawPartecipantLegend();
//Legenda per lo stato di ammisisone alle gare
	if($ShowStatusLegend)
		$pdf->DrawStatusLegend();
}

?>