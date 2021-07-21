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
    if(($sepPosition = strpos($CompCode,'|'))!==false) {
        $CompPin=substr($CompCode,$sepPosition+1);
        $CompCode=substr($CompCode,0,$sepPosition);
    }
	$CompId=getIdFromCode($CompCode);
	if(!$CompId) {
		$CompId=0;
	}
}

if($CompId) {
	// get the ISK-status of the competition...
	$q=safe_r_sql("select ToOptions from Tournament where ToId=$CompId");
	$r=safe_fetch($q);
	if(!empty($r->ToOptions)) {
		$tmp=unserialize($r->ToOptions);
		if(isset($tmp['UseApi'])) {
            $tmpPin = getModuleParameter('ISK', 'ServerUrlPin', '', $CompId);
            if(empty($tmpPin) OR $CompPin == $tmpPin) {
                switch($tmp['UseApi']) {
                    case '1':
                        $JsonResponse['isk']='LITE';
                        break;
                    case '2':
                        $JsonResponse['isk']='PRO';
                        break;
                    case '3':
                        $JsonResponse['isk']='LIVE';
                        SendResult($JsonResponse);
                        die();
                        break;
                }
            } else {
                // not valid so stops here
                SendResult($JsonResponse);
            }
		}
	}
}

$checkTgtInPro= -1;
$SQL=array(
	"IskDvDevice='{$DeviceId}'",
	"IskDvAppVersion=1",
	"IskDvIpAddress='{$_SERVER['REMOTE_ADDR']}'",
	"IskDvLastSeen=".StrSafe_DB(date('Y-m-d H:i:s')),
	"IskDvAuthRequest=1",
	);

// check if some more info is there...
if(!empty($QR->st) and isset($QR->s) and isset($QR->t)) {
	// a complete scorecard QRcode has been requested, so we can already "prepare"" things on the device
	switch($QR->st) {
		case 'Q': // Qualifications
			$SQL[]="IskDvTargetReq=".intval($QR->t);
			break;
		case 'E1': // Eliminations
		case 'E2': // Eliminations
			$items=explode('|', $QR->t);
			$SQL[]="IskDvTargetReq=".intval($items[2]);
			break;
		case 'MI': // Individual matches
		case 'MT': // Team matches
			if(!isset($QR->d)) {
				break;
			}
			$q=safe_r_sql("select FsTarget from FinSchedule 
				where FsTournament=$CompId 
				and FSTeamEvent=".($QR->st=='MI' ? 0 : 1)."
				and FsEvent=".StrSafe_DB($QR->s)."
				and FsMatchNo=".intval($QR->d));
			if($r=safe_fetch($q)) {
				$SQL[]="IskDvTargetReq=".intval($r->FsTarget);
                $SQL[]="IskDvTarget=IF((IskDvTarget!=0 AND IskDvTournament={$CompId}),".intval($r->FsTarget).",IskDvTarget)";
                $checkTgtInPro = intval($r->FsTarget);
			}
			break;
	}
}


$q=safe_r_sql("SELECT * FROM IskDevices WHERE IskDvDevice='{$DeviceId}'");
if($r=safe_fetch($q)) {
	if($CompId) {
	    if(!($JsonResponse['isk']=='PRO' AND $checkTgtInPro != -1 AND $r->IskDvTarget!=$checkTgtInPro AND $r->IskDvTournament!=$CompId)) {
            $SQL[]="IskDvTournament={$CompId}";
        }
	} elseif($JsonResponse['isk']=='PRO' and $r->IskDvTournament!=$CompId) {
		$SQL[]='IskDvTournament=0';
	}
	// check the scanned code with what we have set in the device
	if(!$r->IskDvRunningConf) {
		$r->IskDvState=2;
		$SQL[] = "IskDvState=2";
	} else {
		$RunningConf=@json_decode($r->IskDvRunningConf);
		if($RunningConf!=$QR) {
			$SQL[] = "IskDvState=2";
			$SQL[] = "IskDvRunningConf=''";
			$r->IskDvState=2;
		}
	}
	safe_w_SQL("UPDATE IskDevices SET ".implode(',', $SQL)." WHERE IskDvDevice='{$DeviceId}'");
	$JsonResponse["code"] = $r->IskDvCode;
	if($r->IskDvState!=0) {
		$JsonResponse["auth"] = "OK";
	}
} else {
	$iskCode="0001";
	$q=safe_r_sql("SELECT IskDvCode FROM IskDevices ORDER BY IskDvCode DESC");
	if(safe_num_rows($q)) {
		$r=safe_fetch($q);
        $iskCode = str_pad(base_convert(base_convert($r->IskDvCode,36,10)+1,10,36),4,'0',STR_PAD_LEFT);
	}

	$SQL[]="IskDvCode='{$iskCode}'";
	$SQL[]="IskDvTournament={$CompId}";
	$SQL[]="IskDvState=0";

	safe_w_SQL("INSERT INTO IskDevices set ".implode(',', $SQL));
	$JsonResponse["code"] = $iskCode;
}
$JsonResponse['SQL']=$SQL;
SendResult($JsonResponse);
