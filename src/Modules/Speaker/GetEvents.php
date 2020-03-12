<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Fun_Modules.php');

$schedule=(isset($_REQUEST['schedule']) && preg_match('/^[0-1]{1}[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}(:[0-9]{2})*$/',$_REQUEST['schedule']) ? $_REQUEST['schedule'] : '');

$JSON=array('error' => 1, 'rows' => array(), 'newdata' => '');

if (empty($_SESSION['TourId']) or !$schedule ) {
	JsonOut($JSON);
}
checkACL(AclSpeaker, AclReadOnly, false);

$team=substr($schedule,0,1);

$tmp=explode(' ',substr($schedule,1));
$date=$tmp[0];
$time=$tmp[1];

if($IskSequence=getModuleParameter('ISK', 'Sequence')) {
	if(!isset($IskSequence['session'])) {
		$IskSequence=current($IskSequence);
	}
	// get the running sequence
	$JSON['newdata']=($IskSequence['session']==$tmp[0].$tmp[1] ? '' : 'newdata');
}


$query = "SELECT DISTINCT EvCode AS code, EvEventName as name "
	. "FROM "
		. "FinSchedule "
	. "INNER JOIN "
		. "Events ON FSEvent=EvCode AND FSTeamEvent=EvTeamEvent AND FSTournament=EvTournament "
	. "WHERE "
		. "FSTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FSTeamEvent=" . $team . " "
		. "AND FSScheduledDate=" . StrSafe_DB($date) . " AND FSScheduledTime=" . StrSafe_DB($time) . " "
	. "ORDER BY "
		. "EvProgr, CONCAT(FSScheduledDate, ' ',FSScheduledTime) ASC ";

$JSON['error']=0;

$rs=safe_r_sql($query);

while ($myRow=safe_fetch($rs)) {
	$JSON['rows'][]=array(
		'val' => $myRow->code,
		'txt' => $myRow->name,
		'sel' => '0',
	);
}

JsonOut($JSON);