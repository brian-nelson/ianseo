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

$json_array=array("Success" => false, "Timestamp"=>$_SERVER['REQUEST_TIME']);
if(!empty($TourId)) {
	$tmpFetch = array();
	$Targets = getModuleParameter('Jack', "HandShake", array(), $TourId);
	if(!empty($Targets["API-JSON"])) {
		if(!empty($Targets["API-JSON"]["extraparams"])) {
			foreach ($Targets["API-JSON"]["extraparams"] as $k=>$v) {
				if(time()-$Targets["API-JSON"]["extraparams"][$k]["Lastseen"] > LastSeenTO) {
					unset($Targets["API-JSON"]["extraparams"][$k]);
				}
			}
			if(count($Targets["API-JSON"]["extraparams"])!=0) {
				registerJack("HandShake", "API-JSON", dirname(__FILE__).'/JackInclude.php', 'JackRunUpdate_Check(\''.$ClientId.'\');',$TourId, $Targets["API-JSON"]["extraparams"]);
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
		}
		if(!empty($ClientId) && isset($Targets["API-JSON"]["extraparams"][$ClientId])) {
			$json_array["Success"] = true;
			$json_array["ClientId"] = $ClientId;
			$Targets["API-JSON"]["extraparams"][$ClientId]["Lastseen"] = time();
			registerJack("HandShake", "API-JSON", dirname(__FILE__).'/JackInclude.php', 'JackRunUpdate_Check(\''.$ClientId.'\');',$TourId, $Targets["API-JSON"]["extraparams"]);
			runJack("HandShake",$TourId);
		} elseif(count($Targets["API-JSON"]['extraparams'])!=0) {
			$tmp = array();
			foreach($Targets["API-JSON"]['extraparams'] as $kX=>$vX) {
				$tmp[] = array_merge(array("Id"=>$kX),$vX);
			}
			$json_array["Clients"] = $tmp;
		}
	}

}

SendResult($json_array);