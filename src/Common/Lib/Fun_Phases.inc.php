<?php

function getStandardPhases() {
	$TourLocRule=empty($_SESSION['TourLocRule']) ? 'default' : $_SESSION['TourLocRule'];

	$myPhases=array();
	$MyQuery = "SELECT PhId FROM Phases WHERE  PhLevel IN(0,-1) and PhRuleSets in ('', '$TourLocRule') ORDER BY PhId DESC";
	$Rs=safe_r_sql($MyQuery);
	while($MyRow=safe_fetch($Rs)) {
		$myPhases[] = $MyRow->PhId;
	}
	safe_free_result($Rs);
	return $myPhases;
}

function getPhaseArray(){
	$TourLocRule=empty($_SESSION['TourLocRule']) ? 'default' : $_SESSION['TourLocRule'];
//Carico le fasi in un array
	$myPhases=array();
	$MyQuery = "SELECT PhId, PhDescr FROM Phases WHERE  PhLevel=-1 and PhRuleSets in ('', '$TourLocRule') ORDER BY PhId DESC";
	$Rs=safe_r_sql($MyQuery);
	if(safe_num_rows($Rs)>0)
	{
		while($MyRow=safe_fetch($Rs))
			$myPhases[$MyRow->PhId] = $MyRow->PhDescr;
		safe_free_result($Rs);
	}
	return $myPhases;
}

function getPhaseTV($PhaseId, $OrderNo) {
	$codedPhases = array(4=>"QF", 2=>"SF", 1=>"BF", 0=>"F");
	if(array_key_exists($PhaseId, $codedPhases)) {
		return $codedPhases[$PhaseId];
	} elseif($OrderNo >= 1 && $OrderNo <= 4) {
		return $OrderNo."R";
	}
	return false;

}

function PhaseLog($Phase) {
	if(!$Phase) return -1;
	return ceil(log($Phase, 2));
}

function getPhasesId($startPhase=64) {
	$TourLocRule=empty($_SESSION['TourLocRule']) ? 'default' : $_SESSION['TourLocRule'];
	$myPhases=array();
	$where="";
	if ($startPhase!=-1) {
		$where=" AND PhLevel<=0 AND PhId<=" . StrSafe_DB(bitwisePhaseId($startPhase));
	}
	$MyQuery = "SELECT PhId FROM Phases where PhRuleSets in ('', '$TourLocRule') {$where} ORDER BY PhId DESC";
	$Rs=safe_r_sql($MyQuery);
	if(safe_num_rows($Rs)>0) {
		while($MyRow=safe_fetch($Rs)) {
		    $myPhases[] = $MyRow->PhId;
		}
		safe_free_result($Rs);
	}
	return $myPhases;
}

function numPhases($phase) {
	$TourLocRule=empty($_SESSION['TourLocRule']) ? 'default' : $_SESSION['TourLocRule'];
	$MyQuery = "SELECT PhId FROM Phases  WHERE PhId<{$phase} AND PhLevel=-1 and PhRuleSets in ('', '$TourLocRule')";
	$Rs=safe_r_sql($MyQuery);

	return (safe_num_rows($Rs)+1);
}

function getPhase($matchno) {
	$SQL = "SELECT GrPhase FROM Grids WHERE GrMatchNo={$matchno}";
	$q=safe_r_sql($SQL);
	if($r = safe_fetch($q)) {
        return $r->GrPhase;
    } else {
        return 0;
    }
}

function maxPhaseRank($phase) {
	$max=0;
	$q="SELECT MAX(GrPosition" . ($phase==valueFirstPhase($phase) ? "" : "2")  .") AS `max` FROM Grids WHERE GrPhase=" . valueFirstPhase($phase);
	$r=safe_r_sql($q);
	if ($r && safe_num_rows($r)==1) {
        $max = safe_fetch($r)->max;
    }
	return $max;
}

function numQualifiedByPhase($phase) {
	switch($phase) {
		case 48: return 104; break;
		case 24: return 56; break;
		case 14: return 28; break;
		case 12: return 24; break;
		case 7: return 14; break;
	}
    return $phase*2;
}

