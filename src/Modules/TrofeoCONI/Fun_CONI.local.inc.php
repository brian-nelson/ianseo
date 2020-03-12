<?php
	define('EndPos',16);
	define('MaxRound',3);

	$volee=4;						// volee per il camp ita soc
	$subvoleeArrows=3;				// lunghezza delle sottovolee x il camp ita soc
	$bows=array('OL','AN','CO');	// archi per il camp ita soc

	$Letters=array('A','B','C');	// OL AN CO
	$DivOnLetter=array('A'=>'OL','B'=>'AN','C'=>'CO');

	function makeComboEvent()
	{
		$Query
			= "SELECT EvCode,EvTournament,	EvEventName "
			. "FROM Events "
			. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='1' "
			. "ORDER BY EvProgr ASC ";
		$Rs=safe_r_sql($Query);

		$opts='';
		//print $Query;exit;
		if ($Rs && safe_num_rows($Rs)>0)
		{
			while ($MyRow=safe_fetch($Rs))
			{
				$opts.='<option value="' . $MyRow->EvCode . '">' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true) . '</option>' . "\n";
			}
		}

		$comboEvent
			= '<select name="EventCode">' . "\n"
				. $opts
			. '</select>' . "\n";

		return $comboEvent;
	}

	function makeComboRound($selected=null,$other=null,$opts='')
	{
		$comboRound= '<select name="Round" id="Round" ' . $opts . '>' . "\n";
			if (!is_null($other) && is_array($other))
				$comboRound.='<option value="' . $other[0]. '">' . $other[1].'</option>' . "\n";

			for ($i=1;$i<=MaxRound;++$i)
			{
				$comboRound.='<option value="' . $i .'"' . (!is_null($selected) && $selected==$i ? ' selected' : '') . '>' . get_text('Round#','Tournament',$i) . '</option>' . "\n";
			}
		$comboRound.='</select>' . "\n";

		return $comboRound;
	}

	function roundPager($round,$qs)
	{
		$prev='<a class="Link" href="' . $_SERVER['PHP_SELF'] . '?Round=' . ($round-1) . '&' . $qs . '">&lt;&lt;</a>';
		$next='<a class="Link" href="' . $_SERVER['PHP_SELF'] . '?Round=' . ($round+1) . '&' . $qs . '">&gt;&gt;</a>';

		if ($round==MaxRound)
		{
			$next='';
		}
		elseif ($round==1)
		{
			$prev='';
		}

		return $prev . '&nbsp;&nbsp;' . get_text('Round','Tournament') . '&nbsp;&nbsp;' . $next;
	}

	function getMatchesPhase1($event,$round,$phase=1,$otherWhere="",$limit="")
	{
		$Query
			= "SELECT "
				. "IFNULL(TeamScore1.CaSTarget,'') AS TargetNo1,"
				. "IFNULL(TeamScore2.CaSTarget,'') AS TargetNo2,"
				. "CG.CGMatchNo1 AS Match1,CG.CGMatchNo2 AS Match2,CG.CGGroup AS `Group`,CG.CGRound AS Round, Ev.EvCode AS EventCode, Ev.EvEventName AS EventName, "

				. "IFNULL(Team1.CaTeam,'') AS TeamCode1,IFNULL(Team1.CaSubTeam,'') AS SubTeamCode1,"
				. "IFNULL(Country1.CoCode,'') AS CountryCode1,IFNULL(Country1.CoName,'') AS CountryName1, "
				. "IFNULL(TeamScore1.CaSScore,'0') AS Score1, IFNULL(TeamScore1.CaSTie,'0') AS Tie1, IFNULL(TeamScore1.CaSTiebreak,'') AS Tiebreak1, "
				. "IFNULL(TeamScore1.CaSArrowString,'0') AS ArrowString1, IFNULL(TeamScore1.CaSPoints,'0') AS Point1,"
				. "IFNULL(TeamScore1.CaSSetPoints,'') AS SetPoints1, IFNULL(TeamScore1.CaSSetScore,0) AS SetScore1,"

				. "IFNULL(Team2.CaTeam,'') AS TeamCode2,IFNULL(Team2.CaSubTeam,'') AS SubTeamCode2,"
				. "IFNULL(Country2.CoCode,'') AS CountryCode2,IFNULL(Country2.CoName,'') AS CountryName2, "
				. "IFNULL(TeamScore2.CaSScore,'0') AS Score2 , IFNULL(TeamScore2.CaSTie,'0') AS Tie2, IFNULL(TeamScore2.CaSTiebreak,'') AS Tiebreak2, "
				. "IFNULL(TeamScore2.CaSArrowString,'0') AS ArrowString2, IFNULL(TeamScore2.CaSPoints,'0') AS Point2, "
				. "IFNULL(TeamScore2.CaSSetPoints,'') AS SetPoints2,IFNULL(TeamScore2.CaSSetScore,0) AS SetScore2 "

				//. "TeamScore1.*,TeamScore2.*,CTG.*,Country1.*,Country2.*,Ev.* "

			. "FROM "
				. "CasGrid AS CG "

				. "LEFT JOIN "
					. "CasTeam AS Team1 "
				. "ON CG.CGPhase=Team1.CaPhase AND CG.CGMatchNo1=Team1.CaMatchNo AND Team1.CaTournament=" . StrSafe_DB($_SESSION['TourId']) .  " AND Team1.CaEventCode=" . StrSafe_DB($event) . "  "

				. "LEFT JOIN "
					. "Countries AS Country1 "
				. "ON Team1.CaTeam=Country1.CoId AND Team1.CaTournament=Country1.CoTournament AND Team1.CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " "

				. "LEFT JOIN "
					. "CasScore AS TeamScore1 "
				. "ON CG.CGMatchNo1=TeamScore1.CaSMatchNo AND CG.CGRound=TeamScore1.CaSRound AND TeamScore1.CaSEventCode=Team1.CaEventCode AND  TeamScore1.CaSPhase=CG.CGPhase AND Team1.CaTournament=TeamScore1.CaSTournament AND Team1.CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " "

				. "LEFT JOIN "
					. "CasTeam AS Team2 "
				. "ON CG.CGPhase=Team2.CaPhase AND CG.CGMatchNo2=Team2.CaMatchNo AND Team2.CaTournament=" . StrSafe_DB($_SESSION['TourId']) .  " AND Team2.CaEventCode=" . StrSafe_DB($event) . "  "

				. "LEFT JOIN "
					. "Countries AS Country2 "
				. "ON Team2.CaTeam=Country2.CoId AND Team2.CaTournament=Country2.CoTournament AND Team2.CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " "

				. "LEFT JOIN "
					. "CasScore AS TeamScore2 "
				. "ON CG.CGMatchNo2=TeamScore2.CaSMatchNo AND CG.CGRound=TeamScore2.CaSRound AND TeamScore2.CaSEventCode=Team2.CaEventCode AND  TeamScore2.CaSPhase=CG.CGPhase AND Team2.CaTournament=TeamScore2.CaSTournament AND Team2.CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " "

				. "LEFT JOIN "
					. "Events AS Ev "
				. "ON Team1.CaEventCode=Ev.EvCode AND Team1.CaTournament=Ev.EvTournament AND Ev.EvTeamEvent=1 "


			. "WHERE "
				. "CG.CGPhase=" . StrSafe_DB($phase) . " ";
		if($round!= 0 )
			$Query .= "AND CGRound=" . StrSafe_DB($round) . " ";
		$Query .= $otherWhere . " ";
		$Query .= "ORDER BY CGGroup ASC, CGRound ASC, CGMatchNo1 ASC ";
		if ($limit!="")
			$Query.="LIMIT " . $limit;

		//print $Query.'<br><br>';exit;

		$Rs=safe_r_sql($Query);

		return $Rs;
	}



	function sql_getMatchesPhase1($where="",$limit="")
	{
		$Query
			= "SELECT "
				. "IFNULL(TeamScore1.CaSTarget,'') AS TargetNo1,"
				. "IFNULL(TeamScore2.CaSTarget,'') AS TargetNo2,"
				. "CG.CGMatchNo1 AS Match1,CG.CGMatchNo2 AS Match2,CG.CGGroup AS `Group`,CG.CGRound AS Round, Ev.EvCode AS EventCode, Ev.EvEventName AS EventName, "

				. "IFNULL(Team1.CaTeam,'') AS TeamCode1,IFNULL(Team1.CaSubTeam,'') AS SubTeamCode1,"
				. "IFNULL(Country1.CoCode,'') AS CountryCode1,IFNULL(Country1.CoName,'') AS CountryName1, "
				. "IFNULL(TeamScore1.CaSScore,'0') AS Score1, IFNULL(TeamScore1.CaSTie,'0') AS Tie1, IFNULL(TeamScore1.CaSTiebreak,'') AS Tiebreak1, "
				. "IFNULL(TeamScore1.CaSArrowString,'0') AS ArrowString1, IFNULL(TeamScore1.CaSPoints,'0') AS Point1,"
				. "IFNULL(TeamScore1.CaSSetPoints,'') AS SetPoints1,IFNULL(TeamScore1.CaSSetScore,0) AS SetScore1, "


				. "IFNULL(Team2.CaTeam,'') AS TeamCode2,IFNULL(Team2.CaSubTeam,'') AS SubTeamCode2,"
				. "IFNULL(Country2.CoCode,'') AS CountryCode2,IFNULL(Country2.CoName,'') AS CountryName2, "
				. "IFNULL(TeamScore2.CaSScore,'0') AS Score2 , IFNULL(TeamScore2.CaSTie,'0') AS Tie2, IFNULL(TeamScore2.CaSTiebreak,'') AS Tiebreak2, "
				. "IFNULL(TeamScore2.CaSArrowString,'0') AS ArrowString2, IFNULL(TeamScore2.CaSPoints,'0') AS Point2, "
				. "IFNULL(TeamScore2.CaSSetPoints,'') AS SetPoints2,IFNULL(TeamScore2.CaSSetScore,0) AS SetScore2 "

				//. "TeamScore1.*,TeamScore2.*,CTG.*,Country1.*,Country2.*,Ev.* "

			. "FROM "
				. "CasGrid AS CG "

				. "INNER JOIN "
					. "CasTeam AS Team1 "
				. "ON CG.CGPhase=Team1.CaPhase AND CG.CGMatchNo1=Team1.CaMatchNo AND Team1.CaTournament=" . StrSafe_DB($_SESSION['TourId']) .  "  "

				. "INNER JOIN "
					. "CasScore AS TeamScore1 "
				. "ON CG.CGMatchNo1=TeamScore1.CaSMatchNo AND CG.CGRound=TeamScore1.CaSRound AND TeamScore1.CaSEventCode=Team1.CaEventCode AND  TeamScore1.CaSPhase=CG.CGPhase AND Team1.CaTournament=TeamScore1.CaSTournament AND Team1.CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " "

				. "INNER JOIN "
					. "CasTeam AS Team2 "
				. "ON CG.CGPhase=Team2.CaPhase AND CG.CGMatchNo2=Team2.CaMatchNo AND Team2.CaTournament=" . StrSafe_DB($_SESSION['TourId']) .  "   "

				. "INNER JOIN "
					. "CasScore AS TeamScore2 "
				. "ON CG.CGMatchNo2=TeamScore2.CaSMatchNo AND CG.CGRound=TeamScore2.CaSRound AND TeamScore2.CaSEventCode=Team2.CaEventCode AND  TeamScore2.CaSPhase=CG.CGPhase AND Team2.CaTournament=TeamScore2.CaSTournament AND Team2.CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " "

				. "INNER JOIN "
					. "Events AS Ev "
				. "ON Team1.CaEventCode=Ev.EvCode AND Team1.CaTournament=Ev.EvTournament AND Ev.EvTeamEvent=1 "

				. "LEFT JOIN "
					. "Countries AS Country1 "
				. "ON Team1.CaTeam=Country1.CoId AND Team1.CaTournament=Country1.CoTournament AND Team1.CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " "

				. "LEFT JOIN "
					. "Countries AS Country2 "
				. "ON Team2.CaTeam=Country2.CoId AND Team2.CaTournament=Country2.CoTournament AND Team2.CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";

		if ($where!="")
			$Query.="WHERE " . $where . " ";


		$Query .= "ORDER BY  EvCode ASC,CGGroup ASC,CGRound ASC ,CGMatchNo1 ASC ";

		if ($limit!="")
			$Query.="LIMIT " . $limit;

		//print $Query;exit;

		$Rs=safe_r_sql($Query);

		return $Rs;
	}

	/**
	 * Estae la rank della fase 2 dell'evento $event dei gironi $g usando $tergetType
	 * come tipo bersaglio per valutare il tiebreak
	 * @param string $event: evento
	 * @param int[] $g: gruppi
	 * @return mixed: righe contenenti le squadre in ranking
	 */
