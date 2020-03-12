<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Fun_ChangePhase.inc.php');
require_once('Common/Lib/CommonLib.php');
//require_once('Common/Fun_FormatText.inc.php');
//require_once('Fun_Final.local.inc.php');

CheckTourSession(true);

$JSON=array('error'=>0, 'msg'=>get_text('Error'));

if(empty($_REQUEST['event']) or !isset($_REQUEST['team']) or !isset($_REQUEST['matchno'])) {
	JsonOut($JSON);
}

$event=$_REQUEST['event'];
$team=intval($_REQUEST['team']);
$match=intval($_REQUEST['matchno']);

if($team==0 ? IsBlocked(BIT_BLOCK_IND) : IsBlocked(BIT_BLOCK_TEAM)) {
	JsonOut($JSON);
}

checkACL(($team ? AclTeams : AclIndividuals), AclReadWrite);

// normalize the matchno to get the lower 1
if($match%2) {
	$match--;
}

$prefix=($team ? 'Tf' : 'Fin');
$SQL= "update ".($team ? 'Team' : '')."Finals
		set {$prefix}Confirmed=1,
		{$prefix}Status=1
		where {$prefix}Tournament={$_SESSION['TourId']}
			and {$prefix}Event='$event'
			and {$prefix}Matchno in ($match, ".($match+1).") ";
safe_w_sql($SQL);

$ok=false;
if ($team) {
	$ok=move2NextPhaseTeam(null,$event,$match);
} else {
	$ok=move2NextPhase(null,$event,$match);
}

if ($ok) {
	$JSON['error']=0;
	$JSON['msg']=get_text('CmdOk');
}

JsonOut($JSON);

