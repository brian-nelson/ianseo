<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('config.inc.php');
	require_once('Common/pdf/ResultPDF.inc.php');
	require_once('elab.php');

	$OldTournamentID=$_SESSION['TourId'];
	$OldOnlineId = $_SESSION['OnlineId'];
	$OldOnlineEventCode = $_SESSION['OnlineEventCode'];
	$OnlineAuth = $_SESSION['OnlineAuth'];
	$OnlineServices = $_SESSION['OnlineServices'];

	$TeamResults = array();
	$TeamPrint = null;
	$TeamSepResults = array();
	$TeamSepPrint = array();

	$TeamNames = getTeamList();
	$ResultsSq = getTeamsValue();
	$ResultsInd = getIndMatchesValue();
    $ResultsTeam = getTeamMatchesValue();

	//Carico i punteggi delle squadre
	foreach($ResultsSq as $keyGara=>$tmpGara) {
		foreach($TeamNames as $CoCode => $CoName) {
			if(!array_key_exists($CoCode,$TeamResults)) {
				$TeamResults[$CoCode]=0;
				$TeamPrint[$CoCode]=array(0,0,0,0,0,0);
				$TeamSepPrint[$keyGara][$CoCode][0]=0;
				$TeamPrint[$CoCode][$keyGara*2] = 0;
			}

			if(!empty($tmpGara[$CoCode]->TeScore)) {
				$TeamResults[$CoCode] += $tmpGara[$CoCode]->TeScore;
				$TeamPrint[$CoCode][$keyGara*2] = $tmpGara[$CoCode]->TeScore;
				$TeamSepResults[$keyGara][$CoCode] += $tmpGara[$CoCode]->TeScore;
				$TeamSepPrint[$keyGara][$CoCode][0] = $tmpGara[$CoCode]->TeScore;
			}
		}
	}

	//Carico i Punteggi degli individuali
	foreach($ResultsInd as $keyGara=>$tmpGara) {
		// initialize $TeamSepPrint
		foreach($TeamNames as $CoCode => $CoName) {
			if(empty($TeamSepPrint[$keyGara][$CoCode][0])) {
				$TeamSepPrint[$keyGara][$CoCode][0]=0;
				if(empty($TeamPrint[$CoCode][$keyGara*2])) $TeamPrint[$CoCode][$keyGara*2] = 0;
			}
		}
		foreach($tmpGara as $tmpEvento) {
			foreach($tmpEvento as $tmpLista) {
				if(!array_key_exists($tmpLista[2],$TeamResults)) {
					$TeamResults[$tmpLista[2]]=0;
					$TeamPrint[$tmpLista[2]]=array(0,0,0,0,0,0);
					$TeamSepResults[$keyGara][$tmpLista[2]]=0;
					$TeamSepPrint[$keyGara][$tmpLista[2]]=array(0,0,0,0,0,0);
				}
				$TeamResults[$tmpLista[2]] += $Bonus[$BonusDecode[$keyGara]][abs($tmpLista[0])];
				$TeamPrint[$tmpLista[2]][($keyGara*2)+1] += $Bonus[$BonusDecode[$keyGara]][abs($tmpLista[0])];
				$TeamSepResults[$keyGara][$tmpLista[2]] += $Bonus[$BonusDecode[$keyGara]][abs($tmpLista[0])];
				$TeamSepPrint[$keyGara][$tmpLista[2]][1] += $Bonus[$BonusDecode[$keyGara]][abs($tmpLista[0])];
			}
		}
	}

    //Carico i Punteggi delle squadre
    foreach($ResultsTeam as $keyGara=>$tmpGara) {
        // initialize $TeamSepPrint
        foreach($TeamNames as $CoCode => $CoName) {
            if(empty($TeamSepPrint[$keyGara][$CoCode][0])) {
                $TeamSepPrint[$keyGara][$CoCode][0]=0;
                if(empty($TeamPrint[$CoCode][$keyGara*2])) $TeamPrint[$CoCode][$keyGara*2] = 0;
            }
        }
        foreach($tmpGara as $tmpEvento) {
            foreach($tmpEvento as $tmpLista) {
                if(!array_key_exists($tmpLista[2],$TeamResults)) {
                    $TeamResults[$tmpLista[2]]=0;
                    $TeamPrint[$tmpLista[2]]=array(0,0,0,0,0,0);
                    $TeamSepResults[$keyGara][$tmpLista[2]]=0;
                    $TeamSepPrint[$keyGara][$tmpLista[2]]=array(0,0,0,0,0,0);
                }
                $TeamResults[$tmpLista[2]] += $Bonus[$BonusDecode[$keyGara]][abs($tmpLista[0])];
                $TeamPrint[$tmpLista[2]][($keyGara*2)+1] += $Bonus[$BonusDecode[$keyGara]][abs($tmpLista[0])];
                $TeamSepResults[$keyGara][$tmpLista[2]] += $Bonus[$BonusDecode[$keyGara]][abs($tmpLista[0])];
                $TeamSepPrint[$keyGara][$tmpLista[2]][1] += $Bonus[$BonusDecode[$keyGara]][abs($tmpLista[0])];
            }
        }
    }

	arsort($TeamResults,SORT_NUMERIC);

	CreateTourSession(getIdFromCode($headerCompetition));

	$pdf = new ResultPDF("Results",false);