function rankPhase2($event,$g)
{
	$groups=array
	(
		1=>1,		// E
		2=>1,		// F
		3=>9,		// G
		4=>13		// H
	);

	$rows=array();

	$Query = "SELECT CaTeam,CaSubTeam,CaEventCode,CaMatchNo, CoCode,CoName,CGGroup,SUM(CaSPoints) AS Points,SUM(CaSScore) AS Score,SUM(CaSSetScore) AS SetScore,CaTiebreak, CaRank "
			. "FROM CasTeam "
			. "INNER JOIN Countries ON CaTeam=CoId "
			. "INNER JOIN CasGrid ON CaPhase=CGPhase AND (CaMatchNo=CGMatchNo1 OR CaMatchNo=CGMatchNo2) "
			. "INNER JOIN CasScore ON CaTournament=CaSTournament AND CaPhase=CaSPhase AND CaMatchNo=CaSMatchNo AND  CaEventCode=CaSEventCode AND CGRound=CaSRound "
			. "WHERE CaEventCode=" . StrSafe_DB($event) . " AND CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CaPhase=2 AND CGGroup IN(" . join(',',$g) . ")"
			. "GROUP BY CaPhase,CaEventCode,CGGroup,CaTeam,CaSubTeam,CaMatchNo,CoCode,CoName,CaTiebreak,CaRank "
			. "ORDER BY CaEventCode ASC, CGGroup ASC, SUM(CaSPoints) DESC, SUM(CaSScore) DESC, CaRank ASC ";
	//print $Query . '<br>';
	$Rs=safe_r_sql($Query);

// Variabili per la gestione del ranking
	$myRank = 0;
	$myPos = 0;

// Variabili che contengono i punti del precedente atleta per la gestione del rank
	$OldPoints = 0;
	$OldScore = 0;
	$OldSetScore=0;
	$OldTie = 0;

	$curGroup='xxxx';

	while ($MyRow=safe_fetch($Rs))
	{
		if ($curGroup!=$MyRow->CGGroup)
		{
			$myRank=$groups[$MyRow->CGGroup];
			$myPos=$myRank-1;

			$OldPoints = 0;
			$OldScore = 0;
			$OldSetScore=0;
			$OldTie = 0;
		}

		++$myPos;
		if(! ($MyRow->Points == $OldPoints && $MyRow->Score == $OldScore && $MyRow->SetScore==$OldSetScore && $MyRow->CaTiebreak == $OldTie))
			$myRank=$myPos;

	//Valuto il TieBreak
		$TmpTie = '';

		if(strlen(trim($MyRow->CaTiebreak)) > 0)
		{
			for($countArr=0; $countArr<strlen(trim($MyRow->CaTiebreak)); $countArr = $countArr+3)
				$TmpTie .= ValutaArrowString(substr(trim($MyRow->CaTiebreak),$countArr,3)) . ",";
			$TmpTie = substr($TmpTie,0,-1);
		}

		$r=new StdClass();
		$r->Group=$MyRow->CGGroup;
		$r->Rank=($MyRow->CaRank == 0 ? $myRank : $MyRow->CaRank);
		$r->CountryId=$MyRow->CaTeam . $MyRow->CaSubTeam;
		$r->Country=$MyRow->CoCode . ' - ' .$MyRow->CoName . ($MyRow->CaSubTeam>1 ? " (" . $MyRow->CaSubTeam . ")" : "");
		$r->Points=$MyRow->Points . "#";
		$r->Score=$MyRow->Score . "#";
		$r->Tie=$TmpTie;

		$rows[]=$r;

		$OldPoints = $MyRow->Points;
		$OldScore = $MyRow->Score;
		$OldSetScore = $MyRow->SetScore;
		$OldTie = $MyRow->CaTiebreak;

		$curGroup = $MyRow->CGGroup;
	}

	return $rows;
}