function numMatchesByPhase($phase) {
	switch($phase) {
		case 48: return 48; break;
		case 24: return 24; break;
		case 14: return 12; break;
		case 12: return 8; break;
		case 7: return 6; break;
	}
    return $phase;
}

function bitwisePhaseId($phase) {
	switch($phase) {
		case 48: return 64; break;
		case 24: return 32; break;
		case 14: return 16; break;
		case 12: return 16; break;
		case 7: return 8; break;
	}
    return $phase;
}

function getJumpLines($phase) {
	switch($phase) {
		case 64: return array(5,9,17,33,65); break;
		case 48: return array(5,9,17,33,57); break;
		case 32:
		case 24: return array(5,9,17,33); break;
		case 16:
		case 14:
		case 12: return array(5,9,17); break;
	}
    return array(5,9);
}

function SavedInPhase($phase) {
	switch($phase) {
		case 48: return 8; break;
		case 24: return 8; break;
		case 14: return 4; break;
		case 12: return 8; break;
		case 7: return 2; break;
	}
    return 0;
}

function valueFirstPhase($startPhase) {
	switch($startPhase) {
		case 48: return 64; break;
		case 24: return 32; break;
		case 14: return 16; break;
		case 12: return 16; break;
		case 7: return 8; break;
	}
    return $startPhase;
}

function nextPhase($startPhase) {
	switch($startPhase) {
		case 48: return 24; break;
		case 24: return 16; break;
		case 14: return 8; break;
		case 12: return 8; break;
		case 7: return 4; break;
		case 2: return 0; break;
		case 1: return -1; break;
		case 0: return -1; break;
	}
	return intval($startPhase/2);
}


function namePhase($startPhase, $curPhase) {
	switch(true) {
		case ($startPhase==48 && $curPhase==64): return 48; break;
		case (($startPhase==48 || $startPhase==24) && $curPhase==32): return 24; break;
		case ($startPhase==14 && $curPhase==16): return 14; break;
		case ($startPhase==12 && $curPhase==16): return 12; break;
		case ($startPhase==7 && $curPhase==8): return 7; break;
		case ($curPhase==6): return 8; break; // dirty hack for the phase following 1/12!!!
	}
    return $curPhase;
}

function useGrPostion2($startPhase, $curPhase) {
    return (($startPhase==24 AND $curPhase==32) OR ($startPhase==48 AND ($curPhase==64 OR $curPhase==32)) OR ($startPhase==12 AND $curPhase==16));
}

function isFirstPhase($startPhase, $curPhase) {
	switch(true) {
		case ($startPhase==$curPhase):
		case ($startPhase== 7 AND $curPhase== 8):
		case ($startPhase==12 AND $curPhase==16):
		case ($startPhase==14 AND $curPhase==16):
		case ($startPhase==24 AND $curPhase==32):
		case ($startPhase==48 AND $curPhase==64):
			return true;
	}
	return false;
}

function getFirstPhase($evCode, $isTeamEvent){
	$firstPhase=0;

	$q="SELECT EvFinalFirstPhase FROM Events
		WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode=" . StrSafe_DB($evCode) . " AND EvTeamEvent=" . StrSafe_DB($isTeamEvent);
	$r=safe_r_sql($q);
	if ($r && safe_num_rows($r)==1)
		$firstPhase=safe_fetch($r)->EvFinalFirstPhase;
	return $firstPhase;

}


/**
 * Ritorna il numero di volee, di frecce, di frecce tie e frecce per vincere
 * di un evento
 * @param string $event: evento
 * @param int $phase: fase intesa nel solito modo -> 0=oro,1 bronzo, 2 semi etc...
 * @param int $team: 0 o 1 a seconda se ind o team
 * @return StdClass composto da
 * 		StdClass::ends: numero di volee,
 * 		StdClass::arrows: numero di frecce x volee,
 * 		StdClass::so: numero di frecce tie,
 * 		StdClass::winAt: numero di set x vincere (se cumulativo vale 0)
 */
