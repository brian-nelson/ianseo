<?php


// starts with Qualification Round
if (isset($PdfData->Data['QR']) && count($PdfData->Data['QR']['Data'])>0) {
	$DivSize=($pdf->getPageWidth()-35)/count($PdfData->Data['QR']['Div']);

	$FirstTime=true;

	foreach($PdfData->Data['QR']['Cls'] as $cl) {

		if ($FirstTime || !$pdf->SamePage(16)) {
			$TmpSegue = !$pdf->SamePage(16);
			if($TmpSegue) {
				$pdf->AddPage();
			}
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->SetXY(25,$pdf->GetY()+5);
			$pdf->Cell(($pdf->getPageWidth()-35), 6,  $PdfData->Data['QR']['Title'], 1, 1, 'C', 1);
			if($TmpSegue) {
				$pdf->SetXY(($pdf->getPageWidth()-40),$pdf->GetY()-6);
				$pdf->SetFont($pdf->FontStd,'I',6);
				$pdf->Cell(30, 6,  $PdfData->Continue, 0, 1, 'R', 0);
			}
			$pdf->SetX(25);
			$pdf->SetFont($pdf->FontStd,'B',10);
			foreach($PdfData->Data['QR']['Div'] as $Value) {
				$pdf->Cell($DivSize-0.5, 6,  $Value, 1, 0, 'C', 1);
				$pdf->Cell(0.5, 6, '' , 1, 0, 'C', 1);
			}
			$pdf->Cell(0.1, 6,  '', 0, 1, 'C', 0);
			$pdf->SetX(25);
			$pdf->SetFont($pdf->FontStd,'',7);
			$SubDivCell=($DivSize/count($PdfData->Data['QR']['SubTitle']))-0.2;
			$SubDivSep=count($PdfData->Data['QR']['SubTitle'])*0.2;
			foreach($PdfData->Data['QR']['Div'] as $Value) {
				foreach($PdfData->Data['QR']['SubTitle'] as $k => $v) {
					$pdf->Cell($SubDivCell, 6, $v, 1, 0, 'C', 1);
				}
				$pdf->Cell($SubDivSep, 6, '' , 1, 0, 'C', 1);
			}
			$pdf->ln();

			$FirstTime=false;
		}
		$pdf->SetFont($pdf->FontStd,'',8);
		$pdf->Cell(15, 5, $cl, 1, 0, 'C', 1);
		foreach($PdfData->Data['QR']['Div'] as $div) {
			foreach($PdfData->Data['QR']['SubTitle'] as $k => $v) {
				$pdf->Cell($SubDivCell, 5, empty($PdfData->Data['QR']['Data'][$div][$cl][$k]) ? '':$PdfData->Data['QR']['Data'][$div][$cl][$k], 1, 0, 'C', 0);
			}
			$pdf->Cell($SubDivSep, 5, '' , 1, 0, 'C', 1);
		}
		$pdf->ln();
	}
}

// do the Individual Finals
if (isset($PdfData->Data['IF']) && count($PdfData->Data['IF']['Data'])>0) {
	$FirstTime=true;
	$DivSize=($pdf->getPageWidth()-35)/6;
	foreach($PdfData->Data['IF']['Data'] as $EvCode => $EventData) {
		if ($FirstTime || !$pdf->SamePage(16)) {
			$TmpSegue = !$pdf->SamePage(16);
			if($TmpSegue) {
				$pdf->AddPage();
			} else {
				$pdf->SetXY(25,$pdf->GetY()+5);
			}
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell(0, 6, $PdfData->Data['IF']['Title'], 1, 1, 'C', 1);
			if($TmpSegue) {
				$pdf->SetXY(($pdf->getPageWidth()-40),$pdf->GetY()-6);
				$pdf->SetFont($pdf->FontStd,'I',6);
				$pdf->Cell(30, 6,  $PdfData->Continue, 0, 1, 'R', 0);
			}
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->SetX(25);
			$pdf->Cell($DivSize*2, 6, $PdfData->Data['IF']['SubTitle'][0], 1, 0, 'C', 1);
			$pdf->Cell($DivSize, 6, $PdfData->Data['IF']['SubTitle'][1], 1, 0, 'C', 1);
			$pdf->Cell($DivSize, 6, $PdfData->Data['IF']['SubTitle'][2], 1, 0, 'C', 1);
			$pdf->Cell($DivSize, 6, $PdfData->Data['IF']['SubTitle'][3], 1, 0, 'C', 1);
			$pdf->Cell($DivSize, 6, $PdfData->Data['IF']['SubTitle'][4], 1, 1, 'C', 1);
			$FirstTime=false;
		}
		$pdf->SetFont($pdf->FontStd,'',8);
		$pdf->Cell(15, 5, $EvCode, 1, 0, 'C', 1);
		$pdf->Cell($DivSize*2, 5, $EventData['Name'], 1, 0, 'L', 0);
		$pdf->Cell($DivSize, 5, $EventData['Number'], 1, 0, 'C', 0);
		$pdf->Cell($DivSize, 5, $EventData['Phase'], 1, 0, 'C', $EventData['Invalid']);
		$pdf->Cell($DivSize/2, 5, $EventData['Matches'], 'TBL', 0, 'R', $EventData['Invalid']);
		$pdf->Cell($DivSize/2, 5, $EventData['Byes'], 'TBR', 0, 'R', $EventData['Invalid']);
		$pdf->Cell($DivSize/2, 5, $EventData['ArchersIn'], 'TBL', 0, 'R', $EventData['Invalid']);
		$pdf->Cell($DivSize/2, 5, $EventData['ArchersOut'], 'TBR', 1, 'R', $EventData['Invalid']);
	}
}

