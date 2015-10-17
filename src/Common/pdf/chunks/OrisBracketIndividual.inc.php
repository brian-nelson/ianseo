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
$titArray = array("RR Rank /\nScore#","Bk\nNo#","Name","NOC\nCode");
$misArray = array(array(8,10), 9, 40, 13);

foreach($rankData['sections'] as $Event => $section) {
	if(!isset($PdfData->Events[$Event])) {
		// preparation of the pages
		if(empty($PdfData->Events[$Event]))
			$PdfData->Events[$Event] = new stdClass();
		$PdfData->Events[$Event]->Event = $section['meta']['eventName'];
		$PdfData->Events[$Event]->LastUpdate = $rankData['meta']['lastUpdate'];
		$PdfData->Events[$Event]->FirstToPrint = false;
		$PdfData->Events[$Event]->OtherToPrint = false;
		$PdfData->Events[$Event]->FirstPhase = $section['meta']['firstPhase'];
		$PdfData->Events[$Event]->Medals = array("Gold"=> new $tmpObj, "Silver"=> new $tmpObj,"Bronze"=> new $tmpObj);
		$PdfData->Events[$Event]->Pages = array();
		// first page is ALWAYS 1/4 Finals or semifinals
		$cols=($section['meta']['firstPhase']>=4 ? 4 : 3);
		if(empty($PdfData->Events[$Event]->Pages[0]))
			$PdfData->Events[$Event]->Pages[0] = new stdClass();
		$PdfData->Events[$Event]->Pages[0]->Header = array_merge($titArray, array_slice($PhaseTitles, -1*$cols+1));
		$PdfData->Events[$Event]->Pages[0]->HeaderWidth = array_merge($misArray, array_fill(0, $cols, 110/$cols));
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
			$PdfData->Events[$Event]->Pages[1]->HeaderWidth = array_merge($misArray, array_fill(0, $cols-2, 110/($cols-2)));
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

		//if($PhaseNum==16) debug_svela($Phase, true);

		foreach($Phase['items'] as $item) {

			$DrawMatch = !( ($section['meta']['firstPhase']==48 or $section['meta']['firstPhase']==24) && $Phase>=32 && ($item['saved'] or $item['oppSaved']) );
			$DrawMatch = ($DrawMatch and $item['familyName'] and $item['oppFamilyName']);

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
			$Obj1->PhaseCounter = $CurPhase;
			$Obj1->FinMatchNo = $item['matchNo'];
			$Obj1->FirstName = $item['familyNameUpper'];
			$Obj1->Name = $item['givenName'];
			$Obj1->Country = $item['countryCode'];
			$Obj1->IndRank = $item['qualRank']; 
			$Obj1->FinRank = $item['finRank']; 
			$Obj1->QuScore = $item['qualScore'];
			$Obj1->GrPosition = $item['qualRank'];
			$Obj1->Score = ($item['status']==1 ? 'DSQ-':'') . ($section['meta']['matchMode'] ? $item['setScore'] : $item['score']);
			$Obj1->FinTie = $item['tie'];
			$Obj1->FinTiebreak = $item['tiebreak'];
			$Obj1->SetPoints = $item['setPoints'];
			$Obj1->oppSetPoints = $item['oppSetPoints'];
			$Obj1->OppScore = ($section['meta']['matchMode'] ? $item['oppSetScore'] : $item['oppScore']);
			$Obj1->OppTie = $item['oppTie'];
			$Obj1->FSTarget = ($ShowTargetNo ? $item['target'] : '');
			$Obj1->ScheduledDate = ($ShowSchedule ? $item['scheduledDate'] : '');
			$Obj1->ScheduledTime = ($ShowSchedule ? $item['scheduledTime'] : '');
			$Obj1->Saved = $item['oppSaved'] && ($section['meta']['firstPhase']==24 or $section['meta']['firstPhase']==48) ? $rankData['meta']['saved'] : '';
			$Obj1->DrawMatch=$DrawMatch;
			$Obj1->ToDo = ($DrawMatch or $item['saved']);

			// second athlete of match
			$Obj2=new StdClass();
			$Obj2->PhaseCounter = $CurPhase;
			$Obj2->FinMatchNo = $item['oppMatchNo'];
			$Obj2->FirstName = $item['oppFamilyNameUpper'];
			$Obj2->Name = $item['oppGivenName'];
			$Obj2->Country = $item['oppCountryCode'];
			$Obj2->IndRank = $item['oppQualRank'];
			$Obj2->FinRank = $item['oppFinRank'];
			$Obj2->QuScore = $item['oppQualScore'];
			$Obj2->GrPosition = $item['oppQualRank'];
			$Obj2->Score = ($item['oppStatus']==1 ? 'DSQ-':'') . ($section['meta']['matchMode'] ? $item['oppSetScore'] : $item['oppScore']);
			$Obj2->FinTie = $item['oppTie'];
			$Obj2->FinTiebreak = $item['oppTiebreak'];
			$Obj2->SetPoints = $item['oppSetPoints'];
			$Obj2->oppSetPoints = $item['setPoints'];
			$Obj2->OppScore = ($section['meta']['matchMode'] ? $item['setScore'] : $item['score']);
			$Obj2->OppTie = $item['tie'];
			$Obj2->FSTarget = ($ShowTargetNo ? $item['oppTarget'] : '');
			$Obj2->ScheduledDate = ($ShowSchedule ? $item['scheduledDate'] : '');
			$Obj2->ScheduledTime = ($ShowSchedule ? $item['scheduledTime'] : '');
			$Obj2->Saved = $item['saved'] && ($section['meta']['firstPhase']==24 or $section['meta']['firstPhase']==48) ? $rankData['meta']['saved'] : '';
			$Obj2->DrawMatch=$DrawMatch;
			$Obj2->ToDo = ($DrawMatch or $item['oppSaved']);

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

//debug_svela($PdfData->Events);

foreach($PdfData->Events as $Event => $Pages) {
	$Title=$Pages->Event;
	if($Pages->FirstToPrint) {
		// prints the quarters, medals etc...
		$pdf->setEvent($Pages->Event);
		$pdf->setPhase($Pages->Pages[0]->Phase);
		$pdf->SetDataHeader($Pages->Pages[0]->Header, $Pages->Pages[0]->HeaderWidth);
		$pdf->setDocUpdate($Pages->LastUpdate);
		$pdf->setOrisCode($Pages->Pages[0]->Code, $PdfData->Description);
		$pdf->AddPage();

		$pdf->CellHSp = 110/($Pages->FirstPhase==2 ? 3 : 4);
		$TopY=$pdf->lastY;

		$pdf->CellVSp = (($pdf->GetPageHeight()-15-$TopY)/(16*3)-0.1);
		$pdf->lastY = $TopY + $pdf->CellVSp;

		foreach($Pages->Pages[0]->FirstColumn as $item) {
			$pdf->FirstColumn($item);
		}

		$PhaseCounter=1;
		foreach($Pages->Pages[0]->OtherColumns as $Columns) {
			$pdf->lastY = $TopY + $pdf->CellVSp*($PhaseCounter<=1 ? 1:($PhaseCounter==2 ? 2.5:($PhaseCounter==3 ? 5.5 : 11.5)));
			foreach($Columns as $item) {
				$pdf->OtherColumns($PhaseCounter, $item->FinMatchNo, $item->FirstName, $item->Name, $item->Country, $item->Score, $item->FinTie, $item->FinTiebreak, $item->SetPoints, $item->OppScore, $item->OppTie, $item->FSTarget, $item->ScheduledDate, $item->ScheduledTime);
			}
			$PhaseCounter++;
		}

		$pdf->lastY = $TopY + ($Pages->FirstPhase>=4 ? 4.5 : 1.5)*$pdf->CellVSp;
		$pdf->PrintMedals($PhaseCounter,
			(empty($Pages->Medals["Gold"]->FirstName) ? "" : $Pages->Medals["Gold"]->FirstName), (empty($Pages->Medals["Gold"]->Name) ? "" : $Pages->Medals["Gold"]->Name),(empty($Pages->Medals["Gold"]->Country) ? "" : $Pages->Medals["Gold"]->Country),
			(empty($Pages->Medals["Silver"]->FirstName) ? "" : $Pages->Medals["Silver"]->FirstName), (empty($Pages->Medals["Silver"]->Name) ? "" : $Pages->Medals["Silver"]->Name),(empty($Pages->Medals["Silver"]->Country) ? "" : $Pages->Medals["Silver"]->Country),
			(empty($Pages->Medals["Bronze"]->FirstName) ? "" : $Pages->Medals["Bronze"]->FirstName), (empty($Pages->Medals["Bronze"]->Name) ? "" : $Pages->Medals["Bronze"]->Name),(empty($Pages->Medals["Bronze"]->Country) ? "" : $Pages->Medals["Bronze"]->Country));
	}

	if($Pages->OtherToPrint) {
		// prints the other pages
		for($pag=1; $pag<count($Pages->Pages); $pag++) {
			$pdf->setEvent($Pages->Event);
			$pdf->setPhase($Pages->Pages[$pag]->Phase);
			$pdf->SetDataHeader($Pages->Pages[$pag]->Header, $Pages->Pages[$pag]->HeaderWidth);
			$pdf->AddPage();
			$pdf->setOrisCode($Pages->Pages[$pag]->Code, $PdfData->Description);

			$pdf->CellHSp = 110/(ceil(log($Pages->FirstPhase,2))-1);
			$TopY=$pdf->lastY;

			$pdf->CellVSp = (($pdf->GetPageHeight()-15-$TopY)/(16*3)-0.1);
			$pdf->lastY = $TopY + $pdf->CellVSp;

			foreach($Pages->Pages[$pag]->FirstColumn as $item) {
				$pdf->FirstColumn($item);
			}

			foreach($Pages->Pages[$pag]->OtherColumns as $PhaseCounter => $Columns) {
				$pdf->lastY = $TopY + $pdf->CellVSp*($PhaseCounter<=1 ? 1:($PhaseCounter==2 ? 2.5:($PhaseCounter==3 ? 5.5 : 11.5)));
				foreach($Columns as $item) {
					$pdf->OtherColumns($PhaseCounter, $item->FinMatchNo, $item->FirstName, $item->Name, $item->Country, $item->Score, $item->FinTie, $item->FinTiebreak, $item->SetPoints, $item->OppScore, $item->OppTie, $item->FSTarget, $item->ScheduledDate, $item->ScheduledTime);
				}
			}
		}
	}
}

?>