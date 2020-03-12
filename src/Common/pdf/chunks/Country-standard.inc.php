<?php

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
				$pdf->SetDefaultColor();
			   	$pdf->SetFont($pdf->FontStd,'B',7);
				$pdf->Cell($NatAtlCell, 4, $PdfData->Data['Fields']['Nation'], 1, 0, 'L', 1);
				$pdf->Cell($SesCell, 4, $PdfData->Data['Fields']['Session'], 1, 0, 'C', 1);
				$pdf->Cell($TgtCell, 4, $PdfData->Data['Fields']['TargetNo'], 1, 0, 'C', 1);
				$pdf->Cell($TgtCell, 4, $PdfData->Data['Fields']['Bib'], 1, 0, 'C', 1);
				$pdf->Cell($NatAtlCell, 4, $PdfData->Data['Fields']['Athlete'], 1, 0, 'L', 1);
				if(!$PdfData->HideCols && !$TargetFace) {
					$pdf->Cell($TgtCell, 4, $PdfData->Data['Fields']['AgeClass'], 1, 0, 'C', 1);
					$pdf->Cell($TgtCell, 4, $PdfData->Data['Fields']['SubClass'], 1, 0, 'C', 1);
				}
				$pdf->Cell($TgtCell + ($PdfData->HideCols==true ? $TgtCell:0), 4, $PdfData->Data['Fields']['DivDescription'], 1, 0, 'C', 1);
				$pdf->Cell($TgtCell + ($PdfData->HideCols==true ? $TgtCell:0), 4, $PdfData->Data['Fields']['ClDescription'], 1, 0, 'C', 1);

				if ($TargetFace) {
					$pdf->Cell($TgtCell*2, 4, $PdfData->Data['Fields']['TargetFace'], 1, 0, 'C', 1);
				}

				//Disegna i Pallini
				if(!$PdfData->HideCols)
				{
					$pdf->DrawParticipantHeader();
				   	$pdf->SetFont($pdf->FontStd,'B',7);
					$pdf->Cell($TgtCell, 4, $PdfData->Data['Fields']['Status'], 1, 0, 'C', 1);
					$pdf->Cell($TgtCell, 4, $PdfData->Data['Fields']['Photo'], 1, 0, 'C', 1);
				}
				$pdf->ln();
				$OldTeam='';
				$FirstTime=false;
			}
			if($OldTeam != $MyRow->NationCode)
			{
			   	$pdf->SetFont($pdf->FontStd,'B',1);
				$pdf->Cell(0, 1,  '', 0, 1, 'C', 0);
				$pdf->SetFont($pdf->FontStd,'B',7);
				$pdf->Cell($TgtCell, 4,  $MyRow->NationCode, 'LTB', 0, 'C', 0);
				$pdf->Cell($NatAtlCell-$TgtCell, 4,  $MyRow->Nation, 'RTB', 0, 'L', 0);
				$OldTeam = $MyRow->NationCode;
			}
			else
			{
				$pdf->Cell($NatAtlCell, 4,  '', 0, 0, 'C', 0);
			}
		   	$pdf->SetFont($pdf->FontStd,'',7);
			$pdf->Cell($SesCell, 4,  ($MyRow->Session && $MyRow->IsAthlete ? $MyRow->Session : ''), 1, 0, 'R', 0);
			$pdf->Cell($TgtCell, 4,  ($MyRow->IsAthlete && $MyRow->TargetNo ? (!empty($PdfData->BisTarget) && (intval(substr($MyRow->TargetNo,1)) > $PdfData->NumEnd) ? str_pad((substr($MyRow->TargetNo,0,-1)-$PdfData->NumEnd),3,"0",STR_PAD_LEFT) . substr($MyRow->TargetNo,-1,1) . ' bis'  : $MyRow->TargetNo) : ''), 1, 0, 'R', 0);
			$pdf->Cell($TgtCell, 4,  $MyRow->Bib, 1, 0, 'R', 0);
			$pdf->Cell($NatAtlCell, 4,  $MyRow->Athlete . ($MyRow->EnSubTeam==0 ? "" : " (" . $MyRow->EnSubTeam . ")"), 1, 0, 'L', 0);
			if(!$PdfData->HideCols && !$TargetFace)
			{
				$pdf->Cell($TgtCell, 4,  ($MyRow->AgeClass), 1, 0, 'C', 0);
				$pdf->Cell($TgtCell, 4,  ($MyRow->SubClass), 1, 0, 'C', 0);
			}
			$pdf->Cell($TgtCell + ($PdfData->HideCols==true ? $TgtCell:0), 4,  ($PdfData->HideCols==true ? $MyRow->DivDescription : $MyRow->DivCode), 1, 0, 'C', 0);
			$pdf->Cell($TgtCell + ($PdfData->HideCols==true ? $TgtCell:0), 4,  ($PdfData->HideCols==true ? $MyRow->ClDescription : $MyRow->ClassCode), 1, 0, 'C', 0);

			if ($TargetFace)
			{
				$pdf->Cell($TgtCell*2, 4, get_text($MyRow->TfName,'Tournament','',true), 1, 0, 'C', 0);
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
				$pdf->Cell($TgtCell, 4,  ($MyRow->Status==0 ? '' : ($MyRow->Status)) , 1, 0, 'C', 0);
				$pdf->rect($x=$pdf->getx()+1, $y=$pdf->gety()+1, 2, 2, 'DF', array('LTRB'), array($MyRow->HasPhoto ? 0 : 255));
				$pdf->Cell($TgtCell, 4,  '' , 1, 0, 'C', 0);
			}

			$pdf->ln();

			if(!isset($PdfData->HTML)) continue;

			$PdfData->HTML['Countries'][$MyRow->NationCode]['Description']=$MyRow->Nation;
			$PdfData->HTML['Countries'][$MyRow->NationCode]['Archers'][]=array(
				$MyRow->Athlete,
				(!empty($PdfData->BisTarget) && (intval(substr($MyRow->TargetNo,1)) > $PdfData->NumEnd) ? 'bis ' . (substr($MyRow->TargetNo,0,-1)-$PdfData->NumEnd) . substr($MyRow->TargetNo,-1,1)  : $MyRow->TargetNo),
				$MyRow->DivDescription . ' ' . $MyRow->ClDescription,
				$MyRow->SesName ? $MyRow->SesName : $PdfData->Data['Fields']['Session'].' '. $MyRow->Session,
				);
		}
	}
