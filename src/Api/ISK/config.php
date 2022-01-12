<?php

$AppMinVersion='0.8.5';
$AppMaxVersion='2.99.99';
$SKIP_AUTH=true;

/*
 * Flags meaning for IskDvState
 * 0: unknown/unauthorized
 * 1: authorized and running
 * 2: authorized and new barcode to be sent
 * 3: authorized and barconde sent, waiting for confirmation
 * 4: authorized and new msg to be sent
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Fun_Modules.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Final/Fun_MatchTotal.inc.php');
require_once(dirname(__FILE__).'/Lib.php');

$CompId = 0;
$CompCode = (empty($_REQUEST['compcode']) ? '' : $_REQUEST['compcode']);
$CompPin = '';
$DeviceId = (empty($_REQUEST['devid']) ? '' : $_REQUEST['devid']);
$DEVICE=''; // will contain all data from IskDevices!

// if there is no callback no need to go further...
if(!defined('IN_IANSEO') and empty($_REQUEST["callback"])) die();


// should it be worth to send back an error to the device?
if(!$CompCode) {
	if(empty($SkipCompCode)) SendResult(array('error' => get_text('ISK-NoCompCode', 'Api')));
} else {
    if(($sepPosition = strpos($CompCode,'|'))!==false) {
        $CompPin=substr($CompCode,$sepPosition+1);
        $CompCode=substr($CompCode,0,$sepPosition);
    }
	$CompId=getIdFromCode($CompCode);
	if(!$CompId) SendResult(array('error' => get_text('ISK-BadCompCode', 'Api')));
}

$iskModePro = false; // competition mode
$iskAppPro=false; // device type
$iskStopAutoImport = 0;
$iskStickyEnds = array();
if(empty($SkipCompCode)) {
    //Check PIN
    $tmpPin = getModuleParameter('ISK', 'ServerUrlPin', '', $CompId);
    if(!empty($tmpPin) AND $CompPin != $tmpPin) {
        die();
    }
	//Get Isk Options
	if($tmp=getModuleParameter('ISK', 'Mode', '', $CompId)) {
		if($tmp=='pro') {
            $iskModePro = true;
        }
	} else {
		die();
	}
	$iskStopAutoImport=getModuleParameter('ISK', 'StopAutoImport', 0, $CompId);
	$iskStickyEnds=getModuleParameter('ISK', 'StickyEnds', array(), $CompId);
	define('IMPORT_TYPE', $iskModePro ? 0 : getModuleParameter('ISK', 'ImportType', 0, $CompId));
    define('RKCALC_DivClI', $iskModePro ? 0 : getModuleParameter('ISK','CalcClDivInd',0, $CompId));
    define('RKCALC_DivClT', $iskModePro ? 0 : getModuleParameter('ISK','CalcClDivTeam',0, $CompId));
    define('RKCALC_FinI', $iskModePro ? 0 : getModuleParameter('ISK','CalcFinInd',0, $CompId));
    define('RKCALC_FinT', $iskModePro ? 0 : getModuleParameter('ISK','CalcFinTeam',0, $CompId));
}

if(empty($SkipDeviceCheck) and !defined('IN_IANSEO') and !checkDeviceApp(empty($SkipCompCode))) die();

function SendResult($Result) {
	if(defined('IN_IANSEO')) return($Result);
	UpdateLastSeen();
	JsonOut($Result, 'callback');
}

function SendResetIsk($DevCode, $Version, $IsPro) {
	global $DEVICE;
	// resets the status of the device to "wait for QrCode"
	safe_w_sql("update IskDevices set IskDvState=2 where IskDvCode='$DevCode'");

	$DefAnswer=array('onlypro'=>true, 'devicecode'=>$DevCode, 'error' => get_text('ISK-OnlyProAloud', 'Api'));
	if(!$IsPro) {
		$v=explode('.', $Version);
		for($n=count($v); $n<3; $n++) {
			$v[]=0;
		}

		if(sprintf("%03s-%03s-%04s", $v[0], $v[1], $v[2])<"001-001-0000" and !$IsPro) {
			// old lite version...
			$DefAnswer='';
		}

	}

	JsonOut($DefAnswer, 'callback');
}



/**
 * Given a device ID returns the associated QRcode
 * @param string $States The states to ask for, defaults to OK, to send and send
 */
