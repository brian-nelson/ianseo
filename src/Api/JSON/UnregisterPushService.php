<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Fun_Modules.php');

$TourId = 0;
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
	$TourId = getIdFromCode($_REQUEST['CompCode']);
}

$ClientId = "";
if(isset($_REQUEST['ClientId']) && preg_match("/^[a-z0-9]+$/i", $_REQUEST['ClientId'])) {
	$ClientId = $_REQUEST['ClientId'];
}

$json_array=array("Success" => false );
if(!empty($TourId) && !empty($ClientId)) {

	$tmpFetch = array();
	$Targets = getModuleParameter('Jack', "HandShake", array(), $TourId);
	if(!empty($Targets["API-JSON"])) {
		$tmpFetch = $Targets["API-JSON"]["extraparams"];
		if(isset($tmpFetch[$ClientId])){
			unset($tmpFetch[$ClientId]);
			if(count($tmpFetch)!=0) {
				registerJack("HandShake", "API-JSON", dirname(__FILE__).'/JackInclude.php', 'JackRunUpdate_Check();',$TourId, $tmpFetch);
			} else {
				removeJack("FinArrUpdate", "API-JSON",$TourId);
				removeJack("FinLiveUpdate", "API-JSON",$TourId);
				removeJack("FinConfirmEnd", "API-JSON",$TourId);
				removeJack("FinShootingFirst", "API-JSON",$TourId);
				removeJack("FinRankUpdate", "API-JSON",$TourId);
				removeJack("Wind", "API-JSON",$TourId);
				removeJack("ArrowSpeed", "API-JSON",$TourId);
				removeJack("Timing", "API-JSON",$TourId);
				removeJack("HandShake", "API-JSON",$TourId);
			}
			$json_array["Success"] = true;
			$json_array["Timestamp"] = $_SERVER['REQUEST_TIME'];
			$json_array["ClientId"] = $ClientId;
		}
	}

}

SendResult($json_array);