function getEventArrowsParams($event, $phase, $team, $TourId=0) {
	static $results;

	$tour = $TourId;
	if(empty($tour) && !empty($_SESSION['TourId'])) {
		$tour =	StrSafe_DB($_SESSION['TourId']);
	}

	$resKey=$event.'|'.$phase.'|'.$team.'|'.$tour;
	if(!empty($results[$resKey])) {
		return $results[$resKey];
	}

	$ee=StrSafe_DB($event);
	$tt=StrSafe_DB($team);

	$phase=($phase==12 ? 16: $phase);

	$p=($phase>0 ? 2*bitwisePhaseId($phase) : 1);

	$q="SELECT
			@bit:=IF(({$p} & EvMatchArrowsNo)={$p},1,0),
			IF(@bit=0,EvFinEnds,EvElimEnds) AS `ends`,
			IF(@bit=0,EvFinArrows,EvElimArrows) AS `arrows`,
			IF(@bit=0,EvFinSO,EvElimSO) AS `so`,
			EvMaxTeamPerson as MaxTeam,
			IF(EvMatchMode=1,IF(@bit=0,EvFinEnds,EvElimEnds)+1,0) AS `winAt`,
			EvMatchMode
		FROM Events
		WHERE EvCode={$ee} AND EvTeamEvent={$tt} AND EvTournament={$tour}
	";

	$rs=safe_r_sql($q);

	if (safe_num_rows($rs)==1) {
		$results[$resKey]=safe_fetch($rs);
		return $results[$resKey];
	}

	$results[$resKey]=new StdClass();
	$results[$resKey]->ends=0;
	$results[$resKey]->arrows=0;
	$results[$resKey]->so=0;
	$results[$resKey]->winAt=0;
	$results[$resKey]->MaxTeam=0;
	$results[$resKey]->EvMatchMode=0;

	return $results[$resKey];
}

function get_already_scheduled_events($CurPhase, $CurEvent, $TeamEvent=0) {
	$ret='';
	// get phase data
	$PhaseDesc=getEventArrowsParams($CurEvent, $CurPhase, $TeamEvent);
	// get time and date of 1st match of previous phase
	$q=safe_r_sql('select'
		. ' concat(FSScheduledDate, " ", FSScheduledTime) DateTime '
		. 'from'
		. ' FinSchedule '
		. 'where'
		. ' FsTournament=' . $_SESSION['TourId']
		. ' and FsTeamEvent='.$TeamEvent
		. ' and fsscheduleddate >0 '
		. ' and FSEvent="'.$CurEvent.'"'
		. ' and FSMatchNo>='. $CurPhase*2
		. ' LIMIT 1'
		);
	$r=safe_fetch($q);

	$MyQuery='SELECT'
		. ' @Phase:=ifnull(2*pow(2,truncate(log2(fsmatchno/2),0)),1) Phase'
		. ' , @RealPhase:=truncate(@Phase/2, 0) RealPhase'
		. ' , @PhaseMatch:=(@Phase & EvMatchArrowsNo)'
		. ' , EvMatchArrowsNo'
		. ' , if(@PhaseMatch, EvElimEnds, EvFinEnds) CalcEnds'
		. ' , if(@PhaseMatch, EvElimArrows, EvFinArrows) CalcArrows'
		. ' , if(@PhaseMatch, EvElimSO, EvFinSO) CalcSO'
		. ' , DATE_FORMAT(FSScheduledDate,"' . get_text('DateFmtDBshort') . '") AS Dt '
		. ' , DATE_FORMAT(FSScheduledDate,"' . get_text('DateFmtDB') . '") AS Dat '
		. ' , ' . (defined('hideSchedulerAndAdvancedSession') ? '-1' : 'FSScheduledLen') . ' AS matchLen '
		. ' , EvFinalFirstPhase '
		. ' , fs.* '
		. 'FROM'
		. ' `FinSchedule` fs'
		. ' INNER JOIN Events on FsEvent=EvCode and FsTeamEvent=EvTeamEvent and FsTournament=EvTournament '
		. 'where'
		. ' FsTournament=' . $_SESSION['TourId']
		. ' and FsTeamEvent=' . $TeamEvent
		. ' and fsscheduleddate >0 '
		. ($r && $r->DateTime ? ' and concat(FSScheduledDate, " ", FSScheduledTime)>=\''.$r->DateTime.'\' ' : '')
		. 'group by '
		. ' CalcArrows, '
		. ' FsScheduledDate, '
		. ' FsScheduledTime, '
		. ' FsEvent '
        . ' HAVING CalcArrows='.$PhaseDesc->arrows
        . ' ORDER BY CalcArrows, FsScheduledDate, FsScheduledTime, FsEvent ';
	$q=safe_r_sql($MyQuery);
	$tmp=array();
	while($r=safe_fetch($q)) {
		$tmp[$r->Dat . '§' . substr($r->FSScheduledTime,0,5) . '§'. $r->matchLen]['events'][get_text(namePhase($r->EvFinalFirstPhase, $r->RealPhase) . '_Phase')][]= $r->FSEvent;
		$tmp[$r->Dat . '§' . substr($r->FSScheduledTime,0,5) . '§'. $r->matchLen]['date']= $r->Dt.' '. substr($r->FSScheduledTime,0,5). (defined('hideSchedulerAndAdvancedSession') ? '' : '/'.$r->matchLen);
	}
	foreach($tmp as $k => $v) {
		$val=array();
		foreach($v['events'] as $ph => $ev) $val[]= $ph . ' '.implode('+',$ev).'';
		$ret.='<option value="'.$k.'">'.$v['date']  . ': '. implode('; ',$val).'</option>';
	}
	if($ret) {
		$ret='<br/><select class="ScheduleSelect" onchange="insert_schedule_from_select(\''.$CurEvent.'\', \''.$CurPhase.'\', this.value)"><option value=""></option>'. $ret . '</select>';
	}
	return $ret;
}

