<?php
function getStartListQuery_NL($ORIS=false, $Event='') {
	$q="
		SELECT ToType,ToTypeSubRule FROM Tournament WHERE ToId={$_SESSION['TourId']}
	";
	$r=safe_r_sql($q);
	if ($r && safe_num_rows($r)==1)
	{
		$tmp=safe_fetch($r);

	// se il tipo non è 6 uso quella standard
		if ($tmp->ToType!=6)
		{
			return getStartListQuery($ORIS=false, $Event='');
		}

	// se il tipo è 6 e la sottoregola è championships uso questa se no la standard
		if ($tmp->ToTypeSubRule!='' && $tmp->ToTypeSubRule!='SetChampionship')
			return getStartListQuery($ORIS=false, $Event='');
	}

	global $F2FMatches;
	// calling Phase, the actual Phase has to be the second character (example F0, F1, F2, F3)
	$where=array();
	if ($Event!='') $where[] = "EvCode='{$Event}'";
	if (!empty($_REQUEST['Phase'])) $where[] = "f.F2FPhase='".substr($_REQUEST['Phase'], 1)."'";

	if($where) $where = ' where ' . implode(' AND ', $where);

	$query="
		SELECT
			e.*,
			f.F2FTarget AS TargetNo,
			f.F2FGroup AS Session,
			f.F2FRound AS Round,
			2 AS SesAth4Target,
			CONCAT('Poule ', f.F2FGroup) AS SesName,
			EvCode,
			EvProgr,
			DivDescription,
			ClDescription,
			IFNULL(EvCode,CONCAT(TRIM(EnDivision),TRIM(EnClass))) as EventCode,
			concat(upper(EnFirstName), ' ', EnName) Athlete,
			EnCode as Bib,

			upper(c.CoCode) AS NationCode,
			upper(c.CoName) AS Nation,
			upper(c2.CoCode) NationCode2,
			upper(c2.CoName) Nation2,
			upper(c3.CoCode) NationCode3,
			upper(c3.CoName) Nation3,
			IFNULL(EvEventName,CONCAT('|',DivDescription, '| |', ClDescription)) as EventName,
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
			F2FEntries.F2FPhase,
			F2FEntries.F2FMatchNo,
			F2FEntries.F2FGroup,
			F2FEntries.F2FEventCode
		FROM
			F2FEntries
			INNER JOIN
				F2FFinal AS f
			ON F2FEntries.F2FTournament=f.F2FTournament AND F2FEntries.F2FPhase=f.F2FPhase AND F2FEntries.F2FGroup=f.F2FGroup AND F2FEntries.F2FMatchNo=f.F2FMatchNo AND F2FEntries.F2FEnId=f.F2FEnId AND F2FEntries.F2FEventCode=f.F2FEvent ".($F2FMatches ? '' : ' AND f.F2FRound=1')."
			INNER JOIN Entries AS e ON F2FEntries.F2FEnId=EnId AND e.EnTournament= " . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1
			INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament
			LEFT JOIN Countries AS c2 ON e.EnCountry2=c2.CoId AND e.EnTournament=c2.CoTournament
			LEFT JOIN Countries AS c3 ON e.EnCountry3=c3.CoId AND e.EnTournament=c3.CoTournament
			LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament
			LEFT JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament
			left join Individuals on IndId=EnId and IndTournament=EnTournament
			left join Events on EvCode=IndEvent and EvTournament=EnTournament and EvTeamEvent=0
			LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId
		{$where}
		order by F2FEventCode, F2FPhase, F2FGroup, f.F2FRound, TargetNo
	";
		//print $query;exit;
	return $query;
}


function old_getStartListQuery_F2F($ORIS=false, $Event='') {
	$SubQuery="SELECT en.*, %1\$s TargetNo, g.F2FGroup Session, 2 SesAth4Target, concat('Poule ', g.F2FGroup) SesName FROM F2FGrid g INNER JOIN F2FTargetElim AS te	ON g.F2FPhase=te.F2FPhase AND g.F2FRound=te.F2FRound AND g.F2FMatchNo1=te.F2FMatchNo1 AND g.F2FMatchNo2=te.F2FMatchNo2 AND g.F2FGroup=te.F2FGroup
INNER JOIN (
	SELECT EvCode,
	EvProgr,
	DivDescription,
	ClDescription,
	IFNULL(EvCode,CONCAT(TRIM(EnDivision),TRIM(EnClass))) as EventCode,
	concat(upper(EnFirstName), ' ', EnName) Athlete,
	EnCode as Bib,

	upper(c.CoCode) AS NationCode,
	upper(c.CoName) AS Nation,
	upper(c2.CoCode) NationCode2,
	upper(c2.CoName) Nation2,
	upper(c3.CoCode) NationCode3,
	upper(c3.CoName) Nation3,
	IFNULL(EvEventName,CONCAT('|',DivDescription, '| |', ClDescription)) as EventName,
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
	DATE_FORMAT(EnDob,'%%d %%b %%Y') as DOB,
	TfName,
	F2FPhase,
	F2FMatchNo,
	F2FGroup,
	F2FEventCode
	FROM F2FEntries
	INNER JOIN Entries AS e ON F2FEnId=EnId AND e.EnTournament= " . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1
	INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament
	LEFT JOIN Countries AS c2 ON e.EnCountry2=c2.CoId AND e.EnTournament=c2.CoTournament
	LEFT JOIN Countries AS c3 ON e.EnCountry3=c3.CoId AND e.EnTournament=c3.CoTournament
	LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament
	LEFT JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament
	left join Individuals on IndId=EnId and IndTournament=EnTournament
	left join Events on EvCode=IndEvent and EvTeamEvent=0 and EvTournament=EnTournament
	LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId)
	 en on en.F2FPhase=g.F2FPhase and g.%2\$s=en.F2FMatchNo and g.F2FGroup=en.F2FGroup WHERE g.F2FRound=1";
	if($Event) $SubQuery.=" and EvCode='$Event'";

	$MyQuery = "(".sprintf($SubQuery, 'te.F2FTarget1', 'F2FMatchNo1').") UNION (".sprintf($SubQuery, 'te.F2FTarget2', 'F2FMatchNo2').") order by F2FEventCode, F2FPhase, F2FGroup, TargetNo";

	return $MyQuery;
}
