<?php

$Indices=array('bis','ter','quat', 'quin', 'sex', 'sept', 'oct');

$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);

$OldEvent='#@#@#';
$targetNo=-1;

$OldMatchPhase=-1;

$First=true;
foreach($PdfData->Data['Items'] as $EvCode => $MyRows) {
	if(!$MyRows) {
		continue;
	}

	$check=current($MyRows);
	$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);

	$OldTitle='';
	$Group=0;
	$OldSession=-1;
	$PoolPage=false;

	foreach($MyRows as $MyRow) {
		$NumEnd=($MyRow->Session == 0 ? 12 : 8);
        if((!is_null($MyRow->EventCode) AND $OldEvent != $MyRow->EventCode) || (is_null($MyRow->EventCode) AND $OldEvent!='#@#@#') OR $OldSession!=$MyRow->Session) {
			$pdf->setEvent($MyRow->EventName);
			if($MyRow->Session == 0)
				$pdf->setPhase("Elimination Round 1");
			else if($MyRow->Session == 1 AND ($MyRow->EvElim1 !=0 AND $MyRow->EvElim2 !=0))
				$pdf->setPhase("Elimination Round 2");
			else
				$pdf->setPhase("Elimination Round");

			$pdf->setComment($MyRow->SesName);

			$pdf->setOrisCode('C51A', 'Start List by Target');
			$pdf->AddPage();
			if($First and (empty($pdf->CompleteBookTitle) or $pdf->CompleteBookTitle!=$PdfData->IndexName)) {
				$pdf->Bookmark($PdfData->IndexName, 0);
				$pdf->CompleteBookTitle=$PdfData->IndexName;
			}
			$First=false;
			$pdf->Bookmark($MyRow->EventName, 1);

			$targetNo = -1;
			$OldEvent = $MyRow->EventCode;
            $OldSession = $MyRow->Session;
			$OldMatchPhase=-1;
		}

		$NumTarget= intval($MyRow->TargetNo);
		if($NumTarget>$NumEnd and !$_SESSION['MenuElimPoolDo']) {
			$NumTarget = (($NumTarget-1) % ($NumEnd)) + 1;
		}

		$athlete=$MyRow->FirstName . ' ' . $MyRow->Name;
		if($targetNo != substr($MyRow->TargetNo,0,-1)) {
			$TargetToPrint=$MyRow->TargetNo;
			if($MyRow->TargetNo and $NumTarget!=intval($MyRow->TargetNo)) {
				$TargetToPrint = $NumTarget . $Indices[ceil(intval($MyRow->TargetNo)/($NumEnd))-2] . '-' . substr($MyRow->TargetNo,-1,1);
			}
			$pdf->lastY += 3.5;
			$arc=array(
				trim($TargetToPrint,'0') . "  #",
				$athlete,
				$MyRow->NationCode,
				$MyRow->Nation);
			$arc[]=$MyRow->Ranking."#";
			$arc[]=$MyRow->DOB;
			$pdf->printDataRow($arc);
			$targetNo = substr($MyRow->TargetNo,0,-1);
		} else {
			$arc=array(
				substr($MyRow->TargetNo,-1,1) . "  #",
				$athlete,
				$MyRow->NationCode,
				$MyRow->Nation);
			$arc[]=$MyRow->Ranking."#";
			$arc[]=$MyRow->DOB;
			$pdf->printDataRow($arc);
		}

		if(!isset($PdfData->HTML)) continue;

		$athlete=$MyRow->FirstName . ' ' . $MyRow->Name;
		$titlePools=$MyRow->SesName;
		$PdfData->HTML['sessions'][$MyRow->EventName]['Event']=$MyRow->EventName;
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
