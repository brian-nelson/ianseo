<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Obj_RankFactory.php');

$TourId = 0;
$TourCode = '';
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
	$TourId = getIdFromCode($_REQUEST['CompCode']);
	$TourCode = $_REQUEST['CompCode'];
}

$EvType = -1;
if(isset($_REQUEST['Type']) && preg_match("/^[01]$/", $_REQUEST['Type'])) {
	$EvType = $_REQUEST['Type'];
}

$EvCode = '....';
if(isset($_REQUEST['Event']) && preg_match("/^[a-z0-9_-]+$/i", $_REQUEST['Event'])) {
	$EvCode = $_REQUEST['Event'];
}

$MatchId = -1;
if(isset($_REQUEST['MatchId']) && preg_match("/^[0-9]+$/", $_REQUEST['MatchId'])) {
	$MatchId = $_REQUEST['MatchId'];
}

$json_array=array();
$imgPath='/TV/Photos/' . $TourCode . '-%s-%s.jpg';

$options['tournament']=$TourId;
$options['events']=$EvCode;
$options['matchno']=$MatchId;

$rank=null;
if($EvType) {
	$rank=Obj_RankFactory::create('GridTeam',$options);
} else {
	$rank=Obj_RankFactory::create('GridInd',$options);
}
$rank->read();
$Data=$rank->getData();