function finalRank5_8($event)
{
	$rows=array();

	$SubQuery
		= "SELECT "
			. "CONCAT(TfTeam,TfSubTeam) "
		. "FROM "
			. "TeamFinals "
			. "INNER JOIN "
				. "Grids "
			. "ON TfMatchNo=GrMatchNo AND GrPhase<=2 AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "WHERE "
			. "TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent=" . StrSafe_DB($event) . " ";

	$Query = "SELECT CaTeam,CaSubTeam,CaEventCode,CaMatchNo, CoCode,CoName,CGGroup,SUM(CaSPoints) AS Points,SUM(CaSScore) AS Score,SUM(CaSSetScore) AS SetScore,CaTiebreak "
		. "FROM CasTeam "
		. "INNER JOIN Countries ON CaTeam=CoId "
		. "INNER JOIN CasGrid ON CaPhase=CGPhase AND (CaMatchNo=CGMatchNo1 OR CaMatchNo=CGMatchNo2) "
		. "INNER JOIN CasScore ON CaTournament=CaSTournament AND CaPhase=CaSPhase AND CaMatchNo=CaSMatchNo AND  CaEventCode=CaSEventCode AND CGRound=CaSRound "
		. "WHERE CaEventCode=" . StrSafe_DB($event) . " AND CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CaPhase=2 AND CGGroup IN(1,2) "
			. "AND CONCAT(CaTeam,CaSubTeam) NOT IN (" . $SubQuery . ") "
		. "GROUP BY CaPhase,CaEventCode,CaTeam,CaSubTeam,CaMatchNo,CoCode,CoName,CaTiebreak,CaRank "
		. "ORDER BY CaEventCode ASC,  SUM(CaSPoints) DESC, SUM(CaSScore) DESC ";

	//print $Query;

	$Rs=safe_r_sql($Query);

	// Variabili per la gestione del ranking
	$myRank = 5;		// qui si parte da 5
	$myPos = $myRank-1;

// Variabili che contengono i punti del precedente atleta per la gestione del rank
	$OldPoints = 0;
	$OldScore = 0;
	$OldSetScore=0;
	$OldTie = 0;

	while ($MyRow=safe_fetch($Rs))
	{
		++$myPos;
		if(! ($MyRow->Points == $OldPoints && $MyRow->Score == $OldScore &&  $MyRow->SetScore==$OldSetScore && $MyRow->CaTiebreak == $OldTie))
			$myRank=$myPos;

		//Valuto il TieBreak
		$TmpTie = '';

		if(strlen(trim($MyRow->CaTiebreak)) > 0)
		{
			for($countArr=0; $countArr<strlen(trim($MyRow->CaTiebreak)); $countArr = $countArr+3)
				$TmpTie .= ValutaArrowString(substr(trim($MyRow->CaTiebreak),$countArr,3)) . ",";
			$TmpTie = substr($TmpTie,0,-1);
		}

		$r=new StdClass();
		$r->Group=$MyRow->CGGroup;
		$r->Rank= $myRank;
		$r->CountryId=$MyRow->CaTeam . $MyRow->CaSubTeam;
		$r->Country=$MyRow->CoCode . ' - ' . $MyRow->CoName . ($MyRow->CaSubTeam>1 ? " (" . $MyRow->CaSubTeam . ")" : "");
		$r->Points=$MyRow->Points . '#';
		$r->Score=$MyRow->Score . '#';
		$r->Tie=$TmpTie;

		$rows[]=$r;

		$OldPoints = $MyRow->Points;
		$OldScore = $MyRow->Score;
		$OldSetScore = $MyRow->SetScore;
		$OldTie = $MyRow->CaTiebreak;

	}

	return $rows;
}

function finalRankFirst4($event)
{
	$rows=array();

	$Query
		= "SELECT "
			. "TfTeam, TfSubTeam, CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) AS TeamName, CoCode, "
			. "TfSetScore, TfScore, TfTie, GrPhase "
		. "FROM "
			. "TeamFinals "
			. "INNER JOIN "
				. "Grids "
			. "ON TfMatchNo=GrMatchNo AND GrPhase<2 AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "INNER JOIN "
				. "Countries "
			. "ON TfTeam=CoId AND TfTournament=CoTournament "
		. "WHERE "
			. "TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent=" . StrSafe_DB($event) . " "
		. "ORDER BY "
			. "GrPhase ASC,TfSetScore DESC, TfScore DESC, TfTie DESC ";
	//print $Query;

	$Rs=safe_r_sql($Query);

	// Variabili per la gestione del ranking
	$myRank = 1;
	$myPos = 0;

// Variabili che contengono i punti del precedente atleta per la gestione del rank
	$OldScore = 0;
	$OldSetScore = 0;
	$OldTie = 0;

	while ($MyRow=safe_fetch($Rs))
	{
		++$myPos;
		if ($MyRow->GrPhase<=1)
		{
			if(! ($MyRow->TfSetScore == $OldSetScore && $MyRow->TfScore == $OldScore && $MyRow->TfTie == $OldTie))
				$myRank=$myPos;
		}
/*		else
		{
			$myRank=3;
		}*/
		//print $myRank . '  ' . $MyRow->TeamName .  '<br>';

		$r=new StdClass();

		$r->Rank=$myRank;
		$r->CountryId=$MyRow->TfTeam . $MyRow->TfSubTeam;
		$r->Country=$MyRow->CoCode . ' - ' .  $MyRow->TeamName;
		$r->Points=$MyRow->TfSetScore . '#';
		$r->Score=$MyRow->TfScore . '#';
		$r->Tie=$MyRow->TfTie;

		$rows[]=$r;

		$OldScore = $MyRow->TfScore;
		$OldSetScore = $MyRow->TfSetScore;
		$OldTie = $MyRow->TfTie;
	}

	return $rows;
}

