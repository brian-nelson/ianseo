<?php
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Lib/Obj_RankFactory.php');
/*Posso avere:
 1) solo la fase e muove tutti gli eventi
 2) Fase e evento e li muove
 3) Matchno e evento e muove solo il matchno in question
*/


function move2NextPhase($Phase=NULL, $Event=NULL, $MatchNo=NULL)
{
//verifico i parametri
	if(is_null($Phase) && is_null($MatchNo))	//Devono esistere o la fase o il MatchNo
		return;
	if(is_null($Phase) && is_null($Event))		//Se non ho la Fase (e quindi ho il MatchNo) deve esistere l'evento
		return;

// Remember to check the saved from 64th to 16th!
	$CheckSaved=array();

//Verifico la situazione tiebreak
	$Select
		= "SELECT "
		. "f.FinEvent, EvMatchMode as MatchMode, f.FinMatchNo as MatchNo, f2.FinMatchNo as OppMatchNo,  EvFinalFirstPhase, "
		. "f.FinAthlete AS Athlete, f2.FinAthlete AS OppAthlete, "
		. "if(f.FinMatchNo>15, EvElimEnds, EvFinEnds) FinEnds, if(f.FinMatchNo>15, EvElimArrows, EvFinArrows) FinArrows, if(f.FinMatchNo>15, EvElimSO, EvFinSO) FinSO, "
		. "IF(f.FinDateTime>=f2.FinDateTime, f.FinDateTime, f2.FinDateTime) AS DateTime,"
		. "IF(EvMatchMode=0,f.FinScore,f.FinSetScore) AS Score, f.FinTie as Tie, f.FinTieBreak as TbString, IF(EvMatchMode=0,f2.FinScore,f2.FinSetScore) as OppScore, f2.FinTie as OppTie, f2.FinTieBreak as OppTbString, "
		. "f.FinArrowString as ArrString, f2.FinArrowString as OppArrString,  f.FinSetPoints as SetPoint, f2.FinSetPoints as OppSetPoint "
		. "FROM Finals AS f "
		. "INNER JOIN Finals AS f2 ON f.FinEvent=f2.FinEvent AND f.FinMatchNo=IF((f.FinMatchNo % 2)=0,f2.FinMatchNo-1,f2.FinMatchNo+1) AND f.FinTournament=f2.FinTournament "
		. "INNER JOIN Events ON f.FinEvent=EvCode AND f.FinTournament=EvTournament AND EvTeamEvent=0 ";
		if(!is_null($Phase))
			$Select .= "INNER JOIN Grids ON f.FinMatchNo=GrMatchNo AND GrPhase=" . StrSafe_DB($Phase) . " ";
		else
			$Select .= "INNER JOIN Grids ON f.FinMatchNo=GrMatchNo AND GrMatchNo=" . StrSafe_DB(($MatchNo % 2 == 0 ? $MatchNo:$MatchNo-1)) . " ";
		$Select .= "WHERE f.FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (f.FinMatchNo % 2)=0 ";

		if(!is_null($Event) && $Event!='')
			$Select .= "AND f.FinEvent=" . StrSafe_DB($Event) . " ";
		$Select .= "ORDER BY f.FinEvent, f.FinMatchNo ";
	//echo $Select;exit;
	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)>0) {
		while ($MyRow=safe_fetch($Rs)) {
			if(empty($CheckSaved[$MyRow->FinEvent])) $CheckSaved[$MyRow->FinEvent]=($MyRow->EvFinalFirstPhase==48 and $Phase==64);
			//Se uno dei due ATLETI è ZERO ed ENTRAMBI GLI SCORES sono a ZERO imposto il Bye
			if($MyRow->Athlete!= 0 && $MyRow->OppAthlete==0 && $MyRow->Score == 0 && $MyRow->OppScore==0) {
				$SqlUpdate = "UPDATE Finals SET FinTie=0, FinWinLose=0, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=". StrSafe_DB($MyRow->OppMatchNo) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']);
				safe_w_sql($SqlUpdate);
				$SqlUpdate = "UPDATE Finals SET FinTie=2, FinWinLose=1, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=". StrSafe_DB($MyRow->MatchNo) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']);
				safe_w_sql($SqlUpdate);
			} elseif($MyRow->Athlete== 0 && $MyRow->OppAthlete!=0 && $MyRow->Score == 0 && $MyRow->OppScore==0) {
				$SqlUpdate = "UPDATE Finals SET FinTie=0, FinWinLose=0, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=". StrSafe_DB($MyRow->MatchNo) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']);
				safe_w_sql($SqlUpdate);
				$SqlUpdate = "UPDATE Finals SET FinTie=2, FinWinLose=1, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=". StrSafe_DB($MyRow->OppMatchNo) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']);
				safe_w_sql($SqlUpdate);
			} elseif($MyRow->MatchMode!=0) {
			//Se non ho il BYE, rigestisco il numero di set vinti e aggiorno il conteggio del totale (se i punti set sono valorizzati & le arrowstring sono vuote)
				if(strlen(trim(str_replace("|","",$MyRow->SetPoint))) && strlen(trim(str_replace("|","",$MyRow->OppSetPoint))) && strlen(trim($MyRow->ArrString))==0 && strlen(trim($MyRow->OppArrString))==0) {
					$AthSets=explode("|",$MyRow->SetPoint);
					$OppSets=explode("|",$MyRow->OppSetPoint);
					$AthScore=0;
					$OppScore=0;
					$AthWin=0;
					$OppWin=0;
					if(count($AthSets) == count($OppSets)) {
						for($i=0;$i<count($AthSets);$i++) {
							if(intval($AthSets[$i])>intval($OppSets[$i])) {
								$AthScore += 2;
								$AthWin++;
							} elseif(intval($AthSets[$i])<intval($OppSets[$i])) {
								$OppScore += 2;
								$OppWin++;
							} elseif(intval($AthSets[$i])!=0 && intval($OppSets[$i])!=0) {
								$AthScore += 1;
								$OppScore += 1;
							}
						}
						$SqlUpdate = "UPDATE Finals SET FinWinLose=".($AthScore>$MyRow->FinEnds ? 1 : 0).", FinSetScore=" . StrSafe_DB($AthScore) . ", FinWinnerSet=" . StrSafe_DB($AthWin) . ", FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=" . StrSafe_DB($MyRow->MatchNo) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']);
						safe_w_sql($SqlUpdate);
						$MyRow->Score=$AthScore;
						$SqlUpdate = "UPDATE Finals SET FinWinLose=".($OppScore>$MyRow->FinEnds ? 1 : 0).", FinSetScore=" . StrSafe_DB($OppScore) . ", FinWinnerSet=" . StrSafe_DB($OppWin) . ", FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=" . StrSafe_DB($MyRow->OppMatchNo) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']);
						safe_w_sql($SqlUpdate);
						$MyRow->OppScore=$OppScore;
					}
				}
			}

			//Se uno dei due è diverso da ZERO e non sono uguali oppure se è a set e differiscono di più di un punto
			if(($MyRow->Score!= 0 || $MyRow->OppScore!=0) && $MyRow->Score != $MyRow->OppScore && ($MyRow->MatchMode!=0 && abs($MyRow->Score-$MyRow->OppScore)>1)) {
				//Azzero entrambi i flag di shootoff
				$SqlUpdate = "UPDATE Finals SET FinTie=0, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo IN (". StrSafe_DB($MyRow->MatchNo) . "," . StrSafe_DB($MyRow->OppMatchNo) . ") AND FinTournament=" . StrSafe_DB($_SESSION['TourId']);
				safe_w_sql($SqlUpdate);
			}

			// Se i punteggi sono uguali e diversi da ZERO entro nel dettaglio E le stringhe di frecce sono lunghe uguali e non vuote
			if($MyRow->Score!= 0
					and $MyRow->Score == $MyRow->OppScore
					and strlen(str_replace(' ', '', $MyRow->TbString))>0
					and strlen(str_replace(' ', '', $MyRow->TbString))==strlen(str_replace(' ', '', $MyRow->OppTbString))) {
				$WinnerId=-1;
				//Verifico le stringhe CASE INSENSITIVE - in questo momento me ne frego degli "*"
				if(ValutaArrowString($MyRow->TbString) < ValutaArrowString($MyRow->OppTbString)) {
				 	$WinnerId = $MyRow->OppMatchNo;	//OppTbString è maggiore di TbString --> il secondo vince
				} elseif(ValutaArrowString($MyRow->TbString) > ValutaArrowString($MyRow->OppTbString)) {
					$WinnerId = $MyRow->MatchNo;	//TbString è maggiore di OppTbString --> il primo vince
				} elseif(strcmp(strtolower(substr(trim($MyRow->OppTbString),-1,1)),substr(trim($MyRow->OppTbString),-1,1)) == 0 && strcmp(substr(trim($MyRow->TbString),-1,1),strtoupper(substr(trim($MyRow->TbString),-1,1))) == 0) {
					$WinnerId = $MyRow->OppMatchNo; //le stringhe CASE INSENSITIVE sono uguali -- Verifico gli "*" e lo star è nella stringa del secondo (è maggiore)
				} elseif(strcmp(strtoupper(substr(trim($MyRow->OppTbString),-1,1)),substr(trim($MyRow->OppTbString),-1,1)) == 0 && strcmp(substr(trim($MyRow->TbString),-1,1),strtolower(substr(trim($MyRow->TbString),-1,1))) == 0) {
					$WinnerId = $MyRow->MatchNo; //le stringhe CASE INSENSITIVE sono uguali -- Verifico gli "*" e lo star è nella stringa del primo (è maggiore)
				}

				//Azzero entrambi i flag di shootoff
				$SqlUpdate = "UPDATE Finals SET FinTie=0, FinWinLose=0, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo IN (". StrSafe_DB($MyRow->MatchNo) . "," . StrSafe_DB($MyRow->OppMatchNo) . ") AND FinTournament=" . StrSafe_DB($_SESSION['TourId']);
				safe_w_sql($SqlUpdate);
				if ($WinnerId>-1) {
					$SqlUpdate = "UPDATE Finals SET FinTie=1, FinWinLose=1, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=". StrSafe_DB($WinnerId) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']);
					safe_w_sql($SqlUpdate);
					if($MyRow->MatchMode!=0 && $MyRow->Score == $MyRow->OppScore) {
						$SqlUpdate = "UPDATE Finals SET FinSetScore=FinSetScore+1, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=". StrSafe_DB($WinnerId) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']);
						safe_w_sql($SqlUpdate);
					}
				}
			}
		}
	}

