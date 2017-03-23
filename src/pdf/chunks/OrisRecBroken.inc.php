<?php

$OldStop=$pdf->StopHeader;
$pdf->StopHeader=true;
$pdf->setPhase('As of '.$PdfData->RecordAs);

$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
$Version='';
if($PdfData->DocVersion) {
	$Version=trim('Vers. '.$PdfData->DocVersion . " ($PdfData->DocVersionDate) $PdfData->DocVersionNotes");
}
$pdf->setComment($Version);
$pdf->AddPage();
$pdf->Bookmark($PdfData->IndexName, 0);

$ONLINE=isset($PdfData->HTML);

$AddPage=false;

if(empty($PdfData->Data['Items'])) {
	$pdf->printSectionTitle('No data§', $pdf->GetY()+10);
} else {
	foreach($PdfData->Data['Items'] as $Team => $Rows) {
		if($AddPage) $pdf->addpage();
		$AddPage=true;
		$pdf->SamePage(count($Rows) + 2);
		$pdf->lastY += 3.5;
		$first=true;
		$lstPictures = array();
		$lstDoB = array();
	// 	$pdf->printSectionTitle('As of '.$PdfData->RecordAs);
		foreach($Rows as $RecType => $MyRows) {
			$pdf->printSectionTitle($PdfData->SubSections[$Team][$RecType].'§', $pdf->lastY+10);
			$pdf->ln();
			$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
			$pdf->PrintHeader($pdf->GetX(), $pdf->GetY()+1);
			foreach($MyRows as $MyRow) {
// 				debug_svela($MyRow);
				$tmp=array(
					$MyRow->RtRecDistance,
					'§'.$MyRow->RtRecTotal.($MyRow->RtRecXNine ? "/$MyRow->RtRecXNine" : '') . ' / ' . $MyRow->NewRecord.($MyRow->RtRecXNine ? "/$MyRow->NewXNine" : ''),
					$MyRow->Athlete,
					'§'.$MyRow->CoCode,
					$MyRow->RecordDate.'#'
					);
				$pdf->printDataRow($tmp);

		// 		$PdfData->HTML['Countries'][$MyRow->NationCode]['Description']=$MyRow->Nation;
		// 		$PdfData->HTML['Countries'][$MyRow->NationCode]['Archers'][]=array(
		// 			$MyRow->Athlete,
		// 			(!empty($PdfData->BisTarget) && (intval(substr($MyRow->TargetNo,1)) > $PdfData->NumEnd) ? 'bis ' . (substr($MyRow->TargetNo,0,-1)-$PdfData->NumEnd) . substr($MyRow->TargetNo,-1,1)  : $MyRow->TargetNo),
		// 			$MyRow->EvCode ? $MyRow->EventName : ($MyRow->IsAthlete ? $MyRow->DivDescription . ' ' : '') . $MyRow->ClDescription,
		// 			$MyRow->SesName,
		// 			);

			}
		}
	}
}

$pdf->StopHeader=$OldStop;
