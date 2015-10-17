<?php
function getTeamBracketsQuery($ORIS=false, 	$EventRequested='') {
	require_once('Common/Lib/Fun_PrintOuts.php');

	if(!$EventRequested and !empty($_REQUEST['Event'])) $EventRequested=$_REQUEST['Event'];

	if($ORIS) {
		//Genero la Query dei Nomi
		$MyQueryNames  = "SELECT TfcEvent, TfcCoId, TfcSubTeam, TfcOrder, EnFirstName, EnName, CONCAT(TeRank,CHAR(64+TfcOrder)) AS BackNo  ";
		$MyQueryNames .= "FROM TeamFinComponent ";
		$MyQueryNames .= "INNER JOIN Entries ON TfcId=EnId AND TfcTournament=EnTournament ";
		$MyQueryNames .= "INNER JOIN Teams ON TfcCoId=TeCoId AND TfcSubTeam=TeSubTeam AND TfcEvent=TeEvent AND TfcTournament=TeTournament AND TeFinEvent=1 ";
		$MyQueryNames .= "WHERE TfcTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";

		//Genero la query Degli eventi
		$MyQuery = "SELECT f.TfTeam, f.TfSubTeam, f.TfEvent AS Event, EvEventName AS EventDescr, f.TfMatchNo, EvFinalFirstPhase, TeRank, TeScore, "
			. "IF(GrPhase!=0,GrPhase,1) as Phase, (GrPhase=1) as Finalina, "
			. "CONCAT(CoName, IF(f.TfSubTeam>'1',CONCAT(' (',f.TfSubTeam,')'),'')) as Team, CoCode as Country, IF(EvMatchMode=0,f.TfScore,f.TfSetScore) AS Score, f.TfTie, f.TfTieBreak, IF(EvMatchMode=0,f2.TfScore,f2.TfSetScore) as OppScore, f2.TfTie as OppTie, f.TfSetPoints as SetPoints,  "
			. "IF(EvFinalFirstPhase=48 || EvFinalFirstPhase=24,GrPosition2, GrPosition) GrPosition, EvFinalPrintHead, FSTarget, IFNULL(NComponenti,0) AS NumComponenti, DATE_FORMAT(FSScheduledDate,'" . get_text('DateFmtDB') . "') as ScheduledDate, DATE_FORMAT(FSScheduledTime,'" . get_text('TimeFmt') . "') AS ScheduledTime ";
		$MyQuery .= "FROM TeamFinals as f ";
		$MyQuery .= "INNER JOIN TeamFinals AS f2 ON f.TfEvent=f2.TfEvent AND f.TfMatchNo=IF((f.TfMatchNo % 2)=0,f2.TfMatchNo-1,f2.TfMatchNo+1) AND f.TfTournament=f2.TfTournament ";
		$MyQuery .= "INNER JOIN Events ON f.TfEvent=EvCode AND f.TfTournament=EvTournament AND EvTeamEvent=1 ";
		$MyQuery .= "INNER JOIN Grids ON f.TfMatchNo=GrMatchNo ";
		$MyQuery .= "LEFT JOIN Teams ON f.TfTeam=TeCoId AND f.TfSubTeam=TeSubTeam AND f.TfEvent=TeEvent AND f.TfTournament=TeTournament AND TeFinEvent=1 ";
		$MyQuery .= "LEFT JOIN Countries ON f.TfTeam=CoId AND f.TfTournament=CoTournament ";
		$MyQuery .= "LEFT JOIN FinSchedule ON f.TfEvent=FSEvent AND f.TfMatchNo=FSMatchNo AND f.TfTournament=FSTournament AND FSTeamEvent='1' ";

		$MyQuery .= "LEFT JOIN (SELECT TfcEvent AS Evento, Max(Quanti) AS NComponenti FROM ( ";
		$MyQuery .= "SELECT TfcEvent, Count( * ) AS Quanti FROM TeamFinComponent WHERE TfcTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";
		$MyQuery .= "GROUP BY TfcEvent, TfcCoId, TfcSubTeam) AS Ssqy  GROUP BY TfcEvent) as Sqy ON f.TfEvent=Evento ";

		$MyQuery.= "WHERE f.TfTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";
		if($EventRequested) {
			$MyQuery .= CleanEvents($EventRequested, 'f.TfEvent');
			$MyQueryNames .= CleanEvents($EventRequested, 'TfcEvent');
		}
		$MyQuery .= "ORDER BY EvProgr ASC, EvCode, Phase DESC, f.TfMatchNo ASC ";
		$MyQueryNames .= "ORDER BY TfcEvent, TfcCoId, TfcSubTeam, TfcOrder ";
		//* DEBUG --> */ print $MyQuery;

		$ArrNames = array();
		$RsNames = safe_r_sql($MyQueryNames);
		if(safe_num_rows($RsNames)>0)
		{
			$arrKeys = array();
			$arrValues = array();
			while($MyRow = safe_fetch($RsNames))
			{
				$arrKeys[] = $MyRow->TfcEvent . "_" . $MyRow->TfcCoId . "_" .$MyRow->TfcSubTeam . "_" .$MyRow->TfcOrder;
				$arrValues[] = array($MyRow->BackNo,$MyRow->EnFirstName . ' ' . $MyRow->EnName);
			}
			$ArrNames = array_combine($arrKeys, $arrValues);
		}
		return array($MyQuery, $ArrNames);
	}

	//Genero la Query dei Nomi
	$MyQueryNames  = "SELECT TfcEvent, TfcCoId, TfcSubTeam, TfcOrder, EnFirstName, EnName ";
	$MyQueryNames .= "FROM TeamFinComponent ";
	$MyQueryNames .= "INNER JOIN Entries ON TfcId=EnId AND TfcTournament=EnTournament ";
	$MyQueryNames .= "WHERE TfcTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";

	//Genero la query Degli eventi
	$MyQuery = "SELECT TfTeam, TfSubTeam, TfEvent AS Event, EvEventName AS EventDescr, TfMatchNo, EvFinalFirstPhase, "
		. "IF(GrPhase!=0,GrPhase,1) as Phase, (GrPhase=1) as finalina, "
		. "CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) as Team, CoCode as Country, IF(EvMatchMode=0,TfScore,TfSetScore) as Score, TfTie, TfTieBreak, "
		. "IF(EvFinalFirstPhase=48 || EvFinalFirstPhase=24,GrPosition2, GrPosition) GrPosition, EvFinalPrintHead, FSTarget, NumComponenti, DATE_FORMAT(FSScheduledDate,'" . get_text('DateFmtDB') . "') as ScheduledDate, DATE_FORMAT(FSScheduledTime,'" . get_text('TimeFmt') . "') AS ScheduledTime ";
	$MyQuery .= "FROM TeamFinals ";
	$MyQuery .= "INNER JOIN Events ON TfEvent=EvCode AND TfTournament=EvTournament AND EvTeamEvent=1 ";
	$MyQuery .= "INNER JOIN Grids ON TfMatchNo=GrMatchNo ";
	$MyQuery .= "LEFT JOIN Countries ON TfTeam=CoId AND TfTournament=CoTournament ";
	$MyQuery .= "LEFT JOIN FinSchedule ON TfEvent=FSEvent AND TfMatchNo=FSMatchNo AND TfTournament=FSTournament AND FSTeamEvent='1' ";

	$MyQuery .= "LEFT JOIN (SELECT TfcEvent AS Evento, Max( Quanti ) AS NumComponenti FROM ( ";
	$MyQuery .= "SELECT TfcEvent, Count( * ) AS Quanti FROM TeamFinComponent WHERE TfcTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";
	$MyQuery .= "GROUP BY TfcEvent, TfcCoId, TfcSubTeam) AS Ssqy  GROUP BY TfcEvent) as Sqy ON TfEvent=Evento ";

	$MyQuery.= "WHERE TfTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";
	if($EventRequested) {
		$MyQuery.= CleanEvents($EventRequested, 'TfEvent');
		$MyQueryNames.= CleanEvents($EventRequested, 'TfcEvent');
	}
	$MyQuery .= "ORDER BY EvProgr ASC, EvCode, Phase DESC, TfMatchNo ASC ";
	$MyQueryNames .= "ORDER BY TfcEvent, TfcCoId, TfcSubTeam, TfcOrder ";
	//* DEBUG --> */ print $MyQueryNames;

	$ArrNames = array();
	$RsNames = safe_r_sql($MyQueryNames);
	if(safe_num_rows($RsNames)>0)
	{
		$arrKeys = array();
		$arrValues = array();
		while($MyRow = safe_fetch($RsNames))
		{
			$arrKeys[] = $MyRow->TfcEvent . "_" . $MyRow->TfcCoId . "_" .$MyRow->TfcSubTeam . "_" .$MyRow->TfcOrder;
			$arrValues[] = $MyRow->EnFirstName . ' ' . $MyRow->EnName;
		}
		$ArrNames = array_combine($arrKeys, $arrValues);
	}
	return array($MyQuery, $ArrNames);
}

