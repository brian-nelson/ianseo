<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Fun_Modules.php');
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



$json_array=array("Event"=>$EvCode, "Type"=>$EvType, "MainLanguage"=>'', "SecondLanguage"=>'', "Languages"=>array());

$SQL = "SELECT *
	FROM Awards
	WHERE AwTournament={$TourId} AND AwEvent='{$EvCode}' AND AwTeam={$EvType} AND AwFinEvent=1
	ORDER BY AwGroup DESC, AwOrder, AwFinEvent DESC, AwTeam ASC, AwEvent";

$q=safe_r_sql($SQL);
if (safe_num_rows($q)>0) {
	$json_array["MainLanguage"]  = getModuleParameter('Awards', 'FirstLanguageCode', '', $TourId);
	$json_array["SecondLanguage"] = getModuleParameter('Awards', 'SecondLanguageCode', '', $TourId);
	$SecondLanguage = getModuleParameter('Awards','SecondLanguage',0,$TourId);
	while ($r=safe_fetch($q)) {
		if(substr($r->AwEvent, 0 , 7)=='Custom-') {
			list($dum, $num) = explode('-', $r->AwEvent);
			$json_array = Array("Event"=>$EvCode, "Type"=>$EvType, "Results"=>array());
			$tmp = array("Rank"=>1, "AwardName"=>"");
			if($EvType==0) {
				$tmp += array("Id"=>'', "FamilyName"=>getModuleParameter('Awards','Aw-CustomWinner-1-'. $num), "GivenName"=>'', "NameOrder"=>'0', "Gender"=>'');
			}
			$tmp += array("TeamCode"=>'', "TeamName"=>getModuleParameter('Awards','Aw-CustomNation-1-'. $num));
			$tmp["AwardName"] = 'Winner';

			$json_array["Results"][] = $tmp;

		} else {
			$options['tournament'] = $TourId;
			$options['eventsR'] = $EvCode;
			$options['alpha'] = true; // issue 284 bitbucket
			$options['cutRank'] = max(explode(",",$r->AwPositions));
			$rank=null;
			if($EvType) {
				$rank=Obj_RankFactory::create('FinalTeam',$options);
			} else {
				$rank=Obj_RankFactory::create('FinalInd',$options);
			}
			$rank->read();
			$Data=$rank->getData();

			foreach($Data['sections'] as $kSec=>$vSec) {
				$json_array = Array("Event"=>$EvCode, "Type"=>$EvType, "Results"=>array());
				foreach($vSec['items'] as $kItem=>$vItem) {
					$tmp = array("Rank"=>$vItem["rank"], "AwardName"=>"");
					if($EvType==0) {
						$tmp += array("Id"=>$vItem["bib"], "FamilyName"=>$vItem["familyname"], "GivenName"=>$vItem["givenname"], "NameOrder"=>$vItem["nameOrder"], "Gender"=>$vItem["gender"]);
					}
					$tmp += array("TeamCode"=>$vItem["countryCode"], "TeamName"=>$vItem["countryName"]);
					if($EvType==1) {
						$tmpAth=array();
						foreach($vItem["athletes"] as $kAth=>$vAth) {
							$tmpAth[$kAth]= array("Id"=>$vAth["bib"], "FamilyName"=>$vAth["familyname"], "GivenName"=>$vAth["givenname"], "NameOrder"=>$vAth["nameOrder"], "Gender"=>$vAth["gender"]);
						}
						$tmp["Components"] = $tmpAth;
					}
					$tmp["AwardName"] = getModuleParameter('Awards', 'Aw-Med'.$vItem["rank"].'-1','',$TourId);

					$json_array["Results"][] = $tmp;
				}
			}
		}
	}
}


// Return the json structure with the callback function that is needed by the app
SendResult($json_array);
