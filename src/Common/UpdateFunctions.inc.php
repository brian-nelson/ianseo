<?php
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Fun_Various.inc.php');
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