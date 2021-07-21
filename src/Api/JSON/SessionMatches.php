<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Obj_RankFactory.php');

$TourId = 0;
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
	$TourId = getIdFromCode($_REQUEST['CompCode']);
}

$SesId = -1;
if(isset($_REQUEST['Session']) && preg_match("/^[0-9]+$/", $_REQUEST['Session'])) {
	$SesId = $_REQUEST['Session'];
}


$json_array=array();

/* AGGINGERE SESSION MATCH */


$Sql = "SELECT SesOrder, SesName, SesDtStart, SesDtEnd FROM Session WHERE SesTournament={$TourId} AND SesType='F' AND SesOrder={$SesId}";
$q=safe_r_SQL($Sql);
if($r=safe_fetch($q)) {
	$Sql = "SELECT FsEvent, FsTeamEvent, FsMatchNo
		FROM FinSchedule
		WHERE FsTournament={$TourId} AND (FsMatchNo%2=0) AND (CONCAT(FsScheduledDate, ' ', FsScheduledTime) BETWEEN '{$r->SesDtStart}' AND '{$r->SesDtEnd}')
		ORDER BY FsScheduledDate, FsScheduledTime";
	$q=safe_r_SQL($Sql);
	while($r=safe_fetch($q)) {
		$options['tournament']=$TourId;
		$options['events']=$r->FsEvent;
		$options['matchno']=$r->FsMatchNo;

		$rank=null;
		if($r->FsTeamEvent) {
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
					if($r->FsTeamEvent==0) {
						$tmpL += array("FamilyName"=>$vItem["familyName"], "GivenName"=>$vItem["givenName"], "NameOrder"=>$vItem["nameOrder"], "Gender"=>$vItem["gender"]);
						$tmpR += array("FamilyName"=>$vItem["oppFamilyName"], "GivenName"=>$vItem["oppGivenName"], "NameOrder"=>$vItem["oppNameOrder"], "Gender"=>$vItem["oppGender"]);
					}
					$tmpL += array("TeamCode"=>$vItem["countryCode"], "TeamName"=>$vItem["countryName"]);
					$tmpR += array("TeamCode"=>$vItem["oppCountryCode"], "TeamName"=>$vItem["oppCountryName"]);
					$json_array[] = Array(
						"Event"=>$r->FsEvent,
						"Type"=>$r->FsTeamEvent,
						"MatchId"=>$vItem['matchNo'],
						"MatchName"=>get_text('SessionName', 'ODF', (object) array('Category' => $vSec['meta']['eventName'], 'RoundType'=>$vPh['meta']['matchName'])),
						"PhaseId"=>strval($kPh),
						"ScheduledDateTime"=>date("Y-m-d H:i",strtotime($vItem["scheduledDate"] . " ". $vItem["scheduledTime"])),
						"LeftOpponent"=>$tmpL,
						"RightOpponent"=>$tmpR);
				}
			}
		}
	}
}

// Return the json structure with the callback function that is needed by the app
SendResult($json_array);
