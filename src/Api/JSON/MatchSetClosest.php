<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Final/Fun_MatchTotal.inc.php');

$TourId = 0;
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
    $TourId = getIdFromCode($_REQUEST['CompCode']);
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

$Side = -1;
if(isset($_REQUEST['Side']) && preg_match("/^[01]+$/", $_REQUEST['Side'])) {
    $Side = $_REQUEST['Side'];
}

$JSON=array('Error'=>true);

if(!$TourId or empty($EvCode) or $EvType==-1 or $MatchId==-1) {
    SendResult($JSON);
}

$Pre = $EvType ? 'Tf' : 'Fin';
if($MatchId%2) {
    $MatchL=$MatchId-1;
    $MatchR=$MatchId;
} else {
    $MatchL=$MatchId;
    $MatchR=$MatchId+1;
}
$MatchClosest = $MatchL+$Side;



safe_w_sql("update ".($EvType ? 'Team' : '')."Finals set {$Pre}TbClosest=0, {$Pre}TbDecoded=replace({$Pre}TbDecoded, '+', ''), {$Pre}WinLose=0, {$Pre}Tie=0 where {$Pre}Tournament={$TourId} and {$Pre}Event='{$EvCode}' and {$Pre}Matchno in ($MatchL,$MatchR)");
if($Side!=-1) {
    // there is a closest... but ONLY if there is at least an arrow tie
    safe_w_sql("update ".($EvType ? 'Team' : '')."Finals set {$Pre}TbClosest=1, {$Pre}WinLose=1, {$Pre}Tie=1, {$Pre}TbDecoded=concat({$Pre}TbDecoded, '+') 
		where trim({$Pre}Tiebreak)!='' and {$Pre}Tournament={$TourId} and {$Pre}Event='{$EvCode}' and {$Pre}Matchno={$MatchClosest}");
}

$JSON['Error']=false;
MatchTotal($MatchL, $EvCode, $EvType, $TourId);

SendResult($JSON);