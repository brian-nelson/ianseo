<?php

$ShowTargetNo = (isset($PdfData->ShowTargetNo) ? $PdfData->ShowTargetNo : true);
$ShowSchedule = (isset($PdfData->ShowSchedule) ? $PdfData->ShowSchedule : true);
$ShowSetArrows= (isset($PdfData->ShowSetArrows) ? $PdfData->ShowSetArrows : true);
$pdf->pushMargins();

$pdf->SetLineWidth(0.125);
$pdf->setCellPaddings(0.5,0,0.5,0);

//error_reporting(E_ALL);

//Costanti
$PaginaUtile=$pdf->GetPageHeight()-47;
$InitMargin=10;
$LarghezzaPagina=$pdf->GetPageWidth()-2*$InitMargin;

$Cella=4.5;
$CellaNomi=3;

$MisPos=5; //5
$MisName=30; //20
$MisScore=7; //6.5
$MisTie=7;

$FirstPage=true;

foreach($PdfData->rankData['sections'] as $Event => $section) {
	// New Event
	if(!$FirstPage) $pdf->addPage();
	$FirstPage=false;

	$CellVSp = 0;
	$AddSize=0;
	$LineY=0;
	//MArgine Sinistro Iniziale
	$CellL = $InitMargin;

	$CellHSp=5;
	$tmpCell= ($LarghezzaPagina - $MisPos - ceil(log($section['meta']['firstPhase'],2))*$CellHSp) / (6*(ceil(log($section['meta']['firstPhase'],2))+1));
	$MisName=$tmpCell*4; //20
	$MisScore=$tmpCell; //6.5
	$MisTie=$tmpCell;

//	if($CellHSp>20) {
//		$AddSize=($CellHSp-20)/2;
//		$CellHSp -= ($AddSize* ($section['meta']['firstPhase']==4 ? 1.5 : 2));
//	}
	//Spaziatura orizzontale delle celle

	$FirstPhase=true;
	$Componenti=($section['meta']['maxTeamPerson'] ? $section['meta']['maxTeamPerson'] : 3);
    if($section['meta']['firstPhase']>8) {
        $Componenti=0;
    }

    $CellHeight=$Cella+($Componenti*$CellaNomi);

	$pdf->SetXY($InitMargin,25+($PaginaUtile-(2*$section['meta']['firstPhase']*$CellHeight))/$section['meta']['firstPhase']/2);
	$pdf->SetFont($pdf->FontStd,'B',12);
	$pdf->Cell($LarghezzaPagina, $Cella , $section['meta']['eventName'],0,0,'R');
	if ($section['meta']['printHead']) {
		$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->SetXY($InitMargin,$pdf->gety()+5);
		$pdf->Cell($LarghezzaPagina,$Cella , $section['meta']['printHead'],0,0,'R');
		$pdf->SetFont($pdf->FontStd,'B',12);
	}

	foreach($section['phases'] as $Phase => $Items) {
		if($Phase==1) {
			$pdf->SetY($pdf->GetY()-(2*$CellHeight));						// Per la finalina riposiziono la griglia
			$CellL -= (($MisName+$MisScore+$MisTie) + $CellHSp);
			$pdf->SetX($CellL);
		}
		$pdf->SetLeftMargin($CellL);						// Setto il margine per queste colonne
		//Calcolo il margine per le successive
		// se è la prima colonna, prevedo la misura anche della cella pos
		if($FirstPhase) $CellL += ($MisPos);
		$CellL += ($MisName+$MisScore+$MisTie) + $CellHSp;
		// Spaziatura Verticale per la fase in corso
		$CellVSp = ($PaginaUtile-(2*max(1,$Phase)*$CellHeight))/max(1,$Phase);

		// Offset della prima cella
		$pdf->SetY(25+($CellVSp/2));

		// print Phase
		foreach($Items['items'] as $Match) {
			$LineXstart=$pdf->GetX();
			$OrgY=$pdf->GetY();

			// Target Numbers
			if($ShowTargetNo && ($Match['target'] or $Match['oppTarget']) and !($Match['score'] or $Match['setScore']) and !($Match['oppScore'] or $Match['oppSetScore']) and !$Match['tie'] and !$Match['oppTie']) {
			   	$pdf->SetFont($pdf->FontStd,'I',7);
				if($FirstPhase) {
					$pdf->SetX($LineXstart-7);
					$pdf->Cell(7, $CellHeight, "T# " . $Match['target'], 0, 0, 'R', 0);
					$pdf->SetXY($LineXstart-7, $OrgY+$CellHeight);
					$pdf->Cell(7, $Cella+($Componenti*$CellaNomi), "T# " . $Match['oppTarget'], 0, 0, 'R', 0);
				} else {
					$pdf->SetXY($LineXstart, $OrgY-3);
					$pdf->Cell($MisName+$AddSize+$MisScore, 2.5, "T# " . $Match['target']. ($ShowSchedule ? ' ' . $Match['scheduledDate']  . " @ "  . $Match['scheduledTime'] : ''), 0, 0, 'L', 0);
					$pdf->SetXY($LineXstart, $OrgY+ 2*$Cella);
					$pdf->Cell($MisName+$AddSize+$MisScore, 2.5, "T# " . $Match['oppTarget'], 0, 0, 'L', 0);
				}
				$pdf->SetXY($LineXstart, $OrgY);
			}

			if($ShowSchedule && $Match['scheduledDate']!='00-00-0000'  && $Match['scheduledTime']!='00:00') {
				if($FirstPhase) {
					$pdf->SetFont($pdf->FontStd,'I',6);
					$pdf->SetXY($LineXstart, $OrgY-3);
					$pdf->Cell($MisPos + $MisName + $AddSize + $MisScore, 2.5, $Match['scheduledDate']  . " @ "  . $Match['scheduledTime'] , 0, 0, 'L', 0);
					$pdf->SetXY($LineXstart, $OrgY);
				} elseif(!$ShowTargetNo) {
				   	$pdf->SetFont($pdf->FontStd,'I',7);
					$pdf->SetXY($LineXstart, $OrgY-3);
					$pdf->Cell($MisPos + $MisName + $AddSize + $MisScore, 2.5, $Match['scheduledDate']  . " @ "  . $Match['scheduledTime'] , 0, 0, 'L', 0);
					$pdf->SetXY($LineXstart, $OrgY);
				}
			}

			if($Phase<2) {
				// if Medals write names
			   	$pdf->SetFont($pdf->FontStd,'B',7);
				$pdf->SetXY($LineXstart, $OrgY-$Cella*1.5);
				$pdf->Cell($MisName+$MisScore+$AddSize, $Cella, ($Match['matchNo']==0 ? $PdfData->Final : $PdfData->Bronze), 0, 1, 'C', 0);
				$pdf->SetXY($LineXstart, $OrgY);
			}

			if($ShowSetArrows && ($Match["setPoints"] || $Match["oppSetPoints"])) {
				// metti le caselle di testo con gli scontri
				$pdf->SetXY($LineXstart + ($FirstPhase ? $MisPos : 0) + $MisName  + $MisScore + 3*$AddSize, $OrgY+2*($Cella+($FirstPhase? $Componenti*$CellaNomi : 0)));
				$pdf->SetFont($pdf->FontStd,'I',5.5);
				$pdf->SetLineStyle(array('color' => array(96)));
				$pdf->SetTextColor(96);
				$FinSetScore=explode('|', $Match['setPoints']);
				$OppSetScore=explode('|', $Match['oppSetPoints']);
				$StartXpos=$pdf->GetX()-3*max(count($FinSetScore), count($OppSetScore));
				$pdf->SetXY($StartXpos, $OrgY+ 2*($Cella+($FirstPhase? $Componenti*$CellaNomi : 0)));
				$smallCella=$Cella-1.5;
				foreach($FinSetScore as $score) $pdf->Cell(3, $smallCella, $score?$score:'', 1, 0);
				$pdf->SetXY($StartXpos, $OrgY+2*($Cella+($FirstPhase? $Componenti*$CellaNomi : 0))+$smallCella);
				foreach($OppSetScore as $score) $pdf->Cell(3, $smallCella, $score?$score:'', 1, 0);
				$pdf->SetLineStyle(array('color' => array(0)));
				$pdf->SetTextColor(0);
				$pdf->SetXY($LineXstart,$OrgY);
			}

			// Starting Position
			if($FirstPhase) {
			   	$pdf->SetFont($pdf->FontStd,'B',9);
				$pdf->Cell($MisPos, $CellHeight, $Match['position'], 1, 0, 'C', 0);
				$pdf->SetXY($LineXstart, $OrgY+$CellHeight);
				$pdf->Cell($MisPos, $CellHeight, $Match['oppPosition'], 1, 0, 'C', 0);
				$pdf->SetY($OrgY, false);
			}

			// Nation
			$MyX = $pdf->GetX();
			if($FirstPhase) {
			   	$pdf->SetFont($pdf->FontStd, 'B', 8);
			   	$pdf->Cell($MisName+$AddSize, $Cella, $Match['countryName'], 'TLR', 0, 'L', 0);
				$pdf->SetFont($pdf->FontStd,'',6);
				//Components
				for($n=0; $n<$Componenti; $n++) {
					$name=empty($section['athletes'][$Match['teamId']][$Match['subTeam']][$n]['athlete']) ? '' : $section['athletes'][$Match['teamId']][$Match['subTeam']][$n]['athlete'];
					$pdf->SetXY($MyX, $OrgY+$Cella+$CellaNomi*$n);
					$pdf->Cell($CellaNomi, $CellaNomi,  '', 'L' . ($n==$Componenti-1 ? 'B': ''), 0, 'L', 0);
					$pdf->Cell($MisName - $CellaNomi + $AddSize, $CellaNomi,  $name, 'R' . ($n==$Componenti-1 ? 'B': ''), 0, 'L', 0);
				}
				$pdf->SetXY($MyX, $OrgY+$CellHeight);
			   	$pdf->SetFont($pdf->FontStd, 'B', 8);
			   	$pdf->Cell($MisName+$AddSize, $Cella, $Match['oppCountryName'], 'TLR'.($Componenti==0 ? 'B' : ''), 0, 'L', 0);
				$pdf->SetFont($pdf->FontStd,'',6);
				//Components
				for($n=0; $n<$Componenti; $n++) {
					$name=empty($section['athletes'][$Match['oppTeamId']][$Match['oppSubTeam']][$n]['athlete']) ? '' : $section['athletes'][$Match['oppTeamId']][$Match['oppSubTeam']][$n]['athlete'];
					$pdf->SetXY($MyX, $OrgY+$CellHeight+$Cella+$CellaNomi*$n);
					$pdf->Cell($CellaNomi, $CellaNomi,  '', 'L' . ($n==$Componenti-1 ? 'B': ''), 0, 'L', 0);
					$pdf->Cell($MisName - $CellaNomi + $AddSize, $CellaNomi,  $name, 'R' . ($n==$Componenti-1 ? 'B': ''), 0, 'L', 0);
				}
				$pdf->SetY($OrgY, false);
			} else {
			   	$pdf->SetFont($pdf->FontStd, '', 8);
			   	$pdf->Cell($MisName+$AddSize, $Cella, $Match['countryName'], 1, 0, 'L', 0);		//Squadra
				$pdf->SetXY($MyX, $OrgY+$CellHeight);
			   	$pdf->Cell($MisName+$AddSize, $Cella, $Match['oppCountryName'], 1, 0, 'L', 0);		//Squadra
				$pdf->SetY($OrgY, false);
			}

			$pdf->SetFont($pdf->FontStd,'',6);
			$MyX = $pdf->GetX();
			if($Match['tie']==2) {
			   	$pdf->SetFont($pdf->FontStd,'',5);
				$pdf->Cell($MisScore, $CellHeight, $PdfData->Bye, 1, 0, 'C', 0);	//Bye
			} elseif($Match['tie'] || ($section['meta']['matchMode']=='1' ? $Match['setScore'] || $Match['oppSetScore'] : $Match['score'] || $Match['oppScore']) ) {
			   	$pdf->SetFont($pdf->FontStd,'',8);
				$pdf->Cell($MisScore, $CellHeight, ($section['meta']['matchMode']=='1' ? $Match['setScore'] : $Match['score']) . " ", 1, 0, 'R', 0);	//Punteggio
			} else {
				$pdf->Cell($MisScore, $CellHeight, '', 1, 0, 'R', 0);	//Niente
			}
			$pdf->SetXY($MyX, $OrgY+$CellHeight);
			if($Match['oppTie']==2) {
			   	$pdf->SetFont($pdf->FontStd,'',5);
				$pdf->Cell($MisScore, $CellHeight, $PdfData->Bye, 1, 0, 'C', 0);	//Bye
			} elseif($Match['oppTie'] || ($section['meta']['matchMode']=='1' ? $Match['setScore'] || $Match['oppSetScore'] : $Match['score'] || $Match['oppScore']) ) {
			   	$pdf->SetFont($pdf->FontStd,'',8);
				$pdf->Cell($MisScore, $CellHeight, ($section['meta']['matchMode']=='1' ? $Match['oppSetScore'] : $Match['oppScore']) . " ", 1, 0, 'R', 0);	//Punteggio
			} else {
				$pdf->Cell($MisScore, $CellHeight, '', 1, 0, 'R', 0);	//Niente
			}
			$pdf->SetY($OrgY, false);


		   	$pdf->SetFont($pdf->FontStd,'B',10);

			$LineXend=$pdf->GetX();
			//Gestisco cosa scrivere nel tie
			$MyX = $pdf->GetX();
			$tieText = "";
			if($Match['tiebreakDecoded']) {
				$pdf->SetFont($pdf->FontStd,'',6);
				$tieText = 'T.'.$Match['tiebreakDecoded'];
			} elseif($Match['tie']==1) {
				$tieText = "+";
			}
			if($Match['irmText']) {
				$tieText .= $Match['irmText'];
			} elseif($Match['notes']) {
				$pdf->SetFont($pdf->FontStd,'B',6);
				$tieText .= $Match['notes'];
			}
			$pdf->Cell($MisTie, $CellHeight, $tieText, 0, 0, 'L', 0);
			$pdf->SetXY($MyX, $OrgY+$CellHeight);
			$tieText = "";
			if($Match['oppTiebreakDecoded']) {
				$pdf->SetFont($pdf->FontStd,'',6);
				$tieText = 'T.'.$Match['oppTiebreakDecoded'];
			} elseif($Match['oppTie']==1) {
				$tieText = "+";
			}
			if($Match['oppIrmText']) {
				$tieText .= $Match['oppIrmText'];
			} elseif($Match['oppNotes']) {
				$pdf->SetFont($pdf->FontStd,'B',6);
				$tieText .= $Match['oppNotes'];
			}
			$pdf->Cell($MisTie, $CellHeight, $tieText, 0, 0, 'L', 0);
			$pdf->SetY($OrgY, false);

			$pdf->Cell(0.1, $CellHeight, "", 0, 1, 'C', 0);		//A Capo

			// Lines
			if($Match['matchNo']>=2) {
				$pdf->Line($LineXend,$pdf->GetY(),$LineXend+$MisTie+($CellHSp/2),$pdf->GetY());
				if($LineY==0) {
					$LineY=$pdf->GetY();
				} else {
					$pdf->Line($LineXend+$MisTie+($CellHSp/2),$LineY,$LineXend+$MisTie+($CellHSp/2),$pdf->GetY());
					$LineY=0;
				}
			}

			if(!$FirstPhase && $Phase!=1)
				$pdf->Line($LineXstart,$pdf->GetY(),$LineXstart-($CellHSp/2),$pdf->GetY());

			$pdf->SetY($OrgY+$CellVSp+2*$CellHeight);
		}

		$FirstPhase=false;
		$CellHeight=$Cella;
	}
}

$pdf->SetAutoPageBreak(true);
$pdf->popMargins();


?>
