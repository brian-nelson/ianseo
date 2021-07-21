<?php

/**
* MakeTeams per la generazione delle suqadre nelle Regole UK
*/


// Elimino le squadre della qualifica
$Delete = "DELETE Teams, TeamComponent FROM "
	. "Teams, TeamComponent  "
	. "WHERE TeCoId=TcCoId AND TeEvent=TcEvent AND TeTournament=TcTournament AND TeTournament=" . StrSafe_DB($ToId)
	. (!is_null($Societa) ? ' AND TeCoId=' . StrSafe_DB($Societa) . ' AND TcCoId=' . StrSafe_DB($Societa)  : '')
	. (!is_null($Category) ? ' AND TeEvent=' . StrSafe_DB($Category) . ' AND TcEvent=' . StrSafe_DB($Category) : '')
	. " AND TeFinEvent='0' AND TcFinEvent='0' ";
$Rs=safe_w_sql($Delete);

//LOOP delle Divisioni

foreach($divisions as $div)
{
	//Estraggo le classi "Athlete" divisi in Maschi e Femmine
	$clMen = array();
	$clWomen = array();
	$sqlClasses = "SELECT DISTINCT ClId, ClSex
		FROM Classes
		WHERE ClTournament=" . StrSafe_DB($ToId) . " AND ClAthlete=1
		ORDER BY ClViewOrder";
	$rs = safe_r_sql($sqlClasses);
	while($row = safe_fetch($rs))
	{
		if($row->ClSex == 0)
			$clMen[] = StrSafe_DB($div . $row->ClId);
		else
			$clWomen[] = StrSafe_DB($div . $row->ClId);
	}
	//LOOP DEI Livelli....
	for($level=$startLevel; $level<=$endLevel; $level++)
	{
		$Select = "
			(SELECT " . ($level==0 ? "CoId" : "CoParent".$level) . " AS Country, 1 as CheQuery, EnId, QuScore, QuGold, QuXnine
			FROM Entries
			INNER JOIN Countries ON EnCountry=CoId
			INNER JOIN Qualifications ON EnId=QuId 
			inner join IrmTypes on IrmId=QuIrmType and IrmShowRank=1
			WHERE EnAthlete=1 AND EnCountry<>0  AND EnTeamClEvent=1 AND EnStatus<=1 AND QuScore<>0 AND CoCode<>'0'
			" . (!is_null($Societa) ? "AND EnCountry=" . StrSafe_DB($Societa) : "") . "
			AND EnTournament=" . StrSafe_DB($ToId) . " AND CONCAT(IF(EnDivision='B','R',EnDivision), EnClass) IN (" . implode(",",$clMen) . "))
		UNION ALL
			(SELECT " . ($level==0 ? "CoId" : "CoParent".$level) . " AS Country, 2 as CheQuery, EnId, QuScore, QuGold, QuXnine
			FROM Entries
			INNER JOIN Countries ON EnCountry=CoId
			INNER JOIN Qualifications ON EnId=QuId 
			inner join IrmTypes on IrmId=QuIrmType and IrmShowRank=1
			WHERE EnAthlete=1 AND EnCountry<>0  AND EnTeamClEvent=1 AND EnStatus<=1 AND QuScore<>0 AND CoCode<>'0'
			" . (!is_null($Societa) ? "AND EnCountry=" . StrSafe_DB($Societa) : "") . "
			AND EnTournament=" . StrSafe_DB($ToId) . " AND CONCAT(IF(EnDivision='B','R',EnDivision), EnClass) IN (" . implode(",",$clWomen) . "))
		UNION ALL
			(SELECT " . ($level==0 ? "CoId" : "CoParent".$level) . " AS Country, 3 as CheQuery, EnId, QuScore, QuGold, QuXnine
			FROM Entries
			INNER JOIN Countries ON EnCountry=CoId
			INNER JOIN Qualifications ON EnId=QuId 
			inner join IrmTypes on IrmId=QuIrmType and IrmShowRank=1
			WHERE EnAthlete=1 AND EnCountry<>0  AND EnTeamClEvent=1 AND EnStatus<=1 AND QuScore<>0 AND CoCode<>'0'
			" . (!is_null($Societa) ? "AND EnCountry=" . StrSafe_DB($Societa) : "") . "
			AND EnTournament=" . StrSafe_DB($ToId) . " AND CONCAT(IF(EnDivision='B','R',EnDivision), EnClass) IN (" . implode(",",array_merge($clMen,$clWomen)) . "))
		ORDER BY Country, CheQuery, QuScore DESC, QuGold DESC, QuXnine DESC,EnId ASC";

		$CurTeam = 0;					//Codice team attuale
		$CurSubTeam = 0;				//Codice Subteam attuale -- Sempre "0"
		$CurComponent = array();		//Componenti della Squadra
		$TeamCount = array(0,1,1,2);
		$Scores = array();
		$Golds = array();
		$XNines = array();

		$rs=safe_r_sql($Select);
		while($MyRow = safe_fetch($rs))
		{
			//Gestisco il "cambio squadra"
			if($CurTeam != $MyRow->Country)
			{
				if ($CurTeam!=0)
				{
					if (array_sum($TeamCount)>0 && count($CurComponent)>=2)	//Inserimento Team Parziale se almeno 2 persone
						WriteTeam($CurTeam,$CurSubTeam,$CurComponent,$div.$level,$Scores,$Golds,$XNines);
				}
				$CurTeam = $MyRow->Country;
				$CurComponent = array();
				$TeamCount = array(0,1,1,2);
				$Scores = array();
				$Golds = array();
				$XNines = array();
			}
			//Se sto ancora cercando componenti di squadra vedo che c'è da fare
			if(array_sum($TeamCount)>0)
			{
				if($TeamCount[$MyRow->CheQuery]>0 && !in_array($MyRow->EnId,$CurComponent))
				{
					$CurComponent[] = $MyRow->EnId;
					$Scores[] = $MyRow->QuScore;
					$Golds[] = $MyRow->QuGold;
					$XNines[] = $MyRow->QuXnine;
					$TeamCount[$MyRow->CheQuery]--;
					//Se non mi manca + nessuno, allora salvo
					if(array_sum($TeamCount)==0)
						WriteTeam($CurTeam,$CurSubTeam,$CurComponent,$div.$level,$Scores,$Golds,$XNines);
				}
			}
		}
		if (array_sum($TeamCount)>0 && count($CurComponent)>=2)	//Inserimento Team Parziale se almeno 2 persone
			WriteTeam($CurTeam,$CurSubTeam,$CurComponent,$div.$level,$Scores,$Golds,$XNines);
	}
}

