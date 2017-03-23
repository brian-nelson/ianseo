<?php
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Fun_Various.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Qualification/Fun_Qualification.local.inc.php');

function updateEnTimeStamp_20101126($TournamentID)
{
	$MySql="UPDATE Entries
		INNER JOIN Qualifications On EnId=QuId
		SET EnTimestamp=QuTimestamp
		WHERE EnTournament=" . StrSafe_DB($TournamentID);
	safe_w_SQL($MySql);
}

function recalculateIndividuals_20101211($TournamentID)
{
	// Popolo la tabella degli Individuals
	$events=array();
	MakeIndividuals($events,$TournamentID);
	// Ottengo il numero di Distanze
	$MySql = "SELECT ToNumDist FROM Tournament WHERE ToId=" . StrSafe_DB($TournamentID);
	$rs = safe_r_SQL($MySql);
	$MyRow = safe_fetch($rs);
	safe_free_result($rs);
	// Calcolo la tabella Individuals per ogni distanza + il finale
	for($i=0; $i<=$MyRow->ToNumDist; $i++)
	{
		$rank = Obj_RankFactory::create('Abs',array('tournament'=>$TournamentID,'dist'=>$i,'skipExisting'=>1));
		if($rank)
			$rank->calculate();
	}
	//Prendo le posizione dei Coin toss dalla tabella delle finali - SE senza eliminatorie
	$MySql = "UPDATE Individuals
		INNER JOIN Finals ON IndId=FinAthlete AND IndEvent=FinEvent AND IndTournament=FinTournament
		INNER JOIN Events ON EvCode=FinEvent AND EvTeamEvent=0 AND EvTournament=FinTournament
		INNER JOIN Grids ON GrMatchNo=FinMatchNo AND GrPhase=IF(EvFinalFirstPhase=24,32,EvFinalFirstPhase)
		SET IndRank=GrPosition
		WHERE FinTournament='{$TournamentID}' AND FinAthlete!=0 AND (EvElim1=0 AND EvElim2=0)";
	safe_w_SQL($MySql);
	// Gestisco le posizioni a seguito dello shootoff di entrata - SE le IndRank sono a 0
	$MySql = "SELECT IndId, IndEvent, QuScore, QuGold, QuXnine, IndRank
		FROM Individuals
		INNER JOIN Qualifications ON IndId=QuId
		INNER JOIN Events ON EvCode=IndEvent AND EvTeamEvent=0 AND EvTournament=IndTournament
		LEFT JOIN Finals ON IndTournament=FinTournament AND IndEvent=FinEvent AND IndId=FinAthlete
		LEFT JOIN Eliminations AS e1 ON e1.ElElimPhase=0 AND IndTournament=e1.ElTournament AND IndEvent=e1.ElEventCode AND IndId=e1.ElId
		LEFT JOIN Eliminations AS e2 ON e2.ElElimPhase=1 AND IndTournament=e2.ElTournament AND IndEvent=e2.ElEventCode AND IndId=e2.ElId
		WHERE IndTournament='{$TournamentID}' AND IndSO=1 AND IndRank=0 AND ((EvElim2=0 AND FinAthlete IS NULL) OR (EvElim2>0 AND EvElim1=0 AND e2.ElId IS NULL) OR (EvElim2>0 AND EvElim1>0 AND e1.ElId IS NULL))
		ORDER BY IndEvent, QuScore DESC, QuGold DESC, QuXnine DESC, IndId
		";
	$rs = safe_r_SQL($MySql);
	$curGroup = "-----";
	$myPos = -1;
	$myRank = -1;
	$oldScore = -1;
	$oldGold = -1;
	$oldXnine = -1;
	while($MyRow = safe_fetch($rs))
	{
		if($curGroup != $MyRow->IndEvent)
		{
			$curGroup = $MyRow->IndEvent;
			$myPos = ($MyRow->IndRank);
		}
		$myPos++;
		if($MyRow->QuScore != $oldScore || $MyRow->QuGold != $oldGold || $MyRow->QuXnine != $oldXnine)
			$myRank=$myPos;

		$MySql = "UPDATE Individuals
			SET IndRank = {$myRank}
			WHERE IndId='{$MyRow->IndId}' AND IndEvent='{$MyRow->IndEvent}' AND IndTournament='{$TournamentID}'";
		safe_w_SQL($MySql);
		$oldScore  = $MyRow->QuScore;
		$oldGold = $MyRow->QuGold;
		$oldXnine = $MyRow->QuXnine;
	}
	//Sistemo le Rank di quelli che NON hanno passato i gironi ELiminatori (se c'erano i gironi) e i flag di SO/CT
	$MySql = "SELECT EvCode, EvFinalFirstPhase, EvElim1, EvElim2 FROM Events WHERE (EvElim1!=0 OR EvElim2!=0) AND EvTournament=" . StrSafe_DB($TournamentID) . " AND EvTeamEvent=0";
	$rs = safe_r_SQL($MySql);
	$eventsC=array();
	while($MyRow = safe_fetch($rs))
	{
		if($MyRow->EvElim1>0)
			$eventsC[] = $MyRow->EvCode . "@1";
		if($MyRow->EvElim2>0)
			$eventsC[] = $MyRow->EvCode . "@2";
	}
	Obj_RankFactory::create('ElimInd',array('tournament'=>$TournamentID,'eventsC'=>$eventsC,'skipExisting'=>1))->calculate();

/*
	$MySql = "SELECT ElId, ElElimPhase, ElEventCode, ElQualRank, ElScore, ElGold, ElXnine, ElRank
		FROM Eliminations
		INNER JOIN Events ON EvCode=ElEventCode AND EvTeamEvent=0 AND EvTournament=ElTournament
		WHERE ElTournament='{$TournamentID}' AND  ((EvElim1>0 AND EvE1ShootOff!=0 AND ElElimPhase=0) OR (EvElim2>0 AND EvE2ShootOff!=0 AND ElElimPhase=1))
		ORDER BY ElEventCode, ElElimPhase, ElScore DESC, ElRank ASC, ElGold DESC, ElXnine DESC, ElId
		";
	$rs = safe_r_SQL($MySql);
	$curGroup = "-----";
	$myPos = -1;
	$myRank = -1;
	$oldScore = -1;
	$oldGold = -1;
	$oldXnine = -1;
	while($MyRow = safe_fetch($rs))
	{
		if($curGroup != $MyRow->ElElimPhase . "|". $MyRow->ElEventCode)
		{
			$curGroup = $MyRow->ElElimPhase . "|". $MyRow->ElEventCode;
			$myPos = 0;
		}
		$myPos++;
		if($MyRow->ElScore != $oldScore || $MyRow->ElGold != $oldGold || $MyRow->ElXnine != $oldXnine)
			$myRank=$myPos;

		if($MyRow->ElRank == 0)
		{
			$MySql = "UPDATE Eliminations
				SET ElRank = {$myRank}
				WHERE ElElimPhase='{$MyRow->ElElimPhase}' AND ElEventCode='{$MyRow->ElEventCode}' AND ElTournament='{$TournamentID}' AND ElQualRank='{$MyRow->ElQualRank}'";
			safe_w_SQL($MySql);
		}
		$oldScore  = $MyRow->ElScore;
		$oldGold = $MyRow->ElGold;
		$oldXnine = $MyRow->ElXnine;
	}
*/
	// Calcolo le rank Finali venendo dalle qualifiche
	$MySql = "SELECT EvCode, EvFinalFirstPhase, EvElim1, EvElim2 FROM Events WHERE EvTournament=" . StrSafe_DB($TournamentID) . " AND EvTeamEvent=0";
	$rs = safe_r_SQL($MySql);
	$eventsC=array();
	while($MyRow = safe_fetch($rs))
	{
		$eventsC[] = $MyRow->EvCode . "@-3";
		if($MyRow->EvElim1>0)
			$eventsC[] = $MyRow->EvCode . "@-1";
		if($MyRow->EvElim2>0)
			$eventsC[] = $MyRow->EvCode . "@-2";
		$eventsC[] = $MyRow->EvCode . "@" . $MyRow->EvFinalFirstPhase;
	}
	Obj_RankFactory::create('FinalInd',array('tournament'=>$TournamentID,'eventsC'=>$eventsC))->calculate();
	safe_free_result($rs);
}

