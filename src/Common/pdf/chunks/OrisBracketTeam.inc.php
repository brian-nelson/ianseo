<?php

$ShowTargetNo = (isset($PdfData->ShowTargetNo) ? $PdfData->ShowTargetNo : true);
$ShowSchedule = (isset($PdfData->ShowSchedule) ? $PdfData->ShowSchedule : true);
$ShowSetArrows= (isset($PdfData->ShowSetArrows) ? $PdfData->ShowSetArrows : true);

$arrayTitles = array("1/12\nElimin. Round§","1/8\nElimin. Round§", "1/4\nElimin. Round§", "Semifinals§", "Finals§");
$titArray = array("R. Round\nRank/Score§",'',"NOC","Name");
$misArray = array(array(7.5, 8.5), 9, 9,26);


$FreePageWidth=$pdf->getPageWidth()-20-array_reduce($misArray, function($a,$b) {return $a + (is_array($b) ? array_sum($b) : $b);});

$pdf->NotAwarded = $PdfData->rankData['meta']['notAwarded'];
$pdf->FinalRank  = $PdfData->rankData['meta']['fields']['finRank'];


// Variabile per gestire il cambio di Evento
$PhaseCounter=-1;
$MyEventPhase = -1;

$Finalists=array();
$MaxFinalists=array();
$CountPhases=array();
foreach($PdfData->rankData['sections'] as $Event => $section) {
	// Ho un nuovo Evento
	// preparation of the pages
	$Finalists[$Event]=array();
	$MaxFinalists[$Event]=0;
	if(empty($PdfData->Events[$Event])) {
		$PdfData->Events[$Event] = new stdClass();
	}
	$PdfData->Events[$Event]->Event = $section['meta']['eventName'];
	$PdfData->Events[$Event]->FirstPhase = $section['meta']['firstPhase'];
	$PdfData->Events[$Event]->PrintHead = $section['meta']['printHead'];
	$PdfData->Events[$Event]->Medals = array("Gold"=>'-', "Silver"=>'-',"Bronze"=>'-');
    $arrayTitles[0]="1/".$section['meta']['firstPhase']."\nElimin. Round§";
	$PdfData->Events[$Event]->Header = array_merge($titArray, array_slice($arrayTitles,(4-ceil(log($section['meta']['firstPhase'],2)))));
	$CountPhases[$Event]=count($PdfData->Events[$Event]->Header)-count($misArray);
	$PdfData->Events[$Event]->HeaderWidth = array_merge($misArray, array_fill(0, $CountPhases[$Event]+1,$FreePageWidth/($CountPhases[$Event]+1)));
	$PdfData->Events[$Event]->NumComponenti = ($section['meta']['firstPhase'] <= 8 ? $section['meta']['maxTeamPerson'] : 1);
	$PdfData->Events[$Event]->Records = (empty($section['records']) ? array() : $section['records']);

	$FirstPhase=true;
	foreach($section['phases'] as $Phase => $Items) {
		if($Phase) $PhaseCounter++;

		foreach($Items['items'] as $Match) {
			$Comp1=array();
			$Comp2=array();
			$Obj1=new StdClass();
			$Obj1->PhaseCounter = $PhaseCounter;
			$Obj1->TfMatchNo = $Match['matchNo'];
			$Obj1->Country = $Match['countryCode'];
			$Obj1->Team = $Match['countryName'];
			$Obj1->TeRank = $Match['qualRank'];
			$Obj1->FinRank = $Match['finRank'];
			$Obj1->ShowRank = $Match['showRank'];
			$Obj1->IrmText = $Match['finIrmText'];
			$Obj1->TeScore = $Match['qualScore']; //
			$Obj1->TeNotes = $Match['qualNotes']; //
			$Obj1->GrPosition = $Match['position'];
			$Obj1->Score = ($section['meta']['matchMode'] ? $Match['setScore'] : $Match['score']);
			$Obj1->ScoreDetails = '';
			$Obj1->ScoreMatch = $Match['score'];
			$Obj1->ScoreCell = 6;
			//DOC $Obj1->Score = ($Match['status']==1 ? 'DSQ-':'') . ($section['meta']['matchMode'] ? $Match['setScore'] : $Match['score']);
			$Obj1->TfTie = $Match['tie'];
			$Obj1->TfTieBreak = $Match['tiebreak'];
			$Obj1->TfTieBreakDecoded = $Match['tiebreakDecoded'];
			$Obj1->Bold = $Match['winner'] and !$Match['irm'];
			$Obj1->SetPoints = $Match['setPoints'];
			$Obj1->OppScore = ($section['meta']['matchMode'] ? $Match['oppSetScore'] : $Match['oppScore']);//$Match['oppScore'];
			$Obj1->OppTie = $Match['oppTie'];
			$Obj1->FSTarget = ($ShowTargetNo ? $Match['target'] : '');
			$Obj1->ScheduledDate = ($ShowSchedule ? $Match['scheduledDate'] : '');
			$Obj1->ScheduledTime = ($ShowSchedule ? $Match['scheduledTime'] : '');
            $Obj1->Saved = ($Match['oppPosition'] and $Match['oppPosition']<=$section['meta']['numSaved']) ? $PdfData->rankData['meta']['saved'] : '';
			$Obj1->Componenti = $Comp1;

			$Obj2=new StdClass();
			$Obj2->PhaseCounter = $PhaseCounter;
			$Obj2->TfMatchNo = $Match['oppMatchNo'];
			$Obj2->Country = $Match['oppCountryCode'];
			$Obj2->Team = $Match['oppCountryName'];
			$Obj2->TeRank = $Match['oppQualRank'];
			$Obj2->FinRank = $Match['oppFinRank'];
			$Obj2->ShowRank = $Match['oppShowRank'];
			$Obj2->IrmText = $Match['oppFinIrmText'];
			$Obj2->TeScore = $Match['oppQualScore']; //
			$Obj2->TeNotes = $Match['oppQualNotes']; //
			$Obj2->GrPosition = $Match['oppPosition'];
			$Obj2->Score = ($section['meta']['matchMode'] ? $Match['oppSetScore'] : $Match['oppScore']);
			$Obj2->ScoreDetails = '';
			$Obj2->ScoreMatch = $Match['oppScore'];
			$Obj2->ScoreCell = 6;
			//DOC $Obj2->Score = ($Match['oppStatus']==1 ? 'DSQ-':'') . ($section['meta']['matchMode'] ? $Match['oppSetScore'] : $Match['oppScore']);
			$Obj2->TfTie = $Match['oppTie'];
			$Obj2->TfTieBreak = $Match['oppTiebreak'];
			$Obj2->TfTieBreakDecoded = $Match['oppTiebreakDecoded'];
			$Obj2->Bold = $Match['oppWinner'] and !$Match['oppIrm'];
			$Obj2->SetPoints = $Match['oppSetPoints'];
			$Obj2->OppScore = ($section['meta']['matchMode'] ? $Match['setScore'] : $Match['score']);//$Match['score'];
			$Obj2->OppTie = $Match['tie'];
			$Obj2->FSTarget = ($ShowTargetNo ? $Match['oppTarget'] : '');
			$Obj2->ScheduledDate = ($ShowSchedule ? $Match['scheduledDate'] : '');
			$Obj2->ScheduledTime = ($ShowSchedule ? $Match['scheduledTime'] : '');
            $Obj2->Saved = ($Match['position'] and $Match['position']<=$section['meta']['numSaved']) ? $PdfData->rankData['meta']['saved'] : '';
			$Obj2->Componenti = $Comp2;

			// what exactly has to print as "score" for side A...
			// what exactly has to print as "score" for side A...
			$Obj1->strike=($Match['irm']==20);
			$Obj2->strike=($Match['oppIrm']==20);
			$RealScore1=$Obj1->Score;
			$RealScore2=$Obj2->Score;
			$tmpSetPoint1 = "";
			$tmpSetPoint2 = "";

			// setpoints
			if($section['meta']['matchMode']) {
				$Obj1->ScoreCell = 3.5;
				$Obj2->ScoreCell = 3.5;
				$numSetShot=intval(($RealScore1+$RealScore2)/2);
				$cntSetPoint=0;
				$Obj1Sets=array();
				$Obj2Sets=array();

				if(!empty($Obj1->SetPoints)) {
					$Obj1Sets=explode("|",$Obj1->SetPoints);
				}
				if(!empty($Obj2->SetPoints)) {
					$Obj2Sets=explode("|",$Obj2->SetPoints);
				}
				for($n=0;$n<$numSetShot;$n++) {
					$pt1=isset($Obj1Sets[$n]) ? $Obj1Sets[$n] : '0';
					$pt2=isset($Obj2Sets[$n]) ? $Obj2Sets[$n] : '0';
					if($pt1!=='') {
						$tmpSetPoint1.="$pt1,";
						$tmpSetPoint2.="$pt2,";
					}
				}
				if($tmpSetPoint1) {
					$tmpSetPoint1=substr($tmpSetPoint1, 0, -1);
					$tmpSetPoint2=substr($tmpSetPoint2, 0, -1);
				}
			}

			// manage the tiebreaks
			if($TieArrows = max(strlen(trim($Obj1->TfTieBreak)), strlen(trim($Obj2->TfTieBreak)))) {
				if($tmpSetPoint1) {
					$tmpSetPoint1.='-';
					$tmpSetPoint2.='-';
				}
				$tmpSetPoint1.='T';
				$tmpSetPoint2.='T';

				if(strlen(trim($Obj1->TfTieBreak)) > 0) {
					$tmpSetPoint1.=str_replace('*','+',$Obj1->TfTieBreakDecoded);
				} elseif($Obj1->TfTie==1) {
					$tmpSetPoint1.=" +";
				}

				if(strlen(trim($Obj2->TfTieBreak)) > 0) {
					$tmpSetPoint2.=str_replace('*','+',$Obj2->TfTieBreakDecoded);
				} elseif($Obj2->TfTie==1) {
					$tmpSetPoint2.=" +";
				}
			}

			if($tmpSetPoint1) {
				$Obj1->ScoreDetails .= "($tmpSetPoint1)";
				$Obj2->ScoreDetails .= "($tmpSetPoint2)";
			}

			if($Match['irm']) {
				$Obj1->ScoreDetails.=' '.$Match['irmText'];
			}
			if($Match['oppIrm']) {
				$Obj2->ScoreDetails.=' '.$Match['oppIrmText'];
			}

			if($Match['record']) {
				$Obj1->ScoreDetails.=' '.$Match['record'];
			} elseif($Match['notes']) {
				$Obj1->ScoreDetails.=' '.$Match['notes'];
			} elseif($Match['tie']==2) {
				$Obj1->Score='';
			} elseif($RealScore1==0 and $RealScore2==0) {
				$Obj1->Score='';
				//$Obj1->ScoreDetails='';
				if($Match['oppTie']!=2 and $Match['tie']!=2 and $Obj1->FSTarget) {
					$Obj1->ScoreDetails='T# '.$Obj1->FSTarget;
				}
			}

			if($Match['oppRecord']) {
				$Obj2->ScoreDetails.=' '.$Match['oppRecord'];
			} elseif($Match['oppNotes']) {
				$Obj2->ScoreDetails.=' '.$Match['oppNotes'];
			} elseif($Match['oppTie']==2) {
				$Obj2->Score='';
			} elseif($RealScore1==0 and $RealScore2==0) {
				$Obj2->Score='';
				//$Obj2->ScoreDetails='';
				if($Match['oppTie']!=2 and $Match['tie']!=2 and $Obj2->FSTarget) {
					$Obj2->ScoreDetails='T# '.$Obj2->FSTarget;
				}
			}

			$Obj1->ScoreDetails=trim($Obj1->ScoreDetails);
			$Obj2->ScoreDetails=trim($Obj2->ScoreDetails);

			//Valuto cosa fare se è la prima colonna
			if($FirstPhase) {
			    if($section['meta']['firstPhase']<=8) {
                    for ($n = 0; $n < $section['meta']['maxTeamPerson']; $n++) {
                        $Comp1[] = empty($section['athletes'][$Match['teamId']][$Match['subTeam']][$n]) ? array('', '') : array(
                            $section['athletes'][$Match['teamId']][$Match['subTeam']][$n]['backNo'],
                            $section['athletes'][$Match['teamId']][$Match['subTeam']][$n]['athlete'],
                        );
                        $Comp2[] = empty($section['athletes'][$Match['oppTeamId']][$Match['oppSubTeam']][$n]) ? array('', '') : array(
                            $section['athletes'][$Match['oppTeamId']][$Match['oppSubTeam']][$n]['backNo'],
                            $section['athletes'][$Match['oppTeamId']][$Match['oppSubTeam']][$n]['athlete'],
                        );
                    }
                } else {
                    $bk1 = array();
                    $bk2 = array();
                    $ath1 = array();
                    $ath2 = array();
                    for ($n = 0; $n < $section['meta']['maxTeamPerson']; $n++) {
                    	if(!empty($section['athletes'][$Match['teamId']][$Match['subTeam']][$n])) {
                            $bk1[]  .= empty($section['athletes'][$Match['teamId']][$Match['subTeam']][$n]) ? '' : $section['athletes'][$Match['teamId']][$Match['subTeam']][$n]['backNo'];
                            $ath1[] .= $section['athletes'][$Match['teamId']][$Match['subTeam']][$n]['familyName'];
	                    }
	                    if(!empty($section['athletes'][$Match['oppTeamId']][$Match['oppSubTeam']][$n])) {
	                        $bk2[] .= empty($section['athletes'][$Match['oppTeamId']][$Match['oppSubTeam']][$n]) ? '' : $section['athletes'][$Match['oppTeamId']][$Match['oppSubTeam']][$n]['backNo'];
	                        $ath2[] .= $section['athletes'][$Match['oppTeamId']][$Match['oppSubTeam']][$n]['familyName'];
	                    }
                    }
                    $Comp1[] = array(implode(', ', $bk1), implode(', ', $ath1));
                    $Comp2[] = array(implode(', ', $bk2), implode(', ', $ath2));

                }

				$Obj1->Componenti = $Comp1;
				$Obj2->Componenti = $Comp2;
				$PdfData->Events[$Event]->FirstColumn[]=$Obj1;
				$PdfData->Events[$Event]->FirstColumn[]=$Obj2;
			} else {
				if($Phase) {
					$PdfData->Events[$Event]->OtherColumn[$Phase][]=$Obj1;
					$PdfData->Events[$Event]->OtherColumn[$Phase][]=$Obj2;
				} else {
					// GOLD FINAL MUST BE SHIFTED INTO BRONZE PHASE IN REVERSE ORDER
					array_unshift($PdfData->Events[$Event]->OtherColumn[1], $Obj2);
					array_unshift($PdfData->Events[$Event]->OtherColumn[1], $Obj1);
				}
			}

			if($Match['matchNo']<16) {
				if($Obj1->FinRank and is_numeric($Obj1->FinRank)) {
					if($Obj1->IrmText!='DQB') {
						if($Obj1->FinRank<=4) {
							if(empty($Finalists[$Event][$Obj1->FinRank][0])) {
								$Finalists[$Event][$Obj1->FinRank][0]=$Obj1;
								$MaxFinalists[$Event]++;
							}
						} elseif($Obj1->FinRank<=8) {
							$Finalists[$Event][$Obj1->FinRank][]=$Obj1;
							$MaxFinalists[$Event]++;
						}
						//$MaxFinalists[$Event]=max($MaxFinalists[$Event], $Obj1->FinRank+count($Finalists[$Event][$Obj1->FinRank])-1);
					} else {
						$MaxFinalists[$Event]++;
					}
				}
				if($Obj2->FinRank and is_numeric($Obj2->FinRank)) {
					if($Obj2->IrmText!='DQB') {
						if($Obj2->FinRank<=4) {
							if(empty($Finalists[$Event][$Obj2->FinRank][0])) {
								$Finalists[$Event][$Obj2->FinRank][0]=$Obj2;
								$MaxFinalists[$Event]++;
							}
						} elseif($Obj2->FinRank<=8) {
							$Finalists[$Event][$Obj2->FinRank][]=$Obj2;
							$MaxFinalists[$Event]++;
						}
						//$MaxFinalists[$Event]=max($MaxFinalists[$Event], $Obj2->FinRank+count($Finalists[$Event][$Obj2->FinRank])-1);
					} else {
						$MaxFinalists[$Event]++;
					}
				}
			}

			if($Phase<=1) {
				if($Obj1->FinRank==1)
					$PdfData->Events[$Event]->Medals["Gold"] = $Obj1->Team;
				if($Obj1->FinRank==2)
					$PdfData->Events[$Event]->Medals["Silver"] = $Obj1->Team;
				if($Obj1->FinRank==3)
					$PdfData->Events[$Event]->Medals["Bronze"] = $Obj1->Team;
				if($Obj2->FinRank==1)
					$PdfData->Events[$Event]->Medals["Gold"] = $Obj2->Team;
				if($Obj2->FinRank==2)
					$PdfData->Events[$Event]->Medals["Silver"] = $Obj2->Team;
				if($Obj2->FinRank==3)
					$PdfData->Events[$Event]->Medals["Bronze"] = $Obj2->Team;
			}
		}
		$FirstPhase=false;
	}
}