// Faccio i passaggi di fase
	$MyNextMatchNo='xx';
	$QueryFilter = '';

	$Select
		= "SELECT "
		. "f.FinEvent AS Event, f.FinMatchNo, f2.FinMatchNo OppMatchNo,  "
		. "GrPhase, f.FinAthlete AS Athlete, f2.FinAthlete AS OppAthlete, "
		. "IF(EvMatchMode=0,f.FinScore,f.FinSetScore) AS Score, f.FinTie as Tie, IF(EvMatchMode=0,f2.FinScore,f2.FinSetScore) as OppScore, f2.FinTie as OppTie, "
		. "IF(GrPhase>2, FLOOR(f.FinMatchNo/2),FLOOR(f.FinMatchNo/2)-2) AS NextMatchNo "

		. "FROM Finals AS f "
		. "INNER JOIN Finals AS f2 ON f.FinEvent=f2.FinEvent AND f.FinMatchNo=IF((f.FinMatchNo % 2)=0,f2.FinMatchNo-1,f2.FinMatchNo+1) AND f.FinTournament=f2.FinTournament "
		. "INNER JOIN Events ON f.FinEvent=EvCode AND f.FinTournament=EvTournament AND EvTeamEvent=0 ";
		if(!is_null($Phase))
			$Select .= "INNER JOIN Grids ON f.FinMatchNo=GrMatchNo AND GrPhase=" . StrSafe_DB($Phase) . " ";
		else
			$Select .= "INNER JOIN Grids ON f.FinMatchNo=GrMatchNo AND GrMatchNo=" . StrSafe_DB(($MatchNo % 2 == 0 ? $MatchNo:$MatchNo-1)) . " ";
		$Select .= "LEFT JOIN Entries ON f.FinAthlete=EnId AND f.FinTournament=EnTournament "
		. "WHERE f.FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (f.FinMatchNo % 2)=0 ";

		if(!is_null($Event) && $Event!='')
			$Select .= "AND f.FinEvent=" . StrSafe_DB($Event) . " ";

		$Select .= "ORDER BY f.FinEvent, NextMatchNo ASC, Score DESC, Tie DESC ";
	$Rs=safe_r_sql($Select);

