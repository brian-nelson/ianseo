<?php


$SkipCompCode=true;
require_once(dirname(__FILE__) . '/config.php');

$qrcode = (empty($_REQUEST['qrcode']) ? "" : $_REQUEST['qrcode']);
$JsonResponse=array("error"=>1);

$OrgQrCode=getQrCode();
$ChkQrCode=urldecode($qrcode);
if($ChkQrCode) $ChkQrCode=json_decode($ChkQrCode);

$Resp=array();

if($OrgQrCode and $ChkQrCode) {
	$JsonResponse["error"]=0;
	foreach($OrgQrCode as $k=>$v) {
		// check the exact match of each element of the original QrCode against the equivalent element of the received QrCode
		if(!isset($ChkQrCode->{$k}) or $ChkQrCode->{$k}!=$v) {
			$JsonResponse["error"]=1;
			$Resp['sent'][$k]=$v;
		} else {
			unset($ChkQrCode->{$k}); // this to ensure the resulting ChkQrCode is empty if everything is OK
		}
	}
	$tmp=get_object_vars($ChkQrCode);
	if(!empty($tmp)) {
		// there were other elements that should not have been there!
		$JsonResponse["error"]=1;
		$Resp['received']=$ChkQrCode;
	}
	if(!$JsonResponse["error"]) {
		safe_w_SQL("UPDATE IskDevices SET IskDvState='1' WHERE IskDvDevice='{$DeviceId}' AND  IskDvState='3'");
	}
} elseif($OrgQrCode==$ChkQrCode or !$OrgQrCode) {
	$JsonResponse["error"]=0;
}

SendResult($JsonResponse);