//Tabellona Riassuntiva!!!
	$fieldW = 180/count($competitions);
	//Intestazione
	$pdf->SetFont($pdf->FontStd,'B',12);
	$pdf->SetXY(10,$pdf->GetY()+5);
	$pdf->Cell(8, 17,  "Pos", 1, 0, 'C', 1);
	$pdf->Cell(50, 17,  "Regione", 1, 0, 'C', 1);
	foreach ($competitions as $keygara=>$gara)	{
		$Select
			= "SELECT ".DoniField." Name, ToWhenFrom, ToWhenTo FROM Tournament WHERE ToId=" . StrSafe_DB(getIdFromCode($gara)) . " ";
		$Rs=safe_r_sql($Select);
		$pdf->SetFont($pdf->FontStd,'B',9);
		if (safe_num_rows($Rs)==1)
		{
			$tmp = safe_fetch($Rs);
			$pdf->Cell($fieldW, 7, $tmp->Name, 'TLR', 0, 'C', 1);
			$pdf->SetXY($pdf->GetX()-$fieldW,$pdf->GetY()+7);
			$pdf->SetFont($pdf->FontStd,'',7);
			$pdf->Cell($fieldW, 5, TournamentDate2String($tmp->ToWhenFrom, $tmp->ToWhenTo), 'BLR', 0, 'C', 1);
			$pdf->SetXY($pdf->GetX(),$pdf->GetY()-7);
		}
		else
			$pdf->Cell($fieldW, 12, $gara, 1, 0, 'C', 1);
	}
	$pdf->SetFont($pdf->FontStd,'B',12);
	$pdf->Cell(39, 17,  "Totale", 1, 1, 'C', 1);

	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->SetXY(68,$pdf->GetY()-5);
	foreach ($competitions as $keygara=>$gara)
	{
		$pdf->Cell($fieldW/2, 5, "Squadra", 1, 0, 'C', 1);
		$pdf->Cell($fieldW/2, 5, "Bonus O.R.", 1, 0, 'C', 1);
	}
	$pdf->Ln();


	$n=0;
	$rank=1;
	$OldTotale=-1;
	foreach($TeamResults as $Regione=>$Totale) {
		$n++;
		if($Totale!=$OldTotale) $rank=$n;
		$OldTotale=$Totale;
		$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->Cell(8, 6.5,  $rank, 'LTB', 0, 'C', 0);
		$pdf->Cell(8, 6.5,  substr($Regione,0,2), 'LTB', 0, 'C', 0);
		$pdf->SetFont($pdf->FontStd,'',10);
		$pdf->Cell(42, 6.5,  $TeamNames[$Regione], 'RTB', 0, 'L', 0);
		for($i=0; $i<count($competitions)*2; $i++)
			$pdf->Cell($fieldW/2, 6.5,  number_format($TeamPrint[$Regione][$i],0,",","."), 1, 0, 'R', 0);
		$pdf->SetFont($pdf->FontStd,'B',12);
		$pdf->Cell(39, 6.5,  number_format($Totale,0,",","."), 1, 1, 'R', 0);
	}
