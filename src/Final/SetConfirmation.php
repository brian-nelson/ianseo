<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_Modules.php');

CheckTourSession(true);

if(empty($_REQUEST['confirm']) or !isset($_REQUEST['team']) or !isset($_REQUEST['event']) or !isset($_REQUEST['mode'])) {
	header('Content-Type: text/xml');
	echo '<response error="1" />';
	die();
}

$Error=1;
$Out='';
$Team=intval($_REQUEST['team']);
$Event=$_REQUEST['event'];
$MatchMode=intval($_REQUEST['mode']);
$Starter='';
$Winner=0;

checkACL(($Team ? AclTeams : AclIndividuals), AclReadWrite, false);

foreach($_REQUEST['confirm'] as $Matchno => $Start) {
	$rows=4;
	$cols=3;
	$so=1;
	$Sql1='';
	$Sql2='';
	$Params=getEventArrowsParams($Event, intval(log($Matchno, 2)), $Team);
	$TabIndex=100;
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
			{$TabPrefix}ShootFirst&1 as MatchStarter,
			{$TabPrefix}".($MatchMode ? 'SetScore' : 'Score')." as Points
		from {$Table}Finals
		where {$TabPrefix}Tournament={$_SESSION['TourId']} and {$TabPrefix}Event='$Event' and {$TabPrefix}MatchNo in ($m[0], $m[1])");

	$Error=0;

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
			$Starter='first['.$Team.']['.$Event.']['.$m[0].']['.$r1->CurrentEnd.']';
			safe_w_sql("update {$Table}Finals set {$TabPrefix}ShootFirst=({$TabPrefix}ShootFirst | ".pow(2, $r1->CurrentEnd).") where {$TabPrefix}Tournament={$_SESSION['TourId']} and {$TabPrefix}Event='$Event' and {$TabPrefix}MatchNo={$m[0]}");
			safe_w_sql("update {$Table}Finals set {$TabPrefix}ShootFirst=({$TabPrefix}ShootFirst & ~".pow(2, $r1->CurrentEnd).") where {$TabPrefix}Tournament={$_SESSION['TourId']} and {$TabPrefix}Event='$Event' and {$TabPrefix}MatchNo={$m[1]}");
		}
		if($Team) {
			for($i=$r1->CurrentEnd; $i<$Params->ends; ++$i) {
				// alternate teams each component shoots 1 arrow, so we iterate for as many arrows per archer!
				for($j=0; $j < ceil($Params->arrows/$Params->MaxTeam); $j++) {
					for($k=0; $k<2; $k++) {
						for($l=0; $l<$Params->MaxTeam; $l++) {
							$Out.='<t id="'.'s_' . $m[$k] . '_' . ($i*$Params->arrows + $j*$Params->MaxTeam + $l).'" val="'.($TabIndex + $i*2*$Params->arrows + $j*2*$Params->MaxTeam + $k*$Params->MaxTeam + $l).'"/>';
						}
					}
				}
			}
			// SO, each member shoots 1 arrow alternate
			for($l=0; $l<$Params->so; $l++) {
				for($k=0; $k<2; $k++) {
					$Out.='<t id="'.'t_' . $m[$k] . '_' . ($l).'" val="'.($TabIndex + $Params->ends*2*$Params->arrows + $l*2 + $k).'"/>';
				}
			}
		} else {
			for($i=$r1->CurrentEnd; $i<$Params->ends; ++$i) {
				for($j=0; $j<$Params->arrows; $j++) {
					for($k=0; $k<2; $k++) {
						$Out.='<t id="'.'s_' . $m[$k] . '_' . ($i*$Params->arrows+$j).'" val="'.($TabIndex + $i*2*$Params->arrows + $j*2 + $k).'"/>';
					}
				}
			}
			// SO
			for($j=0; $j<$Params->so; $j++) {
				for($k=0; $k<2; $k++) {
					$Out.='<t id="'.'t_' . $m[$k] . '_' . ($j).'" val="'.($TabIndex + $Params->ends*2*$Params->arrows + $j*2 + $k).'"/>';
				}
			}

		}
	}
}

runJack("FinConfirmEnd", $_SESSION['TourId'], array("Event"=>$Event ,"Team"=>$Team,"MatchNo"=>min($m) ,"TourId"=>$_SESSION['TourId']));

header('Content-Type: text/xml');
echo '<response error="'.$Error.'" start="'.$Starter.'" winner="'.$Winner.'">';
echo $Out;
echo '</response>';
die();

