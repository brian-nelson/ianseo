<?php
/*							- ChangeEventPhase.php -
	Ritorna i matchno di una fase di un evento.
*/
define('debug',false);

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Fun_Final.local.inc.php');

$JSON=array('error'=>1);

if(!isset($_REQUEST['Ev']) or !isset($_REQUEST['Ph']) or !CheckTourSession() or checkACL(array(AclIndividuals,AclTeams, AclOutput), AclReadOnly, false) < AclReadOnly) {
	JsonOut($JSON);
}

$Ev= $_REQUEST['Ev'];
$Ph= $_REQUEST['Ph'];
$Team = (isset($_REQUEST['TeamEvent']) ? intval($_REQUEST['TeamEvent']) : 0);

// tiro fuori i match
$rs=GetFinMatches($Ev,$Ph,null,$Team,true);

if($Ph==-1) {
	require_once('Common/Lib/CommonLib.php');
	$MatchNames=getPoolMatches();
	$MatchNamesWA=getPoolMatchesWA();
}

$JSON['error']=0;
$JSON['match']=array();

while ($row=safe_fetch($rs)) {
	$Target = ltrim($row->target1, '0');
	if ($row->target1 != $row->target2) $Target .= '/' . ltrim($row->target2, '0');
	$title = $Target;
	if ($Ph == -1) {
		if ($row->elimType == 3 and isset($MatchNames[$row->match1])) {
			$title = $MatchNames[$row->match1];
		} elseif ($row->elimType == 4 and isset($MatchNamesWA[$row->match1])) {
			$title = $MatchNamesWA[$row->match1];
		} else {
			$title = get_text($row->phase . '_Phase');
		}
	}
	$JSON['match'][] = array(
		'matchno1' => $row->match1,
		'name1' => $title . ' - ' . $row->name1,
		'matchno2' => $row->match2,
		'name2' => $row->name2,
	);
}

JsonOut($JSON);