/*
	if(DoniSperateRank) {
		// TABELLE SEPARATE DI CLASSIFICA PER OGNI GARA
		$pdf->addpage();
		$OrgY=$pdf->getY();
		$OrgX=$pdf->getX();

		$fieldW = 90/count($competitions);
		$ColGap=5;
		$ColWidth=(277-((count($competitions)-1)*$ColGap))/count($competitions);

		$ColRank=$ColWidth*10/125;
		$ColRegi=$ColWidth*55/125;
		$ColTeam=$ColWidth*20/125;

		foreach ($competitions as $keygara=>$gara) {
			$Select = "SELECT ".DoniField." Name, ToWhenFrom, ToWhenTo FROM Tournament WHERE ToId=" . StrSafe_DB(getIdFromCode($gara)) . " ";
			$Rs=safe_r_sql($Select);

			//Intestazione
			$pdf->SetLeftMargin($OrgX);
			$pdf->setXY($OrgX, $OrgY);

			$pdf->SetFont($pdf->FontStd,'B',12);
			if (safe_num_rows($Rs)==1) {
				$tmp = safe_fetch($Rs);
				$pdf->Cell($ColWidth, 7, $tmp->Name, 'TLR', 1, 'C', 1);
				$pdf->SetFont($pdf->FontStd,'',7);
				$pdf->Cell($ColWidth, 5, TournamentDate2String($tmp->ToWhenFrom, $tmp->ToWhenTo), 'BLR', 1, 'C', 1);
			} else {
				$pdf->Cell($ColWidth, 12, $gara, 1, 1, 'C', 1);
			}

			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell($ColRank, 5,  "Pos", 1, 0, 'C', 1);
			$pdf->Cell($ColRegi, 5,  "Regione", 1, 0, 'C', 1);
			$pdf->Cell($ColTeam, 5, "Punti", 1, 0, 'C', 1);
			$pdf->Cell($ColTeam, 5, "Bonus", 1, 0, 'C', 1);
			$pdf->Cell($ColTeam, 5,  "Totale", 1, 1, 'C', 1);

			if(!empty($TeamSepResults[$keygara])) {
				arsort($TeamSepResults[$keygara],SORT_NUMERIC);

				$n=0;
				$rank=1;
				$OldTotale=-1;
				foreach($TeamSepResults[$keygara] as $Regione=>$Totale) {
					$n++;
					if($Totale!=$OldTotale) $rank=$n;
					$OldTotale=$Totale;
					$pdf->SetFont($pdf->FontStd,'B',10);
					$pdf->Cell($ColRank, 6.5,  $rank, 1, 0, 'C', 0);
					$pdf->Cell(8, 6.5,  substr($Regione,0,2), 'LTB', 0, 'C', 0);
					$pdf->Cell($ColRegi-8, 6.5,  $TeamNames[$Regione], 'RTB', 0, 'L', 0);
					$pdf->SetFont($pdf->FontStd,'',10);
					$pdf->Cell($ColTeam, 6.5,  number_format($TeamSepPrint[$keygara][$Regione][0],0,",","."), 1, 0, 'R', 0);
					$pdf->Cell($ColTeam, 6.5,  number_format($TeamSepPrint[$keygara][$Regione][1],0,",","."), 1, 0, 'R', 0);
					$pdf->SetFont($pdf->FontStd,'B',12);
					$pdf->Cell($ColTeam, 6.5,  number_format($Totale,0,",","."), 1, 1, 'R', 0);
				}

			}

			$OrgX+=$ColGap+$ColWidth;
		}
	}
*/
	$pdf->SetLeftMargin(10);

