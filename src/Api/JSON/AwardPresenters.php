<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Fun_Modules.php');

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
		$Awards=array();
		if($r->AwAwarderGrouping) {
			$Awards=@unserialize($r->AwAwarderGrouping);
		}
		$json_array["Languages"][$json_array["MainLanguage"]] = array();
		$json_array["Languages"][$json_array["SecondLanguage"]] = array();
		foreach($Awards as $k=>$v) {
			@list($name, $charge)=preg_split("/[,\n]/", getModuleParameter('Awards', 'Aw-Awarder-1-'.$v, '', $TourId), 2);
			if(empty($charge)) {
				@list($name, $charge)=explode(' - ', $name, 2);
			}

			if(is_numeric($k)) {
				$complete=get_text_eval(getModuleParameter('Awards', 'Aw-Award-1-'.$k, '', $TourId), getModuleParameter('Awards', 'Aw-Awarder-1-'.$v, '', $TourId));
			} else {
				$complete=get_text_eval(getModuleParameter('Awards', 'Aw-Special-1', '', $TourId), getModuleParameter('Awards', 'Aw-Awarder-1-'.$v, '', $TourId));
				continue;
			}

			$json_array["Languages"][$json_array["MainLanguage"]][] = array('CompleteText'=>trim($complete),'Name'=>trim($name), 'Function'=>trim($charge));

			if($SecondLanguage) {
				@list($name, $charge)=preg_split("/[,\n]/", getModuleParameter('Awards', 'Aw-Awarder-2-'.$v, '', $TourId), 2);
				if(empty($charge)) {
					@list($name, $charge)=preg_split(' - ', $name, 2);
				}

				if(is_numeric($k)) {
					$complete=get_text_eval(getModuleParameter('Awards', 'Aw-Award-2-'.$k, '', $TourId), getModuleParameter('Awards', 'Aw-Awarder-2-'.$v, '', $TourId));
				} else {
					$complete=get_text_eval(getModuleParameter('Awards', 'Aw-Special-2', '', $TourId), getModuleParameter('Awards', 'Aw-Awarder-2-'.$v, '', $TourId));
					continue;
				}
				$json_array["Languages"][$json_array["SecondLanguage"]][] = array('CompleteText'=>trim($complete),'Name'=>trim($name), 'Function'=>trim($charge));
			}
		}
	}
	if(!$SecondLanguage) {
		unset($json_array["Languages"][$json_array["SecondLanguage"]]);
		unset($json_array["SecondLanguage"]);
	}
}


// Return the json structure with the callback function that is needed by the app
SendResult($json_array);