function finalRankFirst4CampItaSoc($event)
{
	$rows=array();

	$Query
		= "SELECT "
			. "TfTeam,TfSubTeam, CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) AS TeamName, CoCode, "
			. "CTFSetScore AS SetScore,CTFScore AS Score,CTFTieScore AS TieScore,CTFScore2 AS Score2,GrPhase "
		. "FROM "
			. "TeamFinals "
			. "INNER JOIN "
				. "Grids "
			. "ON TfMatchNo=GrMatchNo AND GrPhase<2 AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "INNER JOIN "
				. "Countries "
			. "ON TfTeam=CoId AND TfTournament=CoTournament "
			. "INNER JOIN "
				. "CasTeamFinal "
			. "ON CTFEvent=TfEvent AND CTFMatchNo=TfMatchNo AND CTFTournament=TfTournament 	"
		. "WHERE "
			. "TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent=" . StrSafe_DB($event) . " "
		. "ORDER BY "
			. "GrPhase ASC,CTFSetScore DESC, CTFScore DESC,CTFTieScore DESC,CTFScore2 DESC ";
	//print $Query;

	$Rs=safe_r_sql($Query);

	$myRank = 1;
	$myPos = 0;

// Variabili che contengono i punti del precedente atleta per la gestione del rank
	$OldScore = 0;
	$OldSetScore=0;
	$OldTieScore=0;
	$OldScore2=0;
	$OldTie = 0;

	while ($MyRow=safe_fetch($Rs))
	{
		++$myPos;
		if ($MyRow->GrPhase==0)
		{
			if(! ($MyRow->SetScore == $OldSetScore && $MyRow->Score==$OldScore && $MyRow->TieScore==$OldTieScore && $MyRow->Score2== $OldScore2 ))
				$myRank=$myPos;
		}
		else
		{
			$myRank=3;
		}
		//print $myRank . '  ' . $MyRow->TeamName .  '<br>';

		$r=new StdClass();

		$r->Rank=$myRank;
		$r->CountryId=$MyRow->TfTeam . $MyRow->TfSubTeam;
		$r->Country=$MyRow->CoCode . ' - ' .  $MyRow->TeamName;
		$r->Points=$MyRow->SetScore . '#';
		$r->Score=$MyRow->Score . '#';
		if ($MyRow->Score2>0)
			$r->Tie=$MyRow->TieScore . ':' . $MyRow->Score2;
		else
			$r->Tie='';

		$rows[]=$r;

		$OldScore = $MyRow->Score;
		$OldTieScore = $MyRow->TieScore;
		$OldSetScore = $MyRow->SetScore;
		$OldScore2 = $MyRow->Score2;

	}

	return $rows;
}

function getTeamComponents($country, $event)
{
	$rows=array();

	$Query
		= "SELECT "
			. "CONCAT(EnFirstName,' ',EnName) AS Archer "
		. "FROM "
			. "TeamComponent "
			. "RIGHT JOIN "
				. "Entries "
			. "ON TcId=EnId "
		. "WHERE "
			. "TcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TcFinEvent=1 AND "
				. "CONCAT(TcCoId,TcSubTeam)=" . StrSafe_DB($country) . " AND TcEvent = " . StrSafe_DB($event) 
		. "ORDER BY "
			. "TcOrder ASC ";
	//print $Query . '<br><br>';
	$Rs=safe_r_sql($Query);

	while ($MyRow=safe_fetch($Rs))
	{
		$r=new StdClass();
		$r->Archer=$MyRow->Archer;
		$rows[]=$r;
	}

	return $rows;
}

function saveArrowStringCampItaSoc($event,$round,$phase,$matchNo,$arrowString,$target,$tiebreak=false)
{
	global $volee;
	global $subvoleeArrows;
	global $bows;

	$fields2up='';
	$score=0;

// valuto l'arrowstring
	if (!$tiebreak)
	{
		$score=ValutaArrowString($arrowString);

		$fields2up="CaSScore=" . $score . ",CaSArrowString='" . $arrowString. "' ";
	}
	else
	{
		$fields2up="CaSTiebreak='" . $arrowString. "' ";
	}


	$query
		= "UPDATE "
			. "CasScore "
		. "SET "
			. $fields2up
		. "WHERE "
			. "CaSTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CaSPhase=" . StrSafe_DB($phase) . " AND "
				. "CaSRound=" . StrSafe_DB($round) . " AND CaSMatchNo=" . StrSafe_DB($matchNo)  ." AND CaSEventCode=" . StrSafe_DB($event) . " ";
	$rs=safe_w_sql($query);
	//print $query . '<br><br>';
	if ($rs)
		return true;
	else
		return false;
}

