<?php
$SkipDeviceCheck=true;
$SkipCompCode=true;
require_once(dirname(__FILE__) . '/config.php');

$JsonResponse=array("auth"=>"NO", "code"=>"", 'isk' => 'NO');

// search if there is a QRcode passed along
$QR='';
if(!empty($_REQUEST['qr'])) {
	$QR=json_decode($_REQUEST['qr']);
	$CompCode=$QR->c;
	$CompId=getIdFromCode($CompCode);
	if(!$CompId) {
		$CompId=0;
	}
}

$SetCompId=0;

if($CompId) {
	// get the ISK-status of the competition...
	$q=safe_r_sql("select ToOptions from Tournament where ToId=$CompId");
	$r=safe_fetch($q);
	if(!empty($r->ToOptions)) {
		$tmp=unserialize($r->ToOptions);
		if(isset($tmp['UseApi'])) {
			switch($tmp['UseApi']) {
				case '1':
					$JsonResponse['isk']='LITE';
					$SetCompId=$CompId;
					break;
				case '2':
					$JsonResponse['isk']='PRO';
					break;
				case '3':
					$JsonResponse['isk']='LIVE';
					break;
			}
		}
	}
}

$q=safe_r_sql("SELECT * FROM IskDevices WHERE IskDvDevice='{$DeviceId}'");
if(safe_num_rows($q)==0) {
	$iskCode="a0";
	$q=safe_r_sql("SELECT IskDvCode FROM IskDevices ORDER BY IskDvCode DESC");
	if(safe_num_rows($q)) {
		$r=safe_fetch($q);
		$iskCode = base_convert(base_convert($r->IskDvCode,36,10)+1,10,36);
	}
	safe_w_SQL("INSERT INTO IskDevices
		(IskDvTournament, IskDvDevice, IskDvCode, IskDvAppVersion, IskDvState, IskDvIpAddress, IskDvLastSeen, IskDvAuthRequest) VALUES
		('{$SetCompId}', '{$DeviceId}', '{$iskCode}', 1, 0, '" . $_SERVER["REMOTE_ADDR"] . "', '".date('Y-m-d H:i:s')."', 1)");
	$JsonResponse["code"] = $iskCode;
} else {
	$r=safe_fetch($q);
	safe_w_SQL("UPDATE IskDevices SET
		".($SetCompId ? "IskDvTournament={$SetCompId}, " : (($JsonResponse['isk']=='PRO' and $r->IskDvTournament!=$CompId) ? 'IskDvTournament=0, ' : ''))."IskDvIpAddress='" . $_SERVER["REMOTE_ADDR"] . "', IskDvAppVersion=1, IskDvLastSeen='".date('Y-m-d H:i:s')."', IskDvAuthRequest=1
		WHERE IskDvDevice='{$DeviceId}'");
	$JsonResponse["code"] = $r->IskDvCode;
	if($r->IskDvState!=0)
		$JsonResponse["auth"] = "OK";
}

SendResult($JsonResponse);