function PrecNextPhaseForButton()
{
	$phases=getStandardPhases();
	//print_r($phases);
	$indexCur=array_search($_REQUEST['d_Phase'],$phases);
	$indexP=$indexCur;
	$indexN=$indexCur;

// la fase non è nè la prima nè l'ultima disponibile
	if ($indexCur>0 && $indexCur<(count($phases)-1))
	{
		$indexP=$indexCur-1;
		$indexN=$indexCur+1;
	}
	elseif ($indexCur==0)	// sono alla prima disponibile
	{
		$indexN=$indexCur+1;
	}
	elseif ($indexCur==(count($phases)-1))	// sono all'ultima disponibile
	{
		$indexP=$indexCur-1;
	}

	$PP=$phases[$indexP];
	$NP=$phases[$indexN];

	return array($PP,$NP);
}

/*
 * elimFinFromMatchArrowsNo()
 * Dato il parametro EvMatchArrowsNo e una fase di partenza,
 * la funzione stabilisce se ci sono fasi, da $firstPhase in giù che usano
 * la terna di configurazione "EvElimEnds, EvElimArrows, EvElimSO" e quella
 * "EvFinEnds, EvFinArrows, EvFinSO".
 *
 * Ritorna un array di due elementi bool:
 * 		il primo contiene true se ci sono fasi
 * 			che usano la prima terna e false altrimenti;
 * 		il secondo contiene true se ci sono fasi
 * 			che usano la seconda terna e false altrimenti;
 *
 * NOTA:
 * 		la fase passata è secca perchè è la funziona che la processa con bitwisePhaseId()
 */
