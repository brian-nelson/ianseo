<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Fun_Modules.php');

$TourId = 0;
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
	$TourId = getIdFromCode($_REQUEST['CompCode']);
}


$SQL = "SELECT AwEvent, AwFinEvent, AwTeam, IFNULL(EvEventName,'') as EventName "
	. "FROM Awards "
	. "LEFT JOIN Events ON AwEvent=EvCode AND AwTournament=EvTournament AND AwTeam=EvTeamEvent AND AwFinEvent=1 "
	. "WHERE AwTournament={$TourId} AND AwUnrewarded=0 AND AwGroup!=0 "
	. "ORDER BY AwGroup, AwOrder, AwEvent, AwFinEvent DESC, AwTeam";
$json_array=array();

$q=safe_r_sql($SQL);
while ($r=safe_fetch($q)) {
	if(substr($r->AwEvent, 0, 7)=='Custom-') {
		list($dum, $num)=explode('-', $r->AwEvent);
		$r->EventName=getModuleParameter('Awards', 'Aw-CustomEvent-1-'.$num);
	}
	$json_array[] = array("Event"=>$r->AwEvent, "Type"=>$r->AwTeam, "EvName"=>$r->EventName);
}


// Return the json structure with the callback function that is needed by the app
SendResult($json_array);