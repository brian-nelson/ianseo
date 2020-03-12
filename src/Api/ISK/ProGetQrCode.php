<?php
$SkipCompCode=true;
require_once(dirname(__FILE__) . '/config.php');

$battLevel = 0;
if(!empty($_REQUEST['batt'])) {
	$tmp = explode("-",$_REQUEST['batt']);
	$battLevel = intval($tmp[0]) * ($tmp[1] ? -1 : 1);
}
$tgtReq = (empty($_REQUEST['tar']) ? 0 : $_REQUEST['tar']);

$JsonResponse=array("error"=>0,"qrcode"=>"", "msg"=>"");

safe_w_SQL("UPDATE IskDevices SET
	IskDvTargetReq='{$tgtReq}', IskDvBattery='{$battLevel}'
	WHERE IskDvDevice='{$DeviceId}'");

$q=safe_r_sql("SELECT IskDvState, IskDvTournament FROM IskDevices WHERE IskDvDevice='{$DeviceId}'");
if(safe_num_rows($q)==1) {
	$r = safe_fetch($q);
	// updates all the status of the devices that requested authorization and were already authorized to 2 (code to be sent)
	safe_w_sql("update IskDevices set IskDvState=2, IskDvAuthRequest=0 where IskDvTournament=$r->IskDvTournament and IskDvAuthRequest=1 and IskDvState=1");
	if($r->IskDvState==0) {
		$JsonResponse["error"] = 1;
	} else {
		if($tmp=getQrCode('2,3')) { // asks for a code to be sent or already sent
			$JsonResponse["qrcode"] = $tmp;
			// device goes in "qrcode sent, waiting for confirmation" state
			$q=safe_r_sql("update IskDevices set IskDvState=3, IskDvAuthRequest=0  WHERE IskDvDevice='{$DeviceId}'");
		}
	}
}

SendResult($JsonResponse);