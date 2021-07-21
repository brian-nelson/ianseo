<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_Modules.php');
require_once('./Fun_MatchTotal.inc.php');

$JSON=array('error'=>1);

if(!CheckTourSession() or !isset($_REQUEST['closest']) or !isset($_REQUEST['team']) or !isset($_REQUEST['event']) or !isset($_REQUEST['match']) or checkACL(($_REQUEST['team'] ? AclTeams : AclIndividuals), AclReadWrite, false)!=AclReadWrite) {
	JsonOut($JSON);
}

$Team=intval($_REQUEST['team']);
$Event=$_REQUEST['event'];
$MatchL=intval($_REQUEST['match']);
$MatchR=$MatchL+1;
$Pre = $Team ? 'Tf' : 'Fin';


safe_w_sql("update ".($Team ? 'Team' : '')."Finals set {$Pre}TbClosest=0, {$Pre}TbDecoded=replace({$Pre}TbDecoded, '+', ''), {$Pre}WinLose=0, {$Pre}Tie=0 where {$Pre}Tournament={$_SESSION['TourId']} and {$Pre}Event='$Event' and {$Pre}Matchno in ($MatchL,$MatchR)");
if(strlen($_REQUEST['closest'])) {
	// there is a closest... but ONLY if there is at least an arrow tie
	safe_w_sql("update ".($Team ? 'Team' : '')."Finals set {$Pre}TbClosest=1, {$Pre}WinLose=1, {$Pre}Tie=1, {$Pre}TbDecoded=concat({$Pre}TbDecoded, '+') 
		where trim({$Pre}Tiebreak)!='' and {$Pre}Tournament={$_SESSION['TourId']} and {$Pre}Event='$Event' and {$Pre}Matchno=".intval($_REQUEST['closest']));
}

$JSON['error']=0;

MatchTotal($MatchL, $Event, $Team, $_SESSION['TourId']);

// we need to send back the arrow value, the set total, the winner, etc
$options=array();
$options['tournament']=$_SESSION['TourId'];
$options['events']=$Event;
$options['matchno']=$MatchL;

if($Team) {
	$rank=Obj_RankFactory::create('GridTeam',$options);
} else {
	$rank=Obj_RankFactory::create('GridInd',$options);
}
$rank->read();
$Data=$rank->getData();

if(empty($Data['sections'])) {
	JsonOut($JSON);
}
$Section=end($Data['sections']);

if(empty($Section['phases'])) {
	JsonOut($JSON);
}
$Phase=end($Section['phases']);

if(empty($Phase['items'])) {
	JsonOut($JSON);
}
$Match=end($Phase['items']);

$obj=getEventArrowsParams($Event, getPhase($MatchL), $Team);

$JSON['ShootOff']=($Match['tiebreak'] or $Match['oppTiebreak']);

$JSON['winner']=$Match['winner'] ? 'L' : ($Match['oppWinner'] ? 'R' : '');

$JSON['newSOPossible'] = (
	!$JSON['winner'] AND
	(strlen(trim($Match['tiebreak'])) > 0 AND (strlen(trim($Match['tiebreak']))%$obj->so == 0)) AND
	(strlen(trim($Match['oppTiebreak'])) > 0 AND (strlen(trim($Match['oppTiebreak']))%$obj->so == 0)) AND
	(strlen(trim($Match['tiebreak'])) ==strlen(trim($Match['oppTiebreak'])))
);

// Left Side
$Match['tiebreakDecoded']=array_pad(explode(',', $Match['tiebreakDecoded']), 3,'');
// Right side
$Match['oppTiebreakDecoded']=array_pad(explode(',', $Match['oppTiebreakDecoded']), 3,'');

$soEnds=ceil(min(strlen(trim($Match['tiebreak'])), strlen(trim($Match['oppTiebreak'])))/$obj->so);
$TotL=0;
$TotR=0;

for($i=0;$i<$soEnds;$i++) {
	$JSON['t'][]=array(
		'id' => 'EndTotalL_SO_'.$i,
		'val' => $Match['tiebreakDecoded'][$i],
	);
	$JSON['t'][]=array(
		'id' => 'EndTotalR_SO_'.$i,
		'val' => $Match['oppTiebreakDecoded'][$i],
	);
}

$JSON['t'][]=array(
	'id' => 'EndSetL_SO',
	'val' => $Match['setScore'],
);
$JSON['t'][]=array(
	'id' => 'EndSetR_SO',
	'val' => $Match['oppSetScore'],
);

$JSON['error']=0;

$JSON['ClosestL']=$Match['closest'];
$JSON['ClosestR']=$Match['oppClosest'];

$JSON['showClosest']=(($JSON['ShootOff'] and $Match['tiebreakDecoded']==$Match['oppTiebreakDecoded'] and !$JSON['winner']) or $Match['closest'] or $Match['oppClosest']);

JsonOut($JSON);