function getQrCode($States = '1,2,3') {
	global $DeviceId,$CFG;
	$Opts=array();
	$q=safe_r_sql("select IskDevices.*, ToCode from IskDevices inner join Tournament on IskDvTournament=ToId where (IskDvState in ($States) or (IskDvAuthRequest=1 and IskDvState=1)) and IskDvDevice=".StrSafe_DB($DeviceId));
	if($r=safe_fetch($q)) {
		// Check the competition actually is set up to use with the app!
		if(!getModuleParameter('ISK', 'Mode', '', $r->IskDvTournament)) {
			return "";
		}

		// Get the sequence if any...
		$tmp = getModuleParameter('ISK', 'Sequence', array("type"=>'', "session"=>'', "distance"=>'',  "maxdist"=>'', "end"=>''),$r->IskDvTournament);
		if(!isset($tmp["type"])) {
			delModuleParameter('ISK', 'Sequence', $r->IskDvTournament);
			$tmp = getModuleParameter('ISK', 'Sequence', array("type"=>'', "session"=>'', "distance"=>'',  "maxdist"=>'', "end"=>''),$r->IskDvTournament);
		}

		// check if the device has a target assigned
		if(!($r->IskDvTarget and $tmp["type"])) {
			// reset the device!
			SendResetIsk($r->IskDvCode, $r->IskDvVersion, $r->IskDvAppVersion);
		}

		// gets infos from device
        $tmpPin=getModuleParameter('ISK', 'ServerUrlPin', '', $r->IskDvTournament);
		$Opts['u']=getModuleParameter('ISK', 'ServerUrl', '', $r->IskDvTournament).$CFG->ROOT_DIR; // .'Api/ISK-Lite/';
		$Opts['c']=$r->ToCode . (empty($tmpPin) ? '' : '|'.$tmpPin);

		switch ($tmp["type"]) {
			case 'Q':
				// check if there is a target assigned to that target...
				$q=safe_r_sql("select QuId from Qualifications inner join Entries on QuId=EnId and EnTournament={$r->IskDvTournament} where QuSession='{$tmp["session"]}' and substr(QuTargetNo, -4,3)+0= $r->IskDvTarget");
				if(!safe_num_rows($q)) {
					// no available targets !
					SendResetIsk($r->IskDvCode, $r->IskDvVersion, $r->IskDvAppVersion);
				}
				$Opts['st'] = 'Q';
				$Opts['s'] = (string) $tmp["session"];
				$Opts['d'] = (string) $tmp["distance"];
				$Opts['t'] = str_pad($r->IskDvTarget,3,"0",STR_PAD_LEFT);
				if(intval($tmp["end"]))
					$Opts['e'] = (string) $tmp["end"];
				break;
			case 'E':
				// check if there is a target assigned to that target...
				$q=safe_r_sql("select ElId, ElEventCode 
					from Eliminations 
					where ElTournament={$r->IskDvTournament} and ElSession='{$tmp["session"]}' and ElElimPhase={$tmp['distance']} and substr(ElTargetNo, -4,3)+0= $r->IskDvTarget");
				if(!safe_num_rows($q)) {
					// no available targets !
					SendResetIsk($r->IskDvCode, $r->IskDvVersion, $r->IskDvAppVersion);
				}
				$r2=safe_fetch($q);
				$Opts['st'] = 'E'.($tmp['distance']+1);
				$Opts['s'] = (string) $r2->ElEventCode;
				$Opts['d'] = '1';
				$Opts['t'] = 'E'.($tmp['distance']+1).'|'.$r2->ElEventCode.'|'.str_pad($r->IskDvTarget,3,"0",STR_PAD_LEFT);
				if(intval($tmp["end"]))
					$Opts['e'] = (string) $tmp["end"];
				break;
			case 'I':
			case 'T':
				$Opts['st'] = 'M'.$tmp["type"];
				$q=safe_r_SQL("SELECT FSEvent, FSMatchNo, GrPhase
					FROM FinSchedule
					INNER JOIN Grids ON FSMatchNo=GrMatchNo
					WHERE FSTournament=" . $r->IskDvTournament . " AND FsTeamEvent=" . ($tmp["type"]=='I' ? "0":"1") . "
					AND CONCAT(FSScheduledDate,FSScheduledTime)=" . StrSafe_DB($tmp["session"]) . " AND FSTarget=" . StrSafe_DB(str_pad($r->IskDvTarget,3,"0",STR_PAD_LEFT))."
					order by FsMatchNo");
				if($r2=safe_fetch($q) and $r2->FSMatchNo%2==0) {
					$Opts['s'] = $r2->FSEvent;
					$Opts['d'] = (string) $r2->FSMatchNo;
					$Opts['t'] = (string) $r2->GrPhase;
					if(intval($tmp["end"])) {
						$Opts['e'] = (string) $tmp["end"];
					}
				} else {
					SendResetIsk($r->IskDvCode, $r->IskDvVersion, $r->IskDvAppVersion);
				}
				break;
		}
	}
	if(count($Opts))
		return $Opts;
	else
		return "";
}

function checkDeviceApp($chkCompetition) {
	global $DeviceId, $CompId, $iskModePro, $CFG, $iskAppPro, $DEVICE;
	$q=safe_r_sql("SELECT * FROM IskDevices WHERE IskDvDevice='{$DeviceId}'");
	if(safe_num_rows($q)==0) {
		$Version=(empty($_REQUEST['version']) ? '' : preg_replace('/[^a-z0-9.-]/sim', '', $_REQUEST['version']));
		$AppVersion=(empty($_REQUEST['t']) || $_REQUEST['t']!='p' ? 0 : 1);
		if($AppVersion) $iskAppPro=true;
		$iskCode="0001";
		$q=safe_r_sql("SELECT IskDvCode FROM IskDevices where length(IskDvCode)=4 ORDER BY IskDvCode DESC");
		if($r=safe_fetch($q)) {
			$iskCode = str_pad(base_convert(base_convert($r->IskDvCode,36,10)+1,10,36),4,'0',STR_PAD_LEFT);
		}
		safe_w_SQL("INSERT INTO IskDevices
			(IskDvTournament, IskDvDevice, IskDvCode, IskDvVersion, IskDvAppVersion, IskDvState, IskDvIpAddress, IskDvLastSeen) VALUES
			('{$CompId}', '{$DeviceId}', '{$iskCode}', '{$Version}', {$AppVersion}, 0, '" . $_SERVER["REMOTE_ADDR"] . "', '".date('Y-m-d H:i:s')."')");

		$q=safe_r_sql("SELECT * FROM IskDevices WHERE IskDvDevice='{$DeviceId}'");
		$DEVICE=safe_fetch($q);
		if($iskModePro) {
			SendResetIsk($DEVICE->IskDvCode, $DEVICE->IskDvVersion, $DEVICE->IskDvAppVersion);
		} else {
			return true;
		}
	} else {
		$Version='';
		$AppVersion='';
		$NewCode='';
		$DEVICE=safe_fetch($q);
		if(strlen($DEVICE->IskDvCode)!=4) {
			$DEVICE->IskDvCode=str_pad($DEVICE->IskDvCode,4,'0',STR_PAD_LEFT);
			$NewCode=", IskDvCode='{$DEVICE->IskDvCode}'";
		}
		$iskAppPro=$DEVICE->IskDvAppVersion;
		if($DeviceId) {
			if(!empty($_REQUEST['version'])) {
				$Version=", IskDvVersion='".preg_replace('/[^a-z0-9.-]/sim', '', $_REQUEST['version'])."'";
			}
			if(!empty($_REQUEST['t']) and $_REQUEST['t']=='p') {
				$AppVersion=', IskDvAppVersion=1';
				$iskAppPro=true;
			}
		}
		safe_w_SQL("UPDATE IskDevices SET
			IskDvIpAddress='" . $_SERVER["REMOTE_ADDR"] . "', IskDvLastSeen='".date('Y-m-d H:i:s')."' $AppVersion $Version $NewCode
			WHERE IskDvDevice='{$DeviceId}'");
		if($iskModePro && $chkCompetition && $DEVICE->IskDvTournament != $CompId) {
			SendResetIsk($DEVICE->IskDvCode, $DEVICE->IskDvVersion, $DEVICE->IskDvAppVersion);
		}
		if(!$iskModePro || ($DEVICE->IskDvAppVersion==1 && $DEVICE->IskDvState!=0)) {
			return true;
		} else {
			// IskPro for sure!
			SendResetIsk($DEVICE->IskDvCode, $DEVICE->IskDvVersion, $DEVICE->IskDvAppVersion);
		}
	}
}

function UpdateLastSeen() {
	global $DeviceId;
	if(!$DeviceId) return;
	safe_w_SQL("UPDATE IskDevices SET
		IskDvLastSeen='".date('Y-m-d H:i:s')."'
		WHERE IskDvDevice='{$DeviceId}'");
}

function getQualificationTotals($EnId, $dist=1, $end=1, $arr4End, $end4Dist, $G, $X9, $StartTarget=null) {
	global $CompId;
	$res=array('curendscore'=>0,'curscore'=>0,'curscoreatend'=>0,'curgold'=>0,'curxnine'=>0,'score'=>0,'scoreatend'=>0,'gold'=>0,'xnine'=>0);
	$SQL = "SELECT QuTargetNo, QuScore, QuGold, QuXnine, QuD{$dist}Score as dScore, QuD{$dist}Gold as dGold, QuD{$dist}Xnine as dXnine, QuD{$dist}Arrowstring as dArrowstring, (";
	for($i=1;$i<$dist;$i++)
		$SQL .= "QuD{$i}Score+";
	$SQL .= "0) as prevScore, max(DiDistance) as MaxDistance, group_concat(DiEnds*DiArrows order by DiDistance separator '|') as PadArrows,
       		QuD1Arrowstring, QuD2Arrowstring, QuD3Arrowstring, QuD4Arrowstring, QuD5Arrowstring, QuD6Arrowstring, QuD7Arrowstring, QuD8Arrowstring
       		FROM Qualifications 
       		innser join DistanceInformation on DiTournament=$CompId and DiType='Q' and DiSession=QuSession
       		WHERE QuId={$EnId}
       		group by QuId";
	$q=safe_r_sql($SQL);
	if($r=safe_fetch($q)) {
		$curArrowString=str_repeat(" ", $arr4End * $end4Dist);
		$SQL = "SELECT IskDtEndNo, IskDtArrowstring
			FROM IskData
			WHERE IskDtTournament={$CompId} AND IskDtMatchNo=0 AND IskDtEvent='' AND IskDtTeamInd=0 AND IskDtType='Q' AND IskDtTargetNo='{$r->QuTargetNo}' AND IskDtDistance={$dist}
			ORDER BY IskDtEndNo";
		$q=safe_r_sql($SQL);
		while($r2=safe_fetch($q)){
			$curArrowString = substr_replace($curArrowString, $r2->IskDtArrowstring, ($r2->IskDtEndNo-1)*$arr4End, $arr4End);
		}
		for($i=0; $i<($arr4End * $end4Dist); $i++) {
			if($curArrowString[$i]==' ' && isset($r->dArrowstring[$i])) {
				$curArrowString[$i]=$r->dArrowstring[$i];
			}
		}

		$tmp = ValutaArrowStringGX($curArrowString, $G, $X9);
		$res['curendarrstr']  = substr($curArrowString,($end-1)*$arr4End, $arr4End);
		$res['tilendarrstr']  = substr($curArrowString, 0, $end*$arr4End);
		$res['arrowstring']   = $curArrowString;
		$res['curendscore']   = ValutaArrowString($res['curendarrstr']);
		$res['curscore']      = $tmp[0];
		$res['curscoreatend'] = ValutaArrowString($res['tilendarrstr']);
		$res['curgold']       = $tmp[1];
		$res['curxnine']      = $tmp[2];
		$res['score']         = $r->QuScore-$r->dScore+$res['curscore'];
		$res['scoreatend']    = $r->prevScore+$res['curscoreatend'];
		$res['gold']          = $r->QuGold-$r->dGold+$res['curgold'];
		$res['xnine']         = $r->QuXnine-$r->dXnine+$res['curxnine'];
		$res['prevendscored'] = ($end==1 || (trim(substr($curArrowString, $arr4End*($end-2), $arr4End)) !=''));

		$res['arrowstrings']  = array();
		$PadArrows=explode('|', $r->PadArrows);
		foreach(range(1,$r->MaxDistance) as $d) {
			if($d==$dist) {
				$res['arrowstrings'][]=str_pad($curArrowString, $PadArrows[$d-1], ' ', STR_PAD_RIGHT);
			} else {
				$res['arrowstrings'][]=str_pad($r->{'QuD'.$d.'Arrowstring'}, $PadArrows[$d-1], ' ', STR_PAD_RIGHT);
			}
		}

		if(!is_null($StartTarget)) {
			// Field/3d, so we need to "circle" the arrowstring and the endnum to have the correct "alignment" with target archery
			$StartTarget=(($StartTarget-1)%$end4Dist)+1;
			$NewArrowstring=substr($curArrowString, $arr4End*($StartTarget-1)).substr($curArrowString, 0,$arr4End*($StartTarget-1));
			$NewEndNum=(($end+$end4Dist-$StartTarget)%$end4Dist)+1;

			$PrevTotal=ValutaArrowString(substr($NewArrowstring, 0, $NewEndNum*$arr4End));
			// Field or 3D, "circular" scorecard so need to adjust scores adding the already scored ends
			//$res['tilendarrstr']  = substr($curArrowString, 0, $end*$arr4End).$res['tilendarrstr'];
			$res['curscoreatend'] =$PrevTotal;
			$res['scoreatend']    =$r->prevScore+$PrevTotal;
			$res['prevendscored'] = ($end==1 || (trim(substr($res['tilendarrstr'],-2*$arr4End)) !=''));
			$res['prevendscored'] = ($NewEndNum==1 || (trim(substr($NewArrowstring, $arr4End*($NewEndNum-2), $arr4End)) !=''));
		}
	}
	return $res;
}

function importQualifications($EnId, $dist=1, $end=1) {
	global $CompId;
	$amended=array();
	$SQL="SELECT QuId, EnDivision, EnClass, EnSubClass, EnCountry, EnCountry2, EnCountry3, IF(EnCountry2=0,EnCountry,EnCountry2) as TeamCode, EnIndClEvent, EnTeamClEvent, EnIndFEvent, (EnTeamFEvent+EnTeamMixEvent) as EnTeamFEvent,
			QuTargetNo, QuD{$dist}Arrowstring as Arrowstring, IskDtArrowstring, IskDtEndNo, DIDistance, DIEnds, DIArrows, ToGoldsChars, ToXNineChars 
		from Qualifications
		INNER JOIN Entries ON QuId=EnId
		INNER JOIN Tournament ON ToId=EnTournament
		INNER JOIN DistanceInformation ON DITournament=EnTournament AND DISession=QuSession AND DIDistance=".StrSafe_DB($dist)." AND DIType='Q'
		INNER JOIN IskData ON iskDtTournament=EnTournament AND IskDtMatchNo=0 AND IskDtEvent='' AND IskDtTeamInd=0 AND IskDtType='Q' AND IskDtTargetNo=QuTargetNo AND IskDtDistance={$dist} AND IskDtEndNo={$end}
		WHERE EnTournament=$CompId and QuId={$EnId}";
	$q=safe_r_sql($SQL);
	while($r=safe_fetch($q)) {
		$arrowString = str_pad($r->Arrowstring,$r->DIArrows*$r->DIEnds);
		for($i=0; $i<$r->DIArrows; $i++){
			if($r->IskDtArrowstring[$i]!=' '){
				$arrowString[($r->IskDtEndNo-1)*$r->DIArrows+$i]=$r->IskDtArrowstring[$i];
			}
		}
		$Score=0;
		$Gold=0;
		$XNine=0;
		list($Score,$Gold,$XNine)=ValutaArrowStringGX($arrowString,$r->ToGoldsChars,$r->ToXNineChars);

		// Remove spaces from arrowstring and calc the hits using the actual # of arrows
		$trimmedArrowString = preg_replace("/[^a-zA-Z0-9]+/", "", $arrowString);
		$hits = strlen($trimmedArrowString);

		$Update = "UPDATE Qualifications SET
			QuD{$dist}Score={$Score}, QuD{$dist}Gold={$Gold}, QuD{$dist}Xnine={$XNine}, QuD{$dist}ArrowString='{$arrowString}', QuD{$dist}Hits=$hits,
			QuScore=QuD1Score+QuD2Score+QuD3Score+QuD4Score+QuD5Score+QuD6Score+QuD7Score+QuD8Score,
			QuGold=QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold+QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold,
			QuXnine=QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine+QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine,
			QuHits=QuD1Hits+QuD2Hits+QuD3Hits+QuD4Hits+QuD5Hits+QuD6Hits+QuD7Hits+QuD8Hits,
			QuTimestamp=" . StrSafe_DB(date('Y-m-d H:i:s')) . "
			WHERE QuId={$r->QuId}";
		safe_w_SQL($Update);

		$Update = "DELETE FROM IskData
			WHERE IskDtTournament={$CompId} AND IskDtMatchNo=0 AND IskDtEvent='' AND IskDtTeamInd=0 AND IskDtType='Q'
			AND IskDtTargetNo='{$r->QuTargetNo}' AND IskDtDistance={$dist} AND IskDtEndNo={$end} AND IskDtArrowstring='{$r->IskDtArrowstring}'";
		safe_w_SQL($Update);

		require_once('Qualification/Fun_Qualification.local.inc.php');
		$oldSes = (!empty($_SESSION["TourId"]) ? $_SESSION["TourId"] : 0);
		$_SESSION["TourId"]=$CompId;

        if(defined('RKCALC_DivClI') AND RKCALC_DivClI==0 AND $r->EnIndClEvent != 0) {
            Obj_RankFactory::create('DivClass', array('tournament' => $CompId, 'events' => $r->EnDivision . $r->EnClass, 'dist' => $dist))->calculate();
            Obj_RankFactory::create('DivClass', array('tournament' => $CompId, 'events' => $r->EnDivision . $r->EnClass, 'dist' => 0))->calculate();
        }
        if(defined('RKCALC_DivClT') AND RKCALC_DivClI==0 AND $r->EnTeamClEvent != 0) {
            MakeTeams($r->TeamCode, $r->EnDivision.$r->EnClass, $CompId);
        }

        if((defined('RKCALC_FinI') AND RKCALC_FinI==0) OR (defined('RKCALC_FinT') AND RKCALC_FinT==0)) {
			$SQL = "SELECT DISTINCT EvCode, EvTeamEvent, EvTeamCreationMode
				FROM Events
				INNER JOIN EventClass ON EvCode=EcCode AND EvTeamEvent=if(EcTeamEvent=0, 0, 1) AND EvTournament=EcTournament
				WHERE EvTournament={$CompId} AND EcClass='{$r->EnClass}' AND EcDivision='{$r->EnDivision}' and if(EcSubClass='', true, EcSubClass='{$r->EnSubClass}')
				ORDER BY EvTeamEvent, EvCode";
			$q2=safe_r_sql($SQL);
			while($r2=safe_fetch($q2)) {
				if(defined('RKCALC_FinI') AND RKCALC_FinI==0 AND $r2->EvTeamEvent==0 AND $r->EnIndFEvent!=0) {
					Obj_RankFactory::create('Abs',array('tournament'=>$CompId,'events'=>$r2->EvCode,'dist'=>$dist))->calculate();
					Obj_RankFactory::create('Abs',array('tournament'=>$CompId,'events'=>$r2->EvCode,'dist'=>0))->calculate();
					ResetShootoff($r2->EvCode,0,0, $CompId);
				}
                if(defined('RKCALC_FinT') AND RKCALC_FinT==0 AND $r2->EvTeamEvent==1 AND $r->EnTeamFEvent!=0) {
                    $calculateTeam=$r->TeamCode;
                    if($r2->EvTeamCreationMode==1) {
                        $calculateTeam=$r->EnCountry;
                    } elseif($r2->EvTeamCreationMode==2) {
                        $calculateTeam=$r->EnCountry2;
                    } elseif($r2->EvTeamCreationMode==3) {
                        $calculateTeam=$r->EnCountry3;
                    }
                    MakeTeamsAbs($calculateTeam, $r->EnDivision, $r->EnClass, $CompId);
				}
			}
		}
		if($oldSes!=0) {
			$_SESSION["TourId"] = $oldSes;
		}else {
			unset($_SESSION["TourId"]);
		}
	}
}

function getMatchTotals($Event, $MatchNo, $IndTeam, $end=1, $arr4End, $end4Match, $arr4So) {
	global $CompId;
	$isSO = ($end>$end4Match);
	$res=array('curendscore'=>0,'curscore'=>0,'curscoreatend'=>0,'curgold'=>0,'curxnine'=>0,'score'=>0,'scoreatend'=>0,'gold'=>0,'xnine'=>0);
	$tblHead = ($IndTeam==0 ? 'Fin' : 'Tf');
	$SQL = "SELECT {$tblHead}Arrowstring as arrowString, {$tblHead}Tiebreak as tieBreak
		FROM " . ($IndTeam==0 ? 'Finals' : 'TeamFinals') . "
		WHERE {$tblHead}Event='{$Event}' AND {$tblHead}MatchNo='{$MatchNo}' AND {$tblHead}Tournament={$CompId}";
	$q=safe_r_sql($SQL);
	if($r=safe_fetch($q)) {
		$curArrowString=str_repeat(" ", $arr4End * $end4Match);
		$curSoString=str_repeat(" ", $arr4So);
		$SQL = "SELECT IskDtEndNo, IskDtArrowstring
			FROM IskData
			WHERE IskDtTournament={$CompId} AND IskDtMatchNo={$MatchNo} AND IskDtEvent='{$Event}' AND IskDtTeamInd={$IndTeam} AND IskDtType='" . ($IndTeam==0 ? 'I':'T') . "' AND IskDtTargetNo='' AND IskDtDistance=0
			ORDER BY IskDtEndNo";
		$q=safe_r_sql($SQL);
		while($r2=safe_fetch($q)){
			if($r2->IskDtEndNo<=$end4Match)
				$curArrowString = substr_replace($curArrowString, $r2->IskDtArrowstring, ($r2->IskDtEndNo-1)*$arr4End, $arr4End);
			else
				$curSoString = substr_replace($curSoString, $r2->IskDtArrowstring, 0, $arr4So);
		}
		for($i=0; $i<($arr4End * $end4Match); $i++) {
			if($curArrowString[$i]==' ' && isset($r->arrowString[$i])) {
				$curArrowString[$i]=$r->arrowString[$i];
			}
		}
		for($i=0; $i<$arr4So; $i++) {
			if($curSoString[$i]==' ' && isset($r->tieBreak[$i])) {
				$curSoString[$i]=$r->tieBreak[$i];
			}
		}

		$tmpArr = ValutaArrowString($curArrowString);
		$tmpSo = ValutaArrowString($curSoString);
		$res['curendarrstr']  = $isSO ? $curSoString : substr($curArrowString,($end-1)*$arr4End, $arr4End);
		$res['tilendarrstr']  = $isSO ? $curSoString : substr($curArrowString, 0, $end*$arr4End);
		$res['arrowstring']   = $isSO ? $curSoString : $curArrowString;
		$res['curendscore']   = ValutaArrowString($res['curendarrstr']);
		$res['curscore']      = $isSO ? $tmpSo : $tmpArr;
		$res['curscoreatend'] = ValutaArrowString($res['tilendarrstr']);
		$res['curgold']       = 0;
		$res['curxnine']      = 0;
		$res['score']         = $res['curscore'];
		$res['scoreatend']    = $res['curscoreatend'];
		$res['gold']          = 0;
		$res['xnine']         = 0;
	}
	return $res;
}

function importMatches ($Event, $MatchNo, $IndTeam, $end, $arr4End, $end4Match, $arr4So, $ArrIndex=-1) {
	global $CompId, $iskModePro;
	$isSO = ($end>$end4Match);
	$tblHead = ($IndTeam==0 ? 'Fin' : 'Tf');
	$SQL = "SELECT {$tblHead}Arrowstring as Arrowstring, {$tblHead}Tiebreak as TieBreak, IskDtArrowstring, IskDtEndNo
		FROM " . ($IndTeam==0 ? 'Finals' : 'TeamFinals') . "
		INNER JOIN IskData ON IskDtTournament={$tblHead}Tournament AND IskDtMatchNo={$tblHead}MatchNo AND IskDtEvent={$tblHead}Event AND IskDtTeamInd={$IndTeam} AND IskDtType='" . ($IndTeam==0 ? 'I':'T') . "' AND IskDtTargetNo='' AND IskDtDistance=0
		WHERE {$tblHead}Event='{$Event}' AND {$tblHead}MatchNo='{$MatchNo}' AND {$tblHead}Tournament={$CompId} AND IskDtEndNo={$end}";
	$q=safe_r_sql($SQL);
	while($r=safe_fetch($q)) {
		$arrowString = ($isSO ? str_pad($r->TieBreak,$arr4So) : str_pad($r->Arrowstring,$arr4End));
		for($i=0; $i<($isSO ? $arr4So : $arr4End); $i++){
			if($i==$ArrIndex or $r->IskDtArrowstring[$i]!=' '){
				$arrowString[($isSO ? 0 : ($r->IskDtEndNo-1)*$arr4End)+$i]=$r->IskDtArrowstring[$i];
			}
		}
		$startPos = (($isSO ? ($arr4End*$end4Match) : 0) +1);

		// manage the closest to center
		$Closest=0;
		if($isSO) {
			$Closest=intval($arrowString!=strtoupper($arrowString));
			$arrowString=strtoupper($arrowString);
		}
		UpdateArrowString($MatchNo, $Event, $IndTeam, $arrowString, $startPos, ($startPos+($isSO ? $arr4So : $arr4End*$end4Match)-1), $CompId, $Closest);

		$Update = "DELETE FROM IskData
			WHERE IskDtTournament={$CompId} AND IskDtMatchNo={$MatchNo} AND IskDtEvent='{$Event}' AND IskDtTeamInd={$IndTeam} AND IskDtType='" . ($IndTeam==0 ? 'I':'T') . "'
			AND IskDtTargetNo='' AND IskDtDistance=0 AND IskDtEndNo={$end} AND IskDtArrowstring='{$r->IskDtArrowstring}'";
		safe_w_SQL($Update);
	}
}