function savePointsCampItaSoc($event,$round,$phase,$matchNo1,$matchNo2,$target)
{
	global $volee;
	global $subvoleeArrows;
	global $bows;

// tiro fuori le due arrowstring
	$arrowStrings[$matchNo1]=str_pad('',count($bows)*$subvoleeArrows*$volee,' ',STR_PAD_RIGHT);
	$arrowStrings[$matchNo2]=str_pad('',count($bows)*$subvoleeArrows*$volee,' ',STR_PAD_RIGHT);

	$where="AND CG.CGMatchNo1=" . $matchNo1 . " AND CG.CGMatchNo2=" . $matchNo2 . " ";
	$rs=getMatchesPhase1($event,$round,$phase,$where);

	if (!($rs && safe_num_rows($rs)==1))
		return false;

	$row=safe_fetch($rs);

	if ($row->ArrowString1!='')
		$arrowStrings[$row->Match1]=$row->ArrowString1;

	if ($row->ArrowString2!='')
		$arrowStrings[$row->Match2]=$row->ArrowString2;

// puntini
	$points[$matchNo1]=array();
	$points[$matchNo2]=array();

	$sums[$matchNo1]=0;
	$sums[$matchNo2]=0;

	$subArrs[$matchNo1]=str_pad('',$subvoleeArrows,' ',STR_PAD_RIGHT);
	$subArrs[$matchNo2]=str_pad('',$subvoleeArrows,' ',STR_PAD_RIGHT);

// puntoni
	$matchPoints1=0;
	$matchPoints2=0;
	$canMatchPoints=true;

// ora posso calcolare i puntini
	for ($i=0;$i<(count($bows)*$subvoleeArrows*$volee);++$i)
	{
		//$subArrs[$matchNo1][$i]=$arrowStrings[$matchNo1][$i];
		//$subArrs[$matchNo2][$i]=$arrowStrings[$matchNo2][$i];
		$subArrs[$matchNo1]=substr_replace($subArrs[$matchNo1],$arrowStrings[$matchNo1][$i],$i,1);
		$subArrs[$matchNo2]=substr_replace($subArrs[$matchNo2],$arrowStrings[$matchNo2][$i],$i,1);

		if ($i%$subvoleeArrows==($subvoleeArrows-1))
		{
		//	print($subArrs[$matchNo1]).'<br>';
		//	print($subArrs[$matchNo2]);exit;
	/*
	 * se non ho spazi in entrambe le sottovolee vuol dire che tutti e due hanno completo il set
	 * quindi posso fare i conti
	 */
			$p1=0;
			$p2=0;

			if (strpos($subArrs[$matchNo1],' ')===false && strpos($subArrs[$matchNo2],' ')===false)
			{
				$sums[$matchNo1]=ValutaArrowString($subArrs[$matchNo1]);
				$sums[$matchNo2]=ValutaArrowString($subArrs[$matchNo2]);

				if ($sums[$matchNo1]>$sums[$matchNo2])	// vince 1
				{
					$p1=1;
					$p2=0;
				}
				elseif ($sums[$matchNo2]>$sums[$matchNo1])		// vince 2
				{
					$p1=0;
					$p2=1;
				}
				else	// pari
				{
					$p1=1;
					$p2=1;
				}
			}
			else
			{
				$canMatchPoints=false;
			}

			$points[$matchNo1][]=$p1;
			$points[$matchNo2][]=$p2;

			$sums[$matchNo1]=0;
			$sums[$matchNo2]=0;

			$subArrs[$matchNo1]='';
			$subArrs[$matchNo2]='';
		}
	}

	/*print '<pre>';
	print_r($points[$matchNo1]);
	print_r($points[$matchNo2]);
	print '</pre>';exit;*/

	$p1=0;
	$p2=0;
	$p1=array_sum($points[$matchNo1]);
	$p2=array_sum($points[$matchNo2]);
	if ($canMatchPoints)
	{


		if ($p1>$p2)		// vince 1
		{
			$matchPoints1=2;
			$matchPoints2=0;
		}
		elseif ($p2>$p1)	// vince 2
		{
			$matchPoints1=0;
			$matchPoints2=2;
		}
		else	// pari
		{
			$matchPoints1=1;
			$matchPoints2=1;
		}
	}

// scrivo nel db
	$query
		= "UPDATE "
			. "CasScore "
		. "SET "
			. "CaSSetPoints='" . implode('|',$points[$matchNo1]) . "', "
			. "CaSPoints='" . $matchPoints1 . "', "
			. "CaSSetScore='" . $p1 . "' "
		. "WHERE "
			. "CaSTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CaSPhase=" . StrSafe_DB($phase) . " AND "
				. "CaSRound=" . StrSafe_DB($round) . " AND CaSMatchNo=" . StrSafe_DB($matchNo1)  ." AND CaSEventCode=" . StrSafe_DB($event) . " ";
	$rs=safe_w_sql($query);
	//print $query . '<br><br>';
	$query
		= "UPDATE "
			. "CasScore "
		. "SET "
			. "CaSSetPoints='" . implode('|',$points[$matchNo2]) . "', "
			. "CaSPoints='" . $matchPoints2 . "', "
			. "CaSSetScore='" . $p2 . "'  "
		. "WHERE "
			. "CaSTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CaSPhase=" . StrSafe_DB($phase) . " AND "
				. "CaSRound=" . StrSafe_DB($round) . " AND CaSMatchNo=" . StrSafe_DB($matchNo2)  ." AND CaSEventCode=" . StrSafe_DB($event) . " ";
	$rs=safe_w_sql($query);
	//print $query . '<br><br>';exit;
}

function getMatchesFinalCampItaSoc($Event=null,$Phase=null,$otherWhere="")
{
	$query= "
		SELECT
			IF(c1.CoName IS NOT NULL, CONCAT(c1.CoName,' (',c1.CoCode, IF(tf1.TfSubTeam!='0',CONCAT(' - ',tf1.TfSubTeam),''),')'),'***') AS name1,
			IF(c2.CoName IS NOT NULL, CONCAT(c2.CoName,' (',c2.CoCode, IF(tf2.TfSubTeam!='0',CONCAT(' - ',tf2.TfSubTeam),''),')'),'***') AS name2,
			tf1.TfMatchNo AS match1,
			tf2.TfMatchNo AS match2,
			ctt1.CTTTarget AS TargetNo1,
			ctt2.CTTTarget AS TargetNo2,
			tf1.TfEvent AS event,
			g1.GrPhase AS phase,
			ev1.EvTeamEvent AS teamEvent,
			ctf1.CTFScore AS score1,ctf1.CTFSetScore AS setScore1,ctf1.CTFSetPoints AS setPoints1,ctf1.CTFTie AS tie1,ctf1.CTFArrowString AS arrowString1,ctf1.CTFTiebreak AS tiebreak1,ctf1.CTFTiePoins AS tiePoints1,ctf1.CTFTieScore AS tieScore1,ctf1.CTFScore2 AS score21,
			ctf2.CTFScore AS score2,ctf2.CTFSetScore AS setScore2,ctf2.CTFSetPoints AS setPoints2,ctf2.CTFTie AS tie2,ctf2.CTFArrowString AS arrowString2,ctf2.CTFTiebreak AS tiebreak2,ctf2.CTFTiePoins AS tiePoints2,ctf2.CTFTieScore AS tieScore2,ctf2.CTFScore2 AS score22
		";

	$query.= "
		FROM
			TeamFinals AS tf1
			INNER JOIN
				TeamFinals AS tf2
			ON tf1.TfEvent=tf2.TfEvent AND tf1.TfMatchNo=IF((tf1.TfMatchNo % 2)=0,tf2.TfMatchNo-1,tf2.TfMatchNo+1) AND tf1.TfTournament=tf2.TfTournament

			INNER JOIN
				Events AS ev1
			ON tf1.TfEvent=ev1.EvCode AND ev1.EvTeamEvent=1 AND ev1.EvTournament=" . StrSafe_DB($_SESSION['TourId']) . "

			INNER JOIN
				Grids AS g1
			ON tf1.TfMatchNo=g1.GrMatchNo

			INNER JOIN
				CasTeamFinal AS ctf1
			ON tf1.TfEvent=ctf1.CTFEvent AND tf1.TfMatchNo=ctf1.CTFMatchNo AND tf1.TfTournament=ctf1.CTFTournament

			INNER JOIN
				CasTeamFinal AS ctf2
			ON tf2.TfEvent=ctf2.CTFEvent AND tf2.TfMatchNo=ctf2.CTFMatchNo AND tf2.TfTournament=ctf2.CTFTournament

			LEFT JOIN
				Countries AS c1
			ON tf1.TfTeam=c1.CoId

			LEFT JOIN
				Countries AS c2
			ON tf2.TfTeam=c2.CoId

			LEFT JOIN
				CasTeamTarget AS ctt1
			ON tf1.TfEvent=ctt1.CTTEvent AND tf1.TfMatchNo=ctt1.CTTMatchNo AND tf1.TfTournament=ctt1.CTTTournament

			LEFT JOIN
				CasTeamTarget AS ctt2
			ON tf2.TfEvent=ctt2.CTTEvent AND tf2.TfMatchNo=ctt2.CTTMatchNo AND tf2.TfTournament=ctt2.CTTTournament

		WHERE
			tf1.TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (tf1.TfMatchNo % 2)=0
			AND (c1.CoName IS NOT NULL OR c2.CoName IS NOT NULL)
			AND ev1.EvCode=" . StrSafe_DB($Event) . " ";

		if (!is_null($Phase))
			$query.= "AND g1.GrPhase=" . StrSafe_DB($Phase) . " ";
		$query.= $otherWhere . " ";

	$query.="
		ORDER BY EvProgr ASC,GrMatchNo ASC
	";
	//print $query.'<br><br>';exit;
	$rs=safe_r_sql($query);

	return $rs;
}

