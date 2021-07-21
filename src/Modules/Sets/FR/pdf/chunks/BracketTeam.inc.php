<?php
require_once('Common/Lib/Fun_Phases.inc.php');

//error_reporting(E_ALL);
$rankData=$PdfData->rankData;
$parentList = array();

$ShowTargetNo = (isset($PdfData->ShowTargetNo) ? $PdfData->ShowTargetNo : true);
$ShowSchedule = (isset($PdfData->ShowSchedule) ? $PdfData->ShowSchedule : true);
$ShowSetArrows= (isset($PdfData->ShowSetArrows) ? $PdfData->ShowSetArrows : true);

$pdf->pushMargins();

$pdf->SetLineWidth(0.125);
$pdf->setCellPaddings(0.5,0,0.5,0);

//Costanti
$PaginaUtile=$pdf->GetPageHeight()-56; // 35 top margin + 20 bottom margin
if($ShowSetArrows) $PaginaUtile-=6;

$InitMargin=10;
$LarghezzaPagina=$pdf->GetPageWidth()-2*$InitMargin;

$Cella=3;

$MisPos=3.5; //5
$MisCountry=10; //7.5
$MisName=20; //20
$MisScore=5; //6.5
$MisTie=5;

$ShowNation=0;

$FirstPage = true;

// get the children tree

$Children=array();
foreach($rankData['sections'] as $Event => $section) {
	$Children[$Event][valueFirstPhase($section['meta']['firstPhase'])/2]=false;
	if($section['meta']['parent']) {
		$Children[$section['meta']['parent']][$section['meta']['firstPhase']]=true;
	}
}

