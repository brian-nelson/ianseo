<?php

require_once(dirname(__FILE__) . '/config.php');

CreateTourSession($CompId);
$TargetNo=!empty($_GET['qutarget']) ? $_GET['qutarget'] : 0;
list($Event,$EventType,$MatchNo) = explode("|",(!empty($_GET['matchid']) ? $_GET['matchid'] : "0|0|0"));
$EventType=($EventType=='T' ? 1 : 0);
$JsonResult=array();

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
if($TargetNo) {
	$tmp=explode('|', $TargetNo);
	if(count($tmp)==3) {
		// Elimination
		require_once('Elimination/Fun_Eliminations.local.inc.php');

		$JsonResult = SetElimArrowValue($tmp[0], $tmp[1], $tmp[2], $_REQUEST['arrowindex'], $_REQUEST['arrowsymbol'], 'JSON', $CompId);
	} else {
		// Qualification
		require_once('Qualification/Fun_Qualification.local.inc.php');
		$ArrowsPerEnd=3;

		$SQL="Select QuId, QuSession from Qualifications
			inner join Entries on QuId=EnId
			where EnTournament=$CompId and QuTargetNo=".StrSafe_DB($_REQUEST['qutarget']);
		$q=safe_r_SQL($SQL);
		$ArrowSearch=safe_fetch($q);
		// debug_svela($r);

		$PageOutput='JSON';


		$_REQUEST['Index']=$_REQUEST['arrowindex'];
		$_REQUEST['Dist']=$_REQUEST['distnum'];
		$_REQUEST['Id']=$ArrowSearch->QuId;
		$_REQUEST['Point']=$_REQUEST['arrowsymbol'];

		$BlockApi=false;

		require_once('Qualification/UpdateArrow.php');

		if(!$BlockApi and !$JsonResult['error']) {
			useArrowsSnapshot($ArrowSearch->QuSession, $_REQUEST['distnum'], substr($_REQUEST['qutarget'],1,-1), substr($_REQUEST['qutarget'],1,-1), $_REQUEST['arrowindex']+1);
			recalSnapshot($ArrowSearch->QuSession, $_REQUEST['distnum'], substr($_REQUEST['qutarget'],1,-1), substr($_REQUEST['qutarget'],1,-1));
		}
	}
} else {
	require_once('Final/Fun_MatchTotal.inc.php');
	$tgtType=0;
	$Error = 1;
	$SQL = "SELECT EvFinalTargetType FROM Events WHERE EvCode='" . $Event . "' AND EvTeamEvent=$EventType AND EvTournament=$CompId";
	$Rs=safe_r_sql($SQL);
	if($r=safe_fetch($Rs)) {
		$tgtType = $r->EvFinalTargetType;
		$Error = 0;
	}
	$arrowIndex = $_REQUEST['arrowindex']+1;

	if(empty($_REQUEST['arrowsymbol'])) {
		$tmpLetter = ' ';
	} else {
		$tmpLetter=GetLetterFromPrint($_REQUEST['arrowsymbol'], 'T', $tgtType);
		if($tmpLetter==' ')
			$Error = 1;
	}
	if(!$Error) {
		UpdateArrowString($MatchNo, $Event, $EventType, $tmpLetter, $arrowIndex, $arrowIndex);
	}

	$JsonResult=array();
	$JsonResult['error']      = $Error;
	$JsonResult['matchid']   = $Event . "|" . ($EventType ? "T" : "I") . "|" . $MatchNo;
	$JsonResult['distnum']    = 1;
	$JsonResult['arrowindex'] = $arrowIndex -1 ;
	$JsonResult['arrowsymbol']= $_REQUEST['arrowsymbol'];
	$JsonResult['curscore']   = 0 ;
	$JsonResult['curgold']    = 0 ;
	$JsonResult['curxnine']   = 0;
	$JsonResult['score']      = 0 ;
	$JsonResult['gold']       = 0 ;
	$JsonResult['xnine']      = 0;
}

SendResult($JsonResult);
