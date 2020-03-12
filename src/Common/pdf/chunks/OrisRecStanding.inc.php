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
		$pdf->lastY += 3.5;
		$pdf->SamePage(count($Rows), 3.5, $pdf->lastY);
		$first=true;
		$lstPictures = array();
		$lstDoB = array();
	// 	$pdf->printSectionTitle('As of '.$PdfData->RecordAs);
		foreach($Rows as $RecType => $MyRows) {
            $pdf->SamePage(count($MyRows) + 6, 3.5, $pdf->lastY);
			$pdf->printSectionTitle($PdfData->SubSections[$Team][$RecType].'§', $pdf->GetY()+10);
			$pdf->ln();
			$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
			$pdf->PrintHeader($pdf->GetX(), $pdf->GetY()+1);
			foreach($MyRows as $MyRow) {
				$first=true;
				foreach($MyRow->RtRecExtra as $Record) {
					foreach($Record->Archers as $Archers) {
						if($MyRow->EvTeamEvent and $first) {
							$tmp=array(
								$MyRow->RtRecDistance,
								'§'.$MyRow->RtRecTotal.($MyRow->RtRecXNine ? "/$MyRow->RtRecXNine" : ''),
								$Record->NOC ,
								'§'.$Record->NOC,
								$Record->EventNOC,
								$MyRow->RtRecDate.'#'
								);
							$pdf->printDataRow($tmp);
							$first=false;
						}
						$tmp=array(
							$MyRow->RtRecDistance,
							'§'.$MyRow->RtRecTotal.($MyRow->RtRecXNine ? "/$MyRow->RtRecXNine" : ''),
							$MyRow->EvTeamEvent ? '   '.$Archers['Archer'] : $Archers['Archer'],
							'§'.$Record->NOC,
							$Record->EventNOC,
							$MyRow->RtRecDate.'#'
							);
						if(!$first) {
							$tmp[0]='';
							$tmp[1]='';
							$tmp[4]='';
							$tmp[5]='';
							if($MyRow->EvTeamEvent) $tmp[3]='';
						}
						$first=false;
						$pdf->printDataRow($tmp);
					}
				}

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
?>