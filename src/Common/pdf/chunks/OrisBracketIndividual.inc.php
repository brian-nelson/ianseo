<?php

$rankData=$PdfData->rankData;

$ShowTargetNo = (isset($PdfData->ShowTargetNo) ? $PdfData->ShowTargetNo : true);
$ShowSchedule = (isset($PdfData->ShowSchedule) ? $PdfData->ShowSchedule : true);
$ShowSetArrows= (isset($PdfData->ShowSetArrows) ? $PdfData->ShowSetArrows : true);

$tmpObj=new StdClass();
$tmpObj->FirstName='';
$tmpObj->Name='';
$tmpObj->Country='';

$PhaseTitles=array("1/48\nElimin. Round§", "1/24\nElimin. Round§", "1/16\nElimin. Round§", "1/8\nElimin. Round§", "Quarterfinals§", "Semifinals§", "Finals§");
$titArray = array("R. Round\nRank/Score§",'',"Name","NOC\nCode");
$misArray = array(array(7.5, 8.5), 9, 35, 13);

$FreePageWidth=$pdf->getPageWidth()-20-array_reduce($misArray, function($a,$b) {return $a + (is_array($b) ? array_sum($b) : $b);});

$pdf->NotAwarded=$rankData['meta']['notAwarded'];
$pdf->FinalRank=$rankData['meta']['fields']['finRank'];