function elimFinFromMatchArrowsNo($firstPhase,$matchArrowsNo)
{
/*
 * Calcolo l'esponente di 2 per ottenere la fase.
 * Devo moltiplicare per due perchè il bit meno significativo rappresenta
 * la fase zero quindi devo traslare a sinistra di 1 bit che equivale a fare un *2
 */
	$bit=$firstPhase>0 ? 2*bitwisePhaseId($firstPhase) : 1;
// questo è l'esponente!
	$e=log($bit,2);
	//print 'e: '.$e.'<br><br>';
	//print (pow(2,$e+1)-1).'<br>';

/*
 * L'and di $matchArrowsNo con il valore mi serve per buttare a zero tutti i bit più a sinistra di $matchArrowsNo
 * che sono successivi a quello della prima fase buona ($firstPhase).
 * Questo mi assicura di che alla fine $rif avrà (escludendo i bit più significativi a zero)tanti bit quante sono
 * le fasi a partire da  $firstPhase
 */
	$rif=$matchArrowsNo & (pow(2,$e+1)-1);

	$elim=true;
	$fin=true;

// se non ci sono "1" vuol dire "nessuna fase usa la prima terna"
	if ($rif==0)	// no 1
	{
		$elim=false;
	}

// se non ci sono "0" vuol dire "nessuna fase usa la seconda terna"
	if ($rif==1)	// no 0
	{
		$fin=false;
	}
//	print '<pre>';
//	print_r(array($elim,$fin));
//	print '</pre>';
	return array($elim,$fin);
}

/*
 * eventHasScoreTypes()
 * Dice s eun dato evento ha fasi che usano la prima terna e fasi che usano la seconda.
 * (Vedi elimFinFromMatchArrowsNo)
 */
function eventHasScoreTypes($event,$team,$tour=null)
{
	$tourId=$tour===null ? $_SESSION['TourId'] : $tour;

	$q="
		SELECT
			EvCode,EvTeamEvent,EvTournament,
			EvFinalFirstPhase,EvMatchArrowsNo
		FROM
			Events
		WHERE
			EvTournament={$tourId}
			AND EvCode='{$event}'
			AND EvTeamEvent='{$team}'
	";
	//print '<br><br>'.$q.'<br><br>';
	$r=safe_r_sql($q);

	if (!($r && safe_num_rows($r)==1))
		return false;

	$row=safe_fetch($r);

	return elimFinFromMatchArrowsNo($row->EvFinalFirstPhase,$row->EvMatchArrowsNo);
}

/**
 * Deletes an event and all descending events
 * @param string $Event The Event to delete
 * @param int $TeamEvent 0 for individual, 1 for teams
 * @param int $TourId competition ID, if empty takes the open session
 * @return array of events that have been deleted
 */
function deleteEvent($Event, $TeamEvent=0, $TourId=0) {
	$ret=array();
	if(!$TourId) {
		$TourId=$_SESSION['TourId'];
	}

	$EventSafe=StrSafe_DB($Event);

	safe_w_sql("DELETE FROM Events WHERE EvCode={$EventSafe} AND EvTeamEvent={$TeamEvent} AND EvTournament={$TourId}");
	if(safe_w_affected_rows()) {
        // removes all children events
        $q = safe_r_sql("select * from Events where EvCodeParent={$EventSafe} AND EvTeamEvent={$TeamEvent} AND EvTournament={$TourId}");
        while ($r = safe_fetch($q)) {
            $ret = array_merge($ret, deleteEvent($r->EvCode, $TeamEvent, $TourId));
        }

        // deletes schedule
        safe_w_sql("delete from FinSchedule where FsTournament={$TourId} and FsTeamEvent={$TeamEvent} and FsEvent={$EventSafe}");
        // deletes warmup
        safe_w_sql("delete from FinWarmup where FwTournament={$TourId} and FwTeamEvent={$TeamEvent} and FwEvent={$EventSafe}");
        //	elimino le righe da EventClass
        safe_w_sql("DELETE FROM EventClass  WHERE EcTournament={$TourId} AND EcTeamEvent={$TeamEvent} AND EcCode={$EventSafe}");

        if ($TeamEvent) {
            // elimino le righe da Teams
            safe_w_sql("DELETE FROM Teams WHERE TeTournament={$TourId} AND TeEvent={$EventSafe} AND TeFinEvent=1 ");
            // cancello i nomi
            safe_w_sql("DELETE FROM TeamComponent WHERE TcTournament={$TourId} AND TcFinEvent=1 AND TcEvent={$EventSafe}");
            safe_w_sql("DELETE FROM TeamFinComponent WHERE TfcTournament={$TourId} AND TfcEvent={$EventSafe}");
            // elimino le griglie
            safe_w_sql("DELETE FROM TeamFinals WHERE TfTournament={$TourId} AND TfEvent=$EventSafe");
        } else {
            // elimino le righe da Individuals
            safe_w_sql("DELETE FROM Individuals WHERE IndTournament={$TourId} AND IndEvent=$EventSafe");
            // elimino le griglie
            safe_w_sql("DELETE FROM Finals WHERE FinTournament={$TourId} AND FinEvent=$EventSafe");
            // elimino le griglie eliminatorie
            $Rs = safe_w_sql("DELETE FROM Eliminations WHERE ElTournament={$TourId} AND ElEventCode=$EventSafe");
        }
	}

	$ret[]=$Event;
	return $ret;
}