function calcMaxTeamPerson_20110216($TournamentID)
{
	$events=array();

	$q="
		SELECT EvCode
		FROM
			Events
		WHERE
			EvTournament={$TournamentID} AND EvTeamEvent=1
	";
	$r=safe_r_sql($q);

	if (safe_num_rows($r)>0)
	{
		while ($row=safe_fetch($r))
		{
			$events[]=$row->EvCode;
		}
	}

	calcMaxTeamPerson($events,true,$TournamentID);
}

function recalculateTeamRanking_20110216($TournamentID)
{
// per tutte le squadre del torneo ricalcolo le 3 rank
	$rank=Obj_RankFactory::create('DivClassTeam',array('tournament'=>$TournamentID));
	if ($rank)
		$rank->calculate();

	$rank=Obj_RankFactory::create('AbsTeam',array('tournament'=>$TournamentID));
	if ($rank)
		$rank->calculate();

	$MySql = "SELECT EvCode, EvFinalFirstPhase FROM Events WHERE EvTournament=" . StrSafe_DB($TournamentID) . " AND EvTeamEvent=1";
	$rs = safe_r_SQL($MySql);
	$eventsC=array();
	while($MyRow = safe_fetch($rs))
	{
		$eventsC[] = $MyRow->EvCode . "@-3";
		$eventsC[] = $MyRow->EvCode . "@" . $MyRow->EvFinalFirstPhase;
	}
	$rank=Obj_RankFactory::create('FinalTeam',array('eventsC'=>$eventsC, 'tournament'=>$TournamentID));
	if ($rank)
		$rank->calculate();
}

