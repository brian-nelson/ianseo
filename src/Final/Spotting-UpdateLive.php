<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
require_once('Common/Lib/Fun_Final.local.inc.php');
require_once('Common/Lib/Fun_Modules.php');

$event = isset($_REQUEST['d_Event']) ? $_REQUEST['d_Event'] : null;
$TeamEvent = isset($_REQUEST['d_Team']) ? $_REQUEST['d_Team'] : null;
$match = isset($_REQUEST['d_Match']) ? $_REQUEST['d_Match'] : null;

$JSON=array('error'=>1, 'isLive' => 0, 'msg'=>'');

checkACL(($TeamEvent ? AclTeams : AclIndividuals), AclReadWrite, false);

if($match%2!=0)
	$match--;

$isBlocked=($TeamEvent==0 ? IsBlocked(BIT_BLOCK_IND) : IsBlocked(BIT_BLOCK_TEAM));
if (is_null($event) || is_null($TeamEvent) || is_null($match) || $isBlocked) {
	$JSON['msg'] = 'Blocked!';
	JsonOut($JSON);
}

$Rs=setLiveSession($TeamEvent, $event, $match, $_SESSION['TourId']);

if (safe_num_rows($Rs)==1) {
	$myRow=safe_fetch($Rs);
	$JSON['error']=0;
	$JSON['isLive']=($myRow->Live>0);
} else {
	$JSON['msg'] = get_text('Error');
}

runJack("FinLiveUpdate", $_SESSION['TourId'], array("Event"=>$event ,"Team"=>$TeamEvent ,"MatchNo"=>$match ,"TourId"=>$_SESSION['TourId']));

JsonOut($JSON);