function sql_getMatchesFinalCampItaSoc($otherWhere="",$limit="")
{
	$query= "
		SELECT
			IF(c1.CoName IS NOT NULL, c1.CoCode ,'***') AS CountryCode1,
			IF(c2.CoName IS NOT NULL, c2.CoCode ,'***') AS CountryCode2,
			IF(c1.CoName IS NOT NULL, tf1.TfSubTeam ,'***') AS SubTeamCode1,
			IF(c2.CoName IS NOT NULL, tf2.TfSubTeam,'***') AS SubTeamCode2,
			IF(c1.CoName IS NOT NULL, CONCAT(c1.CoName,' (',c1.CoCode, IF(tf1.TfSubTeam!='0',CONCAT(' - ',tf1.TfSubTeam),''),')'),'***') AS name1,
			IF(c2.CoName IS NOT NULL, CONCAT(c2.CoName,' (',c2.CoCode, IF(tf2.TfSubTeam!='0',CONCAT(' - ',tf2.TfSubTeam),''),')'),'***') AS name2,
			tf1.TfMatchNo AS match1,
			tf2.TfMatchNo AS match2,
			ctt1.CTTTarget AS TargetNo1,
			ctt2.CTTTarget AS TargetNo2,
			tf1.TfEvent AS event,
			g1.GrPhase AS phase,
			ev1.EvTeamEvent AS teamEvent,
			ctf1.CTFScore AS score1,ctf1.CTFSetScore AS setScore1,ctf1.CTFSetPoints AS setPoints1,ctf1.CTFTie AS tie1,ctf1.CTFArrowString AS arrowString1,ctf1.CTFTiebreak AS tiebreak1,ctf1.CTFTiePoins AS tiePoints1,ctf1.CTFTieScore AS tieScore1,ctf1.CTFScore2 AS score21,
			ctf2.CTFScore AS score2,ctf2.CTFSetScore AS setScore2,ctf2.CTFSetPoints AS setPoints2,ctf2.CTFTie AS tie2,ctf2.CTFArrowString AS arrowString2,ctf2.CTFTiebreak AS tiebreak2,ctf2.CTFTiePoins AS tiePoints2,ctf2.CTFTieScore AS tieScore2,ctf2.CTFScore2 AS score22
		";

	$query.= "
		FROM
			TeamFinals AS tf1
			INNER JOIN
				TeamFinals AS tf2
			ON tf1.TfEvent=tf2.TfEvent AND tf1.TfMatchNo=IF((tf1.TfMatchNo % 2)=0,tf2.TfMatchNo-1,tf2.TfMatchNo+1) AND tf1.TfTournament=tf2.TfTournament

			INNER JOIN
				Events AS ev1
			ON tf1.TfEvent=ev1.EvCode AND ev1.EvTeamEvent=1 AND ev1.EvTournament=" . StrSafe_DB($_SESSION['TourId']) . "

			INNER JOIN
				Grids AS g1
			ON tf1.TfMatchNo=g1.GrMatchNo

			INNER JOIN
				CasTeamFinal AS ctf1
			ON tf1.TfEvent=ctf1.CTFEvent AND tf1.TfMatchNo=ctf1.CTFMatchNo AND tf1.TfTournament=ctf1.CTFTournament

			INNER JOIN
				CasTeamFinal AS ctf2
			ON tf2.TfEvent=ctf2.CTFEvent AND tf2.TfMatchNo=ctf2.CTFMatchNo AND tf2.TfTournament=ctf2.CTFTournament

			LEFT JOIN
				Countries AS c1
			ON tf1.TfTeam=c1.CoId

			LEFT JOIN
				Countries AS c2
			ON tf2.TfTeam=c2.CoId

			LEFT JOIN
				CasTeamTarget AS ctt1
			ON tf1.TfEvent=ctt1.CTTEvent AND tf1.TfMatchNo=ctt1.CTTMatchNo AND tf1.TfTournament=ctt1.CTTTournament

			LEFT JOIN
				CasTeamTarget AS ctt2
			ON tf2.TfEvent=ctt2.CTTEvent AND tf2.TfMatchNo=ctt2.CTTMatchNo AND tf2.TfTournament=ctt2.CTTTournament

		WHERE
			tf1.TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (tf1.TfMatchNo % 2)=0
			AND (c1.CoName IS NOT NULL OR c2.CoName IS NOT NULL) ";
		$query.= $otherWhere . " ";

	$query.="
		ORDER BY EvProgr ASC,GrMatchNo ASC
	";

	if ($limit!='')
	{
		$query.="LIMIT " . $limit .  " ";
	}

	//print $query.'<br><br>';exit;
	$rs=safe_r_sql($query);

	return $rs;
}