//Tabella Gara a Gara dei Bonus
	foreach ($competitions as $keygara=>$gara) {
		$pdf->AddPage("P");
		$pdf->SetFont($pdf->FontStd,'BI',14);
		$pdf->SetXY(10,$pdf->GetY()+5);

		$Select = "SELECT ToName FROM Tournament WHERE ToId=" . StrSafe_DB(getIdFromCode($gara));
		$Rs=safe_r_sql($Select);
		if (safe_num_rows($Rs)==1) {
			$pdf->Cell(190, 10,   safe_fetch($Rs)->ToName, 1, 1, 'L', 1);
		}
		$TopY=$pdf->GetY()+1;
		$EventCount=0;
		$MaxY=$TopY;

		foreach($ResultsInd[$keygara] as $keyEvento=>$tmpEvento) {
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->SetXY($pdf->GetX()+(95.5*($EventCount%2)),$TopY);
			$pdf->Cell(94.5, 6, $keyEvento, 1, 1, 'C', 1);
			$pdf->SetFont($pdf->FontStd,'B',8);
			$pdf->SetXY($pdf->GetX()+(95.5*($EventCount%2)),$pdf->GetY());
			$pdf->Cell(10, 4, "Pos.", 1, 0, 'C', 1);
			$pdf->Cell(40, 4, "Nome", 1, 0, 'L', 1);
			$pdf->Cell(34, 4, "Regione", 1, 0, 'L', 1);
			$pdf->Cell(10.5, 4, "Punti", 1, 1, 'R', 1);

			$n=1;
			foreach($tmpEvento as $tmpLista) {
				if(DoniLimit and $n++ > DoniLimit) continue;
				$pdf->SetFont($pdf->FontStd,'B',8);
				$pdf->SetXY($pdf->GetX()+(95.5*($EventCount%2)),$pdf->GetY());
				$pdf->Cell(10, 4, ($tmpLista[0]<0 ? '*':'') . abs($tmpLista[0]) . ($tmpLista[0]<0 ? '*':''), 1, 0, 'C', 0);
				$pdf->SetFont($pdf->FontStd,'',8);
				$pdf->Cell(40, 4, $tmpLista[1], 1, 0, 'L', 0);
				$pdf->SetFont($pdf->FontStd,'',8);
				$pdf->Cell(34, 4, substr($tmpLista[2],0,2) . ". " . $TeamNames[$tmpLista[2]], 1, 0, 'L', 0);
				$pdf->SetFont($pdf->FontStd,'',8);
				$pdf->Cell(10.5, 4, $Bonus[$BonusDecode[$keygara]][abs($tmpLista[0])], 1, 1, 'R', 0);

			}

			$EventCount++;
			if($EventCount==2 || $EventCount==4 ) {
				$TopY=max($pdf->GetY(),$MaxY)+1;
			}
			$MaxY=$pdf->GetY();
		}

        foreach($ResultsTeam[$keygara] as $keyEvento=>$tmpEvento) {
            $pdf->SetFont($pdf->FontStd,'B',10);
            $pdf->SetXY($pdf->GetX()+(95.5*($EventCount%2)),$TopY);
            $pdf->Cell(94.5, 6, $keyEvento, 1, 1, 'C', 1);
            $pdf->SetFont($pdf->FontStd,'B',8);
            $pdf->SetXY($pdf->GetX()+(95.5*($EventCount%2)),$pdf->GetY());
            $pdf->Cell(10, 4, "Pos.", 1, 0, 'C', 1);
            $pdf->Cell(30, 4, "Regione", 1, 0, 'L', 1);
            $pdf->Cell(35, 4, "Nomi", 1, 0, 'L', 1);
            $pdf->Cell(9.5, 4, "Qual.", 1, 0, 'R', 1);
            $pdf->Cell(10, 4, "Bonus", 1, 1, 'R', 1);

            $n=1;
            foreach($tmpEvento as $tmpLista) {
                if(DoniLimit and $n++ > DoniLimit) continue;
                $pdf->SetFont($pdf->FontStd,'B',8);
                $pdf->SetXY($pdf->GetX()+(95.5*($EventCount%2)),$pdf->GetY());
                $pdf->Cell(10, 4, ($tmpLista[0]<0 ? '*':'') . abs($tmpLista[0]) . ($tmpLista[0]<0 ? '*':''), 1, 0, 'C', 0);
                $pdf->SetFont($pdf->FontStd,'',8);
                $pdf->Cell(30, 4, substr($tmpLista[2],0,2) . ". " . $TeamNames[$tmpLista[2]], 1, 0, 'L', 0);
                $pdf->SetFont($pdf->FontStd,'',8);
                $pdf->Cell(35, 4, $tmpLista[1], 1, 0, 'L', 0);
                $pdf->SetFont($pdf->FontStd,'',8);
                $pdf->Cell(9.5, 4, $tmpLista[3], 1, 0, 'R', 0);
                $pdf->SetFont($pdf->FontStd,'',8);
                $pdf->Cell(10, 4, $Bonus[$BonusDecode[$keygara]][abs($tmpLista[0])], 1, 1, 'R', 0);

            }

            $EventCount++;
            if($EventCount==2 || $EventCount==4 ) {
                $TopY=max($pdf->GetY(),$MaxY)+1;
            }
            $MaxY=$pdf->GetY();
        }
		$pdf->SetFont($pdf->FontStd,'BI',8);
		$pdf->Cell(190, 10,   "*##* Indica la posizione MINIMA acquisibile alla fase attuale. I bonus sono calcolati sulla base di questa posizione", 0, 0, 'L', 0);
	}

	$pdf->Output();
	CreateTourSession($OldTournamentID);
	$_SESSION['OnlineId'] = $OldOnlineId ;
	$_SESSION['OnlineEventCode'] = $OldOnlineEventCode;
	$_SESSION['OnlineAuth'] = $OnlineAuth;
	$_SESSION['OnlineServices'] = $OnlineServices;