function moveToNextPhaseLoosers($coppie, $TourId) {
	$ret=array();
	$phases = getStandardPhases();
	foreach($coppie as $value) {
		$subEv = array();

		list($ev,$ph) = explode('@',$value);
		if($ph==0) {
			continue;
		}
		$phNew = $phases[array_search($ph, $phases)+1];
		$Sql = "SELECT EvCode FROM Events WHERE EvCodeParent='{$ev}' AND EvFinalFirstPhase='{$phNew}' AND EvTournament='{$TourId}' AND EvTeamEvent=0";
		$q=safe_r_SQL($Sql);
		while($r=safe_fetch($q)) {
			$subEv[] = $r->EvCode;
			$ret[]=$r->EvCode.'@'.$phNew;
		}

		if(count($subEv)) {
			//GetMatchNo of winners
			$Sql = "SELECT if(f1.FinWinLose=1, f2.FinMatchNo, f1.FinMatchNo) as Looser, if(f1.FinWinLose=1, f2.FinAthlete, f1.FinAthlete) as Athlete
				FROM Finals as f1
				INNER JOIN Finals as f2 ON f2.FinEvent=f1.FinEvent AND f2.FinMatchNo=f1.FinMatchNo + 1 AND f2.FinTournament=f1.FinTournament
				INNER JOIN Grids on f1.FinMatchNo=GrMatchNo 
				WHERE f1.FinEvent='{$ev}' AND GrPhase='{$ph}' AND f1.FinTournament={$TourId} and f1.FinMatchNo%2=0
					AND (
						(f1.FinWinLose=1 and f2.FinIrmType!=20)
						or
						(f2.FinWinLose=1 and f1.FinIrmType!=20)
					)";
//			echo $Sql . "<br>";
			$q=safe_r_SQL($Sql);
			while($r=safe_fetch($q)) {
				foreach ($subEv as $subEvent) {
					$Sql = "UPDATE Finals SET FinAthlete={$r->Athlete}, FinDateTime=NOW() 
						WHERE FinEvent='{$subEvent}' AND FinMatchNo='". intval($r->Looser/2) . "' AND FinTournament={$TourId}";
					safe_w_SQL($Sql);
//					echo $Sql . "<br>";
				}
			}
			$Sql = "UPDATE Events SET EvShootOff = 1 WHERE EvCode IN ('" . implode("','",$subEv). "')  AND EvTournament={$TourId} AND EvTeamEvent=0";
//			echo $Sql . "<br>";
			safe_w_SQL($Sql);
		}

	}
	return $ret;
}

