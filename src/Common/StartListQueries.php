<?php
function getStatEntriesByEventQuery($Type='QR') {
	switch($Type) {
		case 'OR':
			$Sql="Select count(*) Quanti, count(distinct EnCountry) Countries, EvCode, EvEventName, EvTeamEvent, EvProgr, EvFirstQualified, EvNumQualified,
				concat(DvMajVersion, '.', DvMinVersion) as DocVersion,
				date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate,
				DvNotes as DocNotes
				from Entries
				inner join Individuals on IndId=EnId and IndTournament=EnTournament
				inner join Events on EvCode=IndEvent and EvTournament=EnTournament and EvTeamEvent=0
				LEFT JOIN DocumentVersions on EnTournament=DvTournament AND DvFile = 'EN'
				where EnIndFEvent=1 and EnTournament={$_SESSION['TourId']}
				group by EvCode
				ORDER BY EvProgr";
			break;
		case 'IF':
			$Sql = "SELECT EvCode as Code, EvEventName as EventName, EvFinalFirstPhase as FirstPhase, COUNT(EnId) as Quanti, count(distinct EnCountry) Countries,
				concat(DvMajVersion, '.', DvMinVersion) as DocVersion, EvFirstQualified, EvNumQualified,
				date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate,
				DvNotes as DocNotes
				FROM Events
				INNER JOIN Individuals ON EvCode=IndEvent AND EvTournament=IndTournament
				INNER JOIN Entries ON EnId=IndId AND EnTournament=IndTournament 
				LEFT JOIN DocumentVersions on EnTournament=DvTournament AND DvFile = 'EN'
				WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0 AND ((EnIndFEvent=1 AND EnStatus<=1) OR EnId IS NULL)
				GROUP BY EvCode, EvFinalFirstPhase
				ORDER BY EvProgr";
			break;
		case 'TF':
			$Sql = "SELECT EvCode, EvEventName as EventName, EvFinalFirstPhase as FirstPhase, EvMixedTeam, EvMultiTeam, EvMultiTeamNo, EvMaxTeamPerson,EvTeamCreationMode, EvFirstQualified, EvNumQualified,
				concat(DvMajVersion, '.', DvMinVersion) as DocVersion,
				date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate,
				DvNotes as DocNotes
				FROM Events
				LEFT JOIN DocumentVersions on EvTournament=DvTournament AND DvFile = 'EN'
				WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1
				ORDER BY EvProgr";
			break;
		default:
			$Sql = "SELECT EnDivision as Divisione, EnClass as Classe, SUM(EnIndClEvent) as QuantiInd, IFNULL(numTeam,0) AS QuantiSq,
				concat(DvMajVersion, '.', DvMinVersion) as DocVersion,
				date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate,
				DvNotes as DocNotes
				FROM Entries
				inner join Divisions on EnDivision=DivId and DivAthlete=1 and DivTournament=" . StrSafe_DB($_SESSION['TourId']) . "
				inner join Classes on EnClass=ClId and ClAthlete=1 and ClTournament=" . StrSafe_DB($_SESSION['TourId']) . "
				LEFT JOIN DocumentVersions on EnTournament=DvTournament AND DvFile = 'EN'
				LEFT JOIN (
				 SELECT sqDiv, sqCl, COUNT(sqQuanti) as numTeam
				 FROM
				 (SELECT EnDivision as sqDiv, EnClass as sqCl, COUNT(EnId) as sqQuanti
				 FROM Entries
				 WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnTeamClEvent=1
				 GROUP BY EnDivision, EnClass, IF(EnCountry2=0,EnCountry,EnCountry2), EnSubTeam
				 HAVING sqQuanti>=3) as sq
				 GROUP BY sqDiv, sqCl
				) AS sqy ON EnDivision=sqDiv AND EnClass=sqCl
				WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . "
				GROUP BY EnDivision, EnClass, numTeam
				order by ClViewOrder, DivViewOrder, numTeam";
			break;
	}
	return $Sql;
}

function getStatEntriesByEventIndQuery() {
	$Sql = "SELECT EvCode as Code, EvEventName as EventName, EvFinalFirstPhase as FirstPhase, COUNT(EnId) as Quanti,
				concat(DvMajVersion, '.', DvMinVersion) as DocVersion,
				date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate,
				DvNotes as DocNotes
		FROM Events
		INNER JOIN Individuals ON EvCode=IndEvent AND EvTournament=IndTournament
		INNER JOIN Entries ON EnId=IndId AND EnTournament=IndTournament 
		LEFT JOIN DocumentVersions on EnTournament=DvTournament AND DvFile = 'EN'
		WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0 AND ((EnIndFEvent=1 AND EnStatus<=1) OR EnId IS NULL)
		GROUP BY EvCode, EvFinalFirstPhase
		ORDER BY EvProgr";
	return $Sql;
}

function getStatEntriesByCountriesQuery($ORIS=false, $Athletes=false) {
	$Sql="";
	if($ORIS) {
		$Sql = "SELECT SUM(IF((DivAthlete AND ClAthlete AND EnSex=0), 1,0)) as `M`, SUM(IF((DivAthlete AND ClAthlete AND EnSex=1), 1,0)) as `W`, SUM(IF((DivAthlete AND ClAthlete), 0,1)) as `Of`,
				CoCode as NationCode, CoName as NationName,
				concat(DvMajVersion, '.', DvMinVersion) as DocVersion,
				date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate,
				DvNotes as DocNotes
			FROM Entries
			INNER JOIN Countries ON EnCountry = CoId
			LEFT JOIN DocumentVersions on EnTournament=DvTournament AND DvFile = 'EN'
			LEFT JOIN Divisions ON EnDivision=DivId AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) . "
			LEFT JOIN Classes ON EnClass=ClId AND ClTournament=" . StrSafe_DB($_SESSION['TourId']) . "
			WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . "
			GROUP BY CoCode
			ORDER BY CoCode ";
	} else {
		$Sql = "SELECT DISTINCT CONCAT(TRIM(EnDivision),'|',TRIM(EnClass)) as Id, (DivAthlete AND ClAthlete) as isAthlete
			FROM Entries
			LEFT JOIN Divisions ON EnDivision=DivId AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) . "
			LEFT JOIN Classes ON EnClass=ClId AND ClTournament=" . StrSafe_DB($_SESSION['TourId']) . "
			WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " "
			. ($Athletes ? 'AND DivAthlete=1 AND ClAthlete=1 ' : '')
			. "ORDER BY LENGTH(EnDivision) DESC, DivViewOrder, EnDivision, LENGTH(EnClass) DESC, ClViewOrder, EnClass";
		$Rs = safe_r_sql($Sql);

		$Sql = "SELECT ";
		if(safe_num_rows($Rs)>0) {
			while($MyRow=safe_fetch($Rs))
				$Sql .= "SUM(IF(CONCAT(TRIM(EnDivision),'|',TRIM(EnClass))='" . $MyRow->Id . "',1,0)) as `" . $MyRow->Id . "`, ";
			safe_free_result($Rs);
		}
		$Sql .= "CoCode as NationCode, CoName as NationName,
				concat(DvMajVersion, '.', DvMinVersion) as DocVersion,
				date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate,
				DvNotes as DocNotes
			FROM Entries
			INNER JOIN Countries ON EnCountry = CoId
			LEFT JOIN DocumentVersions on EnTournament=DvTournament AND DvFile = 'EN'
			WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . "
			GROUP BY CoCode
			ORDER BY CoCode ";
	}
	return $Sql;
}


