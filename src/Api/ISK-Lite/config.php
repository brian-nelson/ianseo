<?php

$AppMinVersion='0.8.5';
$AppMaxVersion='1.9.9';

require_once(dirname(dirname(__FILE__)) . '/config.php');

// if there is no callback no need to go further...
if(empty($_REQUEST["callback"])) die();


$CompCode = (empty($_REQUEST['compcode']) ? '' : $_REQUEST['compcode']);
$DeviceId = (empty($_REQUEST['devid']) ? '' : $_REQUEST['devid']);

// should it be worth to send back an error to the device?
if(!$CompCode) {
	if(empty($SkipCompCode)) SendResult(array('error' => get_text('ISK-Lite-NoCompCode', 'Api')));
} else {
	$CompId=getIdFromCode($CompCode);
}

function SendResult($Result) {
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');

	echo $_REQUEST["callback"] . '(' . json_encode($Result) . ')';
	exit();
}

function getGroupedTargets($TargetNo, $Session=0, $SesType='Q', $SesPhase='') {
	global $CompId;
	// get all targets associated/grouped together with the target requested
	$SubSelect="select TgGroup, TgSession, TgSesType
		from TargetGroups
		where TgTournament=$CompId
		and TgTargetNo='$TargetNo'";
	if($SesType!='Q') {
		$SubSelect.=" and TgSesType='{$SesType}{$SesPhase}'";
	}
	$Tmp=array();
	$q=safe_r_sql("Select TgTargetNo
		from TargetGroups
		where TgTournament=$CompId
		and (TgGroup, TgSession, TgSesType)=($SubSelect) order by TgTargetNo");
	while($r=safe_fetch($q)) $Tmp[]=$r->TgTargetNo;
	if($Tmp) $TargetNo=implode("','", $Tmp);
	return $TargetNo;
}