foreach($Data['sections'] as $kSec=>$vSec) {
	if(!empty($vSec['phases'])) {
		foreach($vSec['phases'] as $kPh=>$vPh) {
			$json_array = Array("Event"=>$EvCode, "Type"=>$EvType, "MatchId"=>$MatchId, "PhaseId"=>strval($kPh), "ScheduledDateTime"=>'', "SessionId"=>strval(0), "SessionName"=>'');
			$objParam=getEventArrowsParams($kSec,$kPh,$EvType,$TourId);
			$json_array['Mode'] = Array("ScoringMode"=>($vSec["meta"]["matchMode"]==1 ? "S" : "C"),
				"Arrows"=>strval($objParam->arrows), "Ends"=>strval($objParam->ends), "ShootoffArrows"=>strval($objParam->so),
				"InitialTime"=>strval($EvType==0 ? 20 : $objParam->arrows*20), "ShootoffInitialTime"=>strval($EvType==0 ? 20 : $objParam->so*20), "TimeResetsOnArrow"=>($EvType==0));
			foreach($vPh['items'] as $kItem=>$vItem) {
				$json_array["ScheduledDateTime"] = date("Y-m-d H:i",strtotime($vItem["scheduledDate"] . " ". $vItem["scheduledTime"]));
				$Sql = "SELECT SesOrder, SesName
					FROM FinSchedule
					INNER JOIN Session ON FsTournament=SesTournament AND CONCAT_WS(' ', FSScheduledDate,FSScheduledTime) BETWEEN SesDtStart AND SesDtEnd
					WHERE FSTournament={$TourId} AND FSEvent='{$EvCode}' AND FSTeamEvent='{$EvType}' AND FSMatchNo={$MatchId}";
				$q=safe_r_SQL($Sql);
				if($r=safe_fetch($q)){
					$json_array["SessionId"] = $r->SesOrder;
					$json_array["SessionName"] = $r->SesName;
				}
				$tmpL = array();
				$tmpR = array();
				if($EvType==0) {
					$tmpL += array("Id"=>$vItem["bib"], "FamilyName"=>$vItem["familyName"], "GivenName"=>$vItem["givenName"], "NameOrder"=>$vItem["nameOrder"], "Gender"=>$vItem["gender"]);
					if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCode.'-En-'.$vItem['id'].'.jpg')) {
						$tmpL += array("ProfilePicURL"=>sprintf($imgPath, 'En', $vItem['id']));
					}
					$tmpR += array("Id"=>$vItem["oppBib"], "FamilyName"=>$vItem["oppFamilyName"], "GivenName"=>$vItem["oppGivenName"], "NameOrder"=>$vItem["oppNameOrder"], "Gender"=>$vItem["oppGender"]);
					if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCode.'-En-'.$vItem['oppId'].'.jpg')) {
						$tmpR += array("ProfilePicURL"=>sprintf($imgPath, 'En', $vItem['oppId']));
					}

				}
				$tmpL += array("TeamCode"=>$vItem["countryCode"], "TeamName"=>$vItem["countryName"]);
				$tmpL += array("Target"=>ltrim($vItem["target"],"0"));
				$tmpL += array("QualificationRank"=>$vItem["qualRank"], "QualificationScore"=>$vItem["qualScore"], "WorldRanking"=>'');
				if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCode.'-Fl-'.$vItem['countryCode'].'.jpg')) {
					$tmpL += array("FlagURL"=>sprintf($imgPath, 'Fl', $vItem['countryCode']));
				}

				$tmpR += array("TeamCode"=>$vItem["oppCountryCode"], "TeamName"=>$vItem["oppCountryName"]);
				$tmpR += array("Target"=>ltrim($vItem["oppTarget"],"0"));
				$tmpR += array("QualificationRank"=>$vItem["oppQualRank"], "QualificationScore"=>$vItem["oppQualScore"], "WorldRanking"=>'');
				if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCode.'-Fl-'.$vItem['oppCountryCode'].'.jpg')) {
					$tmpR += array("FlagURL"=>sprintf($imgPath, 'Fl', $vItem['oppCountryCode']));
				}

				$q= safe_r_SQL("SELECT RankRanking FROM Rankings WHERE RankTournament={$TourId} AND RankTeam={$EvType} AND RankEvent='{$EvCode}' AND RankCode='".($EvType==0 ? $vItem["bib"] : $vItem["countryCode"])."'");
				if($r=safe_fetch($q)){
					$tmpL["WorldRanking"] = $r->RankRanking;
				}
				$q= safe_r_SQL("SELECT RankRanking FROM Rankings WHERE RankTournament={$TourId} AND RankTeam={$EvType} AND RankEvent='{$EvCode}' AND RankCode='".($EvType==0 ? $vItem["oppBib"] : $vItem["oppCountryCode"])."'");
				if($r=safe_fetch($q)){
					$tmpR["WorldRanking"] = $r->RankRanking;
				}
				if($EvType) {
					$q= safe_r_SQL("SELECT EdcExtra FROM ExtraDataCountries WHERE EdcId='".$vItem['teamId']."' AND EdcType='Y' and EdcEvent='{$EvCode}'");
					if($r=safe_fetch($q)){
						$tmpL["ProfileData"] = unserialize($r->EdcExtra);
					}

					$q= safe_r_SQL("SELECT EdcExtra FROM ExtraDataCountries WHERE EdcId='".$vItem['oppTeamId']."' AND EdcType='Y' and EdcEvent='{$EvCode}'");
					if($r=safe_fetch($q)){
						$tmpR["ProfileData"] = unserialize($r->EdcExtra);
					}

					$tmp=array();
					if(!empty($vSec["athletes"][$vItem["teamId"]][$vItem["subTeam"]] )) {
						foreach($vSec["athletes"][$vItem["teamId"]][$vItem["subTeam"]] as $kAth=>$vAth) {
							$tmp[$kAth]= array("Id"=>$vAth["code"], "FamilyName"=>$vAth["familyName"], "GivenName"=>$vAth["givenName"], "NameOrder"=>$vAth["nameOrder"], "Gender"=>$vAth["gender"]);
							if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCode.'-En-'.$vAth['id'].'.jpg')) {
								$tmp[$kAth] += array("ProfilePicURL"=>sprintf($imgPath, 'En', $vAth['id']));
							}
						}
					}
					$tmpL["Components"] = $tmp;
					$tmp=array();
					if(!empty($vSec["athletes"][$vItem["oppTeamId"]][$vItem["oppSubTeam"]] )) {
						foreach($vSec["athletes"][$vItem["oppTeamId"]][$vItem["oppSubTeam"]] as $kAth=>$vAth) {
							$tmp[$kAth]= array("Id"=>$vAth["code"], "FamilyName"=>$vAth["familyName"], "GivenName"=>$vAth["givenName"], "NameOrder"=>$vAth["nameOrder"], "Gender"=>$vAth["gender"]);
							if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCode.'-En-'.$vAth['id'].'.jpg')) {
								$tmp[$kAth] += array("ProfilePicURL"=>sprintf($imgPath, 'En', $vAth['id']));
							}
						}
					}
					$tmpR["Components"] = $tmp;
				} else {
					$q= safe_r_SQL("SELECT EdExtra FROM ExtraData WHERE EdId='".$vItem['id']."' AND EdType='Y' and EdEvent='{$EvCode}'");
					if($r=safe_fetch($q)){
						$tmpL["ProfileData"] = unserialize($r->EdExtra);
					}

					$q= safe_r_SQL("SELECT EdExtra FROM ExtraData WHERE EdId='".$vItem['oppId']."' AND EdType='Y' and EdEvent='{$EvCode}'");
					if($r=safe_fetch($q)){
						$tmpR["ProfileData"] = unserialize($r->EdExtra);
					}
				}
				$json_array['LeftOpponent'] = $tmpL;
				$json_array['RightOpponent'] = $tmpR;
			}
		}
	}
}

// Return the json structure with the callback function that is needed by the app
SendResult($json_array);
