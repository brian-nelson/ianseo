<?php

require_once('Common/Lib/ArrTargets.inc.php');

function SetElimArrowValue($Phase, $Event, $Target, $ArIndex, $ArSymbol, $Output='XML', $CompId='') {
	global $LockedSessions;
	require_once('Common/Lib/Obj_RankFactory.php');
	$JsonResult=array();
	$JsonResult['error']      = 1;
	$JsonResult['qutarget']   = $_REQUEST['qutarget'];
	$JsonResult['distnum']    = $_REQUEST['distnum'] ;
	$JsonResult['arrowindex'] = $_REQUEST['arrowindex'] ;
	$JsonResult['arrowsymbol']= '';
	$JsonResult['curendscore']   = '';
	$JsonResult['curscore']   = '';
	$JsonResult['curgold']    = '';
	$JsonResult['curxnine']   = '';
	$JsonResult['score']      = '';
	$JsonResult['gold']       = '';
	$JsonResult['xnine']      = '';
	$JsonResult['arrowstrings'] = array();

	if(empty($CompId)) $CompId=$_SESSION['TourId'];

	$q=safe_r_sql("select el.*, if(ElElimPhase=0, EvE1Arrows, EvE2Arrows) EvElimArrows, if(ElElimPhase=0, EvE1Ends, EvE2Ends) EvElimEnds
		from Eliminations el
		INNER JOIN Events on ElEventCode=EvCode and ElTournament=EvTournament and EvTeamEvent=0
		where ElElimPhase=".($Phase[1]-1)."
		and ElEventCode='$Event'
		and ElTargetNo='$Target'
		and ElTournament=$CompId
		");
	if($r=safe_fetch($q)) {
		$Arrowstring=str_pad($r->ElArrowString, $r->EvElimArrows*$r->EvElimEnds, ' ', STR_PAD_RIGHT);

		// check if the session is locked


		// check if there is a locked session
		$LockKey='E|'.($Phase[1]-1).'|'.$Event;
		if(!empty($LockedSessions) and in_array($LockKey, $LockedSessions)) {
			$ArSymbol=DecodeFromLetter($Arrowstring[$ArIndex]);
			list($CurScore, $CurGold, $CurXNine)=ValutaArrowStringGX($Arrowstring);
			$JsonResult["curendscore"] = ValutaArrowString(substr($Arrowstring, intval($ArIndex/$r->EvElimArrows)*$r->EvElimArrows, $r->EvElimArrows));
			$JsonResult['error']      = 2;
		} else {
			$xx=GetLetterFromPrint($ArSymbol);
			$Arrowstring[$ArIndex]=str_pad($xx, 1, ' ', STR_PAD_RIGHT);

			$JsonResult['error']      = 0;
			list($CurScore, $CurGold, $CurXNine)=ValutaArrowStringGX($Arrowstring);

			$JsonResult["curendscore"] = ValutaArrowString(substr($Arrowstring, intval($ArIndex/$r->EvElimArrows)*$r->EvElimArrows, $r->EvElimArrows));

			safe_w_sql("update Eliminations
				set ElArrowString='{$Arrowstring}',
				ElScore=$CurScore,
				ElGold=$CurGold,
				ElXnine=$CurXNine
				where ElElimPhase=".($Phase[1]-1)."
				and ElEventCode='$Event'
				and ElTargetNo='$Target'
				and ElTournament=$CompId");

			if(safe_w_affected_rows()) {
				if($Phase[1]==1) {
					require_once('Common/Fun_Sessions.inc.php');
					ResetElimRows($Event, 2);
				}
				Obj_RankFactory::create('ElimInd',array('tournament'=>$CompId, 'eventsC'=>array($Event.'@'.$Phase[1])))->calculate();
			}
		}


		$JsonResult['arrowsymbol']= $ArSymbol;
		$JsonResult['curscore']   = $CurScore;
		$JsonResult['curgold']    = $CurGold;
		$JsonResult['curxnine']   = $CurXNine;
		$JsonResult['score']      = $CurScore;
		$JsonResult['gold']       = $CurGold;
		$JsonResult['xnine']      = $CurXNine;
		$JsonResult['arrowstrings'] = array($Arrowstring);
	}

	return $JsonResult;
}