function WriteTeam($CurTeam,$CurSubTeam,$CurComponent,$EventCode,$Scores,$Golds,$XNines)
{
	// Insert in Teams
	$InsertQuery
	= "REPLACE INTO Teams (TeCoId,TeSubTeam,TeEvent,TeTournament,TeFinEvent,TeScore,TeGold,TeXNine,TeFinal) "
	. "VALUES("
	. StrSafe_DB($CurTeam) . ","
	. StrSafe_DB($CurSubTeam) . ","
	. StrSafe_DB($EventCode) . ","
	. StrSafe_DB($_SESSION['TourId']) . ","
	. "'0',"
	. StrSafe_DB(array_sum($Scores)) . ","
	. StrSafe_DB(array_sum($Golds)) . ","
	. StrSafe_DB(array_sum($XNines)) . ","
	. "'0'"
	. ") ";
	$RsT=safe_w_sql($InsertQuery);

	if(empty($CurComponent)) return;

	$InsertQuery = "REPLACE INTO TeamComponent (TcCoId,TcSubTeam, TcTournament,TcEvent,TcFinEvent,TcId,TcOrder) VALUES ";
	for($i=0; $i<count($CurComponent); $i++)
	{
	$InsertQuery .= "("
				. StrSafe_DB($CurTeam) . ","
				. StrSafe_DB($CurSubTeam) . ","
				. StrSafe_DB($_SESSION['TourId']) . ","
				. StrSafe_DB($EventCode) . ","
				. "'0',"
				. StrSafe_DB($CurComponent[$i]) . ","
				. StrSafe_DB($i+1)
	. '), ';
		}
	$InsertQuery = substr($InsertQuery,0,-2);
	$RsT=safe_w_sql($InsertQuery);
}
?>