function getMapsGoldsXNineChars_20110309()
{
	// mappa x i gold
	$goldMap=array(
		'10'=>'L',
		'6+5'=>'FG',
		'11'=>'M'
	);

	$xnineMap=array(
		'X'=>'K',
		'9'=>'J',
		'6'=>'G',
		'10'=>'L'
	);

	return array('G'=>$goldMap,'X'=>$xnineMap);
}


function initTourGoldsXNineChars_20110309($TournamentID)
{
	$maps=getMapsGoldsXNineChars_20110309();

	$sql="SELECT ToGolds,ToXNine FROM Tournament WHERE ToId={$TournamentID} ";
	$r=safe_r_sql($sql);

	if ($r && safe_num_rows($r)==1)
	{
		$row=safe_fetch($r);

		$gold=$maps['G'][$row->ToGolds];
		$xnine=$maps['X'][$row->ToXNine];

		$sql="UPDATE Tournament SET ToGoldsChars='{$gold}',ToXNineChars='{$xnine}' WHERE ToId={$TournamentID} ";
		$r=safe_w_sql($sql);
	}
}

function RecalcFinRank_20110415($TournamentID)
{
// per tutti gli eventi ricalcolo le rank finali
	$q="
		SELECT
			EvCode,IF(EvTeamEvent=0,'I','T') AS `Team`,EvFinalFirstPhase,EvElim1,EvElim2
		FROM
			Events
		WHERE
			EvTournament={$TournamentID}
	";
	$r=safe_r_sql($q);

	if (safe_num_rows($r)>0)
	{
		$eventsI=array();
		$eventsT=array();

		while ($row=safe_fetch($r))
		{
		// calcolo di sicuro chi si è fermato agli assoluti
			${'events'.$row->Team}[]=$row->EvCode.'@-3';

		// se ho un girone elim
			if ($row->EvElim2!=0 && $row->EvElim1==0)
			{
				${'events'.$row->Team}[]=$row->EvCode.'@-2';
			}
		// e se ne ho due
			elseif ($row->EvElim2!=0 && $row->EvElim1!=0)
			{
				${'events'.$row->Team}[]=$row->EvCode.'@-1';
				${'events'.$row->Team}[]=$row->EvCode.'@-2';
			}

		// dalla prima fase finale
			${'events'.$row->Team}[]=$row->EvCode.'@'.$row->EvFinalFirstPhase;
		}

		Obj_RankFactory::create('FinalInd',array('eventsC'=>$eventsI,'tournament'=>$TournamentID))->calculate();
		Obj_RankFactory::create('FinalTeam',array('eventsC'=>$eventsT,'tournament'=>$TournamentID))->calculate();
	}
}

