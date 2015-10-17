<?php
function getStatEntriesByEventQuery($Type='QR') {
	switch($Type) {
		case 'OR':
			$Sql="Select count(*) Quanti, count(distinct EnCountry) Countries, EvCode, EvEventName, EvTeamEvent, EvProgr
				from Entries
				inner join EventCategories on EnDivision=EcDivision and EnClass=EcClass and EcTournament={$_SESSION['TourId']} and EcTeamEvent=0
				where EnIndFEvent=1 and EnTournament={$_SESSION['TourId']}
				group by EvCode
				ORDER BY EvProgr";
			break;
		case 'IF':
			$Sql = "SELECT EvCode as Code, EvEventName as EventName, EvFinalFirstPhase as FirstPhase, COUNT(EnId) as Quanti, count(distinct EnCountry) Countries
				FROM Events
				INNER JOIN EventClass ON EvCode=EcCode AND EvTeamEvent=EcTeamEvent AND EvTournament=EcTournament
				INNER JOIN Individuals ON EvCode=IndEvent AND EvTournament=IndTournament
				INNER JOIN Entries ON EnId=IndId AND EnTournament=IndTournament AND EcClass=EnClass AND EcDivision=EnDivision
				WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0 AND ((EnIndFEvent=1 AND EnStatus<=1) OR EnId IS NULL)
				GROUP BY EvCode, EvFinalFirstPhase
				ORDER BY EvProgr";
			break;
		case 'TF':
			$Sql = "SELECT EvCode, EvEventName as EventName, EvFinalFirstPhase as FirstPhase, EvMixedTeam, EvMultiTeam, EvMaxTeamPerson,EvTeamCreationMode
				FROM Events
				WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1
				ORDER BY EvProgr";
			break;
		default:
			$Sql = "SELECT EnDivision as Divisione, EnClass as Classe, SUM(EnIndClEvent) as QuantiInd, IFNULL(numTeam,0) AS QuantiSq
				FROM Entries
				inner join Divisions on EnDivision=DivId and DivAthlete=1 and DivTournament=" . StrSafe_DB($_SESSION['TourId']) . "
				inner join Classes on EnClass=ClId and ClAthlete=1 and ClTournament=" . StrSafe_DB($_SESSION['TourId']) . "
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
	$Sql = "SELECT EvCode as Code, EvEventName as EventName, EvFinalFirstPhase as FirstPhase, COUNT(EnId) as Quanti
		FROM Events
		INNER JOIN EventClass ON EvCode=EcCode AND EvTeamEvent=EcTeamEvent AND EvTournament=EcTournament
		INNER JOIN Individuals ON EvCode=IndEvent AND EvTournament=IndTournament
		INNER JOIN Entries ON EnId=IndId AND EnTournament=IndTournament AND EcClass=EnClass AND EcDivision=EnDivision
		WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0 AND ((EnIndFEvent=1 AND EnStatus<=1) OR EnId IS NULL)
		GROUP BY EvCode, EvFinalFirstPhase
		ORDER BY EvProgr";
	return $Sql;
}

function getStatEntriesByCountriesQuery($ORIS=false, $Athletes=false) {
	$Sql="";
	if($ORIS) {
		$Sql = "SELECT SUM(IF((DivAthlete AND ClAthlete AND EnSex=0), 1,0)) as M, SUM(IF((DivAthlete AND ClAthlete AND EnSex=1), 1,0)) as W, SUM(IF((DivAthlete AND ClAthlete), 0,1)) as Of, ";
		$Sql .= "CoCode as NationCode, CoName as NationName ";
		$Sql .= "FROM Entries ";
		$Sql .= "INNER JOIN Countries ON EnCountry = CoId ";
		$Sql .= "LEFT JOIN Divisions ON EnDivision=DivId AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		$Sql .= "LEFT JOIN Classes ON EnClass=ClId AND ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		$Sql .= "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";
		$Sql .= "GROUP BY CoCode ";
		$Sql .= "ORDER BY CoCode ";
	} else {
		$Sql = "SELECT DISTINCT CONCAT(TRIM(EnDivision),'|',TRIM(EnClass)) as Id, (DivAthlete AND ClAthlete) as isAthlete "
			. "FROM Entries "
			. "LEFT JOIN Divisions ON EnDivision=DivId AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "LEFT JOIN Classes ON EnClass=ClId AND ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " "
			. ($Athletes ? 'AND DivAthlete=1 AND ClAthlete=1 ' : '')
				. "ORDER BY LENGTH(EnDivision) DESC, DivViewOrder, EnDivision, LENGTH(EnClass) DESC, ClViewOrder, EnClass";
		$Rs = safe_r_sql($Sql);

		$Sql = "SELECT ";
		if(safe_num_rows($Rs)>0) {
			while($MyRow=safe_fetch($Rs))
				$Sql .= "SUM(IF(CONCAT(TRIM(EnDivision),'|',TRIM(EnClass))='" . $MyRow->Id . "',1,0)) as `" . $MyRow->Id . "`, ";
			safe_free_result($Rs);
		}
		$Sql .= "CoCode as NationCode, CoName as NationName ";
		$Sql .= "FROM Entries ";
		$Sql .= "INNER JOIN Countries ON EnCountry = CoId ";
		$Sql .= "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";
		$Sql .= "GROUP BY CoCode ";
		$Sql .= "ORDER BY CoCode ";
	}
	return $Sql;
}


function getStartListQuery($ORIS=false, $Event='', $Elim=false) {
	global $CFG;

	if(file_exists($f=$CFG->DOCUMENT_PATH.'Modules/Sets/'.$_SESSION['TourLocRule'].'/func/getStartListQuery.php')) {
		include_once($f);
		$func='getStartListQuery_'.$_SESSION['TourLocRule'];
		return $func($ORIS, $Event, $Elim);
	}

	if($Elim) {
		$MyQuery = "SELECT
			SesName,
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
			EvEventName as EventName,
			upper(DATE_FORMAT(EnDob,'%d %b %Y')) as DOB,
			ElSession
			FROM Eliminations
			INNER JOIN Entries ON ElId=EnId
			INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
			INNER JOIN Events ON ElEventCode=EvCode AND EvTeamEvent=0 AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (EvElim1>0 OR EvElim2>0)
			LEFT JOIN Session ON ElSession=SesOrder AND ElTournament=SesTournament AND SesType='E'
			WHERE
				EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		if (isset($_REQUEST['Elim']) && is_numeric($_REQUEST['Elim'])) {
			$MyQuery.="AND ElElimPhase=" . StrSafe_DB($_REQUEST['Elim']) . " ";
		} elseif ($Event) {
			$MyQuery.="AND ElElimPhase=" . ($Event-1) . " ";
		}

		$MyQuery .= "ORDER BY ElSession ASC, ElElimPhase ASC, EvProgr, EventName, ElTargetNo, Name, FirstName ";


// 		$MyQuery = "SELECT  EvEventName ";

	} else {
		$MyQuery = "SELECT distinct "
			. " SesName, "
			. " EvCode, "
			. " DivDescription, "
			. " ClDescription, "
			. " Bib, "
			. " Athlete,"
			. " SUBSTRING(AtTargetNo,1,1) AS Session,"
			. " SUBSTRING(AtTargetNo,2) AS TargetNo,"
			. " NationCode,"
			. " Nation,"
			. " EventCode, "
			. " EventName,"
			. " DOB, "
			. " SesAth4Target,"
			. " ClassCode,"
			. " DivCode,"
			. " AgeClass,"
			. " SubClass,"
			. " Status,"
			. " `IC`,"
			. " `TC`,"
			. " `IF`,"
			. " `TF`,"
			. " `TM`,"
			. " NationCode2,"
			. " Nation2,"
			. " NationCode3,"
			. " Nation3,"
			. " EnSubTeam, "
			. " TfName ";
		$MyQuery.= "FROM AvailableTarget at ";
		$MyQuery.= "INNER JOIN Session ON SUBSTRING(AtTargetNo,1,1)=SesOrder AND AtTournament=SesTournament AND SesType='Q' ";
		$MyQuery.= "LEFT JOIN ";
		$MyQuery.= "(";
		$MyQuery.= "SELECT ".($ORIS ? "EvCode," : "'' EvCode,")
			. ($ORIS ? " EvProgr," : " '' EvProgr,")
			. " DivDescription,"
			. " ClDescription,"
			. ($ORIS ? " IFNULL(EvCode,CONCAT(TRIM(EnDivision),TRIM(EnClass))) as EventCode," : " '' as EventCode,")
			. " concat(upper(EnFirstName), ' ', EnName) Athlete,"
			. " EnCode as Bib,"
			. " QuTargetNo,"
			. " upper(c.CoCode) AS NationCode,"
			. " upper(c.CoName) AS Nation,"
			. " upper(c2.CoCode) NationCode2,"
			. " upper(c2.CoName) Nation2,"
			. " upper(c3.CoCode) NationCode3,"
			. " upper(c3.CoName) Nation3,"
			. ($ORIS ? " IFNULL(EvEventName,CONCAT('|',DivDescription, '| |', ClDescription)) as EventName," : " '' as EventName,")
			. " EnSubTeam,"
			. " EnClass AS ClassCode,"
			. " EnDivision AS DivCode,"
			. " EnAgeClass as AgeClass,"
			. " EnSubClass as SubClass,"
			. " EnStatus as Status,"
			. " EnIndClEvent AS `IC`,"
			. " EnTeamClEvent AS `TC`,"
			. " EnIndFEvent AS `IF`,"
			. " EnTeamFEvent as `TF`,"
			. " EnTeamMixEvent as `TM`,"
			. " DATE_FORMAT(EnDob,'%d %b %Y') as DOB, "
			. " TfName ";
		$MyQuery.= "FROM Qualifications AS q  ";
		$MyQuery.= "INNER JOIN Entries AS e ON q.QuId=e.EnId AND e.EnTournament= " . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1 ";
		$MyQuery.= "INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament ";
		$MyQuery.= "LEFT JOIN Countries AS c2 ON e.EnCountry2=c2.CoId AND e.EnTournament=c2.CoTournament ";
		$MyQuery.= "LEFT JOIN Countries AS c3 ON e.EnCountry3=c3.CoId AND e.EnTournament=c3.CoTournament ";
		$MyQuery.= "LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament ";
		$MyQuery.= "LEFT JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament ";
		$MyQuery.= "LEFT JOIN EventCategories AS ec ON ec.EcTeamEvent=0 AND e.EnTournament=ec.EcTournament AND e.EnClass=ec.EcClass AND e.EnDivision=ec.EcDivision ";
		$MyQuery.= "LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId ";
		$MyQuery.= ") as Sq ON at.AtTargetNo=Sq.QuTargetNo ";
		$MyQuery.= "WHERE AtTournament = " . StrSafe_DB($_SESSION['TourId']) ;
		if(isset($_REQUEST["Session"]) && $_REQUEST["Session"]!='All')
			$MyQuery .= "AND SUBSTRING(AtTargetNo,1,1) = " . StrSafe_DB($_REQUEST["Session"]) . " ";
		if(isset($_REQUEST["x_Session"]) )
			$MyQuery .= "AND SUBSTRING(AtTargetNo,1,1) = " . StrSafe_DB($_REQUEST["x_Session"]) . " ";
		if(isset($_REQUEST["x_From"]) and isset($_REQUEST["x_To"]) ) {
			$MyQuery .= "AND SUBSTRING(AtTargetNo,2,3) >= " . StrSafe_DB(sprintf('%03d', intval($_REQUEST["x_From"]))) . " ";
			$MyQuery .= "AND SUBSTRING(AtTargetNo,2,3) <= " . StrSafe_DB(sprintf('%03d', intval($_REQUEST["x_To"]))) . " ";

		}
		if($ORIS) {
			$MyQuery.= " AND EventName!='' ";
			$MyQuery.= "ORDER BY EvProgr, EventName, AtTargetNo, Athlete ";
		} else   {
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

	$MyQuery = "SELECT DISTINCT "
	. "upper(CoCode) AS NationCode, upper(CoName) AS Nation ";
	$MyQuery.= "FROM Entries AS e ";
	$MyQuery.= "INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament ";
	$MyQuery.= "INNER JOIN Qualifications AS q ON e.EnId=q.QuId ";

	$MyQuery.= "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " "; // 2010-03-16 totlo EnAthlete=1 AND
	if(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"]))
	$MyQuery .= "AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) . " ";
	if($TmpWhere != "")
	$MyQuery .= "AND (" . $TmpWhere . ")";
	$MyQuery.= "ORDER BY CoCode";

	return $MyQuery;
}


function getStartListCountryQuery($ORIS=false, $Athletes=false, $orderByName=false) {
	$SinglePage = isset($_REQUEST['SinglePage']);
	$TargetFace=(isset($_REQUEST['tf']) && $_REQUEST['tf']==1);
	$NoPhoto=isset($_REQUEST['NoPhoto']);

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
		$MyQuery = "SELECT distinct "
			. " SesName"
			. ", EvCode"
			. ", DivDescription"
			. ", ClDescription"
			. ", DivAthlete and ClAthlete as IsAthlete"
			. ", IFNULL(EvCode,CONCAT(TRIM(EnDivision),TRIM(EnClass))) as EventCode"
			. ", EnCode as Bib"
			. ", concat(upper(EnFirstName), ' ', EnName) AS Athlete"
			. ", DATE_FORMAT(EnDob,'%d %b %Y') as DOB"
			. ", QuSession AS Session"
			. ", SUBSTRING(QuTargetNo,2) AS TargetNo"
			. ", upper(CoCode) AS NationCode"
			. ", upper(CoName) AS Nation"
			. ", IFNULL(GROUP_CONCAT(EvEventName SEPARATOR ', '),CONCAT('|',DivDescription, '| |', ClDescription)) as EventName"
			. ", cNumber ";
		$MyQuery.= "FROM Entries AS e ";
		$MyQuery.= "INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament ";
		$MyQuery.= "INNER JOIN Qualifications AS q ON e.EnId=q.QuId ";
		$MyQuery.= "INNER JOIN ( ";
		$MyQuery.= "SELECT EnCountry AS cCode, COUNT(EnId) AS cNumber FROM `Entries` ";
		$MyQuery.= "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " GROUP BY EnCountry ";
		$MyQuery.= ") as sqy ON e.EnCountry=sqy.cCode ";
		$MyQuery.= "LEFT JOIN Individuals on IndId=EnId AND EnTournament=IndTournament ";
		$MyQuery.= "left JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament ";
		$MyQuery.= "left JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament ";
		$MyQuery.= "LEFT JOIN Session on EnTournament=SesTournament and SesType='Q' and SesOrder=QuSession ";
		$MyQuery.= "LEFT JOIN EventCategories AS ec ON ec.EcTeamEvent=0 AND IndTournament=ec.EcTournament AND IndEvent=EcCode and EcClass=EnClass and EcDivision=EnDivision ";

		$MyQuery.= "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " "; // 2010-03-16 totlo EnAthlete=1 AND
		if($Athletes) $MyQuery.= " AND EnAthlete=1 ";
		if(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"]))
			$MyQuery .= "AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) . " ";
		if($TmpWhere != "")
			$MyQuery .= "AND (" . $TmpWhere . ")";
		$MyQuery.= "GROUP BY SesName, DivDescription, ClDescription, IsAthlete, Bib, Athlete, DOB, Session, TargetNo, NationCode, Nation ";
		$MyQuery.= "ORDER BY CoCode, ".($Athletes ? 'DivViewOrder, ClViewOrder, ' : '' )."Athlete, TargetNo ";
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
			. ", EnSubTeam"
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
			. ", IF(EnCountry2=0,0,1) as secTeam "
			. ", TfName ";
	$MyQuery.= "FROM Entries AS e ";
	$MyQuery.= "LEFT JOIN Photos ON e.EnId=PhEnId ";
	$MyQuery.= "LEFT JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament ";
	$MyQuery.= "LEFT JOIN Qualifications AS q ON e.EnId=q.QuId ";
	$MyQuery.= "LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament ";
	$MyQuery.= "LEFT JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament ";
	$MyQuery.= "LEFT JOIN Session on EnTournament=SesTournament and SesType='Q' and SesOrder=QuSession ";
	$MyQuery.= "LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId ";
	$MyQuery.= "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";
	if(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"]))
		$MyQuery .= "AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) . " ";
	if($TmpWhere != "")
		$MyQuery .= "AND (" . $TmpWhere . ")";
	if($NoPhoto) $MyQuery .= "AND (length(PhPhoto)='' or PhPhoto is null) ";
	$MyQuery .= ") UNION ALL ";
	$MyQuery .= "(SELECT"
			. " EnCode as Bib"
			. ", concat(upper(EnFirstName), ' ', EnName) AS Athlete"
			. ", QuSession AS Session"
			. ", SesName"
			. ", SUBSTRING(QuTargetNo,2) AS TargetNo"
			. ", upper(CoCode) AS NationCode"
			. ", upper(CoName) AS Nation"
			. ", EnSubTeam"
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
			. ", 2 as secTeam  "
			. ", TfName ";
	$MyQuery.= "FROM Entries AS e ";
	$MyQuery.= "LEFT JOIN Photos ON e.EnId=PhEnId ";
	$MyQuery.= "LEFT JOIN Countries AS c ON e.EnCountry2=c.CoId AND e.EnTournament=c.CoTournament ";
	$MyQuery.= "LEFT JOIN Qualifications AS q ON e.EnId=q.QuId ";
	$MyQuery.= "LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament ";
	$MyQuery.= "LEFT JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament ";
	$MyQuery.= "LEFT JOIN Session on EnTournament=SesTournament and SesType='Q' and SesOrder=QuSession ";
	$MyQuery.= "LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId ";
	$MyQuery.= "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EnCountry2!=0 AND (EnTeamClEvent!=0 OR EnTeamFEvent!=0 OR EnTeamMixEvent!=0) ";
	if(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"]))
		$MyQuery .= "AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) . " ";
	if($TmpWhere != "")
		$MyQuery .= "AND (" . $TmpWhere . ")";
	if($NoPhoto) $MyQuery .= "AND (length(PhPhoto)='' or PhPhoto is null) ";
	$MyQuery.= ") UNION ALL ";

	$MyQuery .= "(SELECT"
			. " EnCode as Bib"
			. ", concat(upper(EnFirstName), ' ', EnName) AS Athlete"
			. ", QuSession AS Session"
			. ", SesName"
			. ", SUBSTRING(QuTargetNo,2) AS TargetNo"
			. ", upper(CoCode) AS NationCode"
			. ", upper(CoName) AS Nation"
			. ", EnSubTeam"
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
			. ", 3 as secTeam  "
			. ", TfName ";
	$MyQuery.= "FROM Entries AS e ";
	$MyQuery.= "LEFT JOIN Photos ON e.EnId=PhEnId ";
	$MyQuery.= "LEFT JOIN Countries AS c ON e.EnCountry3=c.CoId AND e.EnTournament=c.CoTournament ";
	$MyQuery.= "LEFT JOIN Qualifications AS q ON e.EnId=q.QuId ";
	$MyQuery.= "LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament ";
	$MyQuery.= "LEFT JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament ";
	$MyQuery.= "LEFT JOIN Session on EnTournament=SesTournament and SesType='Q' and SesOrder=QuSession ";
	$MyQuery.= "LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId ";
	$MyQuery.= "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EnCountry3!=0 ";
	if(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"]))
		$MyQuery .= "AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) . " ";
	if($TmpWhere != "")
		$MyQuery .= "AND (" . $TmpWhere . ")";
	if($NoPhoto) $MyQuery .= "AND (length(PhPhoto)='' or PhPhoto is null) ";

	$MyQuery.= ") ORDER BY " . ($orderByName ? "Nation" : "NationCode") . ", ".($SinglePage?'Session, ':'')." Athlete, TargetNo ";

	return $MyQuery;
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

	$MyQuery = "SELECT distinct"
		. " upper(substr(EnFirstname $Collation,1,1)) as FirstLetter"
		. ", SesName"
		. ", EnCode as Bib"
		. ", concat(upper(EnFirstName $Collation), ' ', EnName $Collation)  AS Athlete"
		. ", QuSession AS Session"
		. ", SUBSTRING(QuTargetNo,2) AS TargetNo"
		. ", upper(c.CoCode) AS NationCode"
		. ", upper(c.CoName) AS Nation"
		. ", upper(c2.CoCode) AS NationCode2"
		. ", upper(c2.CoName) AS Nation2"
		. ", upper(c3.CoCode) AS NationCode3"
		. ", upper(c3.CoName) AS Nation3"
		. ", DivDescription"
		. ", ClDescription"
		. ", EnSubTeam"
		. ", EnClass AS ClassCode"
		. ", EnDivision AS DivCode"
		. ", DivAthlete and ClAthlete as IsAthlete"
		. ", EnAgeClass as AgeClass"
		. ", EnSubClass as SubClass"
		. ", EnStatus as Status"
		. ", EnIndClEvent AS `IC`, EnTeamClEvent AS `TC`, EnIndFEvent AS `IF`, EnTeamFEvent as `TF`, EnTeamMixEvent as `TM` "
		. ", EvCode"
		. ", IFNULL(EvCode,CONCAT(TRIM(EnDivision),TRIM(EnClass))) as EventCode"
		. ", DATE_FORMAT(EnDob,'%d %b %Y') as DOB"
		. ", IFNULL(GROUP_CONCAT(EvEventName SEPARATOR ', '),CONCAT('|',DivDescription, '| |', ClDescription)) as EventName "
		. ", TfName ";
	$MyQuery.= "FROM Entries AS e ";
	$MyQuery.= "LEFT JOIN Qualifications AS q ON e.EnId=q.QuId ";
	$MyQuery.= "LEFT JOIN Individuals on IndId=EnId AND EnTournament=IndTournament ";
	$MyQuery.= "LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament ";
	$MyQuery.= "LEFT JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament ";
	$MyQuery.= "LEFT JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament ";
	$MyQuery.= "LEFT JOIN Countries AS c2 ON e.EnCountry2=c2.CoId AND e.EnTournament=c2.CoTournament ";
	$MyQuery.= "LEFT JOIN Countries AS c3 ON e.EnCountry3=c3.CoId AND e.EnTournament=c3.CoTournament ";
	$MyQuery.= "LEFT JOIN Session on EnTournament=SesTournament and SesType='Q' and SesOrder=QuSession ";
	$MyQuery.= "LEFT JOIN EventCategories AS ec ON ec.EcTeamEvent=0 AND EnTournament=ec.EcTournament AND IndEvent=EcCode and EcClass=EnClass and EcDivision=EnDivision ";
	$MyQuery.= "LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId ";
	$MyQuery.= "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) ;
	if(isset($_REQUEST["Session"]) and is_numeric($_REQUEST["Session"])) $MyQuery .= " AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) ;
	if(!empty($_REQUEST["Divisions"])) $MyQuery .= " AND concat(EnDivision, EnClass) like '{$_REQUEST["Divisions"]}'";
	if($TmpWhere) $MyQuery .= " AND (" . $TmpWhere . ")";
	$MyQuery.= " GROUP BY FirstLetter, SesName, Bib, Athlete, Session, TargetNo, NationCode, Nation, NationCode2, Nation2, NationCode3, Nation3,
		DivDescription, ClDescription, EnSubTeam, ClassCode, DivCode, IsAthlete, AgeClass, SubClass, Status, `IC`, `TC`, `IF`, `TF`, `TM`,
		DOB, TfName ";
	$MyQuery.= " ORDER BY Athlete, TargetNo ";

	return $MyQuery;
}

function getStartListCategoryQuery($ORIS=false,$orderByTeam=0) {
	$TmpWhere="";
	if(isset($_REQUEST["ArcherCategories"]) && preg_match("/^[,0-9A-Z]*$/i",str_replace(" ","",$_REQUEST["ArcherCategories"]))) {
		foreach(explode(",",$_REQUEST["ArcherCategories"]) as $Value) {
			$TmpWhere .= "CONCAT(EnDivision,EnClass) LIKE " . StrSafe_DB(stripslashes(trim($Value)) . "%") . " OR ";
		}
		$TmpWhere = substr($TmpWhere,0,-3);
	}

	$Collation = ($_SESSION['TourCollation'] ? "COLLATE utf8_{$_SESSION['TourCollation']}_ci" : '');

	$MyQuery = "SELECT distinct"
			. " IFNULL(EvCode,CONCAT(TRIM(EnDivision),TRIM(EnClass))) as EventCode"
			. ", SesName"
			. ", EnCode as Bib"
			. ", concat(upper(EnFirstName $Collation), ' ', EnName $Collation)  AS Athlete"
			. ", QuSession AS Session"
		. ", SUBSTRING(QuTargetNo,2) AS TargetNo"
		. ", upper(c.CoCode) AS NationCode"
		. ", upper(c.CoName) AS Nation"
		. ", upper(c2.CoCode) AS NationCode2"
		. ", upper(c2.CoName) AS Nation2"
		. ", upper(c3.CoCode) AS NationCode3"
		. ", upper(c3.CoName) AS Nation3"
		. ", DivDescription"
		. ", ClDescription"
		. ", EnSubTeam"
		. ", EnClass AS ClassCode"
		. ", EnDivision AS DivCode"
		. ", DivAthlete and ClAthlete as IsAthlete"
		. ", EnAgeClass as AgeClass"
		. ", EnSubClass as SubClass"
		. ", EnStatus as Status"
		. ", EnIndClEvent AS `IC`, EnTeamClEvent AS `TC`, EnIndFEvent AS `IF`, EnTeamFEvent as `TF`, EnTeamMixEvent as `TM` "
		. ", EvCode"
		. ", DATE_FORMAT(EnDob,'%d %b %Y') as DOB"
		. ", IFNULL(GROUP_CONCAT(EvEventName SEPARATOR ', '),CONCAT('|',DivDescription, '| |', ClDescription)) as EventName "
		. ", TfName ";
		$MyQuery.= "FROM Entries AS e ";
		$MyQuery.= "LEFT JOIN Qualifications AS q ON e.EnId=q.QuId ";
		$MyQuery.= "LEFT JOIN Individuals on IndId=EnId AND EnTournament=IndTournament ";
		$MyQuery.= "LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament ";
		$MyQuery.= "LEFT JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament ";
		$MyQuery.= "LEFT JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament ";
		$MyQuery.= "LEFT JOIN Countries AS c2 ON e.EnCountry2=c2.CoId AND e.EnTournament=c2.CoTournament ";
		$MyQuery.= "LEFT JOIN Countries AS c3 ON e.EnCountry3=c3.CoId AND e.EnTournament=c3.CoTournament ";
	$MyQuery.= "LEFT JOIN Session on EnTournament=SesTournament and SesType='Q' and SesOrder=QuSession ";
	$MyQuery.= "LEFT JOIN EventCategories AS ec ON ec.EcTeamEvent=0 AND EnTournament=ec.EcTournament AND IndEvent=EcCode and EcClass=EnClass and EcDivision=EnDivision ";
	$MyQuery.= "LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId ";
	$MyQuery.= "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) ;
	if(isset($_REQUEST["Session"]) and is_numeric($_REQUEST["Session"])) $MyQuery .= " AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) ;
	if($TmpWhere) $MyQuery .= " AND (" . $TmpWhere . ")";
	$MyQuery.= " GROUP BY EventCode, SesName, Bib, Athlete, Session, TargetNo, NationCode, Nation, NationCode2, Nation2, NationCode3, Nation3,
		DivDescription, ClDescription, EnSubTeam, ClassCode, DivCode, IsAthlete, AgeClass, SubClass, Status, `IC`, `TC`, `IF`, `TF`, `TM`,
			DOB, TfName ";
	$MyQuery.= " ORDER BY EventCode, " . ($orderByTeam ? ($orderByTeam==1 ? " NationCode, ":"Nation, "):"") . " Athlete, TargetNo ";

	return $MyQuery;
}
?>