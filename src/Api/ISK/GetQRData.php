<?php
require_once(dirname(dirname(__FILE__)).'/config.php');
require_once('Common/Lib/ArrTargets.inc.php');

require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/Fun_Modules.php');

CheckTourSession(true);
checkACL(AclISKServer, AclReadWrite);

$PAGE_TITLE=get_text('ISK-GetQRData');

include('Common/Templates/head-min.php');

if(!isset($_REQUEST["Data"])) {
	echo '<form>';
	echo '<table class="Tabella">';
	echo '<tr><th>'.get_text('ISK-GetQRData').'</th></tr>';
	echo '<tr><td class="Center">';
	echo '<textarea name="Data" cols="70" rows="15"></textarea>';
	echo '</td></tr>';
	echo '<tr><td class="Center"><input type="submit" value="'.get_text("CmdSend","Tournament").'"></td></tr>';
	echo '</table>';
	echo '</form>';
} else {
	$data = json_decode($_REQUEST["Data"]);
	if(is_null($data)) {
		echo "invalid data";
	} else {
		$CompId=getIdFromCode($data->compcode);
		foreach($data->ends as $end) {
			$qValue = explode("|",$end->q);
			if(count($qValue)==1) {	//Qualification
				$dist = $end->d;
				$endNum=($end->e+1);
				$tgt = $qValue[0];

				$SQL="SELECT QuId, QuSession, QuTargetNo, DIDistance, DIEnds, DIArrows, ToGoldsChars, ToXNineChars, QuConfirm & ".pow(2, $dist)."=1 as StopImport 
					from Qualifications
					INNER JOIN Entries ON QuId=EnId
					INNER JOIN Tournament ON ToId=EnTournament
					INNER JOIN DistanceInformation ON DITournament=EnTournament AND DISession=QuSession AND DIDistance=".StrSafe_DB($dist)." AND DIType='Q'
					WHERE EnTournament=$CompId and QuTargetNo=".StrSafe_DB($tgt);
				$q=safe_r_SQL($SQL);
				$ArrowSearch=safe_fetch($q);

				if($ArrowSearch->StopImport) {
					continue;
				}

				$arrString = str_repeat(" ",$ArrowSearch->DIArrows);
				$SQL = "SELECT IskDtArrowstring
					FROM IskData
					WHERE IskDtTournament={$CompId} AND IskDtMatchNo=0 AND IskDtEvent='' AND IskDtTeamInd=0 AND IskDtType='Q'
					AND IskDtTargetNo='{$ArrowSearch->QuTargetNo}' AND IskDtDistance={$ArrowSearch->DIDistance} AND IskDtEndNo={$endNum}";
				$q=safe_r_SQL($SQL);
				if($r=safe_fetch($q)) {
					$arrString=$r->IskDtArrowstring;
				}
				for($i=0; $i<count($end->s); $i++) {
					if($end->s[$i]!=-1) {
						$arrString[$i] = GetLetterFromPrint($end->s[$i],$ArrowSearch->QuId,$ArrowSearch->DIDistance);
					}
				}
				$SQL = "INSERT INTO IskData (IskDtTournament, IskDtMatchNo, IskDtEvent, IskDtTeamInd, IskDtType, IskDtTargetNo, IskDtDistance, IskDtEndNo, IskDtArrowstring, IskDtUpdate, IskDtDevice)
					VALUES ({$CompId}, 0, '', 0, 'Q', '{$ArrowSearch->QuTargetNo}', {$ArrowSearch->DIDistance}, {$endNum}, '{$arrString}', '".date('Y-m-d H:i:s')."', '')
					ON DUPLICATE KEY UPDATE IskDtArrowstring='{$arrString}', IskDtUpdate='".date('Y-m-d H:i:s')."', IskDtDevice= ''";
				safe_w_SQL($SQL);
			} else {	//Matches

			}

		}

	}
}

include('Common/Templates/tail-min.php');