function Update3DIta_20120111($TournamentID)
{
	$q="
			UPDATE Tournament
			SET ToTypeSubRule='Set1Dist1Arrow'
			WHERE
				ToId={$TournamentID} AND ToLocRule='IT' AND ToType=11 AND ToTypeSubRule=''
		";
	$r=safe_w_sql($q);
}

function UpdateWinLose_20140322($TourId=0) {
	// Updating Winner of finals up to semifinals
	safe_w_sql("update Finals f1
			inner join Finals f2 on f1.FinTournament=f2.FinTournament and f1.FinEvent=f2.FinEvent and f1.FinAthlete=f2.FinAthlete and (f2.FinMatchNo=floor(f1.FinMatchNo/2) or (f1.FinMatchNo in (4,5,6,7) and f2.FinMatchNo in (0,1))) and f2.FinMatchNo!=f1.FinMatchNo
			set f1.FinWinLose=1
			where
			f2.FinMatchNo not in (2,3) and
			f1.FinAthlete!=0"
			.($TourId ? " and f1.FinTournament=$TourId" : ''));

	safe_w_sql("update TeamFinals f1
			inner join TeamFinals f2 on f1.TfTournament=f2.TfTournament and f1.TfEvent=f2.TfEvent and f1.TfTeam=f2.TfTeam and f1.TfSubTeam=f2.TfSubTeam and (f2.TfMatchNo=floor(f1.TfMatchNo/2) or (f1.TfMatchNo in (4,5,6,7) and f2.TfMatchNo in (0,1))) and f2.TfMatchNo!=f1.TfMatchNo
			set f1.TfWinLose=1
			where
			f2.TfMatchNo not in (2,3) and
			f1.TfTeam!=0"
			.($TourId ? " and f1.TfTournament=$TourId" : ''));

	// Update the medal matches
	safe_w_sql("Update Finals
			inner join Individuals on FinTournament=IndTournament and FinEvent=IndEvent and FinAthlete=IndId and FinMatchNo<4
			set FinWinLose=1
			where IndRankFinal in (1,3)"
			.($TourId ? " and FinTournament=$TourId" : ''));

	safe_w_sql("Update TeamFinals
			inner join Teams on TfTournament=TeTournament and TeEvent=TeEvent and TfTeam=TeCoId and TfSubTeam=TeSubTeam and TfMatchNo<4
			set TfWinLose=1
			where TeRankFinal in (1,3)"
			.($TourId ? " and TfTournament=$TourId" : ''));
}

function UpdateItaRules_20140401($TourId=0) {
	safe_w_sql("UPDATE Tournament SET ToGolds='6',ToXNine='5',ToGoldsChars='G',ToXNineChars= 'F' WHERE ToId=$TourId AND ToLocRule='IT' AND ToWhenFrom>='2014-04-01' AND ToType IN (9,10,12)");
	safe_w_sql("UPDATE Tournament SET ToGolds='11',ToXNine='10',ToGoldsChars='M',ToXNineChars= 'L' WHERE ToId=$TourId AND ToLocRule='IT' AND ToWhenFrom>='2014-04-01' AND ToType IN (11,13)");
	safe_w_sql("UPDATE Events INNER JOIN Tournament ON EvTournament=ToId SET EvMatchMode=1 WHERE ToId=$TourId AND ToLocRule='IT' AND ToWhenFrom>='2014-04-01' AND ToType IN (1,2,3,4,6,7,8,18) AND LEFT(EvCode,2)='OL'");
	safe_w_sql("UPDATE Events INNER JOIN Tournament ON EvTournament=ToId SET EvMatchMode=0 WHERE ToId=$TourId AND ToLocRule='IT' AND ToWhenFrom>='2014-04-01' AND ToType IN (1,2,3,4,6,7,8,18) AND LEFT(EvCode,2)='CO'");
	safe_w_sql("UPDATE Events INNER JOIN Tournament ON EvTournament=ToId SET EvMatchMode=1 WHERE ToId=$TourId AND ToLocRule='IT' AND ToWhenFrom>='2014-04-01' AND ToType IN (6,7,8) AND LEFT(EvCode,2)='AN'");
}