function getIndividualBracketsQuery($ORIS=false) {
	if($ORIS) {
		$MyQuery = "SELECT f.FinEvent AS Event, EvEventName AS EventDescr, f.FinMatchNo, EvFinalFirstPhase, "
			. "IF(GrPhase!=0,GrPhase,1) as Phase, (GrPhase=1) as Finalina, IndRank, QuScore, "
			. "EnFirstName as FirstName, EnName as Name, CoCode as Country, IF(EvMatchMode=0,f.FinScore,f.FinSetScore) AS Score, f.FinTie, f.FinTiebreak, IF(EvMatchMode=0,f2.FinScore,f2.FinSetScore) as OppScore, f2.FinTie as OppTie, f.FinSetPoints as SetPoints, "
			. "IF(EvFinalFirstPhase=48 || EvFinalFirstPhase=24,GrPosition2, GrPosition) GrPosition, EvFinalPrintHead, FSTarget, DATE_FORMAT(FSScheduledDate,'" . get_text('DateFmtDB') . "') as ScheduledDate, DATE_FORMAT(FSScheduledTime,'" . get_text('TimeFmt') . "') AS ScheduledTime ";
		$MyQuery .= "FROM Finals AS f ";
		$MyQuery .= "INNER JOIN Finals AS f2 ON f.FinEvent=f2.FinEvent AND f.FinMatchNo=IF((f.FinMatchNo % 2)=0,f2.FinMatchNo-1,f2.FinMatchNo+1) AND f.FinTournament=f2.FinTournament ";
		$MyQuery .= "INNER JOIN Events ON f.FinEvent=EvCode AND f.FinTournament=EvTournament AND EvTeamEvent=0 ";
		$MyQuery .= "INNER JOIN Grids ON f.FinMatchNo=GrMatchNo ";
		$MyQuery .= "LEFT JOIN Individuals ON f.FinAthlete=IndId AND f.FinEvent=IndEvent AND f.FinTournament=IndTournament ";
		$MyQuery .= "LEFT JOIN Entries ON f.FinAthlete=EnId AND f.FinTournament=EnTournament ";
		$MyQuery .= "LEFT JOIN Qualifications ON EnId=QuId  ";
		$MyQuery .= "LEFT JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament ";
		$MyQuery .= "LEFT JOIN FinSchedule ON f.FinEvent=FSEvent AND f.FinMatchNo=FSMatchNo AND f.FinTournament=FSTournament AND FSTeamEvent='0' ";
		$MyQuery .= "WHERE f.FinTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";
		if(!empty($_REQUEST['Event'])) $MyQuery .= CleanEvents($_REQUEST['Event'], 'f.FinEvent');
		$MyQuery .= "ORDER BY EvProgr ASC, EvCode, Phase DESC, f.FinMatchNo ASC ";

		return $MyQuery;
	}
	$MyQuery = "SELECT f.FinEvent AS Event, EvEventName AS EventDescr, f.FinMatchNo, EvFinalFirstPhase, "
		. "IF(GrPhase!=0,GrPhase,1) as Phase, (GrPhase=1) as finalina, "
		. "f.FinSetPoints, f2.FinSetPoints OppSetPoints, "
		. "concat(EnFirstName, ' ', EnName) as Atleta, concat(EnFirstName, ' ', Substr(EnName,1,1), '.') as AtletaShort, CoCode as Country, "
		. "IF(EvMatchMode=0,f.FinScore,f.FinSetScore) AS Score, IF(EvMatchMode=0,f2.FinScore,f2.FinSetScore) AS OppScore, f.FinTie, f2.FinTie as OppTie, f.FinTiebreak, "
		. "IF(EvFinalFirstPhase=48 || EvFinalFirstPhase=24, GrPosition2, GrPosition) as GrPosition, EvFinalPrintHead, FSTarget, DATE_FORMAT(FSScheduledDate,'" . get_text('DateFmtDB') . "') as ScheduledDate, DATE_FORMAT(FSScheduledTime,'" . get_text('TimeFmt') . "') AS ScheduledTime ";
	$MyQuery .= "FROM Finals as f ";
	$MyQuery .= "INNER JOIN Finals AS f2 ON f.FinEvent=f2.FinEvent AND f.FinMatchNo=IF((f.FinMatchNo % 2)=0,f2.FinMatchNo-1,f2.FinMatchNo+1) AND f.FinTournament=f2.FinTournament ";
	$MyQuery .= "INNER JOIN Events ON f.FinEvent=EvCode AND f.FinTournament=EvTournament AND EvTeamEvent=0 ";
	$MyQuery .= "INNER JOIN Grids ON f.FinMatchNo=GrMatchNo ";
	$MyQuery .= "LEFT JOIN Entries ON f.FinAthlete=EnId AND f.FinTournament=EnTournament ";
	$MyQuery .= "LEFT JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament ";
	$MyQuery .= "LEFT JOIN FinSchedule ON f.FinEvent=FSEvent AND f.FinMatchNo=FSMatchNo AND f.FinTournament=FSTournament AND FSTeamEvent='0' ";
	$MyQuery.= "WHERE f.FinTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";
	if(!empty($_REQUEST['Event'])) $MyQuery .= CleanEvents($_REQUEST['Event'], 'f.FinEvent');
	$MyQuery .= "ORDER BY EvProgr ASC, EvCode, Phase DESC, f.FinMatchNo ASC ";

	return $MyQuery;
}

?>