foreach($rankData['sections'] as $Event => $section) {
	if($section['meta']['parent']=='') {

		$numCols = min(6, PhaseLog(min(32, $section['meta']['firstPhase'])) + 1);
		$BlockLen = min(60, ($LarghezzaPagina - $MisPos - ($ShowNation ? 0 : $MisCountry) - 8 * ($numCols - 1)) / $numCols);

		$SpaceBetween=($LarghezzaPagina - $MisPos - $BlockLen*$numCols) / $numCols;

	}


	if($section['meta']['parent']=='' OR !array_key_exists($section['meta']['parent'],$parentList)) {
		// this is the "main" event or a parent event
		// Variabile per gestire il cambio di Evento
		$MyEventPhase = -1;
		$PrintCountry = false;

		// Variabili di Dimensionamento
		$CellVSp = 0;
		$CellL = $InitMargin;
		$pdf->SetLeftMargin($InitMargin);
		$LineY = 0;
		$AddSize = 0;

		$numCols = min(6, ceil(log(min(32, $section['meta']['firstPhase']), 2)) + 1);
		$numRows = min(32, valueFirstPhase($section['meta']['firstPhase']));
		if ($numRows == 24) $numRows = 32;

		$CellHSp = 8;
		$BlockLen = min(60, ($LarghezzaPagina - $MisPos - ($ShowNation ? 0 : $MisCountry) - $CellHSp * ($numCols - 1)) / $numCols);
		if ($ShowNation) {
			$CellBit = $BlockLen / 8;
			$MisCountry = $CellBit * 2; //7.5
			$MisName = $CellBit * 4; //20
			$MisScore = $CellBit; //6.5
			$MisTie = $CellBit;
		} else {
			$CellBit = $BlockLen / 6;
			$MisName = $CellBit * 4; //20
			$MisScore = $CellBit; //6.5
			$MisTie = $CellBit;
		}

		//Cambio Pagina Per ogni nuovo Evento
		if (!$FirstPage) $pdf->AddPage();
		$FirstPage = false;

		// print headers
		$pdf->SetY(45-2*$Cella);
		$pdf->SetFont($pdf->FontStd,'B',7);
		$pdf->cell($BlockLen+$MisPos+$MisCountry-$MisTie, 0, $section['meta']['phaseNames'][$section['meta']['firstPhase']], '', 0, 'C');

		if($start=PhaseLog(min(32, $section['meta']['firstPhase']))-1) {
			foreach(range($start, 1, -1) as $col) {
				$pdf->cell($CellHSp+$MisTie, 0, '');
				$pdf->cell($BlockLen-$MisTie, 0, $section['meta']['phaseNames'][namePhase($section['meta']['firstPhase'], pow(2,$col))], '', 0, 'C');
			}
		}
		$pdf->ln();

		$rowPosition = -1;
		$rowCounter = 0;

		// Ho un nuovo Evento
		// Riazzero tutte le variabili
		$rowNeedRewind = ($section['meta']['firstPhase'] > 32);

		//Spaziatura orizzontale delle celle

		$pdf->SetXY($InitMargin, 30);
		$pdf->SetFont($pdf->FontStd, 'B', 10);
		$pdf->Cell($LarghezzaPagina, $Cella, $section['meta']['eventName'], 0, 0, 'R');
		if ($section['meta']['printHead']) {
			$pdf->SetFont($pdf->FontStd, 'B', 8);
			$pdf->SetXY($InitMargin, 35);
			$pdf->Cell($LarghezzaPagina, $Cella, $section['meta']['printHead'], 0, 0, 'R');
			$pdf->SetFont($pdf->FontStd, 'B', 10);
		}

		$FirstPhase = true;
		$DateShown = false;
		$DateFlag = false;

		$CellVSp = min(50, ($PaginaUtile -20 - $Cella * 2 * min(32, max(1, $numRows))) / max(1, min(32, $numRows) - 1));

		$OffsetY = 0;

		$ExtraPhase = '';
		if ($section['meta']['firstPhase'] > 32) {
			$section['phases'][33] = $section['phases'][32];
			krsort($section['phases'], SORT_NUMERIC);
		}
	} else {
		$pdf->setPage($parentList[$section['meta']['parent']][$section['meta']['firstPhase']][0]);

		$CellL = $parentList[$section['meta']['parent']][$section['meta']['firstPhase']][1];

		$CellVSp = $parentList[$section['meta']['parent']][$section['meta']['firstPhase']][2];
		$MisPos = $parentList[$section['meta']['parent']][$section['meta']['firstPhase']][3];
		$MisCountry = $parentList[$section['meta']['parent']][$section['meta']['firstPhase']][4];
		$MisName = $parentList[$section['meta']['parent']][$section['meta']['firstPhase']][5];
		$MisScore = $parentList[$section['meta']['parent']][$section['meta']['firstPhase']][6];
		$MisTie = $parentList[$section['meta']['parent']][$section['meta']['firstPhase']][7];
		$OffsetY = $parentList[$section['meta']['parent']][$section['meta']['firstPhase']][8] - 45 + $CellVSp + (2*$Cella);
		$FirstPhase = false;
		$tmpX = $pdf->GetX();
		$tmpY = $pdf->GetY();
		$pdf->SetXY($InitMargin, $OffsetY+45 -(2*$Cella));
		$pdf->SetFont($pdf->FontStd, '', 9);
		$pdf->SetTextColor(255,0,0);
		$pdf->Cell($LarghezzaPagina - $InitMargin, $Cella, $section['meta']['eventName'], 0, 0, 'R');
		$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 0)));
		$RectX=$CellL-($MisPos*0.5);
		$RectW=$LarghezzaPagina - ($CellL - $InitMargin) - $MisPos;
		$RectH=(2*$Cella+$CellVSp)*($section['meta']['firstPhase']-1)+($CellVSp+2*$Cella);
		if(!empty($section['meta']['noRealPhase'])) {
			$RectX+=($MisName + ($PrintCountry ? $MisCountry : 0) + $MisScore + $MisTie) + $CellHSp;
			$RectW-=($MisName + ($PrintCountry ? $MisCountry : 0) + $MisScore + $MisTie) + $CellHSp;
			$RectH=$RectH/2;
		}
		//$pdf->Rect($CellL-($MisPos*0.5), $OffsetY + 45 - (2*$Cella), $LarghezzaPagina - ($CellL - $InitMargin) - $MisPos, (2*$Cella+$CellVSp)*($section['meta']['firstPhase']-1)+($CellVSp+$Cella));
		$pdf->Rect($RectX, $OffsetY + 45 - (2*$Cella), $RectW, $RectH);
		$pdf->SetLineStyle(array('width' => 0.1, 'cap' => 0, 'join' => 0, 'dash' => 0, 'color' => array(0, 0, 0)));
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY($tmpX,$tmpY);
	}

	foreach($section['phases'] as $Phase => $Items) {
		$CellVSpUsed=$CellVSp;
		//if($Phase!=valueFirstPhase($section['meta']['firstPhase']) and empty($Children[$section['meta']['parent']][$section['meta']['firstPhase']])) {
		//	$CellVSpUsed=$CellVSp*2 + $Cella*2;
		//}

		if($Phase==32) {
			$CellL = $InitMargin;					// Setto il margine per queste colonne
		}
		// Setto il margine per queste colonne
		$pdf->SetLeftMargin($CellL);						// Setto il margine per queste colonne
		//Calcolo il margine per le successive
		if($FirstPhase) {
			$PrintCountry = false;							//Alla prima colonna stampo la nazione
			$CellL += ($MisPos);								// se è la prima colonna, prevedo la misura anche della $Cella pos
		} else {
			//Dalla Seconda decido che Farmene della colonna delle nazioni
			$PrintCountry = ($ShowNation==1);
		}


		if(!empty($section['meta']['noRealPhase']) and $Phase==$section['meta']['firstPhase']) {
			// draws nothing
			//$parentList[$Event][$Phase] = array($pdf->getPage(), $CellL, $CellVSpUsed, $MisPos, $MisCountry, $MisName, $MisScore, $MisTie,0);
			$CellL += ($MisName + ($PrintCountry ? $MisCountry : 0) + $MisScore + $MisTie) + $CellHSp;
		} else {
			$parentList[$Event][$Phase] = array($pdf->getPage(), $CellL, $CellVSpUsed, $MisPos, $MisCountry, $MisName, $MisScore, $MisTie,0);
			// Offset della prima $Cella
			$pdf->SetY(45+$OffsetY);
			$CellL += ($MisName + ($PrintCountry ? $MisCountry : 0) + $MisScore + $MisTie) + $CellHSp;

			foreach($Items['items'] as $Match) {
				// flag for the 8 "saved"
				$DrawMatch = !( ($section['meta']['firstPhase']==48 or $section['meta']['firstPhase']==24) && $Phase>=32 && ($Match['saved'] or $Match['oppSaved']) );


				if($Phase==0) {
					// Per la finalina riposiziono la griglia
					$CellL -= 2*(($MisName+($PrintCountry ? $MisCountry : 0)+$MisScore+$MisTie) + $CellHSp);
					$pdf->SetLeftMargin($CellL);
					$pdf->SetX($CellL);
				}
				if($Phase!=1) {
					$OrgY=$pdf->GetY();
					$parentList[$Event][$Phase][8] = $OrgY;
				}

				//Stampo la Colonna di celle
				$LineXstart=$pdf->GetX();

				if(($Phase==33 and ($Match['saved'] or $Match['oppSaved'])) or ($Match['position']==0 and $Match['oppPosition']==0)) {
					// draws NOTHING

					$pdf->ln();		//A Capo
					// draw the lines between the phases
					if($Match['matchNo']>=2) {
						if($LineY==0) {
							$LineY=-1;
						} else {
							$LineY=0;
						}
					}

					// Disegno lo spazio prima del gruppo dopo
					$pdf->SetY($pdf->GetY()+$CellVSpUsed+$Cella);
					continue;
				}

				if($Match['matchNo']==0 || $Match['matchNo']==2) {
					// if Bronze or Gold writes the title
					$TitOffset=0;
					if($section['meta']['winnerFinalRank']==1) {
						$titleGold = $section['meta']['phaseNames'][0];
						$titleBronze = $section['meta']['phaseNames'][1];
					} else {
						$TitOffset=$Cella;
						$titleGold = $section['meta']['winnerFinalRank'].' vs '.($section['meta']['winnerFinalRank']+1);
						$titleBronze = ($section['meta']['winnerFinalRank']+2).' vs '.($section['meta']['winnerFinalRank']+3);
					}
					$pdf->SetXY($LineXstart, $OrgY+$TitOffset-$Cella*2);
					$pdf->SetFont($pdf->FontStd,'B',7);
					$pdf->Cell($MisName+($PrintCountry ? $MisCountry : 0)+$MisScore, $Cella, ($Match['matchNo']==0 ? $titleGold : $titleBronze), 0, 1, 'C', 0);
					$pdf->SetXY($LineXstart,$OrgY);
				}
				// Target Numbers
				if($ShowTargetNo && ($Match['target'] or $Match['oppTarget'])) {
					if($FirstPhase) {
						// Target numbers in front of the row
						$pdf->SetXY($LineXstart-7, $OrgY);
						$pdf->SetFont($pdf->FontStd,'I',6);
						if($Match['target']==$Match['oppTarget']) {
							$pdf->Cell(7, $Cella*2, "T# " . ltrim($Match['target'],'0'), 0, 0, 'R', 0);
						} else {
							$pdf->Cell(7, $Cella, "T# " . ltrim($Match['target'],'0'), 0, 0, 'R', 0);
							$pdf->SetXY($LineXstart-7, $OrgY+$Cella);
							$pdf->Cell(7, $Cella, "T# " . ltrim($Match['oppTarget'],'0'), 0, 0, 'R', 0);
						}
					} else {
						// up and down
						$pdf->SetXY($LineXstart, $OrgY-$Cella);
						$pdf->SetFont($pdf->FontStd,'I',6);
						$pdf->Cell($MisName + $AddSize + $MisScore + 2*$AddSize + ($PrintCountry ? $MisCountry : 0), 2.5, "T# " . ltrim($Match['target'],'0') . (($ShowSchedule and $Match['scheduledTime']!='00:00' and $Match['scheduledDate']!='00-00-0000') ?  ($DateShown ? '' : ' ' . $Match['scheduledDate']) . " @ "  . $Match['scheduledTime'] : '') , 0, 0, 'L', 0);
						if($Match['target']!=$Match['oppTarget']) {
							$pdf->SetXY($LineXstart,$OrgY+ 2*$Cella);
							$pdf->Cell($MisName + $AddSize + $MisScore + 2*$AddSize + ($PrintCountry ? $MisCountry : 0), 2.5, "T# " . ltrim($Match['oppTarget'],'0') , 0, 0, 'L', 0);
						}
						$DateFlag=($DateFlag or ($Match['scheduledTime']!='00:00' && $Match['scheduledDate']!='00-00-0000'));
					}
					$pdf->SetXY($LineXstart,$OrgY);
				}

				if($ShowSchedule and $Match['scheduledTime']!='00:00' and $Match['scheduledDate']!='00-00-0000' and (trim($Match['scheduledTime']) or trim($Match['scheduledDate'])) ) {
					if($FirstPhase && $section['meta']['firstPhase']<24) {
						$pdf->SetY($OrgY-3, false);
						$pdf->SetFont($pdf->FontStd,'I',6);
						$pdf->Cell($MisPos + $MisName + $AddSize + $MisScore + $AddSize + ($PrintCountry ? $MisCountry + $AddSize : 0), 2.5, $Match['scheduledDate'] . " @ "  . $Match['scheduledTime'] , 0, 0, 'L', 0);
						$DateShown=true;
					} elseif(!$FirstPhase && !$ShowTargetNo) {
						// up and down
						$pdf->SetY($OrgY-$Cella, false);
						$pdf->SetFont($pdf->FontStd,'I',6);
						$pdf->Cell($MisName + $AddSize + $MisScore + 2*$AddSize + ($PrintCountry ? $MisCountry : 0), 2.5, $Match['scheduledDate'] . " @ "  . $Match['scheduledTime'] , 0, 0, 'L', 0);
						$DateShown=true;
					}
					$pdf->SetXY($LineXstart,$OrgY);
				}

				if($ShowSetArrows
					&& (!$FirstPhase or $section['meta']['firstPhase']<32)
					&& ($Match['setPoints'] || $Match['oppSetPoints'])
					&& $DrawMatch
				) {
					// metti le caselle di testo con gli scontri
					$pdf->SetXY($LineXstart + ($FirstPhase ? $MisPos : 0) + ($PrintCountry ? $AddSize:0) + $MisName  + $MisScore + 3*$AddSize + ($PrintCountry ? $MisCountry : 0), $OrgY+2*$Cella);
					$pdf->SetFont($pdf->FontStd,'I',5.5);
					$pdf->SetLineStyle(array('color' => array(96)));
					$pdf->SetTextColor(96);
					$FinSetScore=explode('|', $Match['setPoints']);
					$OppSetScore=explode('|', $Match['oppSetPoints']);
					$StartXpos=$pdf->GetX()-3*max(count($FinSetScore), count($OppSetScore));
					$pdf->SetXY($StartXpos, $OrgY+ 2*$Cella);
					$smallCella=$Cella-0.5;
					foreach($FinSetScore as $score) $pdf->Cell(3, $smallCella, $score?$score:'', 1, 0);
					$pdf->SetXY($StartXpos, $OrgY+2*$Cella+$smallCella);
					foreach($OppSetScore as $score) $pdf->Cell(3, $smallCella, $score?$score:'', 1, 0);
					$pdf->SetLineStyle(array('color' => array(0)));
					$pdf->SetTextColor(0);
					$pdf->SetXY($LineXstart,$OrgY);
				}

				// Position
				if($FirstPhase) {
					$pdf->SetFont($pdf->FontStd,'B',6);
					if($DrawMatch or $Match['saved']) $pdf->Cell($MisPos, $Cella, $Match['position'] ? $Match['position'] : '', ($Match['position']!=0 || $Match['oppTie']==2  ? 1 : 0), 0, 'C', 0);
					$pdf->SetXY($LineXstart, $OrgY+$Cella);
					if($DrawMatch or $Match['oppSaved']) $pdf->Cell($MisPos, $Cella, $Match['oppPosition'] ? $Match['oppPosition'] : '', ($Match['oppPosition']!=0 || $Match['tie']==2  ? 1 : 0), 0, 'C', 0);
					$pdf->SetXY($LineXstart+$MisPos, $OrgY);
				}

				// Athlete
				$MyX=$pdf->GetX();
				$pdf->SetFont($pdf->FontStd,$Match['winner'] ? 'b' : '',6);
				if($DrawMatch or $Match['saved']) {
					$pdf->Cell($MisName + ($PrintCountry ? 2 :1 ) * $AddSize, $Cella, $Match['countryName'] . (($FirstPhase and $Match['countryCode']) ? ' ('.$Match['countryCode'].')' : '') , ($Match['position']!=0 || $Match['oppTie']==2 ? 1 : 0), 0, 'L', 0);
				} else {
					$pdf->SetFont($pdf->FontStd,'I',5.5);
					$pdf->Cell($MisName + $MisTie + $MisScore, $Cella,  $rankData['meta']['saved'] , 0, 0, 'L', 0);
					$pdf->SetFont('','');
				}
				$pdf->SetXY($MyX,$OrgY+$Cella);
				$pdf->SetFont($pdf->FontStd,$Match['oppWinner'] ? 'b' : '',6);
				if($DrawMatch or $Match['oppSaved']) {
					$pdf->Cell($MisName + ($PrintCountry ? 2 :1 ) * $AddSize, $Cella, $Match['oppCountryName'] . (($FirstPhase and $Match['oppCountryCode']) ?  ' ('.$Match['oppCountryCode'] . ')' : '') , ($Match['oppPosition']!=0 || $Match['tie']==2 ? 1 : 0), 0, 'L', 0);
				} else {
					$pdf->SetFont($pdf->FontStd,'I',5.5);
					$pdf->Cell($MisName + $MisTie + $MisScore, $Cella,  $rankData['meta']['saved'] , 0, 0, 'L', 0);
					$pdf->SetFont('','');
				}
				$pdf->SetFont($pdf->FontStd,'',6);
				$pdf->SetXY($MyX+$MisName,$OrgY);

				// COUNTRY
				if($PrintCountry) {
					$MyX=$pdf->GetX();
					$pdf->SetFont($pdf->FontStd,'',5);
					if($DrawMatch or $Match['saved']) $pdf->Cell($MisCountry, $Cella, $Match['countryCode'], ($Match['position']!=0 || $Match['oppTie']==2 ? 1 : 0), 0, 'C', 0);	//Nazione
					$pdf->SetXY($MyX,$OrgY+$Cella);
					if($DrawMatch or $Match['oppSaved']) $pdf->Cell($MisCountry, $Cella, $Match['oppCountryCode'], ($Match['oppPosition']!=0 || $Match['tie']==2 ? 1 : 0), 0, 'C', 0);	//Nazione
					$pdf->SetXY($MyX+$MisCountry,$OrgY);
				}

				// SCORE
				$MyX=$pdf->GetX();
				if($DrawMatch) {
					$pdf->SetFont($pdf->FontStd,'',6);
					if($Match['tie']==2) {
						$pdf->SetFont($pdf->FontStd,'',5);
						$pdf->Cell($MisScore + $AddSize, $Cella, $PdfData->Bye, 1, 0, 'C', 0);	//Bye
					} elseif($Match['tie'] || ($section['meta']['matchMode']=='1' ? $Match['setScore'] || $Match['oppSetScore'] : $Match['score'] || $Match['oppScore']) ) {
						$pdf->Cell($MisScore + $AddSize, $Cella, $section['meta']['matchMode']=='1' ? $Match['setScore'] : $Match['score'], 1, 0, 'R', 0);	//Punteggio
					} else {
						$pdf->Cell($MisScore + $AddSize, $Cella, '', ($Match['position']!=0 || $Match['oppTie']==2 ? 1 : 0), 0, 'R', 0);	//Niente
					}
					$pdf->SetXY($MyX,$OrgY+$Cella);
					if($Match['oppTie']==2) {
						$pdf->SetFont($pdf->FontStd,'',5);
						$pdf->Cell($MisScore + $AddSize, $Cella, $PdfData->Bye, 1, 0, 'C', 0);	//Bye
					} elseif($Match['oppTie'] || ($section['meta']['matchMode']=='1' ? $Match['setScore'] || $Match['oppSetScore'] : $Match['score'] || $Match['oppScore']) ) {
						$pdf->Cell($MisScore + $AddSize, $Cella, $section['meta']['matchMode']=='1' ? $Match['oppSetScore'] : $Match['oppScore'], 1, 0, 'R', 0);	//Punteggio
					} else {
						$pdf->Cell($MisScore + $AddSize, $Cella, '', ($Match['oppPosition']!=0 || $Match['tie']==2 ? 1 : 0), 0, 'R', 0);	//Niente
					}
				} elseif($Phase==32 or $Phase==24) {
					$pdf->Cell($MisScore + $AddSize, $Cella, '', ($Match['matchNo']%2 ? 'T' : 'B'), 0, 'R', 0);	//Niente

				}

				$pdf->SetXY($MisScore + $AddSize + $MyX, $OrgY);

				// TIE & SO
				$LineXend=$pdf->GetX();
				//Gestisco cosa scrivere nel tie
				$MyX=$pdf->getX();
				$tieText = "";
				if($Match['tiebreakDecoded']) {
					$pdf->SetFont($pdf->FontStd,'',6);
					$tieText = 'T.'.$Match['tiebreakDecoded'];
				} elseif($Match['tie']==1) {
					$tieText="+";
				}
				if($Match['notes']) {
					$pdf->SetFont($pdf->FontStd,'B',6);
					$tieText .= $Match['notes'];
				}
				$pdf->Cell($MisTie, $Cella, $tieText, 0, 0, 'L', 0);
				$tieText = "";
				$pdf->setXY($MyX, $OrgY+$Cella);
				if($Match['oppTiebreakDecoded']) {
					$pdf->SetFont($pdf->FontStd,'',6);
					$tieText = 'T.'.$Match['oppTiebreakDecoded'];
				} elseif($Match['oppTie']==1) {
					$tieText = "+";
				}
				if($Match['oppNotes']) {
					$pdf->SetFont($pdf->FontStd,'B',6);
					$tieText .= $Match['oppNotes'];
				}
				$pdf->Cell($MisTie, $Cella, $tieText, 0, 0, 'L', 0);		//No Tie
				$pdf->setY($OrgY, false);

				$pdf->ln();		//A Capo



				// draw the lines between the phases
				if($Match['matchNo']>=4) {
					if($Match['matchNo']==$Phase*2) {
						$LineY=$pdf->GetY();
					}
					if($DrawMatch or $Phase==32 or $Phase==24) {
						$pdf->Line($LineXend,$pdf->GetY(),$LineXend+($CellHSp*0.5)+$MisTie,$pdf->GetY());
					}
					if(($DrawMatch and $LineY!=-1) or $Phase==32 or $Phase==24) {
						$pdf->Line($LineXend+($CellHSp*0.5)+$MisTie,$LineY,$LineXend+($CellHSp*0.5)+$MisTie,$pdf->GetY());
					}
					$LineY=$pdf->GetY();
				}

				if(!$FirstPhase and ($section['meta']['firstPhase']!= $Phase) and (empty($section['meta']['noRealPhase']) or $Phase<$section['meta']['firstPhase']/2)) {
					$pdf->Line($LineXstart,$pdf->GetY(),$LineXstart-($CellHSp*0.5),$pdf->GetY());
				}
				// Disegno lo spazio prima del gruppo dopo
				$pdf->SetY($pdf->GetY() + $CellVSpUsed + $Cella);

			}

		}

		$FirstPhase=false;
		$DateShown=$DateFlag;
//LLedit		if($Phase>1) $OffsetY += $Cella + $CellVSpUsed/2;
//LLedit		$CellVSpUsed = 2*$CellVSpUsed + 2*$Cella;

	}
}

$pdf->SetAutoPageBreak(true);
$pdf->popMargins();