function UpdateArrowPosition_20141115($TourId=0) {
	$Sql = "SELECT FinEvent, FinMatchNo, FinTournament, FinArrowPosition, FinTiePosition, EvFinalTargetType, EvTargetSize
		FROM Finals
		INNER JOIN Events ON EvCode=FinEvent AND EvTeamEvent=0 AND EvTournament=FinTournament
		WHERE FinTournament={$TourId} AND (LENGTH(`FinArrowPosition`)>0 OR LENGTH(`FinTiePosition`)>0)
		ORDER BY FinEvent, FinMatchno";
	$r = safe_r_SQL($Sql);
	while($row = safe_fetch($r)) {
		$oldArr = explode("|",trim($row->FinArrowPosition));
		$newArr = array();
		$oldTie = explode("|",trim($row->FinTiePosition));
		$newTie = array();
		$size=($row->EvTargetSize ? $row->EvTargetSize : 122) * 50;
		switch($row->EvFinalTargetType) {
			case 2:
			case 4:
			case 10:
				$size *= 0.5;
				break;
			case 9:
				$size *= 0.6;
				break;
			case 7:
				if(substr($row->FinEvent,0,1)=="C" && $size==6100)
					$size = 80*50;
				break;
		}
		foreach($oldArr as $k=>$v) {
			if(!empty($v) && strpos($v,",")!==false) {
				$tmp = explode(",",$v);
				$newArr[$k] = array(round($size*$tmp[0]/1000,0,PHP_ROUND_HALF_DOWN), round(-1*$size*$tmp[1]/1000,0,PHP_ROUND_HALF_DOWN));
			}
		}
		foreach($oldTie as $k=>$v) {
			if(!empty($v) && strpos($v,",")!==false) {
				$tmp = explode(",",$v);
				$newTie[$k] = array(round($size*$tmp[0]/1000,0,PHP_ROUND_HALF_DOWN), round(-1*$size*$tmp[1]/1000,0,PHP_ROUND_HALF_DOWN));
			}
		}

		$Sql = "UPDATE Finals SET
			FinArrowPosition = '" . (count($newArr) ? serialize($newArr) : "") . "',
			FinTiePosition = '" . (count($newTie) ? serialize($newTie) :  "") . "'
			WHERE FinEvent='{$row->FinEvent}' AND FinMatchNo={$row->FinMatchNo} AND FinTournament={$row->FinTournament}";
		safe_w_SQL($Sql);
	}

	$Sql = "SELECT TfEvent, TfMatchNo, TfTournament, TfArrowPosition, TfTiePosition, EvFinalTargetType, EvTargetSize
		FROM TeamFinals
		INNER JOIN Events ON EvCode=TfEvent AND EvTeamEvent=1 AND EvTournament=TfTournament
		WHERE TfTournament={$TourId} AND (LENGTH(`TfArrowPosition`)>0 OR LENGTH(`TfTiePosition`)>0)
		ORDER BY TfEvent, TfMatchno";
	$r = safe_r_SQL($Sql);
	while($row = safe_fetch($r)) {
		$oldArr = explode("|",trim($row->TfArrowPosition));
		$newArr = array();
		$oldTie = explode("|",trim($row->TfTiePosition));
		$newTie = array();
		$size=($row->EvTargetSize ? $row->EvTargetSize : 122) * 50;
		switch($row->EvFinalTargetType) {
			case 2:
			case 4:
			case 10:
				$size *= 0.5;
				break;
			case 9:
				$size *= 0.6;
				break;
			case 7:
				if(substr($row->FinEvent,0,1)=="C" && $size==6100)
					$size = 80*50;
					break;
		}
		foreach($oldArr as $k=>$v) {
			if(!empty($v) && strpos($v,",")!==false) {
				$tmp = explode(",",$v);
				$newArr[$k] = array(round($size*$tmp[0]/1000,0,PHP_ROUND_HALF_DOWN), round(-1*$size*$tmp[1]/1000,0,PHP_ROUND_HALF_DOWN));
			}
		}
		foreach($oldTie as $k=>$v) {
			if(!empty($v) && strpos($v,",")!==false) {
				$tmp = explode(",",$v);
				$newTie[$k] = array(round($size*$tmp[0]/1000,0,PHP_ROUND_HALF_DOWN), round(-1*$size*$tmp[1]/1000,0,PHP_ROUND_HALF_DOWN));
			}
		}

		$Sql = "UPDATE TeamFinals SET
			TfArrowPosition = '" . (count($newArr) ? serialize($newArr) : "") . "',
			TfTiePosition = '" . (count($newTie) ? serialize($newTie) :  "") . "'
			WHERE TfEvent='{$row->TfEvent}' AND TfMatchNo={$row->TfMatchNo} AND TfTournament={$row->TfTournament}";
		safe_w_SQL($Sql);
	}
}

