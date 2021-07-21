<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/Fun_Phases.inc.php');

$Device = (!empty($_GET['devid']) ? $_GET['devid'] : '');
$DistanceNum = (!empty($_GET['distnum']) ? intval($_GET['distnum']) : 1);
$EndNum = (!empty($_GET['endnum']) ? $_GET['endnum'] : 1);
$TargetNo= (!empty($_GET['qutarget']) ? $_GET['qutarget'] : 0);
list($Event,$EventTypeLetter,$MatchNo) = explode("|",(!empty($_GET['matchid']) ? $_GET['matchid'] : "0|I|0"));
$EventType=($EventTypeLetter=='T' ? 1 : 0);

$ScoreRow=NULL;
$json_array=array(
	'distnum' => $DistanceNum,
	'endnum' => $EndNum,
	'prevendscored' => false,
	'curendscore' => '',
	'curscoreatend' => '',
	'scoreatend' => '',
	'arrowvalues' => array(),
	'locked' => false
	);

$StickyEnds=getModuleParameter('ISK', 'StickyEnds', array('SeqCode'=>'', 'Distance'=>'', 'Ends'=>array()), $CompId);
$LockedSessions=getModuleParameter('ISK', 'LockedSessions', array(), $CompId);


if($TargetNo) {
	$tmp=explode('|', $TargetNo);
	$json_array['qutarget']= $TargetNo;
	if(count($tmp)==3) {
		// ELIMINATION
		$LockKey='E|'.($tmp[0][1]-1).'|'.$tmp[1];
		$Select	= "SELECT ElArrowString AS ArrowString, if(ElElimPhase=0, EvE1Arrows, EvE2Arrows) DiArrows
			FROM Eliminations
			INNER JOIN Events on ElEventCode=EvCode and ElTournament=EvTournament and EvTeamEvent=0
			WHERE ElTargetNo=" . StrSafe_DB($tmp[2]) . "
				AND ElEventCode='{$tmp[1]}'
				AND ElElimPhase=".($tmp[0][1]-1)."
				AND ElTournament=$CompId";

		// Retrieve the score info
		$Rs=safe_r_sql($Select);

		if (safe_num_rows($Rs) == 1) {
			// Now load the json array with the info we need
			$ScoreRow=safe_fetch($Rs);


			$StartPos = (empty($ScoreRow->isSO) ? ($EndNum-1) * $ScoreRow->DiArrows : 0);
			$CurEnd=str_replace(' ', '', substr($ScoreRow->ArrowString, $StartPos, $ScoreRow->DiArrows));
			if($CurEnd) $json_array["curendscore"] = ValutaArrowString($CurEnd);

			$arrow_array = Array();
			for($j=0; $j < $ScoreRow->DiArrows; $j++) {
				array_push($arrow_array, DecodeFromLetter(substr($ScoreRow->ArrowString, $StartPos+$j, 1)) );
			}
			$json_array["arrowvalues"] = $arrow_array;
			$json_array["prevendscored"] = (
					($EndNum==1 and ($DistanceNum==1 or trim(substr($ScoreRow->PrevArrowString, ($ScoreRow->PrevEnds-1)*$ScoreRow->PrevArrows, $ScoreRow->PrevArrows)))
						 or (trim(substr($ScoreRow->ArrowString, $StartPos-$ScoreRow->DiArrows, $ScoreRow->DiArrows)))));
		}
		// check sticky ends
		// get the sequence this match belongs to
		if($StickyEnds['SeqCode'] and $StickyEnds['SeqCode'][0]=='E' and $StickyEnds['SeqCode'][2]==$TargetNo[0]) {
			$json_array['locked']=true;
			if(in_array($EndNum, $StickyEnds['Ends']) and $DistanceNum==$StickyEnds['Distance']) {
				$json_array['locked']=false;
			}
		}
	} else {
		// Qualification
		$LockKey='Q|'.$TargetNo[0].'|'.$DistanceNum;
		$SQL="SELECT QuId, QuTargetNo, QuTarget, DIDistance, QuD{$DistanceNum}Arrowstring as Arrowstring, DIEnds, DIArrows, ToGoldsChars, ToXNineChars, ToElabTeam, ToNumEnds from Qualifications
			INNER JOIN Entries ON QuId=EnId
			INNER JOIN Tournament ON ToId=EnTournament
			INNER JOIN DistanceInformation ON DITournament=EnTournament AND DISession=QuSession AND DIDistance=".StrSafe_DB($DistanceNum)." AND DIType='Q'
			WHERE EnTournament=$CompId and QuTargetNo=".StrSafe_DB($TargetNo);
		$q=safe_r_SQL($SQL);
		$ArrowSearch=safe_fetch($q);
		if($ArrowSearch) {
			$tmp = getQualificationTotals($ArrowSearch->QuId, $ArrowSearch->DIDistance, $EndNum, $ArrowSearch->DIArrows, $ArrowSearch->DIEnds, $ArrowSearch->ToGoldsChars, $ArrowSearch->ToXNineChars, $ArrowSearch->ToElabTeam ? $ArrowSearch->QuTarget : null);
			$json_array['curendscore']   = $tmp['curendscore'];
			$json_array['curscoreatend'] = $tmp['curscoreatend'];
			$json_array['scoreatend']    = $tmp['scoreatend'];
			$json_array['prevendscored'] = $tmp['prevendscored'];
			for($i=0; $i<$ArrowSearch->DIArrows; $i++) {
				$json_array["arrowvalues"][] = DecodeFromLetter($tmp['curendarrstr'][$i]);
			}
		}

		// check sticky ends
		// get the sequence this match belongs to
		if($StickyEnds['SeqCode'] and $StickyEnds['SeqCode'][0]=='Q' and $StickyEnds['SeqCode'][2]==$TargetNo[0]) {
			$json_array['locked']=true;
			if(in_array($EndNum, $StickyEnds['Ends']) and $DistanceNum==$StickyEnds['Distance']) {
				$json_array['locked']=false;
			}
		}
	}
} else {
	$LockKey=($EventType==0 ? 'I':'T').'|'.getPhase($MatchNo).'|'.$Event;

	$json_array['matchid']= $Event."|".($EventType==0 ? 'I':'T')."|".$MatchNo;

	$obj=getEventArrowsParams($Event,getPhase($MatchNo),$EventType,$CompId);
	$tmp = getMatchTotals($Event, $MatchNo, $EventType, $EndNum, $obj->arrows, $obj->ends, $obj->so);
	$json_array['curendscore']   = $tmp['curendscore'];
	$json_array['curscoreatend'] = $tmp['curscoreatend'];
	$json_array['scoreatend']    = $tmp['scoreatend'];
	$json_array['prevendscored'] = ($EndNum==1 || $EndNum==$obj->ends+1 || (trim(substr($tmp['tilendarrstr'],-2*$obj->arrows)) !=''));
	for($i=0; $i<($EndNum==$obj->ends+1 ? $obj->so:$obj->arrows); $i++) {
		$json_array["arrowvalues"][] = DecodeFromLetter($tmp['curendarrstr'][$i]);
	}

	// check sticky ends
	// get the sequence this match belongs to
	$q=safe_r_sql("select concat('".($EventType==0 ? 'I':'T')."', FsScheduledDate, FsScheduledTime) as EventKey from FinSchedule where FsTournament=$CompId and FsTeamEvent=$EventType and FsEvent='$Event' and FsMatchNo=$MatchNo");
	if($r=safe_fetch($q) and $r->EventKey==$StickyEnds['SeqCode']) {
		$json_array['locked']=true;
		if(in_array($EndNum, $StickyEnds['Ends'])) {
			$json_array['locked']=false;
		}
	}
}

if(in_array($LockKey, $LockedSessions)) {
	$json_array['locked']=true;
}

// Return the json structure with the callback function that is needed by the app
SendResult($json_array);
