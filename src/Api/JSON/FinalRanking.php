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

if(isset($_REQUEST["CutPosition"]) && preg_match("/^[0-9]+$/i", $_REQUEST['CutPosition'])) {
	$options['cutRank'] = $_REQUEST['CutPosition'];
}

$json_array=array();

$options['tournament']=$TourId;
$options['eventsR']=$EvCode;

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
		$tmp = array("Rank"=>$vItem["rank"]);
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
		//$tmp += array("Score"=>$vItem["score"], "Gold"=>$vItem["gold"], "XNine"=>$vItem["xnine"]);

		$json_array["Results"][] = $tmp;
	}
}

// Return the json structure with the callback function that is needed by the app
SendResult($json_array);