function getStartListQuery($ORIS=false, $Event='', $Elim=false, $Filled=false, $isPool=false, $BySchedule=false) {
	global $CFG;

	if(file_exists($f=$CFG->DOCUMENT_PATH.'Modules/Sets/'.$_SESSION['TourLocRule'].'/func/getStartListQuery.php')) {
		include_once($f);
		$func='getStartListQuery_'.$_SESSION['TourLocRule'];
		return $func($ORIS, $Event, $Elim);
	}

	if($Elim) {
		switch($isPool) {
			case 3:
				// Elimtype 3 (World Games)
				$PoolA=implode(',', getPoolMatchNos('A'));
				$PoolB=implode(',', getPoolMatchNos('B'));
				$MyQuery= "SELECT distinct
						'' as SesName, EvProgr, EvElimType, FinMatchNo, GrPhase,
						EvCode as EventCode,
						EnCode as Bib,
						EnName AS Name,
						upper(EnFirstName) AS FirstName,
						EnClass AS ClassCode,
						EnDivision AS DivCode,
						EnAgeClass as AgeClass,
						EnSubClass as SubClass,
						if(find_in_set(FinMatchNo, '$PoolA'), 2, if(find_in_set(FinMatchNo, '$PoolB'), 3, 1))*100 + find_in_set(FinMatchNo, '$PoolA') + find_in_set(FinMatchNo, '$PoolB') as Session,
						FsLetter AS TargetNo,
						upper(right(FsLetter,1)) AS TargetLetter,
						CoCode AS NationCode,
						CoName AS Nation,
						EvElim1,
						EvElim2,
						'' as NumTargets,
			            EvOdfCode,
						EvEventName as EventName,
						if(trim(FinArrowstring)!='' or FinConfirmed or FinScore>0 or FinTie>0, if(EvMatchMode=1, FinSetScore, FinScore), concat(date_format(FsScheduledDate, '%e %b'), '@', date_format(FsScheduledTime, '%H:%i')))  as Score, 
						FinTiebreak as Tiebreak,
						FinTbClosest as Closest,
						FinTbDecoded as Decoded,
						concat(date_format(FsScheduledDate, '%e %b'), '@', date_format(FsScheduledTime, '%H:%i'))  as ScheduledStart,
						upper(DATE_FORMAT(EnDob,'%d %b %Y')) as DOB,
						EvProgr as ElSession,
						concat(DvMajVersion, '.', DvMinVersion) as DocVersion,
						date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate,
						DvNotes as DocNotes,
						ifnull(RankRanking, '') as Ranking, 
						ifnull(RankSeasonBest, '') as Season, 
						ifnull(RankPersonalBest, '') as Personal, 
						EnTimestamp
					FROM Finals
					inner join Grids on GrMatchNo=FinMatchNo 
					INNER JOIN Events ON FinEvent=EvCode AND EvTeamEvent=0 AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (EvElim1>0 OR EvElim2>0) and EvElimType=3 and EvShootOff=1
					inner join Tournament on ToId=FinTournament
					left join FinSchedule on FinMatchNo=FSMatchNo and FinEvent=FSEvent and FinTournament=FSTournament and FSTeamEvent=0
					left JOIN Entries ON FinAthlete=EnId
					left JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
					LEFT JOIN Rankings on EnTournament=RankTournament and RankEvent=FinEvent and RankTeam=0 and EnCode=RankCode and ToIocCode='FITA' and EnIocCode in ('', 'FITA') and RankIocCode='FITA'
					LEFT JOIN DocumentVersions on EnTournament=DvTournament AND DvFile = 'ELIM'
					WHERE
						FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " and GrPhase>EvFinalFirstPhase and EvFinalFirstPhase>0
						".($Event ? (is_array($Event) ? "AND FinEvent in (".implode(',', StrSafe_DB($Event)).")" : "AND FinEvent=".StrSafe_DB($Event)) : '');
					if($BySchedule) {
						$MyQuery .= "ORDER BY ElSession ASC, FsScheduledDate, FsScheduledTime, GrPhase desc, FsTarget, Session, FinMatchNo, EvProgr, EventName, Name, FirstName ";
					} else {
						$MyQuery .= "ORDER BY ElSession ASC, Session ASC, TargetNo, EvProgr, EventName, Name, FirstName ";
					}
				break;
			case 4:
				// Elim type 4 (World Archery 2018)
				$PoolA=implode(',', getPoolMatchNosWA('A', false));
				$PoolB=implode(',', getPoolMatchNosWA('B', false));
				$PoolC=implode(',', getPoolMatchNosWA('C', false));
				$PoolD=implode(',', getPoolMatchNosWA('D', false));
				$MyQuery= "SELECT distinct
						'' as SesName, EvProgr, EvElimType, FinMatchNo, GrPhase,
						EvCode as EventCode,
						EnCode as Bib,
						EnName AS Name,
						upper(EnFirstName) AS FirstName,
						EnClass AS ClassCode,
						EnDivision AS DivCode,
						EnAgeClass as AgeClass,
						EnSubClass as SubClass,
						if(find_in_set(FinMatchNo, '$PoolA'), 2, if(find_in_set(FinMatchNo, '$PoolB'), 3, if(find_in_set(FinMatchNo, '$PoolC'), 4, if(find_in_set(FinMatchNo, '$PoolD'), 5, 1))))*1000 + find_in_set(FinMatchNo, '$PoolA') + find_in_set(FinMatchNo, '$PoolB') + find_in_set(FinMatchNo, '$PoolC') + find_in_set(FinMatchNo, '$PoolD') as Session,
						FsLetter AS TargetNo,
						upper(right(FsLetter,1)) AS TargetLetter,
						CoCode AS NationCode,
						CoName AS Nation,
						EvElim1,
						EvElim2,
						'' as NumTargets,
			            EvOdfCode,
						EvEventName as EventName,
						if(trim(FinArrowstring)!='' or FinConfirmed or FinScore>0 or FinTie>0, if(EvMatchMode=1, FinSetScore, FinScore), concat(date_format(FsScheduledDate, '%e %b'), '@', date_format(FsScheduledTime, '%H:%i')))  as Score, 
                		FinTiebreak as Tiebreak,
						FinTbClosest as Closest,
						FinTbDecoded as Decoded,
						concat(date_format(FsScheduledDate, '%e %b'), '@', date_format(FsScheduledTime, '%H:%i'))  as ScheduledStart,
						upper(DATE_FORMAT(EnDob,'%d %b %Y')) as DOB,
						EvProgr as ElSession,
						concat(DvMajVersion, '.', DvMinVersion) as DocVersion,
						date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate,
						DvNotes as DocNotes,
						ifnull(RankRanking, '') as Ranking, 
						ifnull(RankSeasonBest, '') as Season, 
						ifnull(RankPersonalBest, '') as Personal, 
						EnTimestamp
					FROM Finals
					inner join Grids on GrMatchNo=FinMatchNo 
					INNER JOIN Events ON FinEvent=EvCode AND EvTeamEvent=0 AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (EvElim1>0 OR EvElim2>0) and EvElimType=4 and EvShootOff=1
					inner join Tournament on ToId=FinTournament
					left join FinSchedule on FinMatchNo=FSMatchNo and FinEvent=FSEvent and FinTournament=FSTournament and FSTeamEvent=0
					left JOIN Entries ON FinAthlete=EnId
					left JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
					LEFT JOIN Rankings on EnTournament=RankTournament and RankEvent=FinEvent and RankTeam=0 and EnCode=RankCode and ToIocCode='FITA' and EnIocCode in ('', 'FITA') and RankIocCode='FITA'
					LEFT JOIN DocumentVersions on EnTournament=DvTournament AND DvFile = 'ELIM'
					WHERE
						FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " and GrPhase>EvFinalFirstPhase and EvFinalFirstPhase>0
						".($Event ? (is_array($Event) ? "AND FinEvent in (".implode(',', StrSafe_DB($Event)).")" : "AND FinEvent=".StrSafe_DB($Event)) : '');
				if($BySchedule) {
					$MyQuery .= "ORDER BY ElSession ASC, FsScheduledDate, FsScheduledTime, GrPhase desc, FsTarget, Session, FinMatchNo, EvProgr, EventName, Name, FirstName ";
				} else {
					$MyQuery .= "ORDER BY ElSession ASC, Session ASC, TargetNo, EvProgr, EventName, Name, FirstName ";
				}
				break;
			default:
				$MyQuery = "SELECT distinct
						SesName, EvProgr, EvElimType, 0 as FinMatchNo, -1 as GrPhase,
						EvCode as EventCode,
						EnCode as Bib,
						EnName AS Name,
						upper(EnFirstName) AS FirstName,
						EnClass AS ClassCode,
						EnDivision AS DivCode,
						EnAgeClass as AgeClass,
						EnSubClass as SubClass,
						ElElimPhase as Session,
						ElTargetNo AS TargetNo,
						upper(right(ElTargetNo,1)) AS TargetLetter,
						CoCode AS NationCode,
						CoName AS Nation,
						EvElim1,
						EvElim2,
			            ifnull(SesTar4Session, if(ElElimPhase=0, 12, 8)) NumTargets,
			            EvOdfCode,
			            EvEventName as EventName,
						'' Score, '' as Tiebreak,
						upper(DATE_FORMAT(EnDob,'%d %b %Y')) as DOB,
						ElSession,
						concat(DvMajVersion, '.', DvMinVersion) as DocVersion,
						date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate,
						DvNotes as DocNotes,
						ifnull(RankRanking, '') as Ranking, 
						ifnull(RankSeasonBest, '') as Season, 
						ifnull(RankPersonalBest, '') as Personal, 
			            EnTimestamp
					FROM Eliminations
					INNER JOIN Entries ON ElId=EnId
					inner join Tournament on ToId=EnTournament
					INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
					INNER JOIN Events ON ElEventCode=EvCode AND EvTeamEvent=0 AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (EvElim1>0 OR EvElim2>0)
					LEFT JOIN Rankings on EnTournament=RankTournament and RankEvent=ElEventCode and RankTeam=0 and EnCode=RankCode and ToIocCode='FITA' and EnIocCode in ('', 'FITA') and RankIocCode='FITA'
					LEFT JOIN Session ON ElSession=SesOrder AND ElTournament=SesTournament AND SesType='E'
					LEFT JOIN DocumentVersions on EnTournament=DvTournament AND DvFile = 'ELIM'
					WHERE
				EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
				if (isset($_REQUEST['Elim']) && is_numeric($_REQUEST['Elim'])) {
					$MyQuery.="AND ElElimPhase=" . StrSafe_DB($_REQUEST['Elim']) . " ";
				} elseif ($Event) {
					if(is_array($Event)) {
						$MyQuery.="AND ElEventCode in (".implode(',', StrSafe_DB($Event)).") ";
					} else {
						$MyQuery.="AND ElElimPhase=" . ($Event-1) . " ";
					}
				}
				if(!empty($_REQUEST['EnCodes'])) {
					sort($_REQUEST['EnCodes']);
					$MyQuery.= " and EnCode in (".implode(',', $_REQUEST['EnCodes']).") ";
				}
				$MyQuery .= "ORDER BY ElSession ASC, Session ASC, TargetNo, EvProgr, EventName, Name, FirstName ";
		}

		return $MyQuery;
	} else {
		if($ORIS) {
			$Fields="";
			$Join="";
		} else {
			$Fields="";
			$Join="";
		}

		if(!empty($_REQUEST["Session"])) {
			if(is_array($_REQUEST["Session"])) {
				$Sessions=$_REQUEST["Session"];
			} else {
				$Sessions=array($_REQUEST["Session"]);
			}
		}

		$MyQuery = "SELECT distinct SesName, 
				EvCode, EvOdfCode, DivDescription, ClDescription, 
				Bib, Athlete, SUBSTRING(AtTargetNo,1,1) AS Session, SUBSTRING(AtTargetNo,2) AS TargetNo, NationCode, Nation, RealEventCode, RealEventName,
				EventCode, EventName, DOB, SesAth4Target, ClassCode, DivCode, AgeClass, SubClass, Status, 
				`IC`, `TC`, `IF`, `TF`, `TM`, NationCode2, Nation2, NationCode3, Nation3, EnSubTeam, TfName, 
				concat(DvMajVersion, '.', DvMinVersion) as DocVersion, date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate,
				DvNotes as DocNotes,
				ifnull(RankRanking, '') as Ranking, Season, Personal, EnTimestamp
			 FROM AvailableTarget at 
			 INNER JOIN Session ON SUBSTRING(AtTargetNo,1,1)=SesOrder AND AtTournament=SesTournament AND SesType='Q' 
			 LEFT JOIN DocumentVersions on AtTournament=DvTournament AND DvFile = 'TGT' 
			 LEFT JOIN 
			 	(SELECT distinct ".($ORIS ? "EvCode," : "'' EvCode,") . ($ORIS ? " EvProgr," : " '' EvProgr,") . " 
					DivDescription, ClDescription, EvOdfCode,
					EvCode as RealEventCode, EvEventName as RealEventName,
					" . ($ORIS ? " IFNULL(EvCode,CONCAT(TRIM(EnDivision),TRIM(EnClass))) as EventCode," : " '' as EventCode,") . " 
					concat(upper(EnFirstName), ' ', EnName) Athlete,
					EnCode as Bib,
					QuTargetNo,
					upper(c.CoCode) AS NationCode,
					upper(c.CoName) AS Nation,
					upper(c2.CoCode) NationCode2,
					upper(c2.CoName) Nation2,
					upper(c3.CoCode) NationCode3,
					upper(c3.CoName) Nation3,
					" . ($ORIS ? " IFNULL(EvEventName,CONCAT('|',DivDescription, '| |', ClDescription)) as EventName," : " '' as EventName,") . " 
					EnSubTeam,
					EnClass AS ClassCode,
					EnDivision AS DivCode,
					EnAgeClass as AgeClass,
					EnSubClass as SubClass,
					EnStatus as Status,
					EnIndClEvent AS `IC`,
					EnTeamClEvent AS `TC`,
					EnIndFEvent AS `IF`,
					EnTeamFEvent as `TF`,
					EnTeamMixEvent as `TM`,
					DATE_FORMAT(EnDob,'%d %b %Y') as DOB, 
					TfName, 
					" . ($ORIS ? " RankRanking " : "'' ") ." as RankRanking,
					ifnull(RankSeasonBest, '') as Season, 
					ifnull(RankPersonalBest, '') as Personal, 
					EnTimestamp
				FROM Qualifications AS q 
				INNER JOIN Entries AS e ON q.QuId=e.EnId AND e.EnTournament= " . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1 
				INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament
				INNER JOIN Tournament on ToId=EnTournament 
				LEFT JOIN Countries AS c2 ON e.EnCountry2=c2.CoId AND e.EnTournament=c2.CoTournament 
				LEFT JOIN Countries AS c3 ON e.EnCountry3=c3.CoId AND e.EnTournament=c3.CoTournament 
				LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament 
				LEFT JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament 
				left join Individuals on IndId=EnId and IndTournament=EnTournament
				left join Events on EvCode=IndEvent and EvTournament=EnTournament and EvTeamEvent=0 
				LEFT JOIN Rankings on EnTournament=RankTournament and RankEvent=IF(EvWaCategory!='',EvWaCategory,EvCode) and RankTeam=0 and EnCode=RankCode and ToIocCode='FITA' and EnIocCode in ('', 'FITA') and RankIocCode='FITA' 
				LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId 
				) as Sq ON at.AtTargetNo=Sq.QuTargetNo 
			WHERE AtTournament = " . StrSafe_DB($_SESSION['TourId']) ;
		if(isset($_REQUEST["Session"]) && $_REQUEST["Session"]!='All') {
			$MyQuery .= " AND SUBSTRING(AtTargetNo,1,1) in (" . implode(',', $Sessions) . ") ";
		}
		if(isset($_REQUEST["x_Session"]) )
			$MyQuery .= " AND SUBSTRING(AtTargetNo,1,1) = " . StrSafe_DB($_REQUEST["x_Session"]) . " ";
		if(isset($_REQUEST["x_From"]) and isset($_REQUEST["x_To"]) ) {
			$MyQuery .= " AND SUBSTRING(AtTargetNo,2,3) >= " . StrSafe_DB(sprintf('%03d', intval($_REQUEST["x_From"]))) . " ";
			$MyQuery .= " AND SUBSTRING(AtTargetNo,2,3) <= " . StrSafe_DB(sprintf('%03d', intval($_REQUEST["x_To"]))) . " ";

		}
		if(isset($_REQUEST['Empty'])) {
			$MyQuery.=" and QuTargetNo is null
						and AtTargetNo not in (select case right(QuTargetNo,1) when 'A' then concat(left(QuTargetNo, 4),'C') when 'B' then concat(left(QuTargetNo, 4),'D') when 'C' then concat(left(QuTargetNo, 4),'A') when 'D' then concat(left(QuTargetNo, 4),'B') else '' end from (select QuTargetNo from Qualifications inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']} and EnWchair=1) tmp )
						and AtTargetNo not in (select case right(QuTargetNo,1) when 'A' then concat(left(QuTargetNo, 4),'b') when 'B' then concat(left(QuTargetNo, 4),'A') when 'C' then concat(left(QuTargetNo, 4),'D') when 'D' then concat(left(QuTargetNo, 4),'C') else '' end from (select QuTargetNo from Qualifications inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']} and EnDoubleSpace=1) tmp ) ";
		}

		if(!empty($_REQUEST['EnCodes'])) {
			sort($_REQUEST['EnCodes']);
			$MyQuery.= " and Bib in (".implode(',', $_REQUEST['EnCodes']).") ";
		}

		if($Event) {
			if(!is_array($Event)) {
				$Event=array($Event);
			}

			$MyQuery.= " and RealEventCode in (".implode(',', StrSafe_DB($Event)).") ";
		}

		if($Filled) {
			$MyQuery.= " and QuTargetNo is not null ";
		}

		if($ORIS) {
			$MyQuery.= " AND EventName!='' ";
			$MyQuery.= " ORDER BY EvProgr, EventName, AtTargetNo, Athlete ";
		} else {
			$MyQuery.= " ORDER BY AtTargetNo, NationCode, Athlete, Nation ";
		}
	}
	return $MyQuery;
}

function getCountryList() {
	$TmpWhere="";
	if(isset($_REQUEST["CountryName"]) && preg_match("/^[-,0-9A-Z]*$/i",str_replace(" ","",$_REQUEST["CountryName"])))
	{
		foreach(explode(",",$_REQUEST["CountryName"]) as $Value)
		{
			$Tmp=NULL;
			if(preg_match("/^([A-Z0-9]*)-([A-Z0-9]*)$/i",str_replace(" ","",$Value),$Tmp))
			$TmpWhere .= "(CoCode >= " . StrSafe_DB(stripslashes($Tmp[1]) ) . " AND CoCode <= " . StrSafe_DB(stripslashes($Tmp[2].chr(255))) . ") OR ";
			else
			$TmpWhere .= "CoCode LIKE " . StrSafe_DB(stripslashes(trim($Value)) . "%") . " OR ";
		}
		$TmpWhere = substr($TmpWhere,0,-3);
	}

	$MyQuery = "SELECT DISTINCT
			upper(CoCode) AS NationCode, CoNameComplete AS Nation,
			concat(DvMajVersion, '.', DvMinVersion) as DocVersion,
			date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate,
				DvNotes as DocNotes
			FROM Entries AS e
			INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament
			INNER JOIN Qualifications AS q ON e.EnId=q.QuId
			LEFT JOIN DocumentVersions on EnTournament=DvTournament AND DvFile = 'EN'
			WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " "; // 2010-03-16 totlo EnAthlete=1 AND
	if(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"])) {
		$MyQuery .= "AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) . " ";
	}
	if($TmpWhere != "") {
		$MyQuery .= "AND (" . $TmpWhere . ")";
	}
	$MyQuery.= "ORDER BY CoCode";

	return $MyQuery;
}


function getStartListCountryQuery($ORIS=false, $Athletes=false, $orderByName=false, $Events=array(), $Sessions=array()) {
	$SinglePage = isset($_REQUEST['SinglePage']);
	$TargetFace=(isset($_REQUEST['tf']) && $_REQUEST['tf']==1);
	$NoPhoto=isset($_REQUEST['NoPhoto']);
	$Emails=isset($_REQUEST['Emails']);

	$TmpWhere="";
	if(isset($_REQUEST["CountryName"]) && preg_match("/^[-,0-9A-Z]*$/i",str_replace(" ","",$_REQUEST["CountryName"])))
	{
		foreach(explode(",",$_REQUEST["CountryName"]) as $Value)
		{
			$Tmp=NULL;
			if(preg_match("/^([A-Z0-9]*)-([A-Z0-9]*)$/i",str_replace(" ","",$Value),$Tmp))
				$TmpWhere .= "(CoCode >= " . StrSafe_DB(stripslashes($Tmp[1]) ) . " AND CoCode <= " . StrSafe_DB(stripslashes($Tmp[2].chr(255))) . ") OR ";
			else
				$TmpWhere .= "CoCode LIKE " . StrSafe_DB(stripslashes(trim($Value)) . "%") . " OR ";
		}
		$TmpWhere = substr($TmpWhere,0,-3);
	}


	if($ORIS) {
		$MyQuery = "SELECT distinct
				SesName, EvCode, EvCodeParent, EnDivision as DivCode, EnClass as ClassCode, DivDescription, ClDescription, DivAthlete and ClAthlete as IsAthlete,
				IFNULL(EvCode,CONCAT(TRIM(EnDivision),TRIM(EnClass))) as EventCode, EnCode as Bib,
				concat(upper(EnFirstName), ' ', EnName) AS Athlete, DATE_FORMAT(EnDob,'%d %b %Y') as DOB, QuSession AS Session, SUBSTRING(QuTargetNo,2) AS TargetNo,
				upper(CoCode) AS NationCode, upper(CoName) AS Nation, if(CoNameComplete!='', CoNameComplete, CoName) AS NationComplete,
				IFNULL(GROUP_CONCAT(EvEventName SEPARATOR ', '), if(DivAthlete and ClAthlete, CONCAT('|',DivDescription, '| |', ClDescription), ClDescription)) as EventName,
				IFNULL(GROUP_CONCAT(RankRanking order by EvProgr SEPARATOR ', '), '') as Ranking,
				cNumber, PhPhoto is not null as HasPhoto, EnBadgePrinted>0 as HasAccreditation,
				concat(DvMajVersion, '.', DvMinVersion) as DocVersion,
				date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate,
				DvNotes as DocNotes, edmail.EdEmail, edmail.EdExtra, edbib.EdExtra as Bib2, EnDob
			FROM Entries AS e
			INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament
			INNER JOIN Qualifications AS q ON e.EnId=q.QuId
			INNER JOIN (
				SELECT EnCountry AS cCode, COUNT(EnId) AS cNumber FROM `Entries`
				WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " GROUP BY EnCountry
				) as sqy ON e.EnCountry=sqy.cCode
			inner join Tournament on EnTournament=ToId
			LEFT JOIN Individuals on IndId=EnId AND EnTournament=IndTournament
			left join Events on EvCode=IndEvent and EvTournament=EnTournament and EvTeamEvent=0
			left JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament
			left JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament
			LEFT JOIN Session on EnTournament=SesTournament and SesType='Q' and SesOrder=QuSession
			LEFT JOIN Photos ON PhEnId=EnId
			LEFT JOIN ExtraData edmail ON edmail.EdId=EnId and edmail.EdType='E'
			LEFT JOIN ExtraData edbib ON edbib.EdId=EnId and edbib.EdType='Z'
			LEFT JOIN DocumentVersions on EnTournament=DvTournament AND DvFile = 'EN'
			LEFT JOIN Rankings on EnTournament=RankTournament and RankEvent=IF(EvWaCategory!='',EvWaCategory,EvCode) and RankTeam=0 and EnCode=RankCode and ToIocCode='FITA' and EnIocCode in ('', 'FITA') and RankIocCode='FITA'
			WHERE EnTournament = " . intval($_SESSION['TourId']);
		if($Athletes) {
			$MyQuery.= " AND EnAthlete=1 ";
		}

		if($Events) {
			$MyQuery .= "AND IndEvent in (" . implode(',', StrSafe_DB($Events)) . ") ";
		} elseif(isset($_REQUEST["Event"])) {
			$MyQuery .= "AND IndEvent LIKE " . StrSafe_DB($_REQUEST["Event"]) . " ";
		}

		if($Sessions) {
			$MyQuery .= "AND QuSession in (" . implode(',', $Sessions) . ") ";
		} elseif(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"])) {
			$MyQuery .= "AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) . " ";
		}

		if($TmpWhere != "")
			$MyQuery .= "AND (" . $TmpWhere . ")";
		if(!empty($_REQUEST['Exclude'])) {
			$MyQuery .= "AND (EnDivision not in ('" . implode("','", $_REQUEST['Exclude']) . "')) ";
		}
		$MyQuery.= " GROUP BY SesName, DivDescription, ClDescription, IsAthlete, Bib, Athlete, DOB, Session, TargetNo, NationCode, Nation ";
		$MyQuery.= " ORDER BY CoCode, EnAthlete desc, ".($Athletes ? 'DivViewOrder, ClViewOrder, ' : '' )."Athlete, TargetNo ";
		return $MyQuery;
	}

	$MyQuery = "(SELECT"
			. " EnCode as Bib"
			. ", concat(upper(EnFirstName), ' ', EnName) AS Athlete"
			. ", QuSession AS Session"
			. ", SesName"
			. ", SUBSTRING(QuTargetNo,2) AS TargetNo"
			. ", upper(CoCode) AS NationCode"
			. ", upper(CoName) AS Nation"
			. ", CoNameComplete AS NationComplete"
			. ", EnSubTeam"
			. ", EnSex"
			. ", EnClass AS ClassCode"
			. ", ClDescription"
			. ", EnDivision AS DivCode"
			. ", DivDescription"
			. ", DivAthlete and ClAthlete as IsAthlete"
			. ", EnAgeClass as AgeClass"
			. ", EnSubClass as SubClass"
			. ", EnStatus as Status"
			. ", EnIndClEvent AS `IC`"
			. ", EnTeamClEvent AS `TC`"
			. ", EnIndFEvent AS `IF`"
			. ", EnTeamFEvent as `TF`"
			. ", EnTeamMixEvent as `TM`"
            . ", IFNULL(GROUP_CONCAT(EvCode order by EvProgr SEPARATOR ', '), '')  as RealEventCode"
            . ", IFNULL(GROUP_CONCAT(EvEventName order by EvProgr SEPARATOR ', '), '')  as RealEventName"
            . ", GROUP_CONCAT(EvCodeParent order by EvProgr SEPARATOR '')  as EvCodeParent"
            . ", GROUP_CONCAT(RankRanking) as Ranking"
        	. ", IF(EnCountry2=0,0,1) as secTeam "
			. ", TfName, PhPhoto is not null as HasPhoto, edmail.EdEmail, edmail.EdExtra, edbib.EdExtra as Bib2, EnDob ";
	$MyQuery.= "FROM Entries AS e ";
	$MyQuery.= "inner JOIN Tournament ON ToId=EnTournament ";
	$MyQuery.= "LEFT JOIN Individuals ON IndId=EnId and IndTournament=EnTournament ";
	$MyQuery.= "left join Events on EvCode=IndEvent and EvTournament=EnTournament and EvTeamEvent=0  ";
	$MyQuery.= "LEFT JOIN Photos ON e.EnId=PhEnId ";
	$MyQuery.= "LEFT JOIN ExtraData as edmail ON edmail.EdId=EnId and edmail.EdType='E' ";
	$MyQuery.= "LEFT JOIN ExtraData as edbib ON edbib.EdId=EnId and edbib.EdType='Z' ";
	$MyQuery.= "LEFT JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament ";
	$MyQuery.= "LEFT JOIN Qualifications AS q ON e.EnId=q.QuId ";
	$MyQuery.= "LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament ";
	$MyQuery.= "LEFT JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament ";
	$MyQuery.= "LEFT JOIN Session on EnTournament=SesTournament and SesType='Q' and SesOrder=QuSession ";
	$MyQuery.= "LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId ";
	$MyQuery.= "LEFT JOIN Rankings on EnTournament=RankTournament and RankEvent=IndEvent and RankTeam=0 and EnCode=RankCode and ToIocCode='FITA' and EnIocCode in ('', 'FITA') and RankIocCode='FITA' ";
	$MyQuery.= "WHERE EnTournament = " . intval($_SESSION['TourId']);

	if($Athletes) $MyQuery.= " AND EnAthlete=1 ";

	if($Events) {
		$MyQuery .= " AND IndEvent in (" . implode(',', StrSafe_DB($Events)) . ") ";
	}

	if($Sessions) {
		$MyQuery .= " AND QuSession in (" . implode(',', $Sessions) . ") ";
	} elseif(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"])) {
		$MyQuery .= " AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) . " ";
	}

	if($TmpWhere != "")
		$MyQuery .= " AND (" . $TmpWhere . ")";
	if($NoPhoto) $MyQuery .= " AND (length(PhPhoto)='' or PhPhoto is null) ";
    $MyQuery .= " GROUP BY SesName, DivDescription, ClDescription, IsAthlete, Bib, Athlete,  Session, TargetNo, NationCode, Nation";
	$MyQuery .= ") UNION ALL ";
	$MyQuery .= "(SELECT"
			. " EnCode as Bib"
			. ", concat(upper(EnFirstName), ' ', EnName) AS Athlete"
			. ", QuSession AS Session"
			. ", SesName"
			. ", SUBSTRING(QuTargetNo,2) AS TargetNo"
			. ", upper(CoCode) AS NationCode"
			. ", upper(CoName) AS Nation"
			. ", CoNameComplete AS NationComplete"
			. ", EnSubTeam"
			. ", EnSex"
			. ", EnClass AS ClassCode"
			. ", ClDescription"
			. ", EnDivision AS DivCode"
			. ", DivDescription"
			. ", DivAthlete and ClAthlete as IsAthlete"
			. ", EnAgeClass as AgeClass"
			. ", EnSubClass as SubClass"
			. ", EnStatus as Status"
			. ", EnIndClEvent AS `IC`"
			. ", EnTeamClEvent AS `TC`"
			. ", EnIndFEvent AS `IF`"
			. ", EnTeamFEvent as `TF`"
			. ", EnTeamMixEvent as `TM`"
            . ", IFNULL(GROUP_CONCAT(EvCode order by EvProgr SEPARATOR ', '), '')  as RealEventCode"
            . ", IFNULL(GROUP_CONCAT(EvEventName order by EvProgr SEPARATOR ', '), '')  as RealEventName"
            . ", GROUP_CONCAT(EvCodeParent order by EvProgr SEPARATOR '')  as EvCodeParent"
            . ", GROUP_CONCAT(RankRanking) as Ranking"
            . ", 2 as secTeam "
			. ", TfName, PhPhoto is not null as HasPhoto, edmail.EdEmail, edmail.EdExtra, edbib.EdExtra as Bib2, EnDob ";
	$MyQuery.= "FROM Entries AS e ";
	$MyQuery.= "inner JOIN Tournament ON ToId=EnTournament ";
	$MyQuery.= "LEFT JOIN Individuals ON IndId=EnId and IndTournament=EnTournament ";
	$MyQuery.= "left join Events on EvCode=IndEvent and EvTournament=EnTournament and EvTeamEvent=0   ";
	$MyQuery.= "LEFT JOIN Photos ON e.EnId=PhEnId ";
	$MyQuery.= "LEFT JOIN ExtraData as edmail ON edmail.EdId=EnId and edmail.EdType='E' ";
	$MyQuery.= "LEFT JOIN ExtraData as edbib ON edbib.EdId=EnId and edbib.EdType='Z' ";
	$MyQuery.= "LEFT JOIN Countries AS c ON e.EnCountry2=c.CoId AND e.EnTournament=c.CoTournament ";
	$MyQuery.= "LEFT JOIN Qualifications AS q ON e.EnId=q.QuId ";
	$MyQuery.= "LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament ";
	$MyQuery.= "LEFT JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament ";
	$MyQuery.= "LEFT JOIN Session on EnTournament=SesTournament and SesType='Q' and SesOrder=QuSession ";
	$MyQuery.= "LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId ";
	$MyQuery.= "LEFT JOIN Rankings on EnTournament=RankTournament and RankEvent=IndEvent and RankTeam=0 and EnCode=RankCode and ToIocCode='FITA' and EnIocCode in ('', 'FITA') and RankIocCode='FITA' ";
	$MyQuery.= "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EnCountry2!=0 AND (EnTeamClEvent!=0 OR EnTeamFEvent!=0 OR EnTeamMixEvent!=0) ";

	if($Athletes) $MyQuery.= " AND EnAthlete=1 ";

	if($Events) {
		$MyQuery .= "AND IndEvent in (" . implode(',', StrSafe_DB($Events)) . ") ";
	}

	if($Sessions) {
		$MyQuery .= " AND QuSession in (" . implode(',', $Sessions) . ") ";
	} elseif(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"])) {
		$MyQuery .= " AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) . " ";
	}

	if($TmpWhere != "")
		$MyQuery .= " AND (" . $TmpWhere . ")";
	if($NoPhoto) $MyQuery .= " AND (length(PhPhoto)='' or PhPhoto is null) ";
    $MyQuery .= " GROUP BY SesName, DivDescription, ClDescription, IsAthlete, Bib, Athlete,  Session, TargetNo, NationCode, Nation";
	$MyQuery.= ") UNION ALL ";

	$MyQuery .= "(SELECT"
			. " EnCode as Bib"
			. ", concat(upper(EnFirstName), ' ', EnName) AS Athlete"
			. ", QuSession AS Session"
			. ", SesName"
			. ", SUBSTRING(QuTargetNo,2) AS TargetNo"
			. ", upper(CoCode) AS NationCode"
			. ", upper(CoName) AS Nation"
			. ", CoNameComplete AS NationComplete"
			. ", EnSubTeam"
			. ", EnSex"
			. ", EnClass AS ClassCode"
			. ", ClDescription"
			. ", EnDivision AS DivCode"
			. ", DivDescription"
			. ", DivAthlete and ClAthlete as IsAthlete"
			. ", EnAgeClass as AgeClass"
			. ", EnSubClass as SubClass"
			. ", EnStatus as Status"
			. ", EnIndClEvent AS `IC`"
			. ", EnTeamClEvent AS `TC`"
			. ", EnIndFEvent AS `IF`"
			. ", EnTeamFEvent as `TF`"
			. ", EnTeamMixEvent as `TM`"
            . ", IFNULL(GROUP_CONCAT(EvCode order by EvProgr SEPARATOR ', '), '')  as RealEventCode"
            . ", IFNULL(GROUP_CONCAT(EvEventName order by EvProgr SEPARATOR ', '), '')  as RealEventName"
            . ", GROUP_CONCAT(EvCodeParent order by EvProgr SEPARATOR '')  as EvCodeParent"
            . ", GROUP_CONCAT(RankRanking) as Ranking"
            . ", 3 as secTeam "
			. ", TfName, PhPhoto is not null as HasPhoto, edmail.EdEmail, edmail.EdExtra, edbib.EdExtra as Bib2, EnDob ";
	$MyQuery.= "FROM Entries AS e ";
	$MyQuery.= "inner JOIN Tournament ON ToId=EnTournament ";
	$MyQuery.= "LEFT JOIN Individuals ON IndId=EnId and IndTournament=EnTournament ";
	$MyQuery.= "left join Events on EvCode=IndEvent and EvTournament=EnTournament and EvTeamEvent=0  ";
	$MyQuery.= "LEFT JOIN Photos ON e.EnId=PhEnId ";
	$MyQuery.= "LEFT JOIN ExtraData as edmail ON edmail.EdId=EnId and edmail.EdType='E' ";
	$MyQuery.= "LEFT JOIN ExtraData as edbib ON edbib.EdId=EnId and edbib.EdType='Z' ";
	$MyQuery.= "LEFT JOIN Countries AS c ON e.EnCountry3=c.CoId AND e.EnTournament=c.CoTournament ";
	$MyQuery.= "LEFT JOIN Qualifications AS q ON e.EnId=q.QuId ";
	$MyQuery.= "LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament ";
	$MyQuery.= "LEFT JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament ";
	$MyQuery.= "LEFT JOIN Session on EnTournament=SesTournament and SesType='Q' and SesOrder=QuSession ";
	$MyQuery.= "LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId ";
	$MyQuery.= "LEFT JOIN Rankings on EnTournament=RankTournament and RankEvent=IndEvent and RankTeam=0 and EnCode=RankCode and ToIocCode='FITA' and EnIocCode in ('', 'FITA') and RankIocCode='FITA' ";
	$MyQuery.= "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EnCountry3!=0 ";

	if($Athletes) $MyQuery.= " AND EnAthlete=1 ";

	if($Events) {
		$MyQuery .= " AND IndEvent in (" . implode(',', StrSafe_DB($Events)) . ") ";
	}

	if($Sessions) {
		$MyQuery .= " AND QuSession in (" . implode(',', $Sessions) . ") ";
	} elseif(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"])) {
		$MyQuery .= " AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) . " ";
	}

	if($TmpWhere != "")
		$MyQuery .= " AND (" . $TmpWhere . ")";
	if($NoPhoto) $MyQuery .= "AND (length(PhPhoto)='' or PhPhoto is null) ";
    $MyQuery .= " GROUP BY SesName, DivDescription, ClDescription, IsAthlete, Bib, Athlete,  Session, TargetNo, NationCode, Nation";
	$MyQuery.= ") ORDER BY " . ($orderByName ? "Nation" : "NationCode") . ", ".($SinglePage?'Session, ':'')." EnSex, DivCode, ClassCode, Athlete, TargetNo ";

	return $MyQuery;
}

function getStandingRecordsQuery($ORIS=true) {
	$MyQuery="select distinct EvEventName EventName, EvProgr, EvTeamEvent, EvRecCategory, TrHeaderCode, TrHeader, RecTournament.*
		from Events
		inner join RecTournament on EvTournament=RtTournament and EvTeamEvent=RtRecTeam
		inner join TourRecords on TrTournament=EvTournament and TrRecCode=RtRecCode and TrRecTeam=EvTeamEvent and TrRecPara=RtRecPara
		inner join RecAreas on ReArCode=RtRecCode
		where EvTournament={$_SESSION['TourId']} and EvMedals=1 and RtRecCategory=if(ReArWaMaintenance=1, EvRecCategory, EvCode) and RtRecTotal>0
		order by EvTeamEvent, EvProgr, RtRecCode desc, ReArBitLevel desc, RtRecPhase=0, RtRecPhase, RtRecDistance desc";
	return $MyQuery;
}

function getBrokenRecordsQuery($ORIS=true) {
	// The system is completely new and is based on the table RecBroken!
	// check if a double event...
	$q=safe_r_sql("select ToType, ToDouble, ToNumDist, ToXNineChars from Tournament where ToId={$_SESSION['TourId']}");
	$r=safe_fetch($q);
	$IsDouble=$r->ToDouble;
	$NumDistances=$r->ToNumDist;
	$RecordTotal="QuScore";
	$RecordXNine="QuXNine";
	$GenXNine=$r->ToXNineChars;
	if($IsDouble) {
		if($NumDistances==4) {
			// double 70m or 18+25
			$RecordTotal="if(RecBroRecDouble, QuScore, if(RecBroRecMatchno=1, QuD1Score+QuD2Score, QuD3Score+QuD4Score))";
			$RecordXNine="if(RecBroRecDouble, QuXNine, if(RecBroRecMatchno=1, QuD1Xnine+QuD2Xnine, QuD3Xnine+QuD4Xnine))";
		} else {
			$RecordTotal="if(RecBroRecDouble, QuScore, if(RecBroRecMatchno=1, QuD1Score+QuD2Score+QuD3Score+QuD4Score, QuD5Score+QuD6Score+QuD7Score+QuD8Score))";
			$RecordXNine="if(RecBroRecDouble, QuXNine, if(RecBroRecMatchno=1, QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine, QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine))";
		}
	}

	$SQL=array();
	// individuals qualification
	$SQL[]="select
		RecBroRecPhase as Phase,
		RecBroRecSubPhase as SubPhase,
		concat(length(RtRecCategory),RtRecCategory) as OrderBy,
		RtRecCategoryName as EventName,
       	RecBroRecTeam as TeamEvent,
        RtRecCategory as RecCategory,
       	RtRecCategoryName as RecCategoryName,
        $RecordTotal as NewRecord,
        $RecordXNine as NewXNine,
		$RecordTotal=RtRecMaxScore as CheckXNine,
       	RecBroRecDate as RecordDate,
       	date(RecBroRecDate) as RecordDateDate,
       	trim(concat(upper(EnFirstName), ' ', EnName)) as Athlete,
        CoCode,
       	if(RecBroRecPara, ReArOdfParaCode, ReArOdfCode) as TrHeaderCode, 
       	if(RecBroRecPara, ReArOdfParaHeader, ReArOdfHeader) as TrHeader, 
       	ReArBitLevel,
		RecTournament.*
	from RecBroken
	inner join RecAreas on ReArCode=RecBroRecCode
	inner join RecTournament on RtTournament=RecBroTournament and RtRecCode=RecBroRecCode and RtRecCategory=RecBroRecCategory and RtRecTeam=RecBroRecTeam and RtRecPara=RecBroRecPara and RtRecPhase=RecBroRecPhase and RtRecSubphase=RecBroRecSubPhase and RtRecDouble=RecBroRecDouble
	inner join Entries on EnTournament=RecBroTournament and EnId=RecBroAthlete
	inner join Countries on CoId=EnCountry
    inner join Qualifications on QuId=EnId
	inner join Divisions on DivTournament=RecBroTournament and DivId=EnDivision
	inner join Classes on ClTournament=RecBroTournament and ClId=EnClass
	/*left join Individuals on IndTournament=RecBroTournament and IndId=EnId
	left join Events on EvTournament=EnTournament and EvTeamEvent=0 and EvCode=IndEvent*/
	where RecBroTournament={$_SESSION['TourId']} and RecBroRecTeam=0 and RecBroRecPhase=1 
		/* and if(ReArWaMaintenance=1, RecBroRecCategory in (EvRecCategory, concat(DivRecDivision, ClRecClass)), RecBroRecCategory in (EvCode, concat(DivId, ClId)))*/";

	// Team qualification
	$SQL[]="select
		RecBroRecPhase as Phase,
		RecBroRecSubPhase as SubPhase,
		EvProgr as OrderBy,
		EvEventName as EventName,
       	RecBroRecTeam as TeamEvent,
        RtRecCategory as RecCategory,
       	RtRecCategoryName as RecCategoryName,
        TeScore as NewRecord,
        TeXNine as NewXNine,
		TeScore=RtRecMaxScore as CheckXNine,
       	RecBroRecDate as RecordDate,
       	date(RecBroRecDate) as RecordDateDate,
       	trim(concat(CoCode, ' ', CoName, \"\n\", group_concat(concat('   ', upper(EnFirstName), ' ', EnName) separator \"\n\"))) as Athlete,
        CoCode,
       	if(RecBroRecPara, ReArOdfParaCode, ReArOdfCode) as TrHeaderCode, 
       	if(RecBroRecPara, ReArOdfParaHeader, ReArOdfHeader) as TrHeader, 
       	ReArBitLevel,
		RecTournament.*
	from RecBroken
	inner join RecAreas on ReArCode=RecBroRecCode
	inner join RecTournament on RtTournament=RecBroTournament and RtRecCode=RecBroRecCode and RtRecCategory=RecBroRecCategory and RtRecTeam=RecBroRecTeam and RtRecPara=RecBroRecPara and RtRecPhase=RecBroRecPhase and RtRecSubphase=RecBroRecSubPhase
	inner join Teams on TeTournament=RecBroTournament and TeCoId=RecBroTeam and TeSubTeam=RecBroSubTeam and TeEvent=RecBroRecEvent and TeFinEvent=1
	inner join TeamComponent on TcTournament=TeTournament and TcCoId=TeCoId and TcSubTeam=TeSubTeam and TcEvent=TeEvent and TcFinEvent=1
	inner join Entries on EnTournament=RecBroTournament and EnId=TcId
	inner join Countries on CoTournament=TeTournament and CoId=TeCoId
	inner join Events on EvTournament=RecBroTournament and EvCode=RecBroRecEvent and EvTeamEvent=1
	where RecBroTournament={$_SESSION['TourId']} and RecBroRecTeam=1 and RecBroRecPhase=1
	group by RecBroRecCode, RecBroTeam, RecBroSubTeam, RecBroRecCategory";

	// individuals Matches
	$SQL[]="select
		RecBroRecPhase as Phase,
		RecBroRecSubPhase as SubPhase,
		ifnull(EvProgr, concat(length(RtRecCategory),RtRecCategory)) as OrderBy,
		ifnull(EvEventName, RtRecCategoryName) as EventName,
       	RecBroRecTeam as TeamEvent,
        RtRecCategory as RecCategory,
       	RtRecCategoryName as RecCategoryName,
        FinScore as NewRecord,
        length(FinArrowstring)-length(replace(FinArrowstring, if(EvXNineChars='', '{$GenXNine}', EvXNineChars), '')) as NewXNine,
		FinScore=RtRecMaxScore as CheckXNine,
       	RecBroRecDate as RecordDate,
       	date(RecBroRecDate) as RecordDateDate,
       	trim(concat(upper(EnFirstName), ' ', EnName)) as Athlete,
        CoCode,
       	if(RecBroRecPara, ReArOdfParaCode, ReArOdfCode) as TrHeaderCode, 
       	if(RecBroRecPara, ReArOdfParaHeader, ReArOdfHeader) as TrHeader, 
       	ReArBitLevel,
		RecTournament.*
	from RecBroken
	inner join RecAreas on ReArCode=RecBroRecCode
	inner join RecTournament on RtTournament=RecBroTournament and RtRecCode=RecBroRecCode and RtRecCategory=RecBroRecCategory and RtRecTeam=RecBroRecTeam and RtRecPara=RecBroRecPara and RtRecPhase=RecBroRecPhase and RtRecSubphase=RecBroRecSubPhase
	inner join Entries on EnTournament=RecBroTournament and EnId=RecBroAthlete
	inner join Countries on CoId=EnCountry
    inner join Finals on FinTournament=RecBroTournament and FinAthlete=EnId and FinMatchNo=RecBroRecMatchno and FinEvent=RecBroRecEvent
	left join Events on EvTeamEvent=0 and EvTournament=RecBroTournament and EvCode=RecBroRecEvent
	where RecBroTournament={$_SESSION['TourId']} and RecBroRecTeam=0 and RecBroRecPhase=3";

	// Team Matches
	$SQL[]="select
		RecBroRecPhase as Phase,
		RecBroRecSubPhase as SubPhase,
		EvProgr as OrderBy,
		EvEventName as EventName,
       	RecBroRecTeam as TeamEvent,
        RtRecCategory as RecCategory,
       	RtRecCategoryName as RecCategoryName,
        TfScore as NewRecord,
        length(TfArrowstring)-length(replace(TfArrowstring, if(EvXNineChars='', '{$GenXNine}', EvXNineChars), '')) as NewXNine,
		TfScore=RtRecMaxScore as CheckXNine,
       	RecBroRecDate as RecordDate,
       	date(RecBroRecDate) as RecordDateDate,
       	trim(concat(CoCode, ' ', CoName, \"\n\", group_concat(concat('   ', upper(EnFirstName), ' ', EnName) separator \"\n\"))) as Athlete,
        CoCode,
       	if(RecBroRecPara, ReArOdfParaCode, ReArOdfCode) as TrHeaderCode, 
       	if(RecBroRecPara, ReArOdfParaHeader, ReArOdfHeader) as TrHeader, 
       	ReArBitLevel,
		RecTournament.*
	from RecBroken
	inner join RecAreas on ReArCode=RecBroRecCode
	inner join RecTournament on RtTournament=RecBroTournament and RtRecCode=RecBroRecCode and RtRecCategory=RecBroRecCategory and RtRecTeam=RecBroRecTeam and RtRecPara=RecBroRecPara and RtRecPhase=RecBroRecPhase and RtRecSubphase=RecBroRecSubPhase
	inner join TeamFinals on TfTournament=RecBroTournament and TfTeam=RecBroTeam and TfSubTeam=RecBroSubTeam and TfEvent=RecBroRecEvent and TfMatchNo=RecBroRecMatchno
	inner join TeamFinComponent on TfcTournament=TfTournament and TfcCoId=TfTeam and TfcSubTeam=TfSubTeam and TfcEvent=TfEvent
	inner join Entries on EnTournament=RecBroTournament and EnId=TfcId
	inner join Countries on CoTournament=TfTournament and CoId=TfTeam
	inner join Events on EvTournament=RecBroTournament and EvCode=RecBroRecEvent and EvTeamEvent=1
	where RecBroTournament={$_SESSION['TourId']} and RecBroRecTeam=1 and RecBroRecPhase=3
	group by RecBroRecCode, RecBroTeam, RecBroSubTeam, RecBroRecCategory, RecBroRecMatchno";

	return "(".implode(') UNION (', $SQL).") order by ReArBitLevel desc, TeamEvent, OrderBy, Phase, SubPhase, RtRecDistance desc, NewRecord desc";
}

function getStartListAlphaQuery($ORIS=false) {
	$TmpWhere="";
	if(isset($_REQUEST["ArcherName"]) && preg_match("/^[-,0-9A-Z]*$/i",str_replace(" ","",$_REQUEST["ArcherName"]))) {
		foreach(explode(",",$_REQUEST["ArcherName"]) as $Value) {
			$Tmp=NULL;
			if(preg_match("/^([0-9A-Z]*)\-([0-9A-Z]*)$/i",str_replace(" ","",$Value),$Tmp)) {
				$TmpWhere .= "(EnFirstName >= " . StrSafe_DB(stripslashes($Tmp[1]) ) . " AND EnFirstName <= " . StrSafe_DB(stripslashes($Tmp[2].chr(255))) . ") OR ";
			} else {
				$TmpWhere .= "EnFirstName LIKE " . StrSafe_DB(stripslashes(trim($Value)) . "%") . " OR ";
			}
		}
		$TmpWhere = substr($TmpWhere,0,-3);
	}

	$Collation = ($_SESSION['TourCollation'] ? "COLLATE utf8_{$_SESSION['TourCollation']}_ci" : '');

	$MyQuery = "SELECT distinct 
			upper(substr(EnFirstname $Collation,1,1)) as FirstLetter, 
			SesName, 
			EnCode as Bib, 
			concat(upper(EnFirstName $Collation), ' ', EnName $Collation) AS Athlete, 
			QuSession AS Session, 
			SUBSTRING(QuTargetNo,2) AS TargetNo, 
			QuTarget AS TargetButt, 
			upper(c.CoCode) AS NationCode, upper(c.CoName) AS Nation, 
			upper(c2.CoCode) AS NationCode2, upper(c2.CoName) AS Nation2, 
			upper(c3.CoCode) AS NationCode3, upper(c3.CoName) AS Nation3, 
			DivDescription, ClDescription, 
			EnSubTeam, EnClass AS ClassCode, EnDivision AS DivCode, 
			DivAthlete and ClAthlete as IsAthlete, 
			EnAgeClass as AgeClass, 
			EnSubClass as SubClass, 
			EnStatus as Status, 
			EnIndClEvent AS `IC`, 
			EnTeamClEvent AS `TC`, 
			EnIndFEvent AS `IF`, 
			EnTeamFEvent as `TF`, 
			EnTeamMixEvent as `TM`, 
			EvCode, 
			IFNULL(EvCode,CONCAT(TRIM(EnDivision),TRIM(EnClass))) as EventCode, 
			DATE_FORMAT(EnDob,'%d %b %Y') as DOB, 
			IFNULL(GROUP_CONCAT(EvEventName order by EvProgr SEPARATOR ', '), if(DivAthlete and ClAthlete, CONCAT('|',DivDescription, '| |', ClDescription), ClDescription)) as EventName , 
			IFNULL(GROUP_CONCAT(RankRanking order by EvProgr SEPARATOR ', '), '') as Ranking , 
			TfName, 
			concat(DvMajVersion, '.', DvMinVersion) as DocVersion, 
			date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate, 
			DvNotes as DocNotes,
			'' as Location,
			DiDescription 
		FROM Entries AS e 
		inner join Tournament on ToId=EnTournament 
		LEFT JOIN DocumentVersions on EnTournament=DvTournament AND DvFile = 'EN' 
		LEFT JOIN Qualifications AS q ON e.EnId=q.QuId 
		left join (select TdTournament, TdClasses, Di1.DiSession, trim('|' from concat(
				if(Td1!='-' and Di1.DiStart>0, concat(Td1, ': ', left(Di1.DiStart, 5)), '')
				, '|', if(Td2!='-' and Di2.DiStart>0, concat(Td2, ': ', left(Di2.DiStart, 5)), '')
				, '|', if(Td3!='-' and Di3.DiStart>0, concat(Td3, ': ', left(Di3.DiStart, 5)), '')
				, '|', if(Td4!='-' and Di4.DiStart>0, concat(Td4, ': ', left(Di4.DiStart, 5)), '')
				, '|', if(Td5!='-' and Di5.DiStart>0, concat(Td5, ': ', left(Di5.DiStart, 5)), '')
				, '|', if(Td6!='-' and Di6.DiStart>0, concat(Td6, ': ', left(Di6.DiStart, 5)), '')
				, '|', if(Td7!='-' and Di7.DiStart>0, concat(Td7, ': ', left(Di7.DiStart, 5)), '')
				, '|', if(Td8!='-' and Di8.DiStart>0, concat(Td8, ': ', left(Di8.DiStart, 5)), '')
				)) as DiDescription from TournamentDistances 
			left join DistanceInformation Di1 on Di1.DiTournament=TdTournament and Di1.DiDistance=1
			left join DistanceInformation Di2 on Di2.DiTournament=TdTournament and Di2.DiDistance=2 and Di2.DiSession=Di1.DiSession
			left join DistanceInformation Di3 on Di3.DiTournament=TdTournament and Di3.DiDistance=3 and Di3.DiSession=Di1.DiSession
			left join DistanceInformation Di4 on Di4.DiTournament=TdTournament and Di4.DiDistance=4 and Di4.DiSession=Di1.DiSession
			left join DistanceInformation Di5 on Di5.DiTournament=TdTournament and Di5.DiDistance=5 and Di5.DiSession=Di1.DiSession
			left join DistanceInformation Di6 on Di6.DiTournament=TdTournament and Di6.DiDistance=6 and Di6.DiSession=Di1.DiSession
			left join DistanceInformation Di7 on Di7.DiTournament=TdTournament and Di7.DiDistance=7 and Di7.DiSession=Di1.DiSession
			left join DistanceInformation Di8 on Di8.DiTournament=TdTournament and Di1.DiDistance=8 and Di8.DiSession=Di1.DiSession
			group by TdTournament, TdClasses, Di1.DiSession) Distances on TdTournament=EnTournament and DiSession=QuSession and concat(EnDivision, EnClass) like TdClasses
		LEFT JOIN Individuals on IndId=EnId AND EnTournament=IndTournament 
		left join Events on EvCode=IndEvent and EvTournament=EnTournament and EvTeamEvent=0 and EvCodeParent=''
		LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament 
		LEFT JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament 
		LEFT JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament 
		LEFT JOIN Countries AS c2 ON e.EnCountry2=c2.CoId AND e.EnTournament=c2.CoTournament 
		LEFT JOIN Countries AS c3 ON e.EnCountry3=c3.CoId AND e.EnTournament=c3.CoTournament 
		LEFT JOIN Session on EnTournament=SesTournament and SesType='Q' and SesOrder=QuSession 
		LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId 
		LEFT JOIN Rankings on EnTournament=RankTournament and RankEvent=IF(EvWaCategory!='',EvWaCategory,EvCode) and RankTeam=0 and EnCode=RankCode and ToIocCode='FITA' and EnIocCode in ('', 'FITA') and RankIocCode='FITA' 
		WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) ;
	if(isset($_REQUEST["Session"]) and is_numeric($_REQUEST["Session"])) $MyQuery .= " AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) ;
	if(!empty($_REQUEST["Divisions"])) $MyQuery .= " AND concat(EnDivision, EnClass) like '{$_REQUEST["Divisions"]}'";
	if($TmpWhere) $MyQuery .= " AND (" . $TmpWhere . ")";
	$MyQuery.= " GROUP BY FirstLetter, SesName, Bib, Athlete, Session, TargetNo, NationCode, Nation, NationCode2, Nation2, NationCode3, Nation3,
		DivDescription, ClDescription, EnSubTeam, ClassCode, DivCode, IsAthlete, AgeClass, SubClass, Status, `IC`, `TC`, `IF`, `TF`, `TM`,
		DOB, TfName ";
	$MyQuery.= " ORDER BY Athlete, TargetNo ";

	return $MyQuery;
}

function getStartListCategoryQuery($ORIS=false, $orderByTeam=0, $Events=array()) {
	$TmpWhere="";
	if(isset($_REQUEST["ArcherCategories"]) && preg_match("/^[,0-9A-Z]*$/i",str_replace(" ","",$_REQUEST["ArcherCategories"]))) {
		foreach(explode(",",$_REQUEST["ArcherCategories"]) as $Value) {
			$TmpWhere .= "CONCAT(EnDivision,EnClass) LIKE " . StrSafe_DB(stripslashes(trim($Value)) . "%") . " OR ";
		}
		$TmpWhere = substr($TmpWhere,0,-3);
	}
	if($Events) {
		$TmpWhere=" EvCode in (".implode(',', StrSafe_DB($Events)).") ";
	}

	$Collation = ($_SESSION['TourCollation'] ? "COLLATE utf8_{$_SESSION['TourCollation']}_ci" : '');

	$MyQuery = "SELECT distinct
			" . ($ORIS ? ' EvCode as EventCode ' :" IFNULL(EvCode,CONCAT(TRIM(EnDivision),TRIM(EnClass))) as EventCode") . ", 
			SesName, 
			EnCode as Bib, 
			concat(upper(EnFirstName $Collation), ' ', EnName $Collation) AS Athlete, 
			QuSession AS Session, 
			SUBSTRING(QuTargetNo,2) AS TargetNo, 
			upper(c.CoCode) AS NationCode, 
			upper(c.CoName) AS Nation, 
			upper(c2.CoCode) AS NationCode2, 
			upper(c2.CoName) AS Nation2, 
			upper(c3.CoCode) AS NationCode3, 
			upper(c3.CoName) AS Nation3, 
			DivDescription, 
			ClDescription, 
			EnSubTeam, 
			EnClass AS ClassCode, 
			EnDivision AS DivCode, 
			DivAthlete and ClAthlete as IsAthlete, 
			EnAgeClass as AgeClass, 
			EnSubClass as SubClass, 
			EnStatus as Status, 
			EnIndClEvent AS `IC`, 
			EnTeamClEvent AS `TC`, 
			EnIndFEvent AS `IF`, 
			EnTeamFEvent as `TF`, 
			EnTeamMixEvent as `TM`, 
			EvCode, 
			DATE_FORMAT(EnDob,'%d %b %Y') as DOB, 
			IFNULL(GROUP_CONCAT(EvEventName order by EvProgr SEPARATOR ', '),CONCAT('|',DivDescription, '| |', ClDescription)) as EventName , 
			TfName, 
			cNumber, 
			ifnull(GROUP_CONCAT(RankRanking order by EvProgr SEPARATOR ', '), '') as Ranking
		FROM Entries AS e
		INNER JOIN Tournament on EnTournament=ToId
		INNER JOIN Qualifications AS q ON e.EnId=q.QuId 
		LEFT JOIN Individuals on IndId=EnId AND EnTournament=IndTournament 
		LEFT JOIN Events AS ec ON EvTeamEvent=0 AND EvTournament=EnTournament AND IndEvent=EvCode and EvCodeParent=''
		LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament 
		LEFT JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament 
		LEFT JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament 
		LEFT JOIN Countries AS c2 ON e.EnCountry2=c2.CoId AND e.EnTournament=c2.CoTournament 
		LEFT JOIN Countries AS c3 ON e.EnCountry3=c3.CoId AND e.EnTournament=c3.CoTournament
		LEFT JOIN ( 
			SELECT EnCountry AS cCode, COUNT(EnId) AS cNumber, IndEvent as cEvent FROM `Entries` inner join Individuals on EnId=IndId and IndTournament={$_SESSION['TourId']} 
			WHERE EnTournament={$_SESSION['TourId']} GROUP BY EnCountry, IndEvent 
			) as sqy ON e.EnCountry=sqy.cCode and IndEvent=cEvent 
		LEFT JOIN Session on EnTournament=SesTournament and SesType='Q' and SesOrder=QuSession 
		LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId
		LEFT JOIN Rankings on EnTournament=RankTournament and RankEvent=IF(EvWaCategory!='',EvWaCategory,EvCode) and RankTeam=0 and EnCode=RankCode and ToIocCode='FITA' and EnIocCode in ('', 'FITA') and RankIocCode='FITA' 
		WHERE ".($ORIS ? 'EvCode is not null and ' : '')." EnAthlete=1 and EnTournament = " . StrSafe_DB($_SESSION['TourId']) ;
	if(isset($_REQUEST["Session"]) and is_numeric($_REQUEST["Session"])) $MyQuery .= " AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) ;
	if($TmpWhere) $MyQuery .= " AND (" . $TmpWhere . ")";
	$MyQuery.= " GROUP BY EventCode, SesName, Bib, Athlete, Session, TargetNo, NationCode, Nation, NationCode2, Nation2, NationCode3, Nation3,
		DivDescription, ClDescription, EnSubTeam, ClassCode, DivCode, IsAthlete, AgeClass, SubClass, Status, `IC`, `TC`, `IF`, `TF`, `TM`,
			DOB, TfName ";
	if(empty($_REQUEST['byTarget'])) {
		$MyQuery.= " ORDER BY ".($ORIS ? 'EvProgr, NationCode' : 'EventCode').", " . ($orderByTeam ? ($orderByTeam==1 ? " NationCode, ":"Nation, "):"") . " Athlete, TargetNo ";
	} else {
		$MyQuery.= " ORDER BY EvProgr, TargetNo ";
	}

	return $MyQuery;
}
?>
