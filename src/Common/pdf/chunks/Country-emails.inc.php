<?php

$CellH=8;

$LastCellW=$pdf->getPageWidth()-20-$NatAtlCell*2-$TgtCell;

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
				$pdf->Cell($TgtCell, 4, $PdfData->Data['Fields']['Bib'], 1, 0, 'C', 1);
				$pdf->Cell($NatAtlCell, 4, $PdfData->Data['Fields']['Athlete'], 1, 0, 'L', 1);
				$pdf->Cell($NatAtlCell, 4, $PdfData->Data['Fields']['DOB'], 1, 0, 'L', 1);
				$pdf->Cell($LastCellW, 4, $PdfData->Data['Fields']['Email'], 1, 0, 'L', 1);
				$pdf->ln();
				$OldTeam='';
				$FirstTime=false;
			}

			if($OldTeam != $MyRow->NationCode) {
				$pdf->dy(1);
				$pdf->SetFont($pdf->FontStd,'B', 10);
				$pdf->Cell(0, 4,  $MyRow->NationCode . ' - ' . $MyRow->Nation, '1', 1, 'C');
				$OldTeam = $MyRow->NationCode;
			}

			$pdf->SetFont($pdf->FontStd,'',7);
			$pdf->Cell($TgtCell, $CellH,  $MyRow->Bib, 1, 0, 'R', 0);
			$pdf->Cell($NatAtlCell, $CellH,  $MyRow->Athlete, 1, 0, 'L', 0);
			$pdf->Cell($NatAtlCell, $CellH, $MyRow->EnDob, 1, 0, 'L', 0);
			$pdf->Cell($LastCellW, $CellH,  $MyRow->EdEmail, 1, 0, 'L', 0);

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
