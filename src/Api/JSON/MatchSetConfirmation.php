<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_Modules.php');
require_once('Final/Fun_ChangePhase.inc.php');

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

$ConfirmEnd = 0;
if(isset($_REQUEST['ConfirmEnd']) && preg_match("/^[01]+$/", $_REQUEST['ConfirmEnd'])) {
	$ConfirmEnd = $_REQUEST['ConfirmEnd'];
}

$ConfirmMatch = 0;
if(isset($_REQUEST['ConfirmMatch']) && preg_match("/^[01]+$/", $_REQUEST['ConfirmMatch'])) {
	$ConfirmMatch = $_REQUEST['ConfirmMatch'];
}

$JSON=array('Error' => true);

if(!$TourId or !$EvCode or $MatchId==-1 or $EvType==-1 or !($ConfirmMatch or $ConfirmEnd)) {
	SendResult($JSON);
}

if($MatchId%2) {
	$MatchL=$MatchId-1;
	$MatchR=$MatchId;
} else {
	$MatchL=$MatchId;
	$MatchR=$MatchId+1;
}

$TabPrefix=($EvType ? 'Tf' : 'Fin');
$Table=($EvType ? 'Team' : '');

$Params=getEventArrowsParams($EvCode, intval(log($MatchId, 2)), $EvType, $TourId);
$q=safe_r_sql("select
	{$TabPrefix}WinLose Winner,
	{$TabPrefix}Status `Status`,
	{$TabPrefix}MatchNo MatchNo,
	rtrim({$TabPrefix}ArrowString) ArrowString,
	floor((length(rtrim({$TabPrefix}ArrowString))-1)/{$Params->arrows})+1 as CurrentEnd,
	length(rtrim({$TabPrefix}Tiebreak)) as ShootOffShot,
	{$TabPrefix}ShootFirst&1 as MatchStarter,
	{$TabPrefix}".($Params->EvMatchMode ? 'SetScore' : 'Score')." as Points
	from {$Table}Finals
	where {$TabPrefix}Tournament=$TourId and {$TabPrefix}Event='$EvCode' and {$TabPrefix}MatchNo in ($MatchL, $MatchR)
	order by {$TabPrefix}MatchNo");
$r1=safe_fetch($q);
$r2=safe_fetch($q);

if(!$r1 or !$r2) {
	SendResult($JSON);
}

if($ConfirmMatch) {
	if($r1->Winner or $r2->Winner) {
		// if no doubts in arrows...
		if($r1->ArrowString==strtoupper($r1->ArrowString)
				and $r2->ArrowString==strtoupper($r2->ArrowString)
				and strlen($r1->ArrowString) % $Params->arrows == 0
				and strlen($r2->ArrowString) % $Params->arrows == 0
				and $r1->ShootOffShot % $Params->so == 0
				and $r2->ShootOffShot % $Params->so == 0) {
			// updates the confirmation of the match
			safe_w_sql("update {$Table}Finals set {$TabPrefix}Status=1, {$TabPrefix}Confirmed=1, {$TabPrefix}DateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where {$TabPrefix}Tournament=$TourId and {$TabPrefix}Event='$EvCode' and {$TabPrefix}MatchNo in ($MatchL, $MatchR)");
			$JSON['Error']=false;

            if ($EvType) {
                move2NextPhaseTeam(null,$EvCode,$MatchL,$TourId);
            } else {
                move2NextPhase(null, $EvCode, $MatchL, $TourId);
            }

			runJack("FinConfirmEnd", $TourId, array("Event" => $EvCode, "Team" => $EvType, "MatchNo" => $MatchL, "TourId" => $TourId));
			runJack("MatchFinished", $TourId, array("Event" => $EvCode, "Team" => $EvType, "MatchNo" => $MatchL, "TourId" => $TourId));
		}
	}
	// stops here
	SendResult($JSON);
}

if($ConfirmEnd) {
	// check there are no stars in the arrowstrings
	if($r1->ArrowString==strtoupper($r1->ArrowString)
			and $r2->ArrowString==strtoupper($r2->ArrowString)
			and strlen($r1->ArrowString) % $Params->arrows == 0
			and strlen($r2->ArrowString) % $Params->arrows == 0
			and $r1->ShootOffShot % $Params->so == 0
			and $r2->ShootOffShot % $Params->so == 0) {
		safe_w_sql("update {$Table}Finals set {$TabPrefix}Status=3, {$TabPrefix}DateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where {$TabPrefix}Tournament=$TourId and {$TabPrefix}Event='$EvCode' and {$TabPrefix}MatchNo in ($MatchL, $MatchR)");

		// calculates who shoots first following end
		if(!$r1->Winner and !$r2->Winner) {
			// still no winner, so based on the rules lower starts first
			$Shoot1st=" | ".pow(2, $r1->CurrentEnd);
			$Shoot2nd=" & ~".pow(2, $r1->CurrentEnd);
			$MatchNo1st=-1;
			$MatchNo2nd=-1;

			if($r1->Points<$r2->Points) {
				// archer 1 shoots first
				$MatchNo1st=$r1->MatchNo;
				$MatchNo2nd=$r2->MatchNo;
			} elseif($r2->Points<$r1->Points) {
				// archer 2 shoots first
				$MatchNo1st=$r2->MatchNo;
				$MatchNo2nd=$r1->MatchNo;
			} elseif($r1->MatchStarter) {
				// Archer1 started the match
				$MatchNo1st=$r1->MatchNo;
				$MatchNo2nd=$r2->MatchNo;
			} elseif($r2->MatchStarter) {
				// Archer2 started the match
				$MatchNo1st=$r2->MatchNo;
				$MatchNo2nd=$r1->MatchNo;
			}

			if($MatchNo1st != -1 and $MatchNo2nd != -1) {
				safe_w_sql("update {$Table}Finals set {$TabPrefix}ShootFirst=({$TabPrefix}ShootFirst $Shoot1st) where {$TabPrefix}Tournament=$TourId and {$TabPrefix}Event='$EvCode' and {$TabPrefix}MatchNo=$MatchNo1st");
				safe_w_sql("update {$Table}Finals set {$TabPrefix}ShootFirst=({$TabPrefix}ShootFirst $Shoot2nd) where {$TabPrefix}Tournament=$TourId and {$TabPrefix}Event='$EvCode' and {$TabPrefix}MatchNo=$MatchNo2nd");
			}
		}

		$JSON['Error']=false;
		runJack("FinConfirmEnd", $TourId, array("Event"=>$EvCode , "Team"=>$EvType, "MatchNo"=>$MatchL, "TourId"=>$TourId));
	}
}

SendResult($JSON);