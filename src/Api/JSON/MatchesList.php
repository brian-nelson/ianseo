<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Obj_RankFactory.php');

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

$json_array=array();

$options['tournament']=$TourId;
$options['events']=array($EvCode . '@' . $PhId);

$rank=null;
if($EvType) {
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
			if($EvType==0) {
				$tmpL += array("Id"=>$vItem["bib"], "FamilyName"=>$vItem["familyName"], "GivenName"=>$vItem["givenName"], "NameOrder"=>$vItem["nameOrder"], "Gender"=>$vItem["gender"]);
				$tmpR += array("Id"=>$vItem["oppBib"], "FamilyName"=>$vItem["oppFamilyName"], "GivenName"=>$vItem["oppGivenName"], "NameOrder"=>$vItem["oppNameOrder"], "Gender"=>$vItem["oppGender"]);
			}
			$tmpL += array("TeamCode"=>$vItem["countryCode"], "TeamName"=>$vItem["countryName"], "Target"=>ltrim($vItem["target"],"0"),
				"Score"=>$vItem[($vSec['meta']['matchMode']==1 ?  'setScore': 'score')], "TieBreak"=>$vItem['tiebreakDecoded'], "Winner"=>($vItem['winner']? true:false));
			$tmpR += array("TeamCode"=>$vItem["oppCountryCode"], "TeamName"=>$vItem["oppCountryName"], "Target"=>ltrim($vItem["oppTarget"],"0"),
					"Score"=>$vItem[($vSec['meta']['matchMode']==1 ?  'oppSetScore': 'oppScore')], "TieBreak"=>$vItem['oppTiebreakDecoded'], "Winner"=>($vItem['oppWinner']? true:false));
			$json_array[] = Array("Event"=>$EvCode, "Type"=>$EvType, "MatchId"=>$vItem['matchNo'], "ScheduledDateTime"=>date("Y-m-d H:i",strtotime($vItem["scheduledDate"] . " ". $vItem["scheduledTime"])), "LeftOpponent"=>$tmpL, "RightOpponent"=>$tmpR);
		}
	}
}

// Return the json structure with the callback function that is needed by the app
SendResult($json_array);
