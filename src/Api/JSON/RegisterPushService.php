<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Fun_Modules.php');

$TourId = 0;
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
	$TourId = getIdFromCode($_REQUEST['CompCode']);
}

$Address = "";
if(isset($_REQUEST['Address']) && filter_var($_REQUEST['Address'],FILTER_VALIDATE_URL)) {
	$Address = filter_var($_REQUEST['Address'],FILTER_SANITIZE_URL);
}

$json_array=array("Success" => false );
if(!empty($TourId) && !empty($Address)) {
	/*
	FinConfirmEnd
	FinShootingFirst
	FinArrUpdate
	FinLiveUpdate
	HandShake
	*/

	$tmpInsert = array();
	$Targets = getModuleParameter('Jack', "HandShake", array(), $TourId);
	if(!empty($Targets["API-JSON"]) && !empty($Targets["API-JSON"]["extraparams"])) {
		$tmpInsert = $Targets["API-JSON"]["extraparams"];
		foreach ($tmpInsert as $k=>$v) {
			if($v["Address"] == $Address) {
				unset($tmpInsert[$k]);
			}
		}
	}
	$ClientId = uniqid();
	$tmpInsert += array($ClientId => array("Lastseen"=>$_SERVER['REQUEST_TIME'],"Address"=>$Address));
	
	registerJack("FinArrUpdate", "API-JSON", dirname(__FILE__).'/JackInclude.php', 'JackRunUpdate_MatchUpdate(\'@Event\', @Team, @MatchNo, @TourId);',$TourId);
	registerJack("FinLiveUpdate", "API-JSON", dirname(__FILE__).'/JackInclude.php', 'JackRunUpdate_LiveUpdate(\'@Event\', @Team, @MatchNo, @TourId);',$TourId);
	registerJack("FinConfirmEnd", "API-JSON", dirname(__FILE__).'/JackInclude.php', 'JackRunUpdate_MatchUpdate(\'@Event\', @Team, @MatchNo, @TourId);',$TourId);
	registerJack("FinShootingFirst", "API-JSON", dirname(__FILE__).'/JackInclude.php', 'JackRunUpdate_MatchUpdate(\'@Event\', @Team, @MatchNo, @TourId);',$TourId);
	registerJack("FinRankUpdate", "API-JSON", dirname(__FILE__).'/JackInclude.php', 'JackRunUpdate_RankUpdate(\'@Event\', @Team, @TourId);',$TourId);
	registerJack("Wind", "API-JSON", dirname(__FILE__).'/JackInclude.php', 'JackRunUpdate_Wind(@WindSpeed, @WindDirection, \'@WindUM\', @TourId);',$TourId);
	registerJack("ArrowSpeed", "API-JSON", dirname(__FILE__).'/JackInclude.php', 'JackRunUpdate_ArrowSpeed(@ArrowSpeed, \'@ArrowUM\', @TourId);',$TourId);
	registerJack("Timing", "API-JSON", dirname(__FILE__).'/JackInclude.php', 'JackRunUpdate_Time(@Time, \'@Side\', @TourId);',$TourId);
	registerJack("HandShake", "API-JSON", dirname(__FILE__).'/JackInclude.php', 'JackRunUpdate_Check(\''.$ClientId.'\');',$TourId, $tmpInsert);
	$json_array["Success"] = true;
	$json_array["Timestamp"] = $_SERVER['REQUEST_TIME'];
	$json_array["ClientId"] = $ClientId;	
}

SendResult($json_array);