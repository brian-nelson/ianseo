<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_Modules.php');

CheckTourSession(true);

if(!isset($_REQUEST['match']) or !isset($_REQUEST['team']) or !isset($_REQUEST['event']) ) {
	header('Content-Type: text/xml');
	echo '<response error="1" />';
	die();
}

$Error=1;
$Out='';
$Team=intval($_REQUEST['team']);
$Event=$_REQUEST['event'];
$Matchno=intval($_REQUEST['match']);

checkACL(($Team ? AclTeams : AclIndividuals), AclReadWrite, false);

$m=array($Matchno, ($Matchno%2) ? $Matchno-1 : $Matchno+1);
$TabPrefix=($Team ? 'Tf' : 'Fin');
$Table=($Team ? 'Team' : '');

// updates the confirmation of the match
safe_w_sql("update {$Table}Finals set {$TabPrefix}Status=1, {$TabPrefix}Confirmed=1, {$TabPrefix}DateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where {$TabPrefix}Tournament={$_SESSION['TourId']} and {$TabPrefix}Event='$Event' and {$TabPrefix}MatchNo in ($m[0],$m[1])");

$q=safe_r_sql("select {$TabPrefix}WinLose Winner from {$Table}Finals where {$TabPrefix}Tournament={$_SESSION['TourId']} and {$TabPrefix}Event='$Event' and {$TabPrefix}MatchNo in ($m[0],$m[1]) order by {$TabPrefix}MatchNo");

$Winner='';
$Loser='';
if($r1=safe_fetch($q) and $r2=safe_fetch($q) and $r1->Winner+$r2->Winner) {
	if($r1->Winner) {
		$Winner='matchtab1';
		$Loser='matchtab2';
	} else {
		$Winner='matchtab2';
		$Loser='matchtab1';
	}
}

$Error=0;

runJack("FinConfirmEnd", $_SESSION['TourId'], array("Event"=>$Event ,"Team"=>$Team,"MatchNo"=>min($m) ,"TourId"=>$_SESSION['TourId']));
runJack("MatchFinished", $_SESSION['TourId'], array("Event"=>$Event ,"Team"=>$Team,"MatchNo"=>min($m) ,"TourId"=>$_SESSION['TourId']));

header('Content-Type: text/xml');
echo '<response error="'.$Error.'" winner="'.$Winner.'" loser="'.$Loser.'">';
echo $Out;
echo '</response>';
die();

