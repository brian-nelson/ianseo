<?php

function getStandardPhases()
{
	$myPhases=array();
	$MyQuery = "SELECT PhId FROM Phases WHERE  PhLevel IN(0,-1) ORDER BY PhId DESC";
	$Rs=safe_r_sql($MyQuery);
	if(safe_num_rows($Rs)>0)
	{
		while($MyRow=safe_fetch($Rs))
			$myPhases[] = $MyRow->PhId;
		safe_free_result($Rs);
	}
	return $myPhases;
}

function getPhaseArray()
{
//Carico le fasi in un array
	$myPhases=array();
	$MyQuery = "SELECT PhId, PhDescr FROM Phases WHERE  PhLevel=-1 ORDER BY PhId DESC";
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

function getPhasesId($startPhase=64)
{
	$myPhases=array();
	$where="";
	if ($startPhase==-1) {
		$where=" 1=1 ";
	} else {
		$where=" PhId<=" . StrSafe_DB($startPhase) . " ";
	}
	$MyQuery = "SELECT PhId FROM Phases WHERE {$where} ORDER BY PhId DESC";
	$Rs=safe_r_sql($MyQuery);
	if(safe_num_rows($Rs)>0) {
		while($MyRow=safe_fetch($Rs)) {
			if ($startPhase==-1) {
				$myPhases[] = $MyRow->PhId;
			} else {
				if(!(($MyRow->PhId==32 && $startPhase==48) || ($MyRow->PhId==24 && $startPhase==32) || ($MyRow->PhId==48 && $startPhase==64) || ($MyRow->PhId==24 && $startPhase==64)))
					$myPhases[] = $MyRow->PhId;
			}
		}
		safe_free_result($Rs);
	}
	return $myPhases;
}

function numPhases($phase)
{
	$MyQuery = "SELECT PhId FROM Phases  WHERE PhId<{$phase} AND PhLevel=-1";
	$Rs=safe_r_sql($MyQuery);

	return (safe_num_rows($Rs)+1);
}

function getPhase($matchno) {
	$SQL = "SELECT GrPhase FROM Grids WHERE GrMatchNo={$matchno}";
	$q=safe_r_sql($SQL);
	if($r = safe_fetch($q))
		return $r->GrPhase;
	else
		return 0;
}

function maxPhaseRank($phase)
{
	$max=0;

	$q="SELECT MAX(GrPosition" . ($phase==valueFirstPhase($phase) ? "":"2")  .") AS `max` FROM Grids WHERE GrPhase=" . valueFirstPhase($phase);
	$r=safe_r_sql($q);
	if ($r && safe_num_rows($r)==1)
		$max=safe_fetch($r)->max;

	return $max;
}

function bitwisePhaseId($phase)
{
	if($phase==48)
		return 64;
	else if($phase==24)
		return 32;
	else
		return $phase;
}

function valueFirstPhase($startPhase)
{
	if($startPhase==48)
		return 64;
	elseif($startPhase==24)
		return 32;
	else
		return $startPhase;
}


function namePhase($startPhase, $curPhase)
{
	if($startPhase==48 && $curPhase==64)
		return 48;
	elseif(($startPhase==48 || $startPhase==24) && $curPhase==32)
		return 24;
	else
		return $curPhase;
}

function isFirstPhase($startPhase, $curPhase)
{
	if($startPhase==$curPhase || ($startPhase==24 && $curPhase==32) || ($startPhase==48 && $curPhase==64))
		return true;
	else
		return false;
}

function getFirstPhase($evCode, $isTeamEvent)
{
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
function getEventArrowsParams($event,$phase,$team, $TourId=0)
{
	$ee=StrSafe_DB($event);
	$tt=StrSafe_DB($team);
	$tour = $TourId;
	if(empty($tour) && !empty($_SESSION['TourId']))
		$tour =	StrSafe_DB($_SESSION['TourId']);

	$phase=($phase==12 ? 16: $phase);

	$p=($phase>0 ? 2*bitwisePhaseId($phase) : 1);

	//IF(EvMatchMode=1,(FLOOR(IF(@bit=0,EvFinEnds,EvElimEnds)/2)+1)*2,-1) AS `winAt`

	$q="
		SELECT
			@bit:=IF(({$p} & EvMatchArrowsNo)={$p},1,0),

			IF(@bit=0,EvFinEnds,EvElimEnds) AS `ends`,
			IF(@bit=0,EvFinArrows,EvElimArrows) AS `arrows`,
			IF(@bit=0,EvFinSO,EvElimSO) AS `so`,
			EvMaxTeamPerson as MaxTeam,
			IF(EvMatchMode=1,IF(@bit=0,EvFinEnds,EvElimEnds)+1,0) AS `winAt`
		FROM
			Events
		WHERE
			EvCode={$ee} AND EvTeamEvent={$tt} AND EvTournament={$tour}
	";
	//print $q;exit;
	$rs=safe_r_sql($q);

	$r=new StdClass();
		$r->ends=0;
		$r->arrows=0;
		$r->so=0;
		$r->winAt=0;
		$r->MaxTeam=0;

	if ($rs && safe_num_rows($rs)==1)
	{
		$row=safe_fetch($rs);
		$r=clone $row;
	}

	return $r;
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
		. 'HAVING CalcArrows='.$PhaseDesc->arrows;
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
?>