function UpdateToOptions_20150304($ToId=0) {
	$q=safe_r_sql("select ToOptions from Tournament where ToId=$ToId and ToOptions>''");
	if($r=safe_fetch($q)) {
		$v=unserialize($r->ToOptions);
		if(isset($v['ISK-Lite-Mode'])) {
			require_once('Common/Lib/Fun_Modules.php');
			setModuleParameter('ISK', 'ServerUrl', $v['ISK-Lite-ServerUrl']);
			setModuleParameter('ISK', 'Mode', $v['ISK-Lite-Mode']);
			unset($v['ISK-Lite-ServerUrl']);
			unset($v['ISK-Lite-Mode']);
			unset($v['ISK-ServerUrl']);
			safe_w_sql("update Tournament set ToOptions=".StrSafe_DB(serialize($v))." where ToId=$ToId");
		}
	}
}

function UpdateSetPointsByEnd_20150416($ToId=0) {
	// update Individuals Set Points by End
	$sql = "SELECT * from (
			select
				EvCode Event, @ArBit:=(EvMatchArrowsNo & pow(2, if(FinMatchNo=0, 0, floor(LOG(2, FinMatchNo))))),
				if(@ArBit=0, EvFinArrows, EvElimArrows) Arrows, if(@ArBit=0, EvElimEnds, EvFinEnds) Ends,
				FinTournament Tournament,
				FinMatchNo MatchNo,
				FinSetScore as SetScore,
				FinSetPoints SetPoints,
				FinArrowstring arrowstring
			FROM Finals
			INNER JOIN Events ON FinEvent=EvCode AND FinTournament=EvTournament AND EvTeamEvent=0 AND EvFinalFirstPhase!=0 and EvMatchMode=1
			INNER JOIN Grids ON FinMatchNo=GrMatchNo
			WHERE FinMatchNo%2=0 and trim(FinArrowstring)!='' ".($ToId ? "and FinTournament=$ToId" : "")."
			) f1 inner join (
			select
				EvCode OppEvent,
				FinTournament OppTournament,
				FinMatchNo OppMatchNo,
				FinSetScore as OppSetScore,
				FinSetPoints OppSetPoints,
				FinArrowstring oppArrowstring
			FROM Finals
			INNER JOIN Events ON FinEvent=EvCode AND FinTournament=EvTournament AND EvTeamEvent=0 AND EvFinalFirstPhase!=0 and EvMatchMode=1
			INNER JOIN Grids ON FinMatchNo=GrMatchNo
			WHERE FinMatchNo%2=1 AND trim(FinArrowstring)!='' ".($ToId ? "and FinTournament=$ToId" : "")."
			) f2 on Tournament=OppTournament and Event=OppEvent and MatchNo=OppMatchNo-1
		ORDER BY event, MatchNo ASC ";
	$q=safe_r_sql($sql);
	while($r=safe_fetch($q)) {
		$SpBeSx=array();
		$SpBeRx=array();
		$SpSx=explode('|', $r->SetPoints);
		$SpRx=explode('|', $r->OppSetPoints);
		for($i=0; $i<count($SpSx); $i++) {
			$End   =substr($r->arrowstring,    $i*$r->Arrows, $r->Arrows);
			$OppEnd=substr($r->oppArrowstring, $i*$r->Arrows, $r->Arrows);

			if(!strstr($End, ' ') and !strstr($OppEnd, ' ') and strlen($End)==$r->Arrows and strlen($OppEnd)==$r->Arrows) {
				$SpBeSx[$i]=($SpSx[$i]>$SpRx[$i] ? 2 : ($SpSx[$i]==$SpRx[$i] ? 1 : 0));
				$SpBeRx[$i]=($SpSx[$i]<$SpRx[$i] ? 2 : ($SpSx[$i]==$SpRx[$i] ? 1 : 0));
			}
		}
		safe_w_sql("update Finals set FinSetPointsByEnd='".implode('|', $SpBeSx)."' where FinEvent='$r->Event' and FinTournament='$r->Tournament' and FinMatchNo='$r->MatchNo'");
		safe_w_sql("update Finals set FinSetPointsByEnd='".implode('|', $SpBeRx)."' where FinEvent='$r->Event' and FinTournament='$r->Tournament' and FinMatchNo='$r->OppMatchNo'");
	}

	// update Teams Set Points by End
	$sql = "SELECT * from (
			select
				EvCode Event, @ArBit:=(EvMatchArrowsNo & pow(2, if(TfMatchNo=0, 0, floor(LOG(2, TfMatchNo))))),
				if(@ArBit=0, EvFinArrows, EvElimArrows) Arrows, if(@ArBit=0, EvElimEnds, EvFinEnds) Ends,
				TfTournament Tournament,
				TfTeam Team,
				TfMatchNo MatchNo,
				TfSetScore as SetScore,
				TfSetPoints SetPoints,
				TfArrowstring arrowstring
			FROM TeamFinals
			INNER JOIN Events ON TfEvent=EvCode AND TfTournament=EvTournament AND EvTeamEvent=1 AND EvFinalFirstPhase!=0 and EvMatchMode=1
			INNER JOIN Grids ON TfMatchNo=GrMatchNo
			WHERE TfMatchNo%2=0 and trim(TfArrowstring)!='' ".($ToId ? "and TfTournament=$ToId" : "")."
			) f1 inner join (
			select
				EvCode OppEvent,
				TfTournament OppTournament,
				TfTeam OppTeam,
				TfMatchNo OppMatchNo,
				TfSetScore as OppSetScore,
				TfSetPoints OppSetPoints,
				TfArrowstring oppArrowstring
			FROM TeamFinals
			INNER JOIN Events ON TfEvent=EvCode AND TfTournament=EvTournament AND EvTeamEvent=1 AND EvFinalFirstPhase!=0 and EvMatchMode=1
			INNER JOIN Grids ON TfMatchNo=GrMatchNo
			WHERE TfMatchNo%2=1 AND trim(TfArrowstring)!='' ".($ToId ? "and TfTournament=$ToId" : "")."
			) f2 on Tournament=OppTournament and Event=OppEvent and MatchNo=OppMatchNo-1
		ORDER BY event, MatchNo ASC ";
	$q=safe_r_sql($sql);
	while($r=safe_fetch($q)) {
		$SpBeSx=array();
		$SpBeRx=array();
		$SpSx=explode('|', $r->SetPoints);
		$SpRx=explode('|', $r->OppSetPoints);
		for($i=0; $i<count($SpSx); $i++) {
			$End   =substr($r->arrowstring,    $i*$r->Arrows, $r->Arrows);
			$OppEnd=substr($r->oppArrowstring, $i*$r->Arrows, $r->Arrows);

			if(!strstr($End, ' ') and !strstr($OppEnd, ' ') and strlen($End)==$r->Arrows and strlen($OppEnd)==$r->Arrows) {
				$SpBeSx[$i]=($SpSx[$i]>$SpRx[$i] ? 2 : ($SpSx[$i]==$SpRx[$i] ? 1 : 0));
				$SpBeRx[$i]=($SpSx[$i]<$SpRx[$i] ? 2 : ($SpSx[$i]==$SpRx[$i] ? 1 : 0));
			}
		}
		safe_w_sql("update TeamFinals set TfSetPointsByEnd='".implode('|', $SpBeSx)."' where TfEvent='$r->Event' and TfTournament='$r->Tournament' and TfMatchNo='$r->MatchNo'");
		safe_w_sql("update TeamFinals set TfSetPointsByEnd='".implode('|', $SpBeRx)."' where TfEvent='$r->Event' and TfTournament='$r->Tournament' and TfMatchNo='$r->OppMatchNo'");
	}
}

function UpdateSessionsFromAgileModule_20160322($ToId=0) {
	require_once('Tournament/Fun_ManSessions.inc.php');
	$Sessions = getModuleParameter('Agile', 'Sessions', array(),$ToId);
	foreach($Sessions as $kSes=>$vSes) {
		insertSession($ToId,($kSes+1),'F',$vSes[5],0,0,0,0,($vSes[1].' '.$vSes[2]),($vSes[1].' '.$vSes[3]));
	}
}