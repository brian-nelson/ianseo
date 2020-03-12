<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_Modules.php');

CheckTourSession(true);

$JSON=array('error'=>1, 'winner'=>'', 't'=>array(), 'starter'=>'', 'tabindex' => '');

if(empty($_REQUEST['confirm']) or !isset($_REQUEST['team']) or !isset($_REQUEST['event'])) {
	JsonOut($JSON);
}

$Team=intval($_REQUEST['team']);
$Event=$_REQUEST['event'];

//$Error=1;
//$Out='';
//$MatchMode=intval($_REQUEST['mode']);
//$Starter='';
//$Winner=0;

checkACL(($Team ? AclTeams : AclIndividuals), AclReadWrite, false);

$TabIndexOffset=100;

foreach($_REQUEST['confirm'] as $Matchno => $Start) {
	$Params=getEventArrowsParams($Event, intval(log($Matchno, 2)), $Team);

	$m=array($Matchno, ($Matchno%2) ? $Matchno-1 : $Matchno+1);
	$TabPrefix=($Team ? 'Tf' : 'Fin');
	$Table=($Team ? 'Team' : '');

	// updates the confirmation of the arrows
	safe_w_sql("update {$Table}Finals set {$TabPrefix}Status=3, {$TabPrefix}DateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where {$TabPrefix}Tournament={$_SESSION['TourId']} and {$TabPrefix}Event='$Event' and {$TabPrefix}MatchNo=$Matchno");

	$q=safe_r_sql("select
			{$TabPrefix}WinLose Winner,
			{$TabPrefix}Status `Status`,
			{$TabPrefix}MatchNo MatchNo,
			floor((length(rtrim({$TabPrefix}ArrowString))-1)/{$Params->arrows})+1 as CurrentEnd,
			length(rtrim({$TabPrefix}Tiebreak)) as ShootOffShot,
			{$TabPrefix}ShootFirst&1 as MatchStarter,
			{$TabPrefix}".($Params->EvMatchMode ? 'SetScore' : 'Score')." as Points
		from {$Table}Finals
		where {$TabPrefix}Tournament={$_SESSION['TourId']} and {$TabPrefix}Event='$Event' and {$TabPrefix}MatchNo in ($m[0], $m[1])");

	if($r1=safe_fetch($q) and $r2=safe_fetch($q) and $r1->Status==3 and $r2->Status==3) {
		// both ends confirmed so based on the rules lower starts first
		if($r1->Points<$r2->Points) {
			// archer 1 shoots first
			$m=array($r1->MatchNo, $r2->MatchNo);
		} elseif($r2->Points<$r1->Points) {
			// archer 2 shoots first
			$m=array($r2->MatchNo, $r1->MatchNo);
		} elseif($r1->MatchStarter) {
			// Archer1 started the match
			$m=array($r1->MatchNo, $r2->MatchNo);
		} elseif($r2->MatchStarter) {
			// Archer2 started the match
			$m=array($r2->MatchNo, $r1->MatchNo);
		}

		$Winner=($r1->Winner+$r2->Winner);
		if(!$Winner) {
			$JSON['starter']='first['.$Team.']['.$Event.']['.$m[0].']['.$r1->CurrentEnd.']';
			$JSON['tabindex']=$TabIndexOffset + ($r1->CurrentEnd-1)*$Params->arrows*2 + 1;
			if($r1->CurrentEnd==$Params->ends) {
				// we are in the SO so we must add the number of arrows shot
				$JSON['tabindex']+=$r1->ShootOffShot*2+$Params->arrows*2;
			}
			safe_w_sql("update {$Table}Finals set {$TabPrefix}ShootFirst=({$TabPrefix}ShootFirst | ".pow(2, $r1->CurrentEnd).") where {$TabPrefix}Tournament={$_SESSION['TourId']} and {$TabPrefix}Event='$Event' and {$TabPrefix}MatchNo={$m[0]}");
			safe_w_sql("update {$Table}Finals set {$TabPrefix}ShootFirst=({$TabPrefix}ShootFirst & ~".pow(2, $r1->CurrentEnd).") where {$TabPrefix}Tournament={$_SESSION['TourId']} and {$TabPrefix}Event='$Event' and {$TabPrefix}MatchNo={$m[1]}");
		} else {
			if($r1->Winner) {
				$JSON['winner']=($r1->MatchNo%2 ? 'R' : 'L');
			} elseif($r2->Winner) {
				$JSON['winner']=($r2->MatchNo%2 ? 'R' : 'L');
			}
		}
	}

	$JSON['error']=0;

	runJack("FinConfirmEnd", $_SESSION['TourId'], array("Event"=>$Event , "Team"=>$Team, "MatchNo"=>min($m), "TourId"=>$_SESSION['TourId']));
}

// put here as a fallback
JsonOut($JSON);
