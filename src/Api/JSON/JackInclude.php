<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Fun_Modules.php');

define("TYPE_HANDSHAKE","HandShake");
define("TYPE_LIVEUPDATE","LiveMatchUpdated");
define("TYPE_MATCHUPDATE","MatchUpdated");
define("TYPE_RANKUPDATE","RankUpdated");
define("TYPE_WIND","Wind");
define("TYPE_ARROWSPEED","ArrowSpeed");
define("TYPE_TIME","Time");

function JackRunUpdate_MatchUpdate($Event, $Team, $MatchNo, $TourId) {
	$Targets = getModuleParameter('Jack', "HandShake", array(), $TourId);
	$MatchNo = ($MatchNo % 2 == 0 ? $MatchNo : $MatchNo-1);
	if(!empty($Targets["API-JSON"])) {
		if(!empty($Targets["API-JSON"]["extraparams"])) {
			foreach ($Targets["API-JSON"]["extraparams"] as $k=>$v) {
				$Response = "Notification=".TYPE_MATCHUPDATE."&ClientId={$k}&Timestap=".time()."&Event={$Event}&Type={$Team}&MatchId={$MatchNo}&CompCode=".getCodeFromId($TourId);
				$ch = curl_init();
				curl_setopt($ch,CURLOPT_URL, $Targets["API-JSON"]["extraparams"][$k]["Address"]);
				curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 1);
				curl_setopt($ch,CURLOPT_TIMEOUT, 2);
				curl_setopt($ch,CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
				curl_setopt($ch,CURLOPT_POSTFIELDS, $Response);
				$result = curl_exec($ch);
				curl_close($ch);
//				echo "--Jack--\t$Response\t$result\n";
			}
		}
	}
}

function JackRunUpdate_RankUpdate($Event, $Team, $TourId) {
	$Targets = getModuleParameter('Jack', "HandShake", array(), $TourId);
	if(!empty($Targets["API-JSON"])) {
		if(!empty($Targets["API-JSON"]["extraparams"])) {
			foreach ($Targets["API-JSON"]["extraparams"] as $k=>$v) {
				$Response = "Notification=".TYPE_RANKUPDATE."&ClientId={$k}&Timestap=".time()."&Event={$Event}&Type={$Team}&CompCode=".getCodeFromId($TourId);
				$ch = curl_init();
				curl_setopt($ch,CURLOPT_URL, $Targets["API-JSON"]["extraparams"][$k]["Address"]);
				curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 1);
				curl_setopt($ch,CURLOPT_TIMEOUT, 2);
				curl_setopt($ch,CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
				curl_setopt($ch,CURLOPT_POSTFIELDS, $Response);
				$result = curl_exec($ch);
				curl_close($ch);
				//				echo "--Jack--\t$Response\t$result\n";
			}
		}
	}
}

function JackRunUpdate_ArrowSpeed($Speed, $UM, $TourId) {
	$Targets = getModuleParameter('Jack', "HandShake", array(), $TourId);
	if(!empty($Targets["API-JSON"])) {
		if(!empty($Targets["API-JSON"]["extraparams"])) {
			foreach ($Targets["API-JSON"]["extraparams"] as $k=>$v) {
				$Response = "Notification=".TYPE_ARROWSPEED."&ClientId={$k}&Timestap=".time()."&ArrowSpeed={$Speed}&ArrowSpeedUnit={$UM}";
				$ch = curl_init();
				curl_setopt($ch,CURLOPT_URL, $Targets["API-JSON"]["extraparams"][$k]["Address"]);
				curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 1);
				curl_setopt($ch,CURLOPT_TIMEOUT, 2);
				curl_setopt($ch,CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
				curl_setopt($ch,CURLOPT_POSTFIELDS, $Response);
				$result = curl_exec($ch);
				curl_close($ch);
//				echo "--Jack--\n\t$Response\n\t$result\n";
			}
		}
	}
}