$First=true;
foreach($PdfData->Events as $Event => $Pages) {
	$pdf->setEvent($Pages->Event);
	$pdf->setPhase($PdfData->Description);
	$pdf->Records=$Pages->Records;

	$pdf->CellHSp = $FreePageWidth/($CountPhases[$Event]+1);

	$pdf->SetDataHeader($Pages->Header, $Pages->HeaderWidth);

	if($PdfData->rankData['sections'][$Event]['meta']['version']) {
		$pdf->setComment(trim("Vers. {$PdfData->rankData['sections'][$Event]['meta']['version']} ({$PdfData->rankData['sections'][$Event]['meta']['versionDate']}) {$PdfData->rankData['sections'][$Event]['meta']['versionNotes']}"));
	} else {
		$pdf->setComment($Pages->PrintHead);
	}
	$pdf->AddPage();
	$pdf->setOrisCode($section['meta']['OrisCode'], $PdfData->Description);
	if($First and (empty($pdf->CompleteBookTitle) or $pdf->CompleteBookTitle!=$PdfData->IndexName)) {
		$pdf->Bookmark($PdfData->IndexName, 0);
		$pdf->CompleteBookTitle=$PdfData->IndexName;
	}
	$First=false;
	$pdf->Bookmark($Pages->Event, 1);

	$TopY=$pdf->lastY;

	$PhaseCounter=0;

	if($Pages->NumComponenti) {
        $pdf->CellVSp = (($pdf->GetPageHeight() - $TopY) / (($Pages->FirstPhase > 8 ? 16.5 : 8.2) * (4 + 2 * $Pages->NumComponenti)) - ($Pages->FirstPhase > 8 ? 0.20 : 0.35));
    } else {
        $pdf->CellVSp = (($pdf->GetPageHeight() - $TopY) / (40) - 0.25);
    }

	$pdf->lastY = $TopY + ($PhaseCounter==0 ? 1:($PhaseCounter==1 ? (2+$Pages->NumComponenti-1):($PhaseCounter==2 ? (4+2*$Pages->NumComponenti-1):(8+4*$Pages->NumComponenti-1)))) * $pdf->CellVSp;

	//Resetto il carattere
	$pdf->SetFont('','',8);

	foreach($Pages->FirstColumn as $item) {
		$pdf->FirstColumnTeam($item);
	}

	foreach($Pages->OtherColumn as $Column) {
		$PhaseCounter++;
		if($Pages->NumComponenti) {
            $pdf->CellVSp = (($pdf->GetPageHeight() - $TopY) / (($Pages->FirstPhase > 8 ? 16.5 : 8.2) * (4 + 2 * $Pages->NumComponenti)) - ($Pages->FirstPhase > 8 ? 0.20 : 0.35));
        } else {
            $pdf->CellVSp = (($pdf->GetPageHeight() - $TopY) / (40) - 0.25);
        }

		$pdf->lastY = $TopY + ($PhaseCounter==0 ? 1:($PhaseCounter==1 ? (2+$Pages->NumComponenti-1):($PhaseCounter==2 ? (4+2*$Pages->NumComponenti-1):($PhaseCounter==3 ? (8+4*$Pages->NumComponenti-1):(16+8*$Pages->NumComponenti-1))))) * $pdf->CellVSp;

		foreach($Column as $item) {
			$pdf->OtherColumnsTeam($PhaseCounter, $item, $Pages->NumComponenti);
		}
	}

	$PhaseCounter++;
	$pdf->PrintMedalsTeam($PhaseCounter, $Pages->Medals["Gold"], $Pages->Medals["Silver"], $Pages->Medals["Bronze"], $Pages->NumComponenti);

	$pdf->Finalists=$Finalists[$Event];
	$pdf->MaxFinalists=$MaxFinalists[$Event];
	$pdf->PrintFinalistsTeam($Pages->FirstPhase>4);
}

$pdf->Records=array();


