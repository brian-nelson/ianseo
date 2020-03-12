<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
require_once('Common/Lib/ArrTargets.inc.php');

$Event = isset($_REQUEST['Event']) ? $_REQUEST['Event'] : null;
$TeamEvent = isset($_REQUEST['Team']) ? $_REQUEST['Team'] : null;
$CurValue = isset($_REQUEST['CurValue']) ? $_REQUEST['CurValue'] : null;

$JSON=array('error'=>1, 'nextValue'=>'');

checkACL(($TeamEvent ? AclTeams : AclIndividuals), AclReadWrite, false);
$isBlocked=($TeamEvent==0 ? IsBlocked(BIT_BLOCK_IND) : IsBlocked(BIT_BLOCK_TEAM));

if(is_null($Event) or is_null($TeamEvent) or is_null($CurValue) or $isBlocked) {
	JsonOut($JSON);
}

//require_once("Common/Obj_Target.php");
//require_once('Common/Fun_FormatText.inc.php');

$tgt = GetHigerArrowValue($Event,$TeamEvent,$CurValue,$_SESSION['TourId']);

$JSON['error']=0;
$JSON['nextValue'] = $tgt;


JsonOut($JSON);