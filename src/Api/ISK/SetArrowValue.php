<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Fun_Modules.php');

$TargetNo=!empty($_GET['qutarget']) ? $_GET['qutarget'] : 0;
list($Event,$EventTypeLetter,$MatchNo) = explode("|",(!empty($_GET['matchid']) ? $_GET['matchid'] : "0|I|0"));
$EventType=($EventTypeLetter=='T' ? 1 : 0);
$JsonResult=array();

$Error=1;

/*
-- compcode: code of the competition
-- qutarget: complete QuTargetNo
-- distance: distance
-- index: index of the arrow in the arrowstring
-- arrowsymbol: not the points, but the symbol (X, M, etc)

The page will return
$JsonResult['error']    = 1 if error, 0 if none
$JsonResult['qutarget'] = targetno
$JsonResult['dist']     = distance
$JsonResult['index']    = index of the arrow
$JsonResult['curscore'] = distance score
$JsonResult['curgold']  = distance golds
$JsonResult['curxnine'] = distance X/9
$JsonResult['score']    = total score
$JsonResult['gold']     = total golds
$JsonResult['xnine']    = total X/9

*/

$CanScore=true;
$CheckSequence=false;
$StopAutoImport=false;
// If competition is set to pro

if($iskModePro) {
	$CanScore=false;
	// ONLY PRO APP CAN SCORE!!!!
	if($iskAppPro) {
		// is there a sequence?
		if($QrCode=getQrCode()) {
			// device associated... check if the type is correct (ignore other types)
			if($TargetNo) {
				// Qualification and perhaps Eliminations (need to revise the code...)
				if(!in_array($QrCode['st'], array('Q', 'E1', 'E2'))) {
					SendResetIsk($DEVICE->IskDvCode, $DEVICE->IskDvVersion, $DEVICE->IskDvAppVersion);
				}
			} else {
				// should be match then!
				if($EventTypeLetter!=substr($QrCode['st'], 1)) {
					SendResetIsk($DEVICE->IskDvCode, $DEVICE->IskDvVersion, $DEVICE->IskDvAppVersion);
				}
			}
			// device should be OK... checks the sticky ends
			$StickyEnds=getModuleParameter('ISK', 'StickyEnds', array('SeqCode'=>'', 'Distance'=>'', 'Ends'=>array()), $CompId);
			if(empty($QrCode['st']) and empty($StickyEnds['SeqCode'])) {
				$CanScore=true;
			} else {
				// now check if the ends
				$CheckSequence=true;
			}
		} else {
			// no qrcode at all ... pro not associated in the DB... resets state AND competition to be safe
			safe_w_sql("UPDATE IskDevices SET
				IskDvTournament=0, IskDvState=0, IskDvLastSeen='".date('Y-m-d H:i:s')."'
				WHERE IskDvDevice='{$DeviceId}'");

			// cannot score so sends back
			SendResetIsk($DEVICE->IskDvCode, $DEVICE->IskDvVersion, $DEVICE->IskDvAppVersion);
		}
	} else {
		// lite app should not be allowed to arrive here... removes association in the DB and resets state
		safe_w_sql("UPDATE IskDevices SET
			IskDvTournament=0, IskDvState=0, IskDvLastSeen='".date('Y-m-d H:i:s')."'
			WHERE IskDvDevice='{$DeviceId}'");

		// lite app cannot score so sends back
		SendResetIsk($DEVICE->IskDvCode, $DEVICE->IskDvVersion, $DEVICE->IskDvAppVersion);
	}
}

$LockedSessions=getModuleParameter('ISK', 'LockedSessions', array(), $CompId);

if($TargetNo) {
	$tmp=explode('|', $TargetNo);
	if(count($tmp)==3) {
		// need to implement this one. elimination rounds!
		CreateTourSession($CompId);

		$arrIndex = $_REQUEST['arrowindex'];
		$arrValue = $_REQUEST['arrowsymbol'];

		$JsonResult['arrowsymbol']= $_REQUEST['arrowsymbol'];
		$JsonResult['curendscore']   = 0 ;
		$JsonResult['curscore']   = 0 ;
		$JsonResult['curgold']    = 0 ;
		$JsonResult['curxnine']   = 0;
		$JsonResult['score']      = 0 ;
		$JsonResult['gold']       = 0 ;
		$JsonResult['xnine']      = 0;

		// Elimination
		require_once('Elimination/Fun_Eliminations.local.inc.php');

		$JsonResult = SetElimArrowValue($tmp[0], $tmp[1], $tmp[2], $_REQUEST['arrowindex'], $_REQUEST['arrowsymbol'], 'JSON', $CompId);
		$Error=$JsonResult['error'];
	} else {
		//Qualification
		$dist = $_REQUEST['distnum'];
		$arrIndex = $_REQUEST['arrowindex'];
		$arrValue = $_REQUEST['arrowsymbol'];
		$tgt = $_REQUEST['qutarget'];

		$SQL="SELECT QuId, QuSession, QuTargetNo, QuTarget, DIDistance, DIEnds, DIArrows, ToGoldsChars, ToXNineChars, QuConfirm & ".pow(2, $dist)."=1 as StopImport, ToElabTeam, ToNumEnds 
			from Qualifications
			INNER JOIN Entries ON QuId=EnId
			INNER JOIN Tournament ON ToId=EnTournament
			INNER JOIN DistanceInformation ON DITournament=EnTournament AND DISession=QuSession AND DIDistance=".StrSafe_DB($dist)." AND DIType='Q'
			WHERE EnTournament=$CompId and QuTargetNo=".StrSafe_DB($tgt);

		$q=safe_r_SQL($SQL);
		$ArrowSearch=safe_fetch($q);

		if($ArrowSearch->StopImport) {
			// Scorecard has been verified/confirmed so the app CAN NEVER score on this distance...
			SendResetIsk($DEVICE->IskDvCode, $DEVICE->IskDvVersion, $DEVICE->IskDvAppVersion);
		}

		$endNum=intval($arrIndex/$ArrowSearch->DIArrows);
		$arrNum=$arrIndex-($endNum*$ArrowSearch->DIArrows);
		$endNum++;

		// check if the app is allowed to score here
		if(!$CanScore) {
			// check if it is not allowed YET because need to check end/distance/session
			if($CheckSequence) {
				$CanScore=true;
				// check if it is allowed based on sticky ends
				if($StickyEnds['SeqCode']) {
					if($StickyEnds['SeqCode'][0]!='Q' //sticky set on another stage!!
							or $StickyEnds['SeqCode'][2]!=$tgt[0] // sticky on Q but different session
							or $StickyEnds['Distance']!=$dist) { // sticky on a different distance
						$CanScore=false;
					}
					$StopAutoImport=(!in_array($endNum, $StickyEnds['Ends']) or $DEVICE->IskDvTarget!=intval(substr($tgt,1)));
					if($CanScore and !in_array($endNum, $StickyEnds['Ends'])) {
						$Error=2;
					}
				}

				// check if it can score based on sequence
				if(!$StopAutoImport and $CanScore and $QrCode['st']) {
					if($QrCode['st']!='Q'
							or $QrCode['s']!=$tgt[0]
							or $QrCode['d']!=$dist) {
						$CanScore=false;
					}
					// check if there is a target group
					$StopAutoImport=!in_array($DEVICE->IskDvTarget, getGroupedTargets($RealTarget=intval(substr($tgt,1)), $tgt[0], 'Q', '', true));
				}
			}

			if(!$CanScore) {
				// cannot score this end for various reasons... resets to "ask for code" only if the devices is in OK state
				safe_w_sql("UPDATE IskDevices SET IskDvState=2, IskDvLastSeen='".date('Y-m-d H:i:s')."' WHERE IskDvDevice='{$DeviceId}' and IskDvState=1");

				// cannot score so sends back
				SendResetIsk($DEVICE->IskDvCode, $DEVICE->IskDvVersion, $DEVICE->IskDvAppVersion);
			}
		}

		if($Error!=2) {
			$arrString = str_repeat(' ',$ArrowSearch->DIArrows);
			$SQL = "SELECT QuD{$ArrowSearch->DIDistance}Arrowstring as Arrowstring 
                FROM Qualifications INNER JOIN Entries ON QuId=EnId
                WHERE EnTournament={$CompId} AND QuTargetNo='{$ArrowSearch->QuTargetNo}' ";
            $q=safe_r_SQL($SQL);
            if($r=safe_fetch($q)) {
                $arrString=str_pad(substr($r->Arrowstring,($endNum-1)*$ArrowSearch->DIArrows,$ArrowSearch->DIArrows),$ArrowSearch->DIArrows, ' ', STR_PAD_RIGHT);
            }
			$SQL = "SELECT IskDtArrowstring
				FROM IskData
				WHERE IskDtTournament={$CompId} AND IskDtMatchNo=0 AND IskDtEvent='' AND IskDtTeamInd=0 AND IskDtType='Q'
				AND IskDtTargetNo='{$ArrowSearch->QuTargetNo}' AND IskDtDistance={$ArrowSearch->DIDistance} AND IskDtEndNo={$endNum}";
			$q=safe_r_SQL($SQL);
			if($r=safe_fetch($q)) {
                for($i=0; $i<$ArrowSearch->DIArrows; $i++){
                    if($r->IskDtArrowstring[$i]!=' '){
                        $arrString[$i]=$r->IskDtArrowstring[$i];
                    }
                }
			}
			// check if the "key" of the session is allowed to score
			$LockKey='Q|'.$ArrowSearch->QuTargetNo[0].'|'.intval($ArrowSearch->DIDistance);
			if(in_array($LockKey, $LockedSessions)) {
				$StopAutoImport = true;
				$Error=2;
			} else {
				$arrString[$arrNum] = GetLetterFromPrint($arrValue,$ArrowSearch->QuId,$ArrowSearch->DIDistance);
				$SQL = "INSERT INTO IskData (IskDtTournament, IskDtMatchNo, IskDtEvent, IskDtTeamInd, IskDtType, IskDtTargetNo, IskDtDistance, IskDtEndNo, IskDtArrowstring, IskDtUpdate, IskDtDevice)
						VALUES ({$CompId}, 0, '', 0, 'Q', '{$ArrowSearch->QuTargetNo}', {$ArrowSearch->DIDistance}, {$endNum}, '{$arrString}', '".date('Y-m-d H:i:s')."', '{$DeviceId}')
						ON DUPLICATE KEY UPDATE IskDtArrowstring='{$arrString}', IskDtUpdate='".date('Y-m-d H:i:s')."', IskDtDevice= '{$DeviceId}'";
				safe_w_SQL($SQL);

				// check if the import needs to be done after all arrows of the end are in
				if(defined('IMPORT_TYPE') and IMPORT_TYPE) {
					$iskStopAutoImport=(IMPORT_TYPE==2 or strlen(trim($arrString))!=$ArrowSearch->DIArrows);
				}
				$Error=0;
			}
		}

		if(!$iskStopAutoImport and !$StopAutoImport) {
			importQualifications($ArrowSearch->QuId, $ArrowSearch->DIDistance, $endNum) ;
		}

		$tmp = getQualificationTotals($ArrowSearch->QuId, $ArrowSearch->DIDistance, $endNum, $ArrowSearch->DIArrows, $ArrowSearch->DIEnds, $ArrowSearch->ToGoldsChars, $ArrowSearch->ToXNineChars, $ArrowSearch->ToElabTeam ? $ArrowSearch->QuTarget : null);

		$JsonResult=array();
		$JsonResult['qutarget']      = $ArrowSearch->QuTargetNo;
		$JsonResult['distnum']       = (string) $ArrowSearch->DIDistance;
		$JsonResult['arrowindex']    = (string) $arrIndex;
		$JsonResult['arrowsymbol']   = DecodeFromLetter($tmp['curendarrstr'][$arrNum]);
		$JsonResult['curendscore']   = $tmp['curendscore'];
		$JsonResult['curscore']      = $tmp['curscore'];
		$JsonResult['curscoreatend'] = $tmp['curscoreatend'];
		$JsonResult['curgold']       = $tmp['curgold'];
		$JsonResult['curxnine']      = $tmp['curxnine'];
		$JsonResult['score']         = $tmp['score'];
		$JsonResult['scoreatend']    = $tmp['scoreatend'];
		$JsonResult['gold']          = $tmp['gold'];
		$JsonResult['xnine']         = $tmp['xnine'];
		//$JsonResult['arrowstring']   = $tmp['arrowstring'];
		$JsonResult['arrowstrings']  = $tmp['arrowstrings'];

	}
} else {
	$JsonResult=array();
	$arrIndex = $_REQUEST['arrowindex'];
	$arrValue = $_REQUEST['arrowsymbol'];
	$obj=getEventArrowsParams($Event,getPhase($MatchNo),$EventType,$CompId);
	$tmpArrowString='';
	$tgtType=0;
	$Error = 1;
	$SQL = "SELECT EvFinalTargetType FROM Events WHERE EvCode='{$Event}' AND EvTeamEvent={$EventType} AND EvTournament={$CompId}";
	$q=safe_r_sql($SQL);
	if($r=safe_fetch($q)) {
		$tgtType = $r->EvFinalTargetType;
		$Error = 0;
	}

	if(empty($arrValue)) {
		$tmpArrowString = ' ';
	} else {
		$tmpArrowString=GetLetterFromPrint($arrValue, 'T', $tgtType);
		if($tmpArrowString==' ')
			$Error = 1;
	}
	if($arrIndex>=($obj->arrows*$obj->ends)+$obj->so) {
		$Error = 1;
	}

	if(!$Error) {
		$isSo = ($arrIndex >= ($obj->ends*$obj->arrows));
		$endNum=($isSo ? ($obj->ends) : intval($arrIndex/$obj->arrows));
		$arrNum=($isSo ? $arrIndex-($obj->ends*$obj->arrows) : $arrIndex-($endNum*$obj->arrows));
		$endNum++;

		// check if the app is allowed to score here
		if(!$CanScore) {
			// check if it is not allowed YET because need to check end/distance/session
			if($CheckSequence) {
				$MatchNo2=($MatchNo%2 ? $MatchNo-1 : $MatchNo+1);
				$Select = "SELECT DISTINCT CONCAT(IF(FSTeamEvent=0,'I','T'), FSScheduledDate, FSScheduledTime) AS keyValue, max($DEVICE->IskDvTarget = FsTarget+0) as Target
						FROM FinSchedule
						WHERE FSTournament={$CompId} and FSScheduledDate>0 and FsTeamEvent={$EventType} and FsEvent='{$Event}' and FsMatchNo in ({$MatchNo}, $MatchNo2)
						group by FsTeamEvent, FsEvent";
				$chkQ=safe_r_sql($Select);
				$chkR=safe_fetch($chkQ);

				$CanScore=$chkR;
				// check if it is allowed based on sticky ends
				if($CanScore and $StickyEnds['SeqCode']) {
					if($StickyEnds['SeqCode']!=$chkR->keyValue) {
						$CanScore=false;
					}
					$StopAutoImport=(!in_array($endNum, $StickyEnds['Ends']) or !$chkR->Target);
					if($CanScore and !in_array($endNum, $StickyEnds['Ends'])) {
						$Error=2;
					}
				}

				// check if it can score based on sequence
				if($CanScore and $QrCode['st']) {
					if($QrCode['st']!='M'.$EventTypeLetter) {
						$CanScore=false;
					}
					$StopAutoImport=(!$chkR->Target);
				}
			}
			if(!$CanScore) {
				// cannot score this end for various reasons... resets to "ask for code" only if the devices is in OK state
				safe_w_sql("UPDATE IskDevices SET IskDvState=2, IskDvLastSeen='".date('Y-m-d H:i:s')."' WHERE IskDvDevice='{$DeviceId}' and IskDvState=1");

				// cannot score so sends back
				SendResetIsk($DEVICE->IskDvCode, $DEVICE->IskDvVersion, $DEVICE->IskDvAppVersion);
			}
		}

		if($Error!=2) {
			// check if the score is confirmed
			$prefix=($EventType ? 'Tf' : 'Fin');
			$SQL= "select * from ".($EventType ? 'Team' : '')."Finals
				where {$prefix}Confirmed=1
				/* and {$prefix}Status=1 */
				and {$prefix}Tournament={$CompId}
				and {$prefix}Event='{$Event}'
				and {$prefix}Matchno={$MatchNo}";

			$q=safe_r_sql($SQL);
			if(safe_num_rows($q)) {
				// arrow arrived on a confirmed score, so RESET the device...
				SendResetIsk($DEVICE->IskDvCode, $DEVICE->IskDvVersion, $DEVICE->IskDvAppVersion);
			}

			$arrString = str_repeat(" ",($isSo ? $obj->so : $obj->arrows));
			$SQL = "SELECT IskDtArrowstring
				FROM IskData
				WHERE IskDtTournament={$CompId} AND IskDtMatchNo={$MatchNo} AND IskDtEvent='{$Event}' AND IskDtTeamInd={$EventType} AND IskDtType='{$EventTypeLetter}'
				AND IskDtTargetNo='' AND IskDtDistance=0 AND IskDtEndNo={$endNum}";
			$q=safe_r_SQL($SQL);
			if($r=safe_fetch($q)) {
				$arrString=$r->IskDtArrowstring;
			}
			// check if the "key" of the session is allowed to score
			$LockKey=($EventType==0 ? 'I':'T').'|'.getPhase($MatchNo).'|'.$Event;
			if(in_array($LockKey, $LockedSessions)) {
				$StopAutoImport = true;
				$Error=2;
			} else {
				$arrString[$arrNum] = $tmpArrowString;
				$SQL = "INSERT INTO IskData (IskDtTournament, IskDtMatchNo, IskDtEvent, IskDtTeamInd, IskDtType, IskDtTargetNo, IskDtDistance, IskDtEndNo, IskDtArrowstring, IskDtUpdate, IskDtDevice)
						VALUES ({$CompId}, {$MatchNo}, '{$Event}', {$EventType}, '{$EventTypeLetter}', '', 0, {$endNum}, '{$arrString}', '".date('Y-m-d H:i:s')."', '{$DeviceId}')
				ON DUPLICATE KEY UPDATE IskDtArrowstring='{$arrString}', IskDtUpdate='".date('Y-m-d H:i:s')."', IskDtDevice= '{$DeviceId}'";
				safe_w_SQL($SQL);
				$Error=0;

				// check if the import needs to be done after all arrows of the end are in
				if(defined('IMPORT_TYPE') and IMPORT_TYPE) {
					$iskStopAutoImport=(IMPORT_TYPE==2 or strlen(trim($arrString))!=($isSo ? $obj->so : $obj->arrows));
				}
			}

			if(!$iskStopAutoImport and !$StopAutoImport) {
				importMatches($Event, $MatchNo, $EventType, $endNum, $obj->arrows, $obj->ends, $obj->so, $arrNum);
			}

		}

		$tmp = getMatchTotals($Event, $MatchNo, $EventType, $endNum, $obj->arrows, $obj->ends, $obj->so);

		$JsonResult['matchid']       = $Event."|".($EventType==0 ? 'I':'T')."|".$MatchNo;
		$JsonResult['distnum']       = "1";
		$JsonResult['arrowindex']    = (string) $arrIndex;
		$JsonResult['arrowsymbol']   = DecodeFromLetter($isSo ? $tmp['arrowstring'][$arrNum] : $tmp['arrowstring'][$arrIndex]);
		$JsonResult['curendscore']   = $tmp['curendscore'];
		$JsonResult['curscore']      = $tmp['curscore'];
		$JsonResult['curscoreatend'] = $tmp['curscoreatend'];
		$JsonResult['curgold']       = $tmp['curgold'];
		$JsonResult['curxnine']      = $tmp['curxnine'];
		$JsonResult['score']         = $tmp['score'];
		$JsonResult['scoreatend']    = $tmp['scoreatend'];
		$JsonResult['gold']          = $tmp['gold'];
		$JsonResult['xnine']         = $tmp['xnine'];
		$JsonResult['arrowstrings']  = array($tmp['arrowstring']);
	}
}
$JsonResult['error'] = $Error;

SendResult($JsonResult);