function saveArrowStringCampItaSocFin($event,$matchNo,$arrowString,$target,$tiebreak=false)
{
	global $volee;
	global $subvoleeArrows;
	global $bows;

	$fields2up='';
	$score=0;

// valuto l'arrowstring

	$score=ValutaArrowString($arrowString);

	if (!$tiebreak)
	{
		$fields2up="CTFScore=" . $score . ",CTFArrowString='" . $arrowString. "' ";
	}
	else
	{
		$fields2up="CTFScore2=" . $score . ",CTFTiebreak='" . $arrowString. "' ";
	}


	$query
		= "UPDATE "
			. "CasTeamFinal "
		. "SET "
			. $fields2up
		. "WHERE "
			. "CTFTournament=" . StrSafe_DB($_SESSION['TourId']) . "  AND CTFMatchNo=" . StrSafe_DB($matchNo)  ." AND CTFEvent=" . StrSafe_DB($event) . " ";
	$rs=safe_w_sql($query);
	//print $query . '<br><br>';
	if ($rs)
		return true;
	else
		return false;
}
// qui $phase mi serve solo x la chiamata interna a getMatchesFinalCampItaSoc()
function savePointsCampItaSocFin($event,$phase,$matchNo1,$matchNo2,$target)
{
	global $volee;
	global $subvoleeArrows;
	global $bows;

// tiro fuori le due arrowstring
	$arrowStrings[$matchNo1]=str_pad('',count($bows)*$subvoleeArrows*$volee,' ',STR_PAD_RIGHT);
	$arrowStrings[$matchNo2]=str_pad('',count($bows)*$subvoleeArrows*$volee,' ',STR_PAD_RIGHT);

	$where="AND tf1.TfMatchNo=" . $matchNo1 . " AND tf2.TfMatchNo=" . $matchNo2 . " ";
	$rs=getMatchesFinalCampItaSoc($event,$phase,$where);

	if (!($rs && safe_num_rows($rs)==1))
		return false;

	$row=safe_fetch($rs);

// qui mi fotto i punti standard e più avanti mi farò i tie

	if ($row->arrowString1!='')
		$arrowStrings[$row->match1]=$row->arrowString1;

	if ($row->arrowString2!='')
		$arrowStrings[$row->match2]=$row->arrowString2;

// puntini
	$points[$matchNo1]=array();
	$points[$matchNo2]=array();

	$sums[$matchNo1]=0;
	$sums[$matchNo2]=0;

	$subArrs[$matchNo1]=str_pad('',$subvoleeArrows,' ',STR_PAD_RIGHT);
	$subArrs[$matchNo2]=str_pad('',$subvoleeArrows,' ',STR_PAD_RIGHT);



// ora posso calcolare i puntini
	for ($i=0;$i<(count($bows)*$subvoleeArrows*$volee);++$i)
	{
		//$subArrs[$matchNo1][$i]=$arrowStrings[$matchNo1][$i];
		//$subArrs[$matchNo2][$i]=$arrowStrings[$matchNo2][$i];

		$subArrs[$matchNo1]=substr_replace($subArrs[$matchNo1],$arrowStrings[$matchNo1][$i],$i,1);
		$subArrs[$matchNo2]=substr_replace($subArrs[$matchNo2],$arrowStrings[$matchNo2][$i],$i,1);

		if ($i%$subvoleeArrows==($subvoleeArrows-1))
		{
		//	print($subArrs[$matchNo1]).'<br>';
		//	print($subArrs[$matchNo2]);exit;
	/*
	 * se non ho spazi in entrambe le sottovolee vuol dire che tutti e due hanno completo il set
	 * quindi posso fare i conti
	 */
			$p1=0;
			$p2=0;

			if (strpos($subArrs[$matchNo1],' ')===false && strpos($subArrs[$matchNo2],' ')===false)
			{
				$sums[$matchNo1]=ValutaArrowString($subArrs[$matchNo1]);
				$sums[$matchNo2]=ValutaArrowString($subArrs[$matchNo2]);

				if ($sums[$matchNo1]>$sums[$matchNo2])	// vince 1
				{
					$p1=1;
					$p2=0;
				}
				elseif ($sums[$matchNo2]>$sums[$matchNo1])		// vince 2
				{
					$p1=0;
					$p2=1;
				}
				else	// pari
				{
					$p1=1;
					$p2=1;
				}
			}

			$points[$matchNo1][]=$p1;
			$points[$matchNo2][]=$p2;

			$sums[$matchNo1]=0;
			$sums[$matchNo2]=0;

			$subArrs[$matchNo1]='';
			$subArrs[$matchNo2]='';
		}
	}

	/*print '<pre>';
	print_r($points[$matchNo1]);
	print_r($points[$matchNo2]);
	print '</pre>';exit;*/

	$p1=array_sum($points[$matchNo1]);
	$p2=array_sum($points[$matchNo2]);

// eccomi a farmi i tie

	$arrowStrings[$row->match1]=str_pad('',3,' ',STR_PAD_RIGHT);
	$arrowStrings[$row->match2]=str_pad('',3,' ',STR_PAD_RIGHT);

	if ($row->tiebreak1!='')
		$arrowStrings[$row->match1]=$row->tiebreak1;

	if ($row->tiebreak2!='')
		$arrowStrings[$row->match2]=$row->tiebreak2;

	$setPoints[$matchNo1]=array();
	$setPoints[$matchNo2]=array();

	$sums[$matchNo1]=0;
	$sums[$matchNo2]=0;

	$tieScores[$matchNo1]=0;
	$tieScores[$matchNo2]=0;

	$canTieScore=true;

	for ($i=0;$i<3;++$i)
	{
		$x1=0;
		$x2=0;

		if ($arrowStrings[$matchNo1][$i]!=' ' && $arrowStrings[$matchNo2][$i]!=' ')
		{
			$sums[$matchNo1]=ValutaArrowString($arrowStrings[$matchNo1][$i]);
			$sums[$matchNo2]=ValutaArrowString($arrowStrings[$matchNo2][$i]);

			if ($sums[$matchNo1]>$sums[$matchNo2])	// vince 1
			{
				$x1=1;
				$x2=0;
			}
			elseif ($sums[$matchNo2]>$sums[$matchNo1])		// vince 2
			{
				$x1=0;
				$x2=1;
			}
			else	// pari
			{
				$x1=1;
				$x2=1;
			}
		}
		else
		{
			$canTieScore=false;
		}

		$tiePoints[$matchNo1][]=$x1;
		$tiePoints[$matchNo2][]=$x2;
	}

	if ($canTieScore)
	{
		$tieScores[$matchNo1]=array_sum($tiePoints[$matchNo1]);
		$tieScores[$matchNo2]=array_sum($tiePoints[$matchNo2]);
	}

// scrivo nel db
	$query
		= "UPDATE "
			. "CasTeamFinal "
		. "SET "
			. "CTFSetPoints='" . implode('|',$points[$matchNo1]) . "', "
			. "CTFSetScore='" . $p1 . "',  "
			. "CTFTiePoins='" . implode('|',$tiePoints[$matchNo1]). "',"
			. "CTFTieScore='" . $tieScores[$matchNo1]. "' "
		. "WHERE "
			. "CTFTournament=" . StrSafe_DB($_SESSION['TourId']) . "  AND CTFMatchNo=" . StrSafe_DB($matchNo1)  ." AND CTFEvent=" . StrSafe_DB($event) . " ";
	$rs=safe_w_sql($query);
	//print $query . '<br><br>';
	$query
		= "UPDATE "
			. "CasTeamFinal "
		. "SET "
			. "CTFSetPoints='" . implode('|',$points[$matchNo2]) . "', "
			. "CTFSetScore='" . $p2 . "',  "
			. "CTFTiePoins='" . implode('|',$tiePoints[$matchNo2]). "',"
			. "CTFTieScore='" . $tieScores[$matchNo2]. "' "
		. "WHERE "
			. "CTFTournament=" . StrSafe_DB($_SESSION['TourId']) . "  AND CTFMatchNo=" . StrSafe_DB($matchNo2)  ." AND CTFEvent=" . StrSafe_DB($event) . " ";
	$rs=safe_w_sql($query);
	//print $query . '<br><br>';exit;
}

