<?php

$ShowTargetNo = (isset($PdfData->ShowTargetNo) ? $PdfData->ShowTargetNo : true);
$ShowSchedule = (isset($PdfData->ShowSchedule) ? $PdfData->ShowSchedule : true);
$ShowSetArrows= (isset($PdfData->ShowSetArrows) ? $PdfData->ShowSetArrows : true);

$arrayTitles = array("1/8\nElimin. Round§", "1/4\nElimin. Round§", "Semifinals§", "Finals§");

// Variabile per gestire il cambio di Evento
$PhaseCounter=-1;
$MyEventPhase = -1;

foreach($PdfData->rankData['sections'] as $Event => $section) {
	// Ho un nuovo Evento
	// preparation of the pages
	if(empty($PdfData->Events[$Event]))
		$PdfData->Events[$Event] = new stdClass();
	$PdfData->Events[$Event]->Event = $section['meta']['eventName'];
	$PdfData->Events[$Event]->FirstPhase = $section['meta']['firstPhase'];
	$PdfData->Events[$Event]->PrintHead = $section['meta']['printHead'];
	$PdfData->Events[$Event]->Medals = array("Gold"=>'-', "Silver"=>'-',"Bronze"=>'-');
	$PdfData->Events[$Event]->Header = array_merge(array("RR Rank /\nScore#","Bk\nNo#","NOC - Name"),array_slice($arrayTitles,(3-log($section['meta']['firstPhase'],2))));
	$PdfData->Events[$Event]->HeaderWidth = array_merge(array(array(7,11),10,37),array_fill(0,(log($section['meta']['firstPhase'],2)+2),125/(log($section['meta']['firstPhase'],2)+2)));
	$PdfData->Events[$Event]->NumComponenti = $section['meta']['maxTeamPerson'];

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
			$Obj1->TeScore = $Match['qualScore']; //
			$Obj1->GrPosition = $Match['position'];
			$Obj1->Score = ($Match['status']==1 ? 'DSQ-':'') . ($section['meta']['matchMode'] ? $Match['setScore'] : $Match['score']);
			$Obj1->TfTie = $Match['tie'];
			$Obj1->TfTieBreak = $Match['tiebreak'];
			$Obj1->TfTieBreakDecoded = $Match['tiebreakDecoded'];
			$Obj1->SetPoints = $Match['setPoints'];
			$Obj1->OppScore = $Match['oppScore'];
			$Obj1->OppTie = $Match['oppTie'];
			$Obj1->FSTarget = ($ShowTargetNo ? $Match['target'] : '');
			$Obj1->ScheduledDate = ($ShowSchedule ? $Match['scheduledDate'] : '');
			$Obj1->ScheduledTime = ($ShowSchedule ? $Match['scheduledTime'] : '');
			$Obj1->Componenti = $Comp1;

			$Obj2=new StdClass();
			$Obj2->PhaseCounter = $PhaseCounter;
			$Obj2->TfMatchNo = $Match['oppMatchNo'];
			$Obj2->Country = $Match['oppCountryCode'];
			$Obj2->Team = $Match['oppCountryName'];
			$Obj2->TeRank = $Match['oppQualRank'];
			$Obj2->FinRank = $Match['oppFinRank'];
			$Obj2->TeScore = $Match['oppQualScore']; //
			$Obj2->GrPosition = $Match['oppPosition'];
			$Obj2->Score = ($Match['oppStatus']==1 ? 'DSQ-':'') . ($section['meta']['matchMode'] ? $Match['oppSetScore'] : $Match['oppScore']);
			$Obj2->TfTie = $Match['oppTie'];
			$Obj2->TfTieBreak = $Match['oppTiebreak'];
			$Obj2->TfTieBreakDecoded = $Match['oppTiebreakDecoded'];
			$Obj2->SetPoints = $Match['oppSetPoints'];
			$Obj2->OppScore = $Match['score'];
			$Obj2->OppTie = $Match['tie'];
			$Obj2->FSTarget = ($ShowTargetNo ? $Match['oppTarget'] : '');
			$Obj2->ScheduledDate = ($ShowSchedule ? $Match['scheduledDate'] : '');
			$Obj2->ScheduledTime = ($ShowSchedule ? $Match['scheduledTime'] : '');
			$Obj2->Componenti = $Comp2;
			//Valuto cosa fare se è la prima colonna
			if($FirstPhase) {
				for($n=0; $n<$section['meta']['maxTeamPerson']; $n++) {
					//debug_svela($section);
					$Comp1[] = empty($section['athletes'][$Match['teamId']][$Match['subTeam']][$n]) ? array('','') : array(
						$section['athletes'][$Match['teamId']][$Match['subTeam']][$n]['backNo'],
						$section['athletes'][$Match['teamId']][$Match['subTeam']][$n]['athlete'],
						);
					$Comp2[] = empty($section['athletes'][$Match['oppTeamId']][$Match['oppSubTeam']][$n]) ? array('','') : array(
						$section['athletes'][$Match['oppTeamId']][$Match['oppSubTeam']][$n]['backNo'],
						$section['athletes'][$Match['oppTeamId']][$Match['oppSubTeam']][$n]['athlete'],
						);
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


foreach($PdfData->Events as $Event => $Pages) {

	$pdf->setEvent($Pages->Event);
	$pdf->setPhase($PdfData->Description);
	$pdf->setComment($Pages->PrintHead);

	$pdf->CellHSp = 125/(log($Pages->FirstPhase,2)+2);

	$pdf->SetDataHeader($Pages->Header, $Pages->HeaderWidth);
	$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
	$pdf->AddPage();

	$TopY=$pdf->lastY;

	$PhaseCounter=0;
	if($Pages->NumComponenti)
		$pdf->CellVSp = (($pdf->GetPageHeight()-$TopY)/(8*(4+2*$Pages->NumComponenti))-0.35);
	else
		$pdf->CellVSp = (($pdf->GetPageHeight()-$TopY)/(40)-0.25);

	$pdf->lastY = $TopY + ($PhaseCounter==0 ? 1:($PhaseCounter==1 ? (2+$Pages->NumComponenti-1):($PhaseCounter==2 ? (4+2*$Pages->NumComponenti-1):(8+4*$Pages->NumComponenti-1)))) * $pdf->CellVSp;

	//Resetto il carattere
	$pdf->SetFont('','',8);

	foreach($Pages->FirstColumn as $item) {
		$pdf->FirstColumnTeam($item->TfMatchNo, $item->Country, $item->Team, $item->TeRank, $item->TeScore, $item->GrPosition, $item->Score, $item->TfTie, $item->TfTieBreakDecoded, $item->SetPoints, $item->OppScore, $item->OppTie, $item->FSTarget, $item->ScheduledDate, $item->ScheduledTime, '', $item->Componenti, $Pages->NumComponenti);
	}

	foreach($Pages->OtherColumn as $Column) {
		$PhaseCounter++;
		if($Pages->NumComponenti)
			$pdf->CellVSp = (($pdf->GetPageHeight()-$TopY)/(8*(4+2*$Pages->NumComponenti))-0.35);
		else
			$pdf->CellVSp = (($pdf->GetPageHeight()-$TopY)/(40)-0.25);

		$pdf->lastY = $TopY + ($PhaseCounter==0 ? 1:($PhaseCounter==1 ? (2+$Pages->NumComponenti-1):($PhaseCounter==2 ? (4+2*$Pages->NumComponenti-1):(8+4*$Pages->NumComponenti-1)))) * $pdf->CellVSp;

		foreach($Column as $item) {
			$pdf->OtherColumnsTeam($PhaseCounter,
				$item->TfMatchNo,
				$item->Team,
				$item->Score,
				$item->TfTie, $item->TfTieBreakDecoded, $item->SetPoints, $item->OppScore,
				$item->OppTie, $item->FSTarget, $item->ScheduledDate, $item->ScheduledTime, '', $Pages->NumComponenti, $Pages->NumComponenti);
		}
	}

	$PhaseCounter++;
	//debug_svela($Pages);
	$pdf->PrintMedalsTeam($PhaseCounter, $Pages->Medals["Gold"], $Pages->Medals["Silver"], $Pages->Medals["Bronze"], $Pages->NumComponenti);
}



?>