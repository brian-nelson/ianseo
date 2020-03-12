<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Lib/Fun_Phases.inc.php');

$TourId = 0;
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
	$TourId = getIdFromCode($_REQUEST['CompCode']);
}

$EvType = -1;
if(isset($_REQUEST['Type']) && preg_match("/^[01]$/", $_REQUEST['Type'])) {
	$EvType = $_REQUEST['Type'];
}

$EvCode = '....';
if(isset($_REQUEST['Event']) && preg_match("/^[a-z0-9_-]+$/i", $_REQUEST['Event'])) {
	$EvCode = $_REQUEST['Event'];
}

$PhId = -1;
if(isset($_REQUEST['Phase']) && preg_match("/^[0-9]+$/", $_REQUEST['Phase'])) {
	$PhId = $_REQUEST['Phase'];
}

$json_array=array("Event"=>$EvCode, "Type"=>$EvType, "EvName"=>"", "Phases"=>array());

$options['tournament']=$TourId;
$options['events']=array($EvCode);

$rank=null;
if($EvType) {
	$rank=Obj_RankFactory::create('GridTeam',$options);
} else {
	$rank=Obj_RankFactory::create('GridInd',$options);
}
$rank->read();
$Data=$rank->getData();

foreach($Data['sections'] as $kSec=>$vSec) {
	$json_array["EvName"]=$vSec['meta']['eventName'];
	$fldScore = ($vSec['meta']['matchMode']==1 ?  'setScore': 'score');
	$cntPhase = 1;
	foreach($vSec['phases'] as $kPh=>$vPh) {
		if($PhId == -1 || $kPh<=$PhId) {
			$tmpPhase = array("PhCode"=>$kPh, "PhPhase"=>get_text($kPh."_Phase"), "PhNameShort"=>getPhaseTV($kPh,$cntPhase) ,"PhName"=>get_text(getPhaseTV($kPh,$cntPhase)."_Phase","Tournament"), "Matches"=>array());
			foreach($vPh['items'] as $kItem=>$vItem) {
				$tmpL = array();
				$tmpR = array();
				if($EvType==0) {
					$tmpL += array("Id"=>$vItem["bib"], "FamilyName"=>$vItem["familyName"], "GivenName"=>$vItem["givenName"], "NameOrder"=>$vItem["nameOrder"], "Gender"=>$vItem["gender"]);
					$tmpR += array("Id"=>$vItem["oppBib"], "FamilyName"=>$vItem["oppFamilyName"], "GivenName"=>$vItem["oppGivenName"], "NameOrder"=>$vItem["oppNameOrder"], "Gender"=>$vItem["oppGender"]);
				}
				$tmpL += array("TeamCode"=>$vItem["countryCode"], "TeamName"=>$vItem["countryName"],
					"Score"=>$vItem[($vSec['meta']['matchMode']==1 ?  'setScore': 'score')],
					"TieBreak"=>$vItem['tiebreakDecoded'],
					"Winner"=>($vItem['winner']? true:false));
				$tmpR += array("TeamCode"=>$vItem["oppCountryCode"], "TeamName"=>$vItem["oppCountryName"],
					"Score"=>$vItem[($vSec['meta']['matchMode']==1 ?  'oppSetScore': 'oppScore')],
					"TieBreak"=>$vItem['oppTiebreakDecoded'],
					"Winner"=>($vItem['oppWinner']? true:false));

				$tmpPhase["Matches"][] = Array("MatchId"=>$vItem['matchNo'], "ScheduledDateTime"=>date("Y-m-d H:i",strtotime($vItem["scheduledDate"] . " ". $vItem["scheduledTime"])), "LeftOpponent"=>$tmpL, "RightOpponent"=>$tmpR);
			}
			$json_array["Phases"][]=$tmpPhase;
		}
		$cntPhase++;
	}
}

// Return the json structure with the callback function that is needed by the app
SendResult($json_array);
