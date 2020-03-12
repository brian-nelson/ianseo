<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

$isEvent = (isset($_REQUEST['isEvent']) && is_numeric($_REQUEST['isEvent']) ? $_REQUEST['isEvent'] : null);
$viewInd = (isset($_REQUEST['viewInd']) && is_numeric($_REQUEST['viewInd']) ? $_REQUEST['viewInd'] : null);
$viewTeam = (isset($_REQUEST['viewTeam']) && is_numeric($_REQUEST['viewTeam']) ? $_REQUEST['viewTeam'] : null);

if (empty($_SESSION['TourId']) && (!CheckTourSession() || is_null($isEvent) || is_null($viewInd) || is_null($viewTeam)))
	exit;

checkACL(AclSpeaker, AclReadOnly, false);

$xml='';
$error=0;

$query = "";
if($isEvent)
{
	$query = "SELECT DISTINCT EvCode AS code, EvEventName as name, EvTeamEvent as isTeam
		FROM Events
		WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) .
		($viewInd==$viewTeam ? "" :($viewInd? " AND EvTeamEvent=0 " : ($viewTeam ? " AND EvTeamEvent!=0" : "" ))) . "
		ORDER BY EvTeamEvent, EvProgr, EvCode";
}
else
{
	if($viewInd)
	{
		$query .= "SELECT DISTINCT 0 Team, CONCAT(ClId, '_', DivId) as code, CONCAT(DivDescription, ' - ', ClDescription) as name, 0 as isTeam, DivViewOrder, ClViewOrder
			FROM Entries
			INNER JOIN Qualifications ON EnId=QuId
			INNER JOIN Classes ON EnClass=ClId AND EnTournament=ClTournament AND ClAthlete=1
			INNER JOIN Divisions ON EnDivision=DivId AND EnTournament=DivTournament AND DivAthlete=1
			WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1 AND EnStatus<=1";
	}
	if($viewTeam)
	{
		if($query!="")
			$query .= " UNION ALL ";
		$query .= "SELECT DISTINCT 1, CONCAT(ClId, '_', DivId) as code, CONCAT(DivDescription, ' - ', ClDescription) as name, 1 as isTeam, DivViewOrder, ClViewOrder
			FROM Teams
			LEFT JOIN
			(
				SELECT CONCAT(DivId, ClId) DivClass, Divisions.*, Classes.*
				FROM Divisions
				INNER JOIN Classes ON DivTournament=ClTournament
				WHERE DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND DivAthlete AND ClAthlete
			) AS DivClass ON TeEvent=DivClass AND TeTournament=DivTournament
			WHERE TeTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TeFinEvent=0";
	}
	if($query!="")
		$query .= " ORDER BY DivViewOrder, ClViewOrder, Team";
}

$rs=safe_r_sql($query);

if ($rs && safe_num_rows($rs)>0)
{
	while ($myRow=safe_fetch($rs))
	{
		$xml.='<event><code>' . $myRow->isTeam . "_" . $myRow->code . '</code><name>' . ($myRow->isTeam ? get_text('Team'):get_text('Individual')) . ": " . $myRow->name . '</name></event>' . "\n";
	}
}

header('Content-Type: text/xml');

print '<response>' . "\n";

print '<error>' . $error . '</error>' . "\n";

print $xml;

print '</response>' . "\n";
?>