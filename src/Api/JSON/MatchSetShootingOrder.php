<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_Modules.php');

$TourId = 0;
$TourCode = '';
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
	$TourId = getIdFromCode($_REQUEST['CompCode']);
	$TourCode = preg_replace('/[^a-z0-9_-]+/i','', $_REQUEST['CompCode']);
}

$EvType = -1;
if(isset($_REQUEST['Type']) && preg_match("/^[01]$/", $_REQUEST['Type'])) {
	$EvType = $_REQUEST['Type'];
}

$EvCode = '';
if(isset($_REQUEST['Event']) && preg_match("/^[a-z0-9_-]+$/i", $_REQUEST['Event'])) {
	$EvCode = $_REQUEST['Event'];
}

$MatchId = -1;
if(isset($_REQUEST['MatchId']) && preg_match("/^[0-9]+$/", $_REQUEST['MatchId'])) {
	$MatchId = $_REQUEST['MatchId'];
}

$End = -1;
if(isset($_REQUEST['End']) && preg_match("/^[0-9]+$/", $_REQUEST['End'])) {
	$End = $_REQUEST['End'];
}

$Side = -1;
if(isset($_REQUEST['Side']) && preg_match("/^[01]+$/", $_REQUEST['Side'])) {
	$Side = $_REQUEST['Side'];
}

$JSON=array('Error' => true);

if(!$TourId or !$EvCode or $MatchId==-1 or $EvType==-1 or $End==-1 or $Side==-1) {
	SendResult($JSON);
}

if($MatchId%2) {
	$MatchL=$MatchId-1;
	$MatchR=$MatchId;
} else {
	$MatchL=$MatchId;
	$MatchR=$MatchId+1;
}

$Shoot1st=" | ".pow(2, $End);
$Shoot2nd=" & ~".pow(2, $End);

if($EvType) {
	// team
	switch($Side) {
		case 0: // left shoots first
			safe_w_sql("update TeamFinals set TfShootFirst=(TfShootFirst $Shoot1st), TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where TfTournament=$TourId and TfEvent='$EvCode' and TfMatchNo=$MatchL");
			safe_w_sql("update TeamFinals set TfShootFirst=(TfShootFirst $Shoot2nd), TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where TfTournament=$TourId and TfEvent='$EvCode' and TfMatchNo=$MatchR");
			break;
		case 1: // right shoots first
			safe_w_sql("update TeamFinals set TfShootFirst=(TfShootFirst $Shoot1st), TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where TfTournament=$TourId and TfEvent='$EvCode' and TfMatchNo=$MatchR");
			safe_w_sql("update TeamFinals set TfShootFirst=(TfShootFirst $Shoot2nd), TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where TfTournament=$TourId and TfEvent='$EvCode' and TfMatchNo=$MatchL");
			break;
		default:// no side selected, removes the flag from both
			safe_w_sql("update TeamFinals set TfShootFirst=(TfShootFirst $Shoot2nd), TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where TfTournament=$TourId and TfEvent='$EvCode' and TfMatchNo in ($MatchL, $MatchR)");
	}
} else {
	// individual
	switch($Side) {
		case 0: // left shoots first
			safe_w_sql("update Finals set FinShootFirst=(FinShootFirst $Shoot1st), FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where FinTournament=$TourId and FinEvent='$EvCode' and FinMatchNo=$MatchL");
			safe_w_sql("update Finals set FinShootFirst=(FinShootFirst $Shoot2nd), FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where FinTournament=$TourId and FinEvent='$EvCode' and FinMatchNo=$MatchR");
			break;
		case 1: // right shoots first
			safe_w_sql("update Finals set FinShootFirst=(FinShootFirst $Shoot1st), FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where FinTournament=$TourId and FinEvent='$EvCode' and FinMatchNo=$MatchR");
			safe_w_sql("update Finals set FinShootFirst=(FinShootFirst $Shoot2nd), FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where FinTournament=$TourId and FinEvent='$EvCode' and FinMatchNo=$MatchL");
			break;
		default:// no side selected, removes the flag from both
			safe_w_sql("update Finals set FinShootFirst=(FinShootFirst $Shoot2nd), FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where FinTournament=$TourId and FinEvent='$EvCode' and FinMatchNo in ($MatchL, $MatchR)");
	}
}

$JSON['Error']=false;

// triggers JACK to notify shootng first changed
runJack("FinShootingFirst", $TourId, array("Event"=>$EvCode ,"Team"=>$EvType,"MatchNo"=>$MatchL ,"TourId"=>$TourId));

SendResult($JSON);