function moveToNextPhaseLoosersTeam($coppie, $TourId) {
	$ret=array();
	$ToMove=array();
	$phases = getStandardPhases();
	foreach($coppie as $value) {
		$subEv = array();
		$Byes = array();

		list($ev,$ph) = explode('@',$value);
		if($ph==0) {
			continue;
		}

		if(in_array($ph, $phases)) {
			$phNew = $phases[array_search($ph, $phases)+1];
		}

		$Sql = "SELECT kid.EvCode, dad.EvFinalFirstPhase FROM Events kid
 			inner join Events dad on kid.EvCodeParent=dad.EvCode and kid.EvTournament=dad.EvTournament and kid.EvTeamEvent=dad.EvTeamEvent
 			WHERE kid.EvCodeParent='{$ev}' AND kid.EvFinalFirstPhase='{$phNew}' AND kid.EvTournament='{$TourId}' AND kid.EvTeamEvent=1";
		$q=safe_r_SQL($Sql);
		while($r=safe_fetch($q)) {
			$ret[]=$r->EvCode.'@'.$phNew;
			$ToMove[]=array($r->EvCode, $phNew);
			$subEv[$r->EvCode] = ($r->EvFinalFirstPhase>=$ph ? 0 : 2);
		}

		if(count($subEv)) {
			//GetMatchNo of winners
			$Sql = "SELECT if(f1.TfWinLose=1, f2.TfMatchNo, f1.TfMatchNo) as Looser, if(f1.TfWinLose=1, f2.TfTeam, f1.TfTeam) as Team, if(f1.TfWinLose=1, f2.TfSubTeam, f1.TfSubTeam) as SubTeam
				FROM TeamFinals as f1
				INNER JOIN TeamFinals as f2 ON f2.TfEvent=f1.TfEvent AND f2.TfMatchNo=f1.TfMatchNo + 1 AND f2.TfTournament=f1.TfTournament
				INNER JOIN Grids on f1.TfMatchNo=GrMatchNo 
				WHERE f1.TfEvent='{$ev}' AND GrPhase='{$ph}' AND f1.TfTournament={$TourId} and f1.TfMatchNo%2=0
					AND (
						(f1.TfWinLose=1 and f2.TfIrmType!=20)
						or
						(f2.TfWinLose=1 and f1.TfIrmType!=20)
					)";
			$q=safe_r_SQL($Sql);
			while($r=safe_fetch($q)) {
				foreach ($subEv as $subEvent => $LoosersHaveBye) {
					$Bye=($r->Team ? $LoosersHaveBye : 0);
					$Sql = "UPDATE TeamFinals SET TfTeam={$r->Team}, TfSubTeam='{$r->SubTeam}', TfDateTime=NOW(), TfTie=$Bye
						WHERE TfEvent='{$subEvent}' AND TfMatchNo='". intval($r->Looser/2) . "' AND TfTournament={$TourId}";
					safe_w_SQL($Sql);

					$t=safe_r_sql("select * from Teams where TeCoId=$r->Team and TeSubTeam=$r->SubTeam and TeEvent='$ev' and TeTournament=$TourId and TeFinEvent=1");
					if($u=safe_fetch($t)) {
						$u->TeEvent=$subEvent;

						$Sql = array();
						foreach($u as $k => $v) {
							$Sql[] = "$k = '$v'";
						}

						safe_w_SQL("insert ignore into Teams set ".implode(', ', $Sql));
					}
				}
			}
			$Sql = "UPDATE Events SET EvShootOff = 1 WHERE EvCode IN ('" . implode("','", array_keys($subEv)). "')  AND EvTournament={$TourId} AND EvTeamEvent=1";
			safe_w_SQL($Sql);
		}

	}
	// move to next phase the newly created couples
	foreach($ToMove as $EvPh) {
		move2NextPhaseTeam($EvPh[1], $EvPh[0], null, $TourId);
		if(!empty($subEv[$EvPh[0]])) {
			move2NextPhaseTeam($EvPh[1]/2, $EvPh[0], null, $TourId);
			if($EvPh[1]==2) {
				move2NextPhaseTeam(0, $EvPh[0], null, $TourId);
			}
		}
	}
	return $ret;
}

function getChildrenEvents($Events, $Team=0, $TourId=0) {
	if(!$TourId) $TourId=$_SESSION['TourId'];
	if(!is_array($Events)) $Events=array($Events);
	$ret=$Events;

	if($Events) {
		$q=safe_r_SQL("select * from Events where EvTournament=$TourId and EvTeamEvent=$Team and EvCodeParent in (".implode(',', StrSafe_DB($Events)).")");
		while($r=safe_fetch($q)) {
			$ret=array_merge($ret, getChildrenEvents($r->EvCode, $Team, $TourId));
		}
	}

	return array_unique($ret);
}