// Do the Team Finals
if (isset($PdfData->Data['TF']) && count($PdfData->Data['TF']['Data'])>0) {
	$FirstTime=true;
	$DivSize=($pdf->getPageWidth()-35)/6;
	foreach($PdfData->Data['TF']['Data'] as $EvCode => $Items) {
		if ($FirstTime || !$pdf->SamePage(16)) {
			$TmpSegue = !$pdf->SamePage(16);
			if($TmpSegue) {
				$pdf->AddPage();
			}
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->SetXY(25,$pdf->GetY()+5);
			$pdf->Cell($DivSize*6, 6, $PdfData->Data['TF']['Title'], 1, 1, 'C', 1);
			if($TmpSegue) {
				$pdf->SetXY(($pdf->getPageWidth()-40),$pdf->GetY()-6);
				$pdf->SetFont($pdf->FontStd,'I',6);
				$pdf->Cell(30, 6, $PdfData->Continue, 0, 1, 'R', 0);
			}
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->SetX(25);
			$pdf->Cell($DivSize*4/3, 6, $PdfData->Data['TF']['SubTitle'][0], 1, 0, 'C', 1);
			$pdf->Cell($DivSize*2/3, 6, $PdfData->Data['TF']['SubTitle'][1], 1, 0, 'C', 1);
			$pdf->Cell($DivSize, 6, $PdfData->Data['TF']['SubTitle'][2], 1, 0, 'C', 1);
			$pdf->Cell($DivSize, 6, $PdfData->Data['TF']['SubTitle'][3], 1, 0, 'C', 1);
			$pdf->Cell($DivSize, 6, $PdfData->Data['TF']['SubTitle'][4], 1, 0, 'C', 1);
			$pdf->Cell($DivSize, 6, $PdfData->Data['TF']['SubTitle'][5], 1, 1, 'C', 1);
			$FirstTime=false;
		}
		$pdf->SetFont($pdf->FontStd,'',8);
		$pdf->Cell(15, 5, $EvCode, 1, 0, 'C', 1);
		$pdf->Cell($DivSize*4/3, 5, $Items['Name'], 1, 0, 'L', 0);
		$pdf->Cell($DivSize*2/3, 5, $Items['MixedTeam'], 1, 0, 'C', 0);
		$pdf->Cell($DivSize, 5, $Items['Number'], 1, 0, 'C', 0);
		$pdf->Cell($DivSize, 5, $Items['FirstPhase'], 1, 0, 'C', $Items['Invalid']);
		$pdf->Cell($DivSize/2, 5, $Items['Matches'], 'TBL', 0, 'R', $Items['Invalid']);
		$pdf->Cell($DivSize/2, 5, $Items['Byes'], 'TBR', 0, 'R', $Items['Invalid']);
		$pdf->Cell($DivSize/2, 5, $Items['ArchersIn'], 'TBL', 0, 'R', $Items['Invalid']);
		$pdf->Cell($DivSize/2, 5, $Items['ArchersOut'], 'TBR', 1, 'R', $Items['Invalid']);
	}
}
