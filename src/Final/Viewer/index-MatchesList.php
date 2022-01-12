<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
CheckTourSession(true);

$JSON=array('error'=>1, 'data'=>array());

if(empty($_REQUEST['Event']) or !preg_match("/^[a-z0-9_-|]+$/i", $_REQUEST['Event']) or !isset($_REQUEST['Phase'])) {
	JsonOut($JSON);
}
$EvCode = $_REQUEST['Event'];

$TeamEvent = (substr($EvCode,-2)=='|T');
if($TeamEvent) {
    $EvCode = substr($EvCode,0,-2);
} else {
    $TeamEvent=0;
}
checkACL(array(($TeamEvent ? AclTeams:AclIndividuals), AclOutput), AclReadOnly, false);
require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/Obj_RankFactory.php');

$options=array();

$Prefix=array();

if(is_numeric($_REQUEST['Phase'])) {
	$options['events']=array($EvCode . '@' . intval($_REQUEST['Phase']));
} else {
	$options['events']=array($EvCode);
	$PhId=-1;
	$Matches=array();
	// no valid phases check if it could be a WG or Field/3D pool system
	$q=safe_r_sql("select EvElimType from Events where EvTeamEvent=$TeamEvent and EvCode=".StrSafe_DB($EvCode)." and EvTournament={$_SESSION['TourId']}");
	if($r=safe_fetch($q)) {
		switch($r->EvElimType) {
			case '3':
				if($_REQUEST['Phase']=='A' or $_REQUEST['Phase']=='B' or $_REQUEST['Phase']=='C') {
					$options['matchnoArray']=getPoolMatchNos($_REQUEST['Phase']);
					$Prefix=getPoolMatchesShort();
				}
				break;
			case '4':
				if($_REQUEST['Phase']=='A' or $_REQUEST['Phase']=='B' or $_REQUEST['Phase']=='C' or $_REQUEST['Phase']=='D') {
					$options['matchnoArray']=getPoolMatchNosWA($_REQUEST['Phase']);
					$Prefix=getPoolMatchesShortWA();
				}
				break;
			default:
				// dies here as nothing meaningfull detected
				JsonOut($JSON);
		}
	}
}

$JSON['error']=0;

$rank=null;
if($TeamEvent) {
	$rank=Obj_RankFactory::create('GridTeam',$options);
} else {
	$rank=Obj_RankFactory::create('GridInd',$options);
}
$rank->read();
$Data=$rank->getData();

foreach($Data['sections'] as $kSec=>$vSec) {
	foreach($vSec['phases'] as $kPh=>$vPh) {
		foreach($vPh['items'] as $kItem=>$vItem) {
			$tmpL = array();
			$tmpR = array();
			if($TeamEvent==0) {
				$tmpL += array("Id"=>$vItem["bib"], "FamilyName"=>$vItem["familyName"], "GivenName"=>$vItem["givenName"], "NameOrder"=>$vItem["nameOrder"], "Gender"=>$vItem["gender"]);
				$tmpR += array("Id"=>$vItem["oppBib"], "FamilyName"=>$vItem["oppFamilyName"], "GivenName"=>$vItem["oppGivenName"], "NameOrder"=>$vItem["oppNameOrder"], "Gender"=>$vItem["oppGender"]);
			}
			$tmpL += array("TeamCode"=>$vItem["countryCode"], "TeamName"=>$vItem["countryName"], "Target"=>ltrim($vItem["target"],"0"),
				"Score"=>$vItem[($vSec['meta']['matchMode']==1 ?  'setScore': 'score')], "TieBreak"=>$vItem['tiebreakDecoded'], "Winner"=>($vItem['winner']? true:false));
			$tmpR += array("TeamCode"=>$vItem["oppCountryCode"], "TeamName"=>$vItem["oppCountryName"], "Target"=>ltrim($vItem["oppTarget"],"0"),
					"Score"=>$vItem[($vSec['meta']['matchMode']==1 ?  'oppSetScore': 'oppScore')], "TieBreak"=>$vItem['oppTiebreakDecoded'], "Winner"=>($vItem['oppWinner']? true:false));
			$JSON['data'][] = Array('Prefix' => (isset($Prefix[$vItem['matchNo']]) ? $Prefix[$vItem['matchNo']] : ''), "Event"=>$EvCode, "Type"=>$TeamEvent, "MatchId"=>$vItem['matchNo'], "ScheduledDateTime"=>date("Y-m-d H:i",strtotime($vItem["scheduledDate"] . " ". $vItem["scheduledTime"])), "LeftOpponent"=>$tmpL, "RightOpponent"=>$tmpR);
		}
	}
}

JsonOut($JSON);

