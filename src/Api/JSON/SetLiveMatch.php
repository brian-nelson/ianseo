<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Fun_Final.local.inc.php');
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

$JSON=array('Error' => true);

if(!$TourId or !$EvCode or $MatchId==-1 or $EvType==-1 ) {
	SendResult($JSON);
}

if($MatchId%2!=0) {
    $MatchId--;
}

$Rs=setLiveSession($EvType, $EvCode, $MatchId, $TourId, false);

if (safe_num_rows($Rs)==1) {
    $JSON['Error']=false;
}

runJack("FinLiveUpdate", $TourId, array("Event"=>$EvCode ,"Team"=>$EvType ,"MatchNo"=>$MatchId ,"TourId"=>$TourId));

SendResult($JSON);
