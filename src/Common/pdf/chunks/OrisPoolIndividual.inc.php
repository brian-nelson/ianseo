<?php

$Indices=array('bis','ter','quat', 'quin', 'sex', 'sept', 'oct');

$PdfData->HeaderPool[6]='';
$PdfData->HeaderPool[5]='';

$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);

$OldEvent='#@#@#';
$targetNo=-1;

$OldMatchPhase=-1;

$First=true;
foreach($PdfData->Data['Items'] as $EvCode => $MyRows) {
	if(!$MyRows) {
		continue;
	}

	$start=current($MyRows);
	$pdf->SetDataHeader($PdfData->HeaderPool, $PdfData->HeaderWidthPool);

	// needed for the online filename!
	$Descr=$start->EventName;

	if($start->EvElimType==4) {
		// need to restructure the whole thing
		if((!is_null($start->EventCode) && $OldEvent != $start->EventCode) || (is_null($start->EventCode) && $OldEvent == '#@#@#')) {
			$pdf->setEvent($start->EventName ? $start->EventName : $start->EvOdfCode);
			$pdf->setPhase("Elimination Round");

			$pdf->setOrisCode('C51A', 'Start List by Target');
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
		}

		$OldTitle='';
		$Group=0;
		$PoolPage=false;
		$Structure=array(
			'rows' => 0,
			'group' => array(1 => false, 2 => false, 3 => false, 4 => false),
			'data' => array()
		);

		foreach($MyRows as $MyRow) {
			if($OldMatchPhase!=$MyRow->GrPhase) {
				if($OldMatchPhase<$MyRow->GrPhase) {
					$Group++;
					if ($Group == 3 and !$PoolPage) {
						$pdf->addPage();
						$PoolPage = true;
					}
					$PrintSubTitle = true;
					$targetNo = -1;
				}
				$OldMatchPhase=$MyRow->GrPhase;
			}

			if(empty($Structure['group'][$Group])) {
				$Structure['group'][$Group]=false;
			}

			switch($MyRow->FinMatchNo) {
				case 12:
				case 13:
					$tit=$PdfData->MatchTitleAB;
					break;
				case 10:
				case 11:
					$tit=$PdfData->MatchTitleCD;
					break;
				default:
					$tit=$PdfData->MatchTitleGroups[$Group];
			}
			if(empty($Structure['data'][$tit][$PdfData->MatchTitlesWA[intval($MyRow->FinMatchNo/2)*2]])) {
				$Structure['data'][$tit][$PdfData->MatchTitlesWA[intval($MyRow->FinMatchNo/2)*2]]=array();
			}

			$NumTarget= intval($MyRow->TargetNo);

			$athlete=$MyRow->FirstName . ' ' . $MyRow->Name;
			if(!trim($athlete)) {
				if($MyRow->EvElimType==3 and !empty($PdfData->MatchSlots[$MyRow->FinMatchNo])) {
					$athlete=$PdfData->MatchSlots[$MyRow->FinMatchNo];
				} elseif($MyRow->EvElimType==4 and !empty($PdfData->MatchSlotsWA[$MyRow->FinMatchNo])) {
					$athlete=$PdfData->MatchSlotsWA[$MyRow->FinMatchNo];
				}
			}
			if($targetNo != substr($MyRow->TargetNo,0,-1)) {
				$TargetToPrint=$MyRow->TargetNo;
				//$pdf->lastY += 3.5;
				$arc=array(
					trim($TargetToPrint,'0') . "  #",
					$athlete,
					$MyRow->NationCode,
					$MyRow->Nation);
				if($MyRow->EvElimType==3 or $MyRow->EvElimType==4) {
					$arc[]=$MyRow->Score.(isset($MyRow->TiebreakDecoded) ? ' T.'.$MyRow->TiebreakDecoded.($MyRow->Closest ? '+' : '') : '');
				}
				//$arc[]=$MyRow->Ranking."#";
				//$arc[]=$MyRow->DOB;
				//$pdf->printDataRow($arc);
				$targetNo = substr($MyRow->TargetNo,0,-1);
			} else {
				$arc=array(
					substr($MyRow->TargetNo,-1,1) . "  #",
					$athlete,
					$MyRow->NationCode,
					$MyRow->Nation);
				if($MyRow->EvElimType==3 or $MyRow->EvElimType==4) {
					$arc[]=$MyRow->Score.(isset($MyRow->TiebreakDecoded) ? ' T.'.$MyRow->TiebreakDecoded.($MyRow->Closest ? '+' : '') : '');
				}
				//$arc[]=$MyRow->Ranking."#";
				//$arc[]=$MyRow->DOB;
				//$pdf->printDataRow($arc);
			}

			$Structure['rows']++;
			$Structure['data'][$tit][$PdfData->MatchTitlesWA[intval($MyRow->FinMatchNo/2)*2]][]=$arc;

			// removes the match if no opponents
			if(count($Structure['data'][$tit][$PdfData->MatchTitlesWA[intval($MyRow->FinMatchNo/2)*2]])==2) {
				$targetNo=-1;
				if(!$Structure['group'][$Group]) {
					if($Structure['data'][$tit][$PdfData->MatchTitlesWA[intval($MyRow->FinMatchNo/2)*2]][0][2] or $Structure['data'][$tit][$PdfData->MatchTitlesWA[intval($MyRow->FinMatchNo/2)*2]][1][2]) {
						$Structure['group'][$Group]=true;
					} else {
						unset($Structure['data'][$tit][$PdfData->MatchTitlesWA[intval($MyRow->FinMatchNo/2)*2]]);
						$Structure['rows']-=2;
						if(empty($Structure['data'][$tit])) {
							unset($Structure['data'][$tit]);
						}
					}
				}
			}
		}

		foreach($Structure['data'] as $Title => $Groups) {
			$OldFont=$pdf->getFontSizePt();
			$pdf->SetFont('', 'b', $OldFont+3);
			$pdf->SetY($pdf->lastY+3.5);
			$pdf->Cell(0, 5, $Title, '',1);
			$pdf->lastY+=7;
			$pdf->SetFont('', '', $OldFont);

			foreach($Groups as $MatchTitle => $Matches) {
				$pdf->lastY+=3.5;
				$pdf->SetY($pdf->lastY);
				$pdf->cell(0, 0, $MatchTitle);

				$pdf->SetFont('', '');
				$targetNo = -1;
				$OldMatchPhase=$MyRow->GrPhase;

				$pdf->lastY += 3.5;
				$pdf->printDataRow($Matches[0]);
				$pdf->printDataRow($Matches[1]);

				if(isset($PdfData->HTML)) {
					$athlete=$MyRow->FirstName . ' ' . $MyRow->Name;
					$titlePools=$MyRow->SesName;
					if($MyRow->EvElimType==3) {
						$titlePools=$PdfData->MatchTitles[intval($MyRow->FinMatchNo/2)*2];
						if(!trim($athlete)) {
							$athlete=$PdfData->MatchSlots[$MyRow->FinMatchNo];
						}
					} elseif($MyRow->EvElimType==4) {
						$titlePools=$PdfData->MatchTitlesWA[intval($MyRow->FinMatchNo/2)*2];
						if(!trim($athlete)) {
							$athlete=$PdfData->MatchSlotsWA[$MyRow->FinMatchNo];
						}
					}
					$PdfData->HTML['sessions'][$Title]['Event']=$MyRow->EventName;
					$PdfData->HTML['sessions'][$Title]['Description']=$MyRow->EventName . ' ' . $Title;
					$PdfData->HTML['sessions'][$Title]['Targets'][$MatchTitle][]=$Matches[0];
					$PdfData->HTML['sessions'][$Title]['Targets'][$MatchTitle][]=$Matches[1];
					//$PdfData->HTML['sessions'][$Title]['Targets'][$TargetToPrint][]=array(
					//	ltrim(substr($TargetToPrint,0,-1),'0').substr($MyRow->TargetNo,-1),
					//	$athlete,
					//	$MyRow->NationCode,
					//	$MyRow->Nation,
					//	$MyRow->EventName,
					//);
				}

			}
			if($Structure['rows']>30 and $Title==$PdfData->MatchTitleAB) {
				$pdf->AddPage();
			}
		}
	} else {
		$OldTitle='';
		$Group=0;
		$OldSession=-1;
		$PoolPage=false;

		foreach($MyRows as $MyRow) {
			$NumEnd=($MyRow->Session == 0 ? 12 : 8);
			if((!is_null($MyRow->EventCode) AND $OldEvent != $MyRow->EventCode) || (is_null($MyRow->EventCode) AND $OldEvent!='#@#@#') OR ($MyRow->EvElimType<=2 AND $OldSession!=$MyRow->Session)) {
				$pdf->setEvent($MyRow->EventName);
				if($MyRow->Session == 0)
					$pdf->setPhase("Elimination Round 1");
				else if($MyRow->Session == 1 AND ($MyRow->EvElim1 !=0 AND $MyRow->EvElim2 !=0))
					$pdf->setPhase("Elimination Round 2");
				else
					$pdf->setPhase("Elimination Round");

				if($MyRow->EvElimType<=2 and !is_null($MyRow->SesName)) {
					$pdf->setComment($MyRow->SesName);
				}

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

			if($OldMatchPhase!=$MyRow->GrPhase or ($MyRow->EvElimType==3 and $OldTitle!=$PdfData->MatchTitles[intval($MyRow->FinMatchNo/2)*2])) {
				if($MyRow->EvElimType==3) {
					// print the title of the match
					$pdf->SetFont('', 'b');
					$pdf->lastY+=3.5;
					$pdf->SetY($pdf->lastY);
					$pdf->cell(0, 0, $PdfData->MatchTitles[intval($MyRow->FinMatchNo/2)*2]);

					$OldTitle=$PdfData->MatchTitles[intval($MyRow->FinMatchNo/2)*2];
					$pdf->SetFont('', '');
					$targetNo = -1;
					$OldMatchPhase=$MyRow->GrPhase;
				}
			}

			$NumTarget= intval($MyRow->TargetNo);
			if($NumTarget>$NumEnd and $MyRow->EvElimType<=2) {
				$NumTarget = (($NumTarget-1) % ($NumEnd)) + 1;
			}

			$athlete=$MyRow->FirstName . ' ' . $MyRow->Name;
			if(!trim($athlete)) {
				if($MyRow->EvElimType==3 and !empty($PdfData->MatchSlots[$MyRow->FinMatchNo])) {
					$athlete=$PdfData->MatchSlots[$MyRow->FinMatchNo];
				}
			}
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
				if($MyRow->EvElimType==3) {
					$arc[]=$MyRow->Score.(isset($MyRow->TiebreakDecoded) ? ' T.'.$MyRow->TiebreakDecoded.($MyRow->Closest ? '+' : '') : '');
				}
				//$arc[]=$MyRow->Ranking."#";
				//$arc[]=$MyRow->DOB;
				$pdf->printDataRow($arc);
				$targetNo = substr($MyRow->TargetNo,0,-1);
			} else {
				$arc=array(
					substr($MyRow->TargetNo,-1,1) . "  #",
					$athlete,
					$MyRow->NationCode,
					$MyRow->Nation);
				if($MyRow->EvElimType==3) {
					$arc[]=$MyRow->Score.(isset($MyRow->TiebreakDecoded) ? ' T.'.$MyRow->TiebreakDecoded.($MyRow->Closest ? '+' : '') : '');
				}
				//$arc[]=$MyRow->Ranking."#";
				//$arc[]=$MyRow->DOB;
				$pdf->printDataRow($arc);
			}

			if(!isset($PdfData->HTML)) continue;

			$athlete=$MyRow->FirstName . ' ' . $MyRow->Name;
			$titlePools=$MyRow->SesName;
			if($MyRow->EvElimType==3) {
				$titlePools=$PdfData->MatchTitles[intval($MyRow->FinMatchNo/2)*2];
				if(!trim($athlete)) {
					$athlete=$PdfData->MatchSlots[$MyRow->FinMatchNo];
				}
			}
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
}