function move2NextPhaseCampItaSoc($Phase=NULL, $Event=NULL, $MatchNo=NULL)
{
//verifico i parametri
	if(is_null($Phase) && is_null($MatchNo))	//Devono esistere o la fase o il MatchNo
		return;
	if(is_null($Phase) && is_null($Event))		//Se non ho la Fase (e quindi ho il MatchNo) deve esistere l'evento
		return;

	// Faccio i passaggi di fase
	$MyNextMatchNo='xx';
	$QueryFilter = '';

	$Select
		= "SELECT "
		. "tf.TfEvent AS Event, tf.TfMatchNo,  "
		. "GrPhase, tf.TfTeam AS Team,tf2.TfSubTeam AS SubTeam, tf2.TfTeam AS OppTeam,tf2.TfSubTeam AS OppSubTeam, "
		//. "IF(EvMatchMode=0,tf.TfScore,tf.TfSetScore) AS Score, tf.TfTie as Tie, IF(EvMatchMode=0,tf2.TfScore,tf2.TfSetScore) as OppScore, tf2.TfTie as OppTie, "
		. "ctf1.CTFSetScore AS SetScore,ctf1.CTFScore AS Score,ctf1.CTFTieScore AS TieScore,ctf1.CTFScore2 AS Score2, "
		. "ctf2.CTFSetScore AS OppSetScore,ctf2.CTFScore AS OppScore,ctf2.CTFTieScore AS OppTieScore,ctf2.CTFScore2 AS OppScore2, "
		. "IF(GrPhase>2, FLOOR(tf.TfMatchNo/2),FLOOR(tf.TfMatchNo/2)-2) AS NextMatchNo "

		. "FROM TeamFinals AS tf "
		. "INNER JOIN TeamFinals AS tf2 ON tf.TfEvent=tf2.TfEvent AND tf.TfMatchNo=IF((tf.TfMatchNo % 2)=0,tf2.TfMatchNo-1,tf2.TfMatchNo+1) AND tf.TfTournament=tf2.TfTournament "
		. "INNER JOIN Events ON tf.TfEvent=EvCode AND tf.TfTournament=EvTournament AND EvTeamEvent=1 "
		. "INNER JOIN CasTeamFinal AS ctf1 ON tf.TfEvent=ctf1.CTFEvent AND tf.TfMatchNo=ctf1.CTFMatchNo AND tf.TfTournament=ctf1.CTFTournament "
		. "INNER JOIN CasTeamFinal AS ctf2 ON tf2.TfEvent=ctf2.CTFEvent AND tf2.TfMatchNo=ctf2.CTFMatchNo AND tf2.TfTournament=ctf2.CTFTournament ";

		if(!is_null($Phase))
			$Select .= "INNER JOIN Grids ON tf.TfMatchNo=GrMatchNo AND GrPhase=" . StrSafe_DB($Phase) . " ";
		else
			$Select .= "INNER JOIN Grids ON tf.TfMatchNo=GrMatchNo AND GrMatchNo=" . StrSafe_DB(($MatchNo % 2 == 0 ? $MatchNo:$MatchNo-1)) . " ";
		$Select .= "LEFT JOIN Countries ON tf.TfTeam=CoId AND tf.TfTournament=CoTournament "
		. "WHERE tf.TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (tf.TfMatchNo % 2)=0 ";

		if(!is_null($Event) && $Event!='')
			$Select .= "AND tf.TfEvent=" . StrSafe_DB($Event) . " ";

	$Select .= "ORDER BY tf.TfEvent, NextMatchNo ASC, SetScore DESC, Score DESC, TieScore DESC,Score2 DESC ";
	//print $Select;	exit;
	$Rs=safe_r_sql($Select);

	$AthPropTs = NULL;

	if (safe_num_rows($Rs)>0)
	{
		$AthPropTs = date('Y-m-d H:i:s');

		while ($MyRow=safe_fetch($Rs))
		{
			$AthProp = '0';
			$AthSubProp = '0';
			$WhereProp = '0';
			$WhereSubProp = '0';


			if ($MyRow->GrPhase>=2)
			{
				if ($MyRow->SetScore>$MyRow->OppSetScore)	// vince1
				{
					//print '1';exit;
					$AthProp=$MyRow->Team;
					$AthSubProp=$MyRow->SubTeam;
					$WhereProp=$MyRow->OppTeam;
					$WhereSubProp=$MyRow->OppSubTeam;
				}
				elseif ($MyRow->SetScore<$MyRow->OppSetScore)		// vince2
				{
					$AthProp=$MyRow->OppTeam;
					$AthSubProp=$MyRow->OppSubTeam;
					$WhereProp=$MyRow->Team;
					$WhereSubProp=$MyRow->SubTeam;
				}
				else		// pari
				{
					if ($MyRow->Score>$MyRow->OppScore)	// vince1
					{
						//print '2';exit;
						$AthProp=$MyRow->Team;
						$AthSubProp=$MyRow->SubTeam;
						$WhereProp=$MyRow->OppTeam;
						$WhereSubProp=$MyRow->OppSubTeam;
					}
					elseif ($MyRow->Score<$MyRow->OppScore)	// vince2
					{
							//print '3';exit;
						$AthProp=$MyRow->OppTeam;
						$AthSubProp=$MyRow->OppSubTeam;
						$WhereProp=$MyRow->Team;
						$WhereSubProp=$MyRow->SubTeam;
					}
					else		// pari
					{
						if ($MyRow->TieScore>$MyRow->OppTieScore)	// vince1
						{
							//print '4';exit;
							$AthProp=$MyRow->Team;
							$AthSubProp=$MyRow->SubTeam;
							$WhereProp=$MyRow->OppTeam;
							$WhereSubProp=$MyRow->OppSubTeam;
						}
						elseif ($MyRow->TieScore<$MyRow->OppTieScore)		// vince2
						{
							//print '5';exit;
							$AthProp=$MyRow->OppTeam;
							$AthSubProp=$MyRow->OppSubTeam;
							$WhereProp=$MyRow->Team;
							$WhereSubProp=$MyRow->SubTeam;
						}
						else	// pari
						{
							if ($MyRow->Score2>$MyRow->OppScore2)	// vince1
							{
								//print '6';exit;
								$AthProp=$MyRow->Team;
								$AthSubProp=$MyRow->SubTeam;
								$WhereProp=$MyRow->OppTeam;
								$WhereSubProp=$MyRow->OppSubTeam;
							}
							elseif ($MyRow->Score2<$MyRow->OppScore2)	// vince2
							{
								//print '7';exit;
								$AthProp=$MyRow->OppTeam;
								$AthSubProp=$MyRow->OppSubTeam;
								$WhereProp=$MyRow->Team;
								$WhereSubProp=$MyRow->SubTeam;
							}
							else		// non passa nessuno xchè devo azzerare
							{
								//print '8';exit;
								$AthProp='0';
								$AthSubProp='0';
								$WhereProp='0';
								$WhereSubProp='0';
							}
						}
					}
				}

				$MyUpQuery = "UPDATE TeamFinals SET ";
				$MyUpQuery.= "TfTeam =" . StrSafe_DB($AthProp) . ", ";
				$MyUpQuery.= "TfSubTeam =" . StrSafe_DB($AthSubProp) . ", ";
				$MyUpQuery.= "TfDateTime=" . StrSafe_DB($AthPropTs) . " ";
				$MyUpQuery.= "WHERE TfEvent=" . StrSafe_DB($MyRow->Event) . " AND TfMatchNo=" . StrSafe_DB($MyRow->NextMatchNo) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
				$RsUp=safe_w_sql($MyUpQuery);

				if($MyRow->GrPhase==2)
				{
					$MyUpQuery = "UPDATE TeamFinals SET ";
					$MyUpQuery.= "TfTeam =" . StrSafe_DB($WhereProp) . ", ";
					$MyUpQuery.= "TfSubTeam =" . StrSafe_DB($WhereSubProp) . ", ";
					$MyUpQuery.= "TfDateTime=" . StrSafe_DB($AthPropTs) . " ";
					$MyUpQuery.= "WHERE TfEvent=" . StrSafe_DB($MyRow->Event) . " AND TfMatchNo=" . StrSafe_DB(($MyRow->NextMatchNo+2)) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$RsUp=safe_w_sql($MyUpQuery);
				}
			}

			$OldId=($AthProp!=0 ? StrSafe_DB($WhereProp) : StrSafe_DB($MyRow->Team) . ',' . StrSafe_DB($MyRow->OppTeam));
			$OldSubId=($AthSubProp!=0 ? StrSafe_DB($WhereSubProp) : StrSafe_DB($MyRow->SubTeam) . ',' . StrSafe_DB($MyRow->OppSubTeam));

			$Update
				= "UPDATE TeamFinals SET "
				. "TfTeam=" . StrSafe_DB($AthProp) . ", "
				. "TfDateTime=" . StrSafe_DB($AthPropTs) . " "
				. "WHERE TfTeam IN (" . $OldId . ") "
				. "AND TfSubTeam IN (" . $OldSubId . ") "
				. "AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
				. "AND TfEvent=" . StrSafe_DB($MyRow->Event) . " "
				. "AND tfMatchNo<"	. StrSafe_DB($MyRow->NextMatchNo) . " ";

			if($OldId!="'0'")
				$RsProp = safe_w_sql($Update);
		}
	}
}