$Finalists=array();
$MaxFinalists=array();
foreach($rankData['sections'] as $Event => $section) {
	$Finalists[$Event]=array();
	$MaxFinalists[$Event]=0;
	if(!isset($PdfData->Events[$Event])) {
		// preparation of the pages
		if(empty($PdfData->Events[$Event])) {
			$PdfData->Events[$Event] = new stdClass();
		}
		$PdfData->Events[$Event]->Event = $section['meta']['eventName'];
		$PdfData->Events[$Event]->LastUpdate = $rankData['meta']['lastUpdate'];
		$PdfData->Events[$Event]->FirstToPrint = false;
		$PdfData->Events[$Event]->OtherToPrint = false;
		$PdfData->Events[$Event]->FirstPhase = $section['meta']['firstPhase'];
		$PdfData->Events[$Event]->Medals = array("Gold"=> new $tmpObj, "Silver"=> new $tmpObj,"Bronze"=> new $tmpObj);
		$PdfData->Events[$Event]->Records = (empty($section['records']) ? array() : $section['records']);
		$PdfData->Events[$Event]->Pages = array();
		// first page is ALWAYS 1/4 Finals or semifinals
		$cols=($section['meta']['firstPhase']>=4 ? 4 : 3);
		if(empty($PdfData->Events[$Event]->Pages[0])) {
			$PdfData->Events[$Event]->Pages[0] = new stdClass();
		}
		$PdfData->Events[$Event]->Pages[0]->Header = array_merge($titArray, array_slice($PhaseTitles, -1*$cols+1));
		$PdfData->Events[$Event]->Pages[0]->HeaderWidth = array_merge($misArray, array_fill(0, $cols, $FreePageWidth/$cols));
		$PdfData->Events[$Event]->Pages[0]->Phase = 'Final Round';
		$PdfData->Events[$Event]->Pages[0]->Code='C75B';

		if($section['meta']['firstPhase']>4) {
			// ALWAYS at least 1 page up to 16th
			$cols=ceil(log($section['meta']['firstPhase'], 2))+1;
			$tmpTitle=$PhaseTitles;
			if($section['meta']['firstPhase']==32) $tmpTitle[1]="1/32\nElimin. Round§";
			if(empty($PdfData->Events[$Event]->Pages[1]))
				$PdfData->Events[$Event]->Pages[1] = new stdClass();
			$PdfData->Events[$Event]->Pages[1]->Header = array_merge($titArray, array_slice($tmpTitle, -1*$cols, -2));
			$PdfData->Events[$Event]->Pages[1]->HeaderWidth = array_merge($misArray, array_fill(0, $cols-2, $FreePageWidth/($cols-2)));
			$PdfData->Events[$Event]->Pages[1]->Phase = 'Elimination Round';
			$PdfData->Events[$Event]->Pages[1]->Code='C75A';
			if($section['meta']['firstPhase']>16) {
				// if start phase is 24 or 32, add a second page
				$PdfData->Events[$Event]->Pages[2] = clone $PdfData->Events[$Event]->Pages[1];
				if($section['meta']['firstPhase']>32) {
					// if start phase is 48 or 64, add 2 more pages
					$PdfData->Events[$Event]->Pages[3] = clone $PdfData->Events[$Event]->Pages[1];
					$PdfData->Events[$Event]->Pages[4] = clone $PdfData->Events[$Event]->Pages[1];
				}
			}
		}
	}

	$CurPhase=0;
	foreach($section['phases'] as $PhaseNum => $Phase) {
		$page=0;

		foreach($Phase['items'] as $item) {

			$DrawMatch = !( ($section['meta']['firstPhase']==48 or $section['meta']['firstPhase']==24) && $Phase>=32 && ($item['saved'] or $item['oppSaved']) );
			$DrawMatch = ($DrawMatch and $item['id'] and $item['oppId']);

			if($section['meta']['firstPhase']>32) {
				if($item['matchNo']==8 or $item['matchNo']==10 or $item['matchNo']==12 or $item['matchNo']==14
					or $item['matchNo']==16 or $item['matchNo']==20 or $item['matchNo']==24 or $item['matchNo']==28
					or $item['matchNo']==32 or $item['matchNo']==40 or $item['matchNo']==48 or $item['matchNo']==56
					or $item['matchNo']==64 or $item['matchNo']==80 or $item['matchNo']==96 or $item['matchNo']==112
					or $item['matchNo']==128 or $item['matchNo']==160 or $item['matchNo']==192 or $item['matchNo']==224
					) $page++;

			} elseif($section['meta']['firstPhase']>16)  {
				if($item['matchNo']==8 or $item['matchNo']==12
					or $item['matchNo']==16 or $item['matchNo']==24
					or $item['matchNo']==32 or $item['matchNo']==48
					or $item['matchNo']==64 or $item['matchNo']==96
					) $page++;
			} elseif($section['meta']['firstPhase']>4)  {
				if($item['matchNo']==8 or $item['matchNo']==16 or $item['matchNo']==32) $page++;
			}

			$Obj1=new StdClass();
			$Obj1->EnId = $item['bib'];
			$Obj1->RealEnId = $item['id'];
			$Obj1->PhaseCounter = $CurPhase;
			$Obj1->FinMatchNo = $item['matchNo'];
			$Obj1->FirstName = $item['familyNameUpper'];
			$Obj1->Name = $item['givenName'];
			$Obj1->Country = $item['countryCode'];
			$Obj1->IndRank = $item['qualRank'];
			$Obj1->FinRank = $item['finRank'];
			$Obj1->ShowRank = $item['showRank'];
			$Obj1->IrmText = $item['finIrmText'];
			$Obj1->QuScore = $item['qualScore'];
			$Obj1->QuNotes = $item['qualNotes'];
			$Obj1->GrPosition = $item['qualRank'];
			$Obj1->Score = ($section['meta']['matchMode'] ? $item['setScore'] : $item['score']);
			$Obj1->ScoreDetails = '';
			$Obj1->ScoreMatch = $item['score'];
			$Obj1->ScoreCell = 6;
			//DOC $Obj1->Score = ($item['status']==1 ? 'DSQ-':'') . ($section['meta']['matchMode'] ? $item['setScore'] : $item['score']);
			$Obj1->FinTie = $item['tie'];
			$Obj1->FinTiebreak = $item['tiebreak'];
            $Obj1->FinTiebreakDecoded = $item['tiebreakDecoded'];
			$Obj1->SetPoints = $item['setPoints'];
			$Obj1->Bold = $item['winner'] and !$item['irm'];
			$Obj1->oppSetPoints = $item['oppSetPoints'];
			$Obj1->OppScore = ($section['meta']['matchMode'] ? $item['oppSetScore'] : $item['oppScore']);
			$Obj1->OppTie = $item['oppTie'];
			$Obj1->OppFinRank = $item['oppFinRank'];
			$Obj1->FSTarget = ($ShowTargetNo ? $item['target'] : '');

			$Obj1->ScheduledDate = ($ShowSchedule ? $item['scheduledDate'] : '');
			$Obj1->ScheduledTime = ($ShowSchedule ? $item['scheduledTime'] : '');
			$Obj1->Saved = ($item['oppPosition'] and $item['oppPosition']<=$section['meta']['numSaved']) ? $PdfData->rankData['meta']['saved'] : '';
			$Obj1->DrawMatch=$DrawMatch;
			$Obj1->ToDo = ($DrawMatch or $item['saved']);

			// second athlete of match
			$Obj2=new StdClass();
			$Obj2->EnId = $item['oppBib'];
			$Obj2->RealEnId = $item['oppId'];
			$Obj2->PhaseCounter = $CurPhase;
			$Obj2->FinMatchNo = $item['oppMatchNo'];
			$Obj2->FirstName = $item['oppFamilyNameUpper'];
			$Obj2->Name = $item['oppGivenName'];
			$Obj2->Country = $item['oppCountryCode'];
			$Obj2->IndRank = $item['oppQualRank'];
			$Obj2->FinRank = $item['oppFinRank'];
			$Obj2->ShowRank = $item['oppShowRank'];
			$Obj2->IrmText = $item['oppFinIrmText'];
			$Obj2->QuScore = $item['oppQualScore'];
			$Obj2->QuNotes = $item['oppQualNotes'];
			$Obj2->GrPosition = $item['oppQualRank'];
			$Obj2->Score = ($section['meta']['matchMode'] ? $item['oppSetScore'] : $item['oppScore']);
			$Obj2->ScoreDetails = '';
			$Obj2->ScoreCell = 6;
			$Obj2->ScoreMatch = $item['oppScore'];
			//DOC $Obj2->Score = ($item['oppStatus']==1 ? 'DSQ-':'') . ($section['meta']['matchMode'] ? $item['oppSetScore'] : $item['oppScore']);
			$Obj2->FinTie = $item['oppTie'];
			$Obj2->FinTiebreak = $item['oppTiebreak'];
            $Obj2->FinTiebreakDecoded = $item['oppTiebreakDecoded'];
			$Obj2->Bold = $item['oppWinner'] and !$item['oppIrm'];
			$Obj2->SetPoints = $item['oppSetPoints'];
			$Obj2->oppSetPoints = $item['setPoints'];
			$Obj2->OppScore = ($section['meta']['matchMode'] ? $item['setScore'] : $item['score']);
			$Obj2->OppTie = $item['tie'];
			$Obj2->OppFinRank = $item['oppFinRank'];
			$Obj2->FSTarget = ($ShowTargetNo ? $item['oppTarget'] : '');
			$Obj2->ScheduledDate = ($ShowSchedule ? $item['scheduledDate'] : '');
			$Obj2->ScheduledTime = ($ShowSchedule ? $item['scheduledTime'] : '');
			$Obj2->Saved = ($item['position'] and $item['position']<=$section['meta']['numSaved']) ? $PdfData->rankData['meta']['saved'] : '';
			$Obj2->DrawMatch=$DrawMatch;
			$Obj2->ToDo = ($DrawMatch or $item['oppSaved']);

			// what exactly has to print as "score" for side A...
			$Obj1->strike=($item['finIrm']==20);
			$Obj2->strike=($item['oppFinIrm']==20);
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
			if(!empty($Obj1->FinTiebreakDecoded) OR !empty($Obj2->FinTiebreakDecoded)) {
				if($tmpSetPoint1) {
					$tmpSetPoint1.='-';
					$tmpSetPoint2.='-';
				}
				$tmpSetPoint1.='T. '.$Obj1->FinTiebreakDecoded;
				$tmpSetPoint2.='T. '.$Obj2->FinTiebreakDecoded;
			}

			if($tmpSetPoint1) {
				$Obj1->ScoreDetails .= "($tmpSetPoint1)";
				$Obj2->ScoreDetails .= "($tmpSetPoint2)";
			}

			// Has an IRM
			if($item['irm']) {
				$Obj1->ScoreDetails.=' '.$item['irmText'];
			}
			if($item['oppIrm']) {
				$Obj2->ScoreDetails.=' '.$item['oppIrmText'];
			}

			if($item['record']) {
				$Obj1->ScoreDetails.=' '.$item['record'];
			} elseif($item['notes']) {
				$Obj1->ScoreDetails.=' '.$item['notes'];
			} elseif($item['tie']==2) {
				$Obj1->Score='';
			} elseif($RealScore1==0 and $RealScore2==0) {
				$Obj1->Score='';
				//$Obj1->ScoreDetails='';
				if($item['oppTie']!=2 and $item['tie']!=2 and $Obj1->FSTarget and empty($Obj1->ScoreDetails)) {
					$Obj1->ScoreDetails='T# '.$Obj1->FSTarget;
				}
			}

			if($item['oppRecord']) {
				$Obj2->ScoreDetails.=' '.$item['oppRecord'];
			} elseif($item['oppNotes']) {
				$Obj2->ScoreDetails.=' '.$item['oppNotes'];
			} elseif($item['oppTie']==2) {
				$Obj2->Score='';
			} elseif($RealScore1==0 and $RealScore2==0) {
				$Obj2->Score='';
				//$Obj2->ScoreDetails='';
				if($item['oppTie']!=2 and $item['tie']!=2 and $Obj2->FSTarget and empty($Obj2->ScoreDetails)) {
					$Obj2->ScoreDetails='T# '.$Obj2->FSTarget;
				}
			}

			$Obj1->ScoreDetails=trim($Obj1->ScoreDetails);
			$Obj2->ScoreDetails=trim($Obj2->ScoreDetails);

			$PdfData->Events[$Event]->FirstToPrint = ($PdfData->Events[$Event]->FirstToPrint or ($Obj1->FinMatchNo<=8 and ($Obj2->FirstName or $Obj1->FirstName)));
			$PdfData->Events[$Event]->OtherToPrint = ($PdfData->Events[$Event]->OtherToPrint or ($Obj1->FinMatchNo>8 and ($Obj2->FirstName or $Obj1->FirstName)));
			if($CurPhase) {
				if($PhaseNum==0) {
					array_unshift($PdfData->Events[$Event]->Pages[$page]->OtherColumns[$CurPhase-1], $Obj2);
					array_unshift($PdfData->Events[$Event]->Pages[$page]->OtherColumns[$CurPhase-1], $Obj1);
				} else {
					$PdfData->Events[$Event]->Pages[$page]->OtherColumns[$CurPhase][] = $Obj1;
					$PdfData->Events[$Event]->Pages[$page]->OtherColumns[$CurPhase][] = $Obj2;
				}
			} else {
				if(empty($PdfData->Events[$Event]->Pages[$page])) {
					$PdfData->Events[$Event]->Pages[$page] = new stdClass();
				}
				$PdfData->Events[$Event]->Pages[$page]->FirstColumn[] = $Obj1;
				$PdfData->Events[$Event]->Pages[$page]->FirstColumn[] = $Obj2;
			}

			// the quarters are 1st column in page 0
			if($section['meta']['firstPhase']>4 and $item['matchNo']<16) {
				// the quarters!
				$Obj1->PhaseCounter = ($PhaseNum==4 ? 0 : ($PhaseNum==2 ? 1 : 2));
				$Obj2->PhaseCounter = $Obj1->PhaseCounter;
				if($PhaseNum==4) {
					$PdfData->Events[$Event]->Pages[0]->FirstColumn[] = $Obj1;
					$PdfData->Events[$Event]->Pages[0]->FirstColumn[] = $Obj2;
				}
			}

			if($Obj1->FinRank and is_numeric($Obj1->FinRank) and $Obj1->IrmText!='DQB' and $Obj1->FinRank<=8) {
				$Finalists[$Event][$Obj1->FinRank][$Obj1->RealEnId]=$Obj1;
				//if($Obj1->FinRank<=4) {
				//} elseif($Obj1->FinRank<=8) {
				//	$Finalists[$Event][$Obj1->FinRank][]=$Obj1;
				//}
				$MaxFinalists[$Event]=max($MaxFinalists[$Event], $Obj1->FinRank+count($Finalists[$Event][$Obj1->FinRank])-1);
			}
			if($Obj2->FinRank and is_numeric($Obj2->FinRank) and $Obj2->IrmText!='DQB' and $Obj2->FinRank<=8) {
				$Finalists[$Event][$Obj2->FinRank][$Obj2->RealEnId]=$Obj2;
				//if($Obj2->FinRank<=4) {
				//} elseif($Obj2->FinRank<=8) {
				//	$Finalists[$Event][$Obj2->FinRank][]=$Obj2;
				//}
				$MaxFinalists[$Event]=max($MaxFinalists[$Event], $Obj2->FinRank+count($Finalists[$Event][$Obj2->FinRank])-1);
			}

			if($PhaseNum<=1) {
				if($Obj1->FinRank==1) {
					$PdfData->Events[$Event]->Medals["Gold"]->FirstName = $Obj1->FirstName;
					$PdfData->Events[$Event]->Medals["Gold"]->Name = $Obj1->Name;
					$PdfData->Events[$Event]->Medals["Gold"]->Country = $Obj1->Country;
				}
				if($Obj1->FinRank==2) {
					$PdfData->Events[$Event]->Medals["Silver"]->FirstName = $Obj1->FirstName;
					$PdfData->Events[$Event]->Medals["Silver"]->Name = $Obj1->Name;
					$PdfData->Events[$Event]->Medals["Silver"]->Country = $Obj1->Country;
				}
				if($Obj1->FinRank==3) {
					$PdfData->Events[$Event]->Medals["Bronze"]->FirstName = $Obj1->FirstName;
					$PdfData->Events[$Event]->Medals["Bronze"]->Name = $Obj1->Name;
					$PdfData->Events[$Event]->Medals["Bronze"]->Country = $Obj1->Country;
				}
				if($Obj2->FinRank==1) {
					$PdfData->Events[$Event]->Medals["Gold"]->FirstName = $Obj2->FirstName;
					$PdfData->Events[$Event]->Medals["Gold"]->Name = $Obj2->Name;
					$PdfData->Events[$Event]->Medals["Gold"]->Country = $Obj2->Country;
				}
				if($Obj2->FinRank==2) {
					$PdfData->Events[$Event]->Medals["Silver"]->FirstName = $Obj2->FirstName;
					$PdfData->Events[$Event]->Medals["Silver"]->Name = $Obj2->Name;
					$PdfData->Events[$Event]->Medals["Silver"]->Country = $Obj2->Country;
				}
				if($Obj2->FinRank==3) {
					$PdfData->Events[$Event]->Medals["Bronze"]->FirstName = $Obj2->FirstName;
					$PdfData->Events[$Event]->Medals["Bronze"]->Name = $Obj2->Name;
					$PdfData->Events[$Event]->Medals["Bronze"]->Country = $Obj2->Country;
				}
			}
		}

		$CurPhase++;
	}
}