function JackRunUpdate_Wind($Speed, $Direction, $UM, $TourId) {
	$Targets = getModuleParameter('Jack', "HandShake", array(), $TourId);
	if(!empty($Targets["API-JSON"])) {
		if(!empty($Targets["API-JSON"]["extraparams"])) {
			foreach ($Targets["API-JSON"]["extraparams"] as $k=>$v) {
				$Response = "Notification=".TYPE_WIND."&ClientId={$k}&Timestap=".time()."&WindSpeed={$Speed}&WindSpeedUnit={$UM}&WindDirection={$Direction}";
				$ch = curl_init();
				curl_setopt($ch,CURLOPT_URL, $Targets["API-JSON"]["extraparams"][$k]["Address"]);
				curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 1);
				curl_setopt($ch,CURLOPT_TIMEOUT, 2);
				curl_setopt($ch,CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
				curl_setopt($ch,CURLOPT_POSTFIELDS, $Response);
				$result = curl_exec($ch);
				curl_close($ch);
//				echo "--Jack--\n\t$Response\n\t$result\n";
			}
		}
	}
}

function JackRunUpdate_Time($Time, $Side, $TourId) {
	$Targets = getModuleParameter('Jack', "HandShake", array(), $TourId);
	if(!empty($Targets["API-JSON"])) {
		if(!empty($Targets["API-JSON"]["extraparams"])) {
			foreach ($Targets["API-JSON"]["extraparams"] as $k=>$v) {
				$Response = "Notification=".TYPE_TIME."&ClientId={$k}&Timestap=".time()."&TimeValue={$Time}&Side={$Side}";
				$ch = curl_init();
				curl_setopt($ch,CURLOPT_URL, $Targets["API-JSON"]["extraparams"][$k]["Address"]);
				curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 1);
				curl_setopt($ch,CURLOPT_TIMEOUT, 2);
				curl_setopt($ch,CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
				curl_setopt($ch,CURLOPT_POSTFIELDS, $Response);
				$result = curl_exec($ch);
				curl_close($ch);
//				echo "--Jack--\t$Response\t$result\n";
			}
		}
	}
}

function JackRunUpdate_LiveUpdate($Event, $Team, $MatchNo, $TourId) {
	$Targets = getModuleParameter('Jack', "HandShake", array(), $TourId);
	$MatchNo = ($MatchNo % 2 == 0 ? $MatchNo : $MatchNo-1);
	if(!empty($Targets["API-JSON"])) {
		if(!empty($Targets["API-JSON"]["extraparams"])) {
			foreach ($Targets["API-JSON"]["extraparams"] as $k=>$v) {
				$Response = "Notification=".TYPE_LIVEUPDATE."&ClientId={$k}&Timestap=".time()."&CompCode=".getCodeFromId($TourId);
				$ch = curl_init();
				curl_setopt($ch,CURLOPT_URL, $Targets["API-JSON"]["extraparams"][$k]["Address"]);
				curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 1);
				curl_setopt($ch,CURLOPT_TIMEOUT, 2);
				curl_setopt($ch,CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
				curl_setopt($ch,CURLOPT_POSTFIELDS, $Response);
				$result = curl_exec($ch);
				curl_close($ch);
//				echo "--Jack--\t$Response\t$result\n";
			}
		}
	}
}

function JackRunUpdate_TimeSide($Event, $Team, $MatchNo, $TourId) {
//	echo "--JACK--TimeSide\n";
}

function JackRunUpdate_Check($ClientId) {
	global $Targets;
	if(isset($Targets["API-JSON"]["extraparams"][$ClientId])) {
		$Response = "Notification=".TYPE_HANDSHAKE."&ClientId=".$ClientId."&Timestap=".time();
		$ch = curl_init();
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $Targets["API-JSON"]["extraparams"][$ClientId]["Address"]);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($ch,CURLOPT_TIMEOUT, 2);
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $Response);
		//execute post
		$result = curl_exec($ch);
		//close connection
		curl_close($ch);
	}
}
