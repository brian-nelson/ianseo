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

$query = "SELECT DISTINCT EvCode AS code, EvEventName as name, EvTeamEvent as isTeam
	FROM Events
	WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) .
	" AND EvTeamEvent=0 and EvElim".($isEvent ? 2 : 1).">0
	ORDER BY EvTeamEvent, EvProgr, EvCode";

$rs=safe_r_sql($query);

if ($rs && safe_num_rows($rs)>0)
{
	while ($myRow=safe_fetch($rs))
	{
		$xml.='<event><code>' . $myRow->code . '@' . ($isEvent ? 2 : 1) . '</code><name>' . ($myRow->isTeam ? get_text('Team'):get_text('Individual')) . ": " . $myRow->name . '</name></event>' . "\n";
	}
}

header('Content-Type: text/xml');

print '<response>' . "\n";

print '<error>' . $error . '</error>' . "\n";

print $xml;

print '</response>' . "\n";
?>