$First=true;

foreach($PdfData->Events as $Event => $Pages) {
	$Title=$Pages->Event;
	if($Pages->FirstToPrint and strstr($pdf->OrisPages, 'B')) {
		// prints the quarters, medals etc...
		$pdf->setEvent($Pages->Event);
		$pdf->Records=$Pages->Records;
		$pdf->setPhase($Pages->Pages[0]->Phase);
		$pdf->SetDataHeader($Pages->Pages[0]->Header, $Pages->Pages[0]->HeaderWidth);
		$pdf->setDocUpdate($Pages->LastUpdate);

		if($rankData['sections'][$Event]['meta']['version']) {
			$pdf->setComment(trim("Vers. {$rankData['sections'][$Event]['meta']['version']} ({$rankData['sections'][$Event]['meta']['versionDate']}) {$rankData['sections'][$Event]['meta']['versionNotes']}"));
		} else {
			$pdf->setComment('');
		}

		$pdf->AddPage();
		$pdf->setOrisCode($Pages->Pages[0]->Code, $PdfData->Description);
		if($First and (empty($pdf->CompleteBookTitle) or $pdf->CompleteBookTitle!=$PdfData->IndexName)) {
			$pdf->Bookmark($PdfData->IndexName, 0);
			$pdf->CompleteBookTitle=$PdfData->IndexName;
		}
		$First=false;
		$pdf->Bookmark($Pages->Event, 1);

		$pdf->CellHSp = $FreePageWidth/($Pages->FirstPhase==2 ? 3 : 4);
		$TopY=$pdf->lastY;
        $pdf->CellVSp = (($pdf->GetPageHeight()-(15+$pdf->extraBottomMargin)-$TopY)/(16*3)-0.1);
		$pdf->lastY = $TopY + $pdf->CellVSp;

		$pdf->SetY($pdf::topStart-6);

		foreach($Pages->Pages[0]->FirstColumn as $item) {
			$pdf->FirstColumn($item);
		}

		$PhaseCounter=1;
		foreach($Pages->Pages[0]->OtherColumns as $Columns) {
			$pdf->lastY = $TopY + $pdf->CellVSp*($PhaseCounter<=1 ? 1:($PhaseCounter==2 ? 2.5:($PhaseCounter==3 ? 5.5 : 11.5)));
			foreach($Columns as $item) {
				$pdf->OtherColumns($PhaseCounter, $item);
			}
			$PhaseCounter++;
		}

		$pdf->lastY = $TopY + ($Pages->FirstPhase>=4 ? 4.5 : 1.5)*$pdf->CellVSp;
		$pdf->PrintMedals($PhaseCounter,
			(empty($Pages->Medals["Gold"]->FirstName) ? "" : $Pages->Medals["Gold"]->FirstName), (empty($Pages->Medals["Gold"]->Name) ? "" : $Pages->Medals["Gold"]->Name),(empty($Pages->Medals["Gold"]->Country) ? "" : $Pages->Medals["Gold"]->Country),
			(empty($Pages->Medals["Silver"]->FirstName) ? "" : $Pages->Medals["Silver"]->FirstName), (empty($Pages->Medals["Silver"]->Name) ? "" : $Pages->Medals["Silver"]->Name),(empty($Pages->Medals["Silver"]->Country) ? "" : $Pages->Medals["Silver"]->Country),
			(empty($Pages->Medals["Bronze"]->FirstName) ? "" : $Pages->Medals["Bronze"]->FirstName), (empty($Pages->Medals["Bronze"]->Name) ? "" : $Pages->Medals["Bronze"]->Name),(empty($Pages->Medals["Bronze"]->Country) ? "" : $Pages->Medals["Bronze"]->Country));

		$pdf->Finalists=$Finalists[$Event];
		$pdf->MaxFinalists=$MaxFinalists[$Event];
		$pdf->PrintFinalists();
	}

	if($Pages->OtherToPrint and strstr($pdf->OrisPages,'A')) {
		// prints the other pages
		for($pag=1; $pag<count($Pages->Pages); $pag++) {
			$pdf->setEvent($Pages->Event);
			$pdf->setPhase($Pages->Pages[$pag]->Phase);
			$pdf->SetDataHeader($Pages->Pages[$pag]->Header, $Pages->Pages[$pag]->HeaderWidth);
			$pdf->Records=$Pages->Records;
			if($rankData['sections'][$Event]['meta']['version']) {
				$pdf->setComment(trim("Vers. {$rankData['sections'][$Event]['meta']['version']} ({$rankData['sections'][$Event]['meta']['versionDate']}) {$rankData['sections'][$Event]['meta']['versionNotes']}"));
			} else {
				$pdf->setComment('');
			}
			$pdf->AddPage();
// 			$pdf->Bookmark($PdfData->Description, 1);
			$pdf->setOrisCode($Pages->Pages[$pag]->Code, $PdfData->Description);

			$pdf->CellHSp = $FreePageWidth/(ceil(log($Pages->FirstPhase,2))-1);
			$TopY=$pdf->lastY;
            $pdf->CellVSp = (($pdf->GetPageHeight()-(15+$pdf->extraBottomMargin)-$TopY)/(16*3)-0.1);
			$pdf->lastY = $TopY + $pdf->CellVSp;

			foreach($Pages->Pages[$pag]->FirstColumn as $item) {
				$pdf->FirstColumn($item);
			}

			foreach($Pages->Pages[$pag]->OtherColumns as $PhaseCounter => $Columns) {
				$pdf->lastY = $TopY + $pdf->CellVSp*($PhaseCounter<=1 ? 1:($PhaseCounter==2 ? 2.5:($PhaseCounter==3 ? 5.5 : 11.5)));
				foreach($Columns as $item) {
					$pdf->OtherColumns($PhaseCounter, $item);
				}
			}
		}
	}
}
$pdf->Records=array();