// conterrà i parametri per il calcolo delle RankFinal
	$coppie=array();


	$AthPropTs = NULL;
	if (safe_num_rows($Rs)>0) {
		$AthPropTs = date('Y-m-d H:i:s');
		while ($MyRow=safe_fetch($Rs)) {
		/*
		 * Dato che potrei avere più fasi gestite da questa funzione, io ricavo le coppie
		 * per la RankFinal dalle righe del recordset.
		 * Visto che mi imbatterò più volte nella stessa coppia evento/fase, solo se la coppia
		 * non l'ho già contata la aggiungo nel vettore.
		 */
			if (!in_array($MyRow->Event.'@'.$MyRow->GrPhase,$coppie)) {
				$coppie[]=$MyRow->Event.'@'.$MyRow->GrPhase;
			}

			// sets the WinLose Flag
			$WinLose=-1;

			$AthProp = '0';
			$WhereProp = '0';
			if (intval($MyRow->Score)>intval($MyRow->OppScore) || (intval($MyRow->Score)==intval($MyRow->OppScore) && intval($MyRow->Tie)>intval($MyRow->OppTie))) {
				$WinLose=$MyRow->FinMatchNo;
				if ($MyRow->GrPhase>=2) {
					$MyUpQuery = "UPDATE Finals SET
						FinAthlete =" . StrSafe_DB($MyRow->Athlete) . ",
						FinDateTime=" . StrSafe_DB($AthPropTs) . "
						WHERE FinEvent=" . StrSafe_DB($MyRow->Event) . " AND FinMatchNo=" . StrSafe_DB($MyRow->NextMatchNo) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$RsUp=safe_w_sql($MyUpQuery);

					$AthProp=$MyRow->Athlete;
					$WhereProp=$MyRow->OppAthlete;
					if($MyRow->GrPhase==2) {
						$MyUpQuery = "UPDATE Finals SET
							FinAthlete =" . StrSafe_DB($MyRow->OppAthlete) . ",
							FinDateTime=" . StrSafe_DB($AthPropTs) . "
							WHERE FinEvent=" . StrSafe_DB($MyRow->Event) . " AND FinMatchNo=" . StrSafe_DB(($MyRow->NextMatchNo+2)) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
						$RsUp=safe_w_sql($MyUpQuery);
					}
				}
			} elseif (intval($MyRow->Score)<intval($MyRow->OppScore) || (intval($MyRow->Score)==intval($MyRow->OppScore) && intval($MyRow->Tie)<intval($MyRow->OppTie))) {
				$WinLose=$MyRow->OppMatchNo;
				if ($MyRow->GrPhase>=2) {
					$MyUpQuery = "UPDATE Finals SET ";
					$MyUpQuery.= "FinAthlete =" . StrSafe_DB($MyRow->OppAthlete) . ", ";
					$MyUpQuery.= "FinDateTime=" . StrSafe_DB($AthPropTs) . " ";
					$MyUpQuery.= "WHERE FinEvent=" . StrSafe_DB($MyRow->Event) . " AND FinMatchNo=" . StrSafe_DB($MyRow->NextMatchNo) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$RsUp=safe_w_sql($MyUpQuery);

					$AthProp=$MyRow->OppAthlete;
					$WhereProp=$MyRow->Athlete;
					if($MyRow->GrPhase==2) {
						$MyUpQuery = "UPDATE Finals SET ";
						$MyUpQuery.= "FinAthlete =" . StrSafe_DB($MyRow->Athlete) . ", ";
						$MyUpQuery.= "FinDateTime=" . StrSafe_DB($AthPropTs) . " ";
						$MyUpQuery.= "WHERE FinEvent=" . StrSafe_DB($MyRow->Event) . " AND FinMatchNo=" . StrSafe_DB(($MyRow->NextMatchNo+2)) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
						$RsUp=safe_w_sql($MyUpQuery);
					}
				}

			} else {
				if ($MyRow->GrPhase>=2) {
					$MyUpQuery = "UPDATE Finals SET ";
					$MyUpQuery.= "FinAthlete ='0', ";
					$MyUpQuery.= "FinDateTime=" . StrSafe_DB($AthPropTs) . " ";
					$MyUpQuery.= "WHERE FinEvent=" . StrSafe_DB($MyRow->Event) . " AND FinMatchNo=" . StrSafe_DB($MyRow->NextMatchNo) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$RsUp=safe_w_sql($MyUpQuery);

					if($MyRow->GrPhase==2) {
						$MyUpQuery = "UPDATE Finals SET ";
						$MyUpQuery.= "FinAthlete ='0', ";
						$MyUpQuery.= "FinDateTime=" . StrSafe_DB($AthPropTs) . " ";
						$MyUpQuery.= "WHERE FinEvent=" . StrSafe_DB($MyRow->Event) . " AND FinMatchNo=" . StrSafe_DB(($MyRow->NextMatchNo+2)) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
						$RsUp=safe_w_sql($MyUpQuery);
					}
				}
			}

			// update the winner of previous match
			safe_w_sql("update Finals set FinWinLose=if(FinMatchNo=".$WinLose.", 1, 0) where FinMatchNo in (" . StrSafe_DB($MyRow->FinMatchNo) . "," . StrSafe_DB($MyRow->OppMatchNo) . ") AND FinEvent=" . StrSafe_DB($MyRow->Event) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']) . "");

			$OldId=($AthProp!=0 ? StrSafe_DB($WhereProp) : StrSafe_DB($MyRow->Athlete) . ',' . StrSafe_DB($MyRow->OppAthlete));
			if($OldId!="'0'") {
				// if Athlete and Opponent are both there or if athlete is not present
				// propagates the winner in next matches

				$Update
					= "UPDATE Finals SET "
					. "FinAthlete=" . StrSafe_DB($AthProp) . ", "
					. "FinDateTime=" . StrSafe_DB($AthPropTs) . " "
					. ($AthProp==0 ? ', FinWinLose=0 ' : '')
					. "WHERE FinAthlete IN (" . $OldId . ") "
					. "AND FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
					. "AND FinEvent=" . StrSafe_DB($MyRow->Event) . " "
					. "AND FinMatchNo<"	. StrSafe_DB($MyRow->NextMatchNo) . " ";

				$RsProp = safe_w_sql($Update);
			}
		}
	}

	foreach($CheckSaved as $MyEvent=>$DoSave) {
		if(!$DoSave) continue;
		// we get here only if we check phase 64 and we have a starting phase of 48, so the first 8 are automatically put in the 16th matches
		// start putting a bye to the first 8 athletes in 24th
		$SqlUpdate = "UPDATE Finals inner join Grids on FinMatchNo=GrMatchNo and GrPhase=32 and GrPosition2<=8 and GrPosition2>0
			SET FinTie=2, FinWinLose=1, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . "
			WHERE FinEvent=" . StrSafe_DB($MyEvent) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']);
		safe_w_SQL($SqlUpdate);

		// Push the first 8 to 16th... postponed as it breaks the brackets in the infosystem
		// in write SQL as it must be certain that it takes the right people without waiting for replica!!!
		$q=safe_w_sql("select FinAthlete, GrPosition2 from Finals inner join Grids on FinMatchNo=GrMatchNo and GrPhase=64 and GrPosition2<=8 and GrPosition2>0
			WHERE FinEvent=" . StrSafe_DB($MyEvent) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']));
		while($r=safe_fetch($q)) {
			safe_w_sql("UPDATE Finals inner join Grids on FinMatchNo=GrMatchNo and GrPhase=16 and GrPosition2=$r->GrPosition2
			SET FinAthlete=$r->FinAthlete, FinWinLose=1, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . "
			WHERE FinEvent=" . StrSafe_DB($MyEvent) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']));
		}
	}

// se ho delle coppie calcolo per queste la RankFinal
	if (count($coppie)>0) {
		Obj_RankFactory::create('FinalInd',array('eventsC'=>$coppie))->calculate();
	}
	return $AthPropTs;
}

function move2NextPhaseTeam($Phase=NULL, $Event=NULL, $MatchNo=NULL) {
//verifico i parametri
	if(is_null($Phase) && is_null($MatchNo))	//Devono esistere o la fase o il MatchNo
		return;
	if(is_null($Phase) && is_null($Event))		//Se non ho la Fase (e quindi ho il MatchNo) deve esistere l'evento
		return;

//Verifico la situazione tiebreak
	$Select
		= "SELECT "
		. "tf.TfEvent, EvMatchMode as MatchMode, tf.TfMatchNo as MatchNo, tf2.TfMatchNo as OppMatchNo,  "
		. "tf.TfTeam AS Team, tf.TfSubteam AS SubTeam, tf2.TfTeam AS OppTeam, tf2.TfSubTeam AS OppSubTeam,"
		. "if(tf.TfMatchNo>15, EvElimEnds, EvFinEnds) FinEnds, if(tf.TfMatchNo>15, EvElimArrows, EvFinArrows) FinArrows, if(tf.TfMatchNo>15, EvElimSO, EvFinSO) FinSO, "
		. "IF(tf.TfDateTime>=tf2.TfDateTime, tf.TfDateTime, tf2.TfDateTime) AS DateTime,"
		. "IF(EvMatchMode=0,tf.TfScore,tf.TfSetScore) AS Score, tf.TfTie as Tie, tf.TfTieBreak as TbString, IF(EvMatchMode=0,tf2.TfScore,tf2.TfSetScore) as OppScore, tf2.TfTie as OppTie, tf2.TfTieBreak as OppTbString, "
		. "tf.TfArrowString as ArrString, tf2.TfArrowString as OppArrString,  tf.TfSetPoints as SetPoint, tf2.TfSetPoints as OppSetPoint "
		. "FROM TeamFinals AS tf "
		. "INNER JOIN TeamFinals AS tf2 ON tf.TfEvent=tf2.TfEvent AND tf.TfMatchNo=IF((tf.TfMatchNo % 2)=0,tf2.TfMatchNo-1,tf2.TfMatchNo+1) AND tf.TfTournament=tf2.TfTournament "
		. "INNER JOIN Events ON tf.TfEvent=EvCode AND tf.TfTournament=EvTournament AND EvTeamEvent=1 ";
		if(!is_null($Phase))
			$Select .= "INNER JOIN Grids ON tf.TfMatchNo=GrMatchNo AND GrPhase=" . StrSafe_DB($Phase) . " ";
		else
			$Select .= "INNER JOIN Grids ON tf.TfMatchNo=GrMatchNo AND GrMatchNo=" . StrSafe_DB(($MatchNo % 2 == 0 ? $MatchNo:$MatchNo-1)) . " ";
		$Select .= "WHERE tf.TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (tf.TfMatchNo % 2)=0 ";

		if(!is_null($Event) && $Event!='')
			$Select .= "AND tf.TfEvent=" . StrSafe_DB($Event) . " ";
		$Select .= "ORDER BY tf.TfEvent, tf.TfMatchNo ";

	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0) {
		while ($MyRow=safe_fetch($Rs)) {
			//Se uno dei due ATLETI è ZERO ed ENTRAMBI GLI SCORES sono a ZERO imposto il Bye
			if($MyRow->Team!=0 && $MyRow->OppTeam==0 && $MyRow->Score == 0 && $MyRow->OppScore==0) {
				$SqlUpdate = "UPDATE TeamFinals SET TfTie=0, TfWinLose=0, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=". StrSafe_DB($MyRow->OppMatchNo) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']);
				safe_w_sql($SqlUpdate);
				$SqlUpdate = "UPDATE TeamFinals SET TfTie=2, TfWinLose=1, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=". StrSafe_DB($MyRow->MatchNo) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']);
				safe_w_sql($SqlUpdate);
			} elseif($MyRow->Team==0 && $MyRow->OppTeam!=0 && $MyRow->Score == 0 && $MyRow->OppScore==0) {
				$SqlUpdate = "UPDATE TeamFinals SET TfTie=0, TfWinLose=0, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=". StrSafe_DB($MyRow->MatchNo) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']);
				safe_w_sql($SqlUpdate);
				$SqlUpdate = "UPDATE TeamFinals SET TfTie=2, TfWinLose=1, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=". StrSafe_DB($MyRow->OppMatchNo) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']);
				safe_w_sql($SqlUpdate);
			} elseif($MyRow->MatchMode!=0) {
			//Se non ho il BYE, rigestisco il numero di set vinti e aggiorno il conteggio del totale (se i punti set sono valorizzati & le arrowstring sono vuote)
				if(strlen(trim(str_replace("|","",$MyRow->SetPoint))) && strlen(trim(str_replace("|","",$MyRow->OppSetPoint))) && strlen(trim($MyRow->ArrString))==0 && strlen(trim($MyRow->OppArrString))==0) {
					$AthSets=explode("|",$MyRow->SetPoint);
					$OppSets=explode("|",$MyRow->OppSetPoint);
					$AthScore=0;
					$OppScore=0;
					$AthWin=0;
					$OppWin=0;
					if(count($AthSets) == count($OppSets)) {
						for($i=0;$i<count($AthSets);$i++) {
							if(intval($AthSets[$i])>intval($OppSets[$i])) {
								$AthScore += 2;
								$AthWin++;
							} elseif(intval($AthSets[$i])<intval($OppSets[$i])) {
								$OppScore += 2;
								$OppWin++;
							} elseif(intval($AthSets[$i])!=0 && intval($OppSets[$i])!=0) {
								$AthScore += 1;
								$OppScore += 1;
							}
						}
						$SqlUpdate = "UPDATE TeamFinals SET TfWinLose=".($AthScore>$MyRow->FinEnds ? 1 : 0).", TfSetScore=" . StrSafe_DB($AthScore) . ", TfWinnerSet=" . StrSafe_DB($AthWin) . ", TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=" . StrSafe_DB($MyRow->MatchNo) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']);
						safe_w_sql($SqlUpdate);
						$MyRow->Score=$AthScore;
						$SqlUpdate = "UPDATE TeamFinals SET TfWinLose=".($OppScore>$MyRow->FinEnds ? 1 : 0).", TfSetScore=" . StrSafe_DB($OppScore) . ", TfWinnerSet=" . StrSafe_DB($OppWin) . ", TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=" . StrSafe_DB($MyRow->OppMatchNo) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']);
						safe_w_sql($SqlUpdate);
						$MyRow->OppScore=$OppScore;
					}
				}

			}

		    // Se uno dei due è diverso da ZERO e non sono uguali oppure se è a set e differiscono di più di un punto
			if(($MyRow->Score!= 0 || $MyRow->OppScore!=0) && $MyRow->Score != $MyRow->OppScore && ($MyRow->MatchMode!=0 && abs($MyRow->Score-$MyRow->OppScore)>1)) {
				//Azzero entrambi i flag di shootoff
				$SqlUpdate = "UPDATE TeamFinals SET TfTie=0, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo IN (". StrSafe_DB($MyRow->MatchNo) . "," . StrSafe_DB($MyRow->OppMatchNo) . ") AND TfTournament=" . StrSafe_DB($_SESSION['TourId']);
				safe_w_sql($SqlUpdate);
			}

			// Se i punteggi sono uguali e diversi da ZERO entro nel dettaglio E le stringhe di frecce sono lunghe uguali e non vuote
			if($MyRow->Score!=0
				and $MyRow->Score == $MyRow->OppScore
				and strlen(trim($MyRow->TbString))>0
				and strlen(trim($MyRow->TbString))==strlen(trim($MyRow->OppTbString))) {
				$WinnerId=-1;
				//Verifico le stringhe CASE INSENSITIVE - in questo momento me ne frego degli "*"
				if(ValutaArrowString($MyRow->TbString) < ValutaArrowString($MyRow->OppTbString)) {
				 	$WinnerId = $MyRow->OppMatchNo;	//OppTbString è maggiore di TbString --> il secondo vince
				} elseif(ValutaArrowString($MyRow->TbString) > ValutaArrowString($MyRow->OppTbString)) {
					$WinnerId = $MyRow->MatchNo;	//TbString è maggiore di OppTbString --> il primo vince
				}elseif(!ctype_upper(trim($MyRow->OppTbString)) and ctype_upper(trim($MyRow->TbString))) {
						// Verifico gli "*" e lo star è nella stringa del secondo (è maggiore)
					$WinnerId = $MyRow->OppMatchNo;
				} elseif(ctype_upper(trim($MyRow->OppTbString)) and !ctype_upper(trim($MyRow->TbString))) {
						// Verifico gli "*" e lo star è nella stringa del primo (è maggiore)
					$WinnerId = $MyRow->MatchNo;
				}

				//Azzero entrambi i flag di shootoff
				$SqlUpdate = "UPDATE TeamFinals SET TfTie=0, TfWinLose=0, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo IN (". StrSafe_DB($MyRow->MatchNo) . "," . StrSafe_DB($MyRow->OppMatchNo) . ") AND TfTournament=" . StrSafe_DB($_SESSION['TourId']);
				safe_w_sql($SqlUpdate);
				if ($WinnerId>-1) {
					$SqlUpdate = "UPDATE TeamFinals SET TfTie=1, TfWinLose=1, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=". StrSafe_DB($WinnerId) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']);
					safe_w_sql($SqlUpdate);
					if($MyRow->MatchMode!=0 && $MyRow->Score == $MyRow->OppScore) {
						$SqlUpdate = "UPDATE TeamFinals SET TfSetScore=TfSetScore+1, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=". StrSafe_DB($WinnerId) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']);
						safe_w_sql($SqlUpdate);
					}
				}
			}
		}
	}

// Faccio i passaggi di fase
	$MyNextMatchNo='xx';
	$QueryFilter = '';

	$Select
		= "SELECT "
		. "tf.TfEvent AS Event, tf.TfMatchNo, tf2.TfMatchNo OppMatchNo, "
		. "GrPhase, tf.TfTeam AS Team,tf.TfSubTeam AS SubTeam, tf2.TfTeam AS OppTeam,tf2.TfSubTeam AS OppSubTeam, "
		. "IF(EvMatchMode=0,tf.TfScore,tf.TfSetScore) AS Score, tf.TfTie as Tie, IF(EvMatchMode=0,tf2.TfScore,tf2.TfSetScore) as OppScore, tf2.TfTie as OppTie, "
		. "IF(GrPhase>2, FLOOR(tf.TfMatchNo/2),FLOOR(tf.TfMatchNo/2)-2) AS NextMatchNo "

		. "FROM TeamFinals AS tf "
		. "INNER JOIN TeamFinals AS tf2 ON tf.TfEvent=tf2.TfEvent AND tf.TfMatchNo=IF((tf.TfMatchNo % 2)=0,tf2.TfMatchNo-1,tf2.TfMatchNo+1) AND tf.TfTournament=tf2.TfTournament "
		. "INNER JOIN Events ON tf.TfEvent=EvCode AND tf.TfTournament=EvTournament AND EvTeamEvent=1 ";
	if(!is_null($Phase)) {
		$Select .= "INNER JOIN Grids ON tf.TfMatchNo=GrMatchNo AND GrPhase=" . StrSafe_DB($Phase) . " ";
	} else {
		$Select .= "INNER JOIN Grids ON tf.TfMatchNo=GrMatchNo AND GrMatchNo=" . StrSafe_DB(($MatchNo % 2 == 0 ? $MatchNo:$MatchNo-1)) . " ";
	}
	$Select .= "LEFT JOIN Countries ON tf.TfTeam=CoId AND tf.TfTournament=CoTournament "
		. "WHERE tf.TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (tf.TfMatchNo % 2)=0 ";

		if(!is_null($Event) && $Event!='')
			$Select .= "AND tf.TfEvent=" . StrSafe_DB($Event) . " ";

		$Select .= "ORDER BY tf.TfEvent, NextMatchNo ASC, Score DESC, Tie DESC ";
	$Rs=safe_r_sql($Select);
	//print $Select . '<br>';exit;

	// conterrà i parametri per il calcolo delle RankFinal
	$coppie=array();

	$AthPropTs = NULL;

	if (safe_num_rows($Rs)>0) {
		$AthPropTs = date('Y-m-d H:i:s');
		while ($MyRow=safe_fetch($Rs)) {
		/*
		 * Dato che potrei avere più fasi gestite da questa funzione, io ricavo le coppie
		 * per la RankFinal dalle righe del recordset.
		 * Visto che mi imbatterò più volte nella stessa coppia evento/fase, solo se la coppia
		 * non l'ho già contata la aggiungo nel vettore.
		 */
			if (!in_array($MyRow->Event.'@'.$MyRow->GrPhase,$coppie)) {
				$coppie[]=$MyRow->Event.'@'.$MyRow->GrPhase;
			}

			$AthProp = '0';
			$AthSubProp = '0';
			$WhereProp = '0';
			$WhereSubProp = '0';

			// Flag for winlose
			$WinLose=-1;

			if (intval($MyRow->Score)>intval($MyRow->OppScore) || (intval($MyRow->Score)==intval($MyRow->OppScore) && intval($MyRow->Tie)>intval($MyRow->OppTie))) {
				$WinLose=$MyRow->TfMatchNo;
				if ($MyRow->GrPhase>=2) {
					$MyUpQuery = "UPDATE TeamFinals SET ";
					$MyUpQuery.= "TfTeam =" . StrSafe_DB($MyRow->Team) . ", ";
					$MyUpQuery.= "TfSubTeam =" . StrSafe_DB($MyRow->SubTeam) . ", ";
					$MyUpQuery.= "TfDateTime=" . StrSafe_DB($AthPropTs) . " ";
					$MyUpQuery.= "WHERE TfEvent=" . StrSafe_DB($MyRow->Event) . " AND TfMatchNo=" . StrSafe_DB($MyRow->NextMatchNo) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$RsUp=safe_w_sql($MyUpQuery);

					$AthProp=$MyRow->Team;
					$AthSubProp=$MyRow->SubTeam;
					$WhereProp=$MyRow->OppTeam;
					$WhereSubProp=$MyRow->OppSubTeam;

					if($MyRow->GrPhase==2) {
						$MyUpQuery = "UPDATE TeamFinals SET ";
						$MyUpQuery.= "TfTeam =" . StrSafe_DB($MyRow->OppTeam) . ", ";
						$MyUpQuery.= "TfSubTeam =" . StrSafe_DB($MyRow->OppSubTeam) . ", ";
						$MyUpQuery.= "TfDateTime=" . StrSafe_DB($AthPropTs) . " ";
						$MyUpQuery.= "WHERE TfEvent=" . StrSafe_DB($MyRow->Event) . " AND TfMatchNo=" . StrSafe_DB(($MyRow->NextMatchNo+2)) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
						$RsUp=safe_w_sql($MyUpQuery);
					}
				}
			} elseif (intval($MyRow->Score)<intval($MyRow->OppScore) || (intval($MyRow->Score)==intval($MyRow->OppScore) && intval($MyRow->Tie)<intval($MyRow->OppTie))) {
				$WinLose=$MyRow->OppMatchNo;
				if ($MyRow->GrPhase>=2) {
					$MyUpQuery = "UPDATE TeamFinals SET ";
					$MyUpQuery.= "TfTeam =" . StrSafe_DB($MyRow->OppTeam) . ", ";
					$MyUpQuery.= "TfSubTeam =" . StrSafe_DB($MyRow->OppSubTeam) . ", ";
					$MyUpQuery.= "TfDateTime=" . StrSafe_DB($AthPropTs) . " ";
					$MyUpQuery.= "WHERE TfEvent=" . StrSafe_DB($MyRow->Event) . " AND TfMatchNo=" . StrSafe_DB($MyRow->NextMatchNo) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$RsUp=safe_w_sql($MyUpQuery);

					$AthProp=$MyRow->OppTeam;
					$AthSubProp=$MyRow->OppSubTeam;
					$WhereProp=$MyRow->Team;
					$WhereSubProp=$MyRow->SubTeam;
					if($MyRow->GrPhase==2)
					{
						$MyUpQuery = "UPDATE TeamFinals SET ";
						$MyUpQuery.= "TfTeam =" . StrSafe_DB($MyRow->Team) . ", ";
						$MyUpQuery.= "TfSubTeam =" . StrSafe_DB($MyRow->SubTeam) . ", ";
						$MyUpQuery.= "TfDateTime=" . StrSafe_DB($AthPropTs) . " ";
						$MyUpQuery.= "WHERE TfEvent=" . StrSafe_DB($MyRow->Event) . " AND TfMatchNo=" . StrSafe_DB(($MyRow->NextMatchNo+2)) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
						$RsUp=safe_w_sql($MyUpQuery);
					}
				}
			} else {
				if ($MyRow->GrPhase>=2) {
					$MyUpQuery = "UPDATE TeamFinals SET ";
					$MyUpQuery.= "TfTeam ='0', ";
					$MyUpQuery.= "TfSubTeam ='0', ";
					$MyUpQuery.= "TfDateTime=" . StrSafe_DB($AthPropTs) . " ";
					$MyUpQuery.= "WHERE TfEvent=" . StrSafe_DB($MyRow->Event) . " AND TfMatchNo=" . StrSafe_DB($MyRow->NextMatchNo) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$RsUp=safe_w_sql($MyUpQuery);

					if($MyRow->GrPhase==2) {
						$MyUpQuery = "UPDATE TeamFinals SET ";
						$MyUpQuery.= "TfWinLose ='0', ";
						$MyUpQuery.= "TfTeam ='0', ";
						$MyUpQuery.= "TfSubTeam ='0', ";
						$MyUpQuery.= "TfDateTime=" . StrSafe_DB($AthPropTs) . " ";
						$MyUpQuery.= "WHERE TfEvent=" . StrSafe_DB($MyRow->Event) . " AND TfMatchNo=" . StrSafe_DB(($MyRow->NextMatchNo+2)) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
						$RsUp=safe_w_sql($MyUpQuery);
					}
				}
			}

			// reset winners of previous matches
			safe_w_sql("update TeamFinals set TfWinLose=if(TfMatchNo=$WinLose, 1, 0) WHERE TfMatchNo in (" . StrSafe_DB($MyRow->TfMatchNo) . "," . StrSafe_DB($MyRow->OppMatchNo) . ") AND TfEvent=" . StrSafe_DB($MyRow->Event) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']));

			$OldId=($AthProp!=0 ? StrSafe_DB($WhereProp) : StrSafe_DB($MyRow->Team) . ',' . StrSafe_DB($MyRow->OppTeam));
			$OldSubId=($AthSubProp!=0 ? StrSafe_DB($WhereSubProp) : StrSafe_DB($MyRow->SubTeam) . ',' . StrSafe_DB($MyRow->OppSubTeam));

			$Update
				= "UPDATE TeamFinals SET "
				. "TfTeam=" . StrSafe_DB($AthProp) . ", "
				. "TfSubTeam=" . StrSafe_DB($AthSubProp) . ", "
				. "TfDateTime=" . StrSafe_DB($AthPropTs) . " "
				. ($AthProp==0 ? ', TfWinLose=0 ' : '')
				. "WHERE TfTeam IN (" . $OldId . ") "
				. "AND TfSubTeam IN (" . $OldSubId . ") "
				. "AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
				. "AND TfEvent=" . StrSafe_DB($MyRow->Event) . " "
				. "AND tfMatchNo<"	. StrSafe_DB($MyRow->NextMatchNo) . " ";

			if($OldId!="'0'") {
				$RsProp = safe_w_sql($Update);
			}
		}
	}

// se ho delle coppie calcolo per queste la RankFinal
	if (count($coppie)>0) {
		Obj_RankFactory::create('FinalTeam',array('eventsC'=>$coppie))->calculate();
	}

	